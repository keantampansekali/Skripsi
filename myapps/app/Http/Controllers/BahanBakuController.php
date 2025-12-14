<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Events\BahanBakuUpdated;
use App\Events\StokUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $query = BahanBaku::where('id_cabang', $idCabang);
        
        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama_bahan', 'like', "%{$search}%");
        }
        
        $items = $query->latest()->paginate(10)->withQueryString();
        return view('master.bahan-baku.index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('master.bahan-baku.create');
    }

    public function store(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        
        $validated = $request->validate([
            'nama_bahan' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('bahan_baku')->where(function ($query) use ($idCabang) {
                    return $query->where('id_cabang', $idCabang);
                })
            ],
            'jenis_satuan' => ['required', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga_satuan' => ['required', 'numeric', 'min:0'],
        ], [
            'nama_bahan.unique' => 'Nama bahan baku sudah ada di cabang ini. Silakan gunakan nama yang berbeda.',
        ]);

        // Set satuan dengan format "1 {jenis_satuan}" untuk backward compatibility
        $validated['satuan'] = '1 ' . $validated['jenis_satuan'];
        unset($validated['jenis_satuan']);

        $validated['id_cabang'] = $idCabang;

        DB::transaction(function () use ($validated) {
            $bahanBaku = BahanBaku::create($validated);
            
            // Broadcast bahan baku created/updated
            try {
                broadcast(new BahanBakuUpdated($validated['id_cabang'], [
                    'id' => $bahanBaku->id,
                    'nama_bahan' => $bahanBaku->nama_bahan,
                    'harga_satuan' => $bahanBaku->harga_satuan,
                    'stok' => $bahanBaku->stok,
                ]));
                
                broadcast(new StokUpdated($validated['id_cabang'], [
                    'tipe' => 'bahan_baku',
                    'id' => $bahanBaku->id,
                    'nama' => $bahanBaku->nama_bahan,
                    'stok' => $bahanBaku->stok,
                ]));
            } catch (\Exception $e) {
                \Log::warning('Failed to broadcast bahan baku update: ' . $e->getMessage());
            }
        });

        return redirect()->route('bahan-baku.index')->with('success', 'Bahan baku berhasil dibuat');
    }

    public function show(BahanBaku $bahan_baku)
    {
        return view('master.bahan-baku.show', [
            'item' => $bahan_baku,
        ]);
    }

    public function edit(BahanBaku $bahan_baku)
    {
        return view('master.bahan-baku.edit', [
            'item' => $bahan_baku,
        ]);
    }

    public function update(Request $request, BahanBaku $bahan_baku)
    {
        $idCabang = (int) session('id_cabang');
        
        $validated = $request->validate([
            'nama_bahan' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('bahan_baku')->where(function ($query) use ($idCabang) {
                    return $query->where('id_cabang', $idCabang);
                })->ignore($bahan_baku->id)
            ],
            'jenis_satuan' => ['required', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga_satuan' => ['required', 'numeric', 'min:0'],
        ], [
            'nama_bahan.unique' => 'Nama bahan baku sudah ada di cabang ini. Silakan gunakan nama yang berbeda.',
        ]);

        // Set satuan dengan format "1 {jenis_satuan}" untuk backward compatibility
        $validated['satuan'] = '1 ' . $validated['jenis_satuan'];
        unset($validated['jenis_satuan']);

        DB::transaction(function () use ($bahan_baku, $validated) {
            $bahan_baku->update($validated);
            
            // Broadcast bahan baku updated
            try {
                broadcast(new BahanBakuUpdated($bahan_baku->id_cabang, [
                    'id' => $bahan_baku->id,
                    'nama_bahan' => $bahan_baku->nama_bahan,
                    'harga_satuan' => $bahan_baku->harga_satuan,
                    'stok' => $bahan_baku->stok,
                ]));
                
                broadcast(new StokUpdated($bahan_baku->id_cabang, [
                    'tipe' => 'bahan_baku',
                    'id' => $bahan_baku->id,
                    'nama' => $bahan_baku->nama_bahan,
                    'stok' => $bahan_baku->stok,
                ]));
            } catch (\Exception $e) {
                \Log::warning('Failed to broadcast bahan baku update: ' . $e->getMessage());
            }
        });

        return redirect()->route('bahan-baku.index')->with('success', 'Bahan baku berhasil diperbarui');
    }

    public function destroy(BahanBaku $bahan_baku)
    {
        $bahan_baku->delete();
        return redirect()->route('bahan-baku.index')->with('success', 'Bahan baku berhasil dihapus');
    }
}


