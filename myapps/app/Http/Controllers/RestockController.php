<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\RestockItem;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Events\StokUpdated;
use App\Events\RestockCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestockController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $query = Restock::where('id_cabang', $idCabang)
            ->withCount('items')
            ->with('supplier');
        
        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_nota', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('nama_supplier', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('tanggal') && $request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        $restocks = $query->latest('tanggal')->paginate(10)->withQueryString();
        return view('inventori.restock.index', compact('restocks'));
    }

    public function create()
    {
        $idCabang = (int) session('id_cabang');
        $suppliers = Supplier::where('id_cabang', $idCabang)->orderBy('nama_supplier')->get();
        $bahan = BahanBaku::where('id_cabang', $idCabang)->orderBy('nama_bahan')->get();
        return view('inventori.restock.create', compact('suppliers', 'bahan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'supplier_id' => ['required', 'integer', 'exists:supplier,id'],
            'no_nota' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'ppn' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.bahan_baku_id' => ['required', 'integer', 'exists:bahan_baku,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['id_cabang'] = (int) session('id_cabang');

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            foreach ($validated['items'] as $row) {
                $subtotal += ($row['qty'] * $row['harga_satuan']);
            }

            // Hitung diskon dan PPN
            $diskon = $validated['diskon'] ?? 0;
            $subtotalSetelahDiskon = $subtotal - $diskon;
            $ppn = $validated['ppn'] ?? 0;
            $total = $subtotalSetelahDiskon + $ppn;

            $restock = Restock::create([
                'id_cabang' => $validated['id_cabang'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'no_nota' => $validated['no_nota'] ?? null,
                'tanggal' => $validated['tanggal'],
                'catatan' => $validated['catatan'] ?? null,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'ppn' => $ppn,
                'total' => $total,
            ]);

            foreach ($validated['items'] as $row) {
                $item = $restock->items()->create([
                    'bahan_baku_id' => $row['bahan_baku_id'],
                    'qty' => $row['qty'],
                    'harga_satuan' => $row['harga_satuan'],
                    'subtotal' => $row['qty'] * $row['harga_satuan'],
                ]);

                $bahanBaku = BahanBaku::find($row['bahan_baku_id']);
                if ($bahanBaku) {
                    // Increment stok (tidak perlu cek stok habis karena ini restock)
                    $bahanBaku->increment('stok', $row['qty']);
                    $bahanBaku->refresh();
                    
                    // Broadcast stok updated
                    try {
                        broadcast(new StokUpdated($validated['id_cabang'], [
                            'tipe' => 'bahan_baku',
                            'id' => $bahanBaku->id,
                            'nama' => $bahanBaku->nama_bahan,
                            'stok' => $bahanBaku->stok,
                        ]));
                    } catch (\Exception $e) {
                        \Log::warning('Failed to broadcast stock update: ' . $e->getMessage());
                    }
                }

                StockMovement::create([
                    'bahan_baku_id' => $row['bahan_baku_id'],
                    'tipe' => 'in',
                    'qty' => $row['qty'],
                    'ref_type' => 'restock',
                    'ref_id' => $restock->id,
                    'id_cabang' => $validated['id_cabang'],
                    'keterangan' => 'Restock',
                ]);
            }
            
            // Broadcast restock created
            try {
                $restock->load('supplier', 'items.bahan');
                $tanggal = $restock->tanggal instanceof \Carbon\Carbon 
                    ? $restock->tanggal->format('d/m/Y')
                    : Carbon::parse($restock->tanggal)->format('d/m/Y');
                broadcast(new RestockCreated($validated['id_cabang'], [
                    'id' => $restock->id,
                    'no_nota' => $restock->no_nota,
                    'tanggal' => $tanggal,
                    'total' => $restock->total,
                    'supplier' => $restock->supplier ? $restock->supplier->nama_supplier : null,
                ]));
            } catch (\Exception $e) {
                \Log::warning('Failed to broadcast restock: ' . $e->getMessage());
            }
        });

        return redirect()->route('restock.index')->with('success', 'Nota pembelian berhasil disimpan dan stok bahan baku telah ditambahkan');
    }

    public function show(Restock $restock)
    {
        $idCabang = (int) session('id_cabang');
        
        // Pastikan restock milik cabang yang aktif
        if ($restock->id_cabang != $idCabang) {
            abort(403, 'Unauthorized');
        }

        $restock->load('supplier.contacts', 'items.bahan');
        return view('inventori.restock.show', compact('restock'));
    }
}


