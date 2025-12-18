<?php

namespace App\Http\Controllers;

use App\Models\PenyesuaianStok;
use App\Models\PenyesuaianItem;
use App\Models\BahanBaku;
use App\Models\StockMovement;
use App\Events\StokUpdated;
use App\Events\PenyesuaianStokCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenyesuaianStokController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $query = PenyesuaianStok::where('id_cabang', $idCabang)
            ->withCount('items');
        
        // Filter berdasarkan tanggal
        if ($request->has('tanggal') && $request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        $rows = $query->latest()->paginate(10)->withQueryString();
        return view('inventori.penyesuaian.index', compact('rows'));
    }

    public function create()
    {
        $idCabang = (int) session('id_cabang');
        $bahan = BahanBaku::where('id_cabang', $idCabang)->orderBy('nama_bahan')->get();
        return view('inventori.penyesuaian.create', compact('bahan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.bahan_baku_id' => ['required', 'integer', 'exists:bahan_baku,id'],
            'items.*.stok_baru' => ['required', 'numeric', 'min:0'],
            'items.*.keterangan' => ['nullable', 'string'],
        ]);

        $validated['id_cabang'] = (int) session('id_cabang');

        DB::transaction(function () use ($validated) {
            $penyesuaian = PenyesuaianStok::create([
                'id_cabang' => $validated['id_cabang'],
                'tanggal' => $validated['tanggal'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            foreach ($validated['items'] as $row) {
                $bahan = BahanBaku::find($row['bahan_baku_id']);
                $stokLama = $bahan->stok;
                $stokBaru = $row['stok_baru'];
                $selisih = $stokBaru - $stokLama;

                $penyesuaian->items()->create([
                    'bahan_baku_id' => $row['bahan_baku_id'],
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,
                    'selisih' => $selisih,
                    'keterangan' => $row['keterangan'] ?? null,
                ]);

                $bahan->update(['stok' => $stokBaru]);
                
                // Cek jika stok habis setelah penyesuaian (observer akan handle ini, tapi kita pastikan)
                $bahan->refresh();
                if ($stokLama > 0 && $bahan->stok <= 0) {
                    \App\Events\StokHabis::dispatch($bahan);
                }
                
                // Broadcast stok updated
                try {
                    broadcast(new StokUpdated($validated['id_cabang'], [
                        'tipe' => 'bahan_baku',
                        'id' => $bahan->id,
                        'nama' => $bahan->nama_bahan,
                        'stok' => $bahan->stok,
                    ]));
                } catch (\Exception $e) {
                    \Log::warning('Failed to broadcast stock update: ' . $e->getMessage());
                }

                StockMovement::create([
                    'bahan_baku_id' => $row['bahan_baku_id'],
                    'tipe' => 'adj',
                    'qty' => $selisih,
                    'ref_type' => 'penyesuaian',
                    'ref_id' => $penyesuaian->id,
                    'id_cabang' => $validated['id_cabang'],
                    'keterangan' => 'Penyesuaian stok',
                ]);
            }
            
            // Broadcast penyesuaian created
            try {
                $penyesuaian->load('items.bahan');
                $tanggal = $penyesuaian->tanggal instanceof Carbon 
                    ? $penyesuaian->tanggal->format('d/m/Y')
                    : Carbon::parse($penyesuaian->tanggal)->format('d/m/Y');
                broadcast(new PenyesuaianStokCreated($validated['id_cabang'], [
                    'id' => $penyesuaian->id,
                    'tanggal' => $tanggal,
                    'catatan' => $penyesuaian->catatan,
                ]));
            } catch (\Exception $e) {
                \Log::warning('Failed to broadcast penyesuaian: ' . $e->getMessage());
            }
        });

        return redirect()->route('penyesuaian.index')->with('success', 'Penyesuaian stok berhasil disimpan');
    }

    public function show(PenyesuaianStok $penyesuaian)
    {
        $idCabang = (int) session('id_cabang');
        
        // Pastikan penyesuaian milik cabang yang aktif
        if ($penyesuaian->id_cabang != $idCabang) {
            abort(403, 'Unauthorized');
        }

        $penyesuaian->load('items.bahan');
        return view('inventori.penyesuaian.show', compact('penyesuaian'));
    }
}

