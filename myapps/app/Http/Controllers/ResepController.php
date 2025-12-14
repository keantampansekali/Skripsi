<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\ResepItem;
use App\Models\Produk;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $query = Resep::where('id_cabang', $idCabang)
            ->withCount('items')
            ->with('produk');
        
        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_resep', 'like', "%{$search}%")
                  ->orWhereHas('produk', function($pq) use ($search) {
                      $pq->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }
        
        $reseps = $query->latest()->paginate(10)->withQueryString();
        return view('master.resep.index', compact('reseps'));
    }

    public function create()
    {
        $idCabang = (int) session('id_cabang');
        $produks = Produk::where('id_cabang', $idCabang)->orderBy('nama_produk')->get();
        $bahan = BahanBaku::where('id_cabang', $idCabang)->orderBy('nama_bahan')->get();
        return view('master.resep.create', compact('produks', 'bahan'));
    }

    public function store(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        
        $validated = $request->validate([
            'nama_resep' => ['required', 'string', 'max:255'],
            'produk_id' => ['nullable', 'integer', 'exists:produk,id'],
            'deskripsi' => ['nullable', 'string'],
            'items' => ['array'],
            'items.*.bahan_baku_id' => ['required_with:items', 'integer', 'exists:bahan_baku,id'],
            'items.*.qty' => ['required_with:items', 'numeric', 'min:0.01'],
        ]);

        // Validasi produk dan bahan baku harus dari cabang yang sama
        if (!empty($validated['produk_id'])) {
            $produk = Produk::find($validated['produk_id']);
            if ($produk && $produk->id_cabang != $idCabang) {
                return back()->withErrors(['produk_id' => 'Produk harus dari cabang yang sama'])->withInput();
            }
        }

        // Validasi bahan baku harus dari cabang yang sama
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $key => $item) {
                if (!empty($item['bahan_baku_id'])) {
                    $bahan = BahanBaku::find($item['bahan_baku_id']);
                    if ($bahan && $bahan->id_cabang != $idCabang) {
                        return back()->withErrors(["items.{$key}.bahan_baku_id" => 'Bahan baku harus dari cabang yang sama'])->withInput();
                    }
                }
            }
        }

        $validated['id_cabang'] = $idCabang;

        DB::transaction(function () use ($validated) {
            $resep = Resep::create([
                'id_cabang' => $validated['id_cabang'],
                'nama_resep' => $validated['nama_resep'],
                'produk_id' => $validated['produk_id'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
            ]);

            foreach (($validated['items'] ?? []) as $row) {
                if (!empty($row['bahan_baku_id'])) {
                    $resep->items()->create([
                        'bahan_baku_id' => $row['bahan_baku_id'],
                        'qty' => $row['qty'] ?? 0,
                    ]);
                }
            }
        });

        return redirect()->route('resep.index')->with('success', 'Resep berhasil dibuat');
    }

    public function edit(Resep $resep)
    {
        $idCabang = (int) session('id_cabang');
        // Pastikan resep milik cabang yang sama
        if ($resep->id_cabang != $idCabang) {
            abort(403, 'Anda tidak memiliki akses ke resep ini');
        }
        
        $resep->load('items');
        $produks = Produk::where('id_cabang', $idCabang)->orderBy('nama_produk')->get();
        $bahan = BahanBaku::where('id_cabang', $idCabang)->orderBy('nama_bahan')->get();
        return view('master.resep.edit', compact('resep', 'produks', 'bahan'));
    }

    public function update(Request $request, Resep $resep)
    {
        $idCabang = (int) session('id_cabang');
        
        // Pastikan resep milik cabang yang sama
        if ($resep->id_cabang != $idCabang) {
            abort(403, 'Anda tidak memiliki akses ke resep ini');
        }
        
        $validated = $request->validate([
            'nama_resep' => ['required', 'string', 'max:255'],
            'produk_id' => ['nullable', 'integer', 'exists:produk,id'],
            'deskripsi' => ['nullable', 'string'],
            'items' => ['array'],
            'items.*.bahan_baku_id' => ['required_with:items', 'integer', 'exists:bahan_baku,id'],
            'items.*.qty' => ['required_with:items', 'numeric', 'min:0.01'],
        ]);

        // Validasi produk dan bahan baku harus dari cabang yang sama
        if (!empty($validated['produk_id'])) {
            $produk = Produk::find($validated['produk_id']);
            if ($produk && $produk->id_cabang != $idCabang) {
                return back()->withErrors(['produk_id' => 'Produk harus dari cabang yang sama'])->withInput();
            }
        }

        // Validasi bahan baku harus dari cabang yang sama
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $key => $item) {
                if (!empty($item['bahan_baku_id'])) {
                    $bahan = BahanBaku::find($item['bahan_baku_id']);
                    if ($bahan && $bahan->id_cabang != $idCabang) {
                        return back()->withErrors(["items.{$key}.bahan_baku_id" => 'Bahan baku harus dari cabang yang sama'])->withInput();
                    }
                }
            }
        }

        DB::transaction(function () use ($resep, $validated) {
            $resep->update([
                'nama_resep' => $validated['nama_resep'],
                'produk_id' => $validated['produk_id'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
            ]);

            $resep->items()->delete();
            foreach (($validated['items'] ?? []) as $row) {
                if (!empty($row['bahan_baku_id'])) {
                    $resep->items()->create([
                        'bahan_baku_id' => $row['bahan_baku_id'],
                        'qty' => $row['qty'] ?? 0,
                    ]);
                }
            }
        });

        return redirect()->route('resep.index')->with('success', 'Resep berhasil diperbarui');
    }

    public function destroy(Resep $resep)
    {
        $resep->delete();
        return redirect()->route('resep.index')->with('success', 'Resep berhasil dihapus');
    }
}


