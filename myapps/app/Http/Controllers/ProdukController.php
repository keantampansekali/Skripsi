<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Cabang;
use App\Events\ProdukUpdated;
use App\Events\StokUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query()
            ->where('id_cabang', session('id_cabang'));

        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama_produk', 'like', "%{$search}%");
        }

        $produk = $query->latest()->paginate(10)->withQueryString();
        return view('master.produk.index', [
            'produk' => $produk,
        ]);
    }

    public function create()
    {
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('master.produk.create', [
            'cabang' => $cabang,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_produk' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'id_cabang' => ['nullable', 'integer', 'exists:tabel_cabang,id_cabang'],
        ];
        
        // Validasi foto hanya jika ada file yang diupload
        if ($request->hasFile('foto')) {
            $rules['foto'] = ['required', 'file', 'image', 'max:2048'];
        } else {
            $rules['foto'] = ['nullable'];
        }
        
        $validated = $request->validate($rules);

        $validated['id_cabang'] = $validated['id_cabang']
            ?? (int) (session('id_cabang'));

        DB::transaction(function () use ($validated, $request) {
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                
                // Pastikan folder uploads/produk ada
                $uploadPath = public_path('uploads/produk');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Sanitize nama file dan tambahkan timestamp
                $originalName = $foto->getClientOriginalName();
                $extension = $foto->getClientOriginalExtension();
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                
                $foto->move($uploadPath, $fileName);
                $validated['foto'] = 'uploads/produk/' . $fileName;
            }
            
            $produk = Produk::create($validated);
            
            // Broadcast produk created/updated
            try {
                broadcast(new ProdukUpdated($validated['id_cabang'], [
                    'id' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => $produk->harga,
                    'stok' => $produk->stok,
                ]));
                
                broadcast(new StokUpdated($validated['id_cabang'], [
                    'tipe' => 'produk',
                    'id' => $produk->id,
                    'nama' => $produk->nama_produk,
                    'stok' => $produk->stok,
                ]));
            } catch (\Exception $e) {
                \Log::warning('Failed to broadcast produk update: ' . $e->getMessage());
            }
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dibuat');
    }

    public function show(Produk $produk)
    {
        $produk->load('cabang');
        return view('master.produk.show', compact('produk'));
    }

    public function edit(Produk $produk)
    {
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('master.produk.edit', [
            'produk' => $produk,
            'cabang' => $cabang,
        ]);
    }

    public function update(Request $request, Produk $produk)
    {
        $rules = [
            'nama_produk' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
        ];
        
        // Validasi foto hanya jika ada file yang diupload
        if ($request->hasFile('foto')) {
            $rules['foto'] = ['required', 'file', 'image', 'max:2048'];
        } else {
            $rules['foto'] = ['nullable'];
        }
        
        $validated = $request->validate($rules);

        // Cabang tidak dapat diubah saat edit, gunakan nilai yang sudah ada
        $validated['id_cabang'] = $produk->id_cabang;

        DB::transaction(function () use ($produk, $validated, $request) {
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($produk->foto && file_exists(public_path($produk->foto))) {
                    unlink(public_path($produk->foto));
                }
                
                $foto = $request->file('foto');
                
                // Pastikan folder uploads/produk ada
                $uploadPath = public_path('uploads/produk');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Sanitize nama file dan tambahkan timestamp
                $originalName = $foto->getClientOriginalName();
                $extension = $foto->getClientOriginalExtension();
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                
                $foto->move($uploadPath, $fileName);
                $validated['foto'] = 'uploads/produk/' . $fileName;
            } else {
                // Jika tidak ada foto baru, tetap gunakan foto lama
                unset($validated['foto']);
            }
            
            $produk->update($validated);
            
            // Broadcast produk updated
            try {
                broadcast(new ProdukUpdated($validated['id_cabang'], [
                    'id' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => $produk->harga,
                    'stok' => $produk->stok,
                ]));
                
                broadcast(new StokUpdated($validated['id_cabang'], [
                    'tipe' => 'produk',
                    'id' => $produk->id,
                    'nama' => $produk->nama_produk,
                    'stok' => $produk->stok,
                ]));
            } catch (\Exception $e) {
                \Log::warning('Failed to broadcast produk update: ' . $e->getMessage());
            }
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Produk $produk)
    {
        // Hapus foto jika ada
        if ($produk->foto && file_exists(public_path($produk->foto))) {
            unlink(public_path($produk->foto));
        }
        
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
}


