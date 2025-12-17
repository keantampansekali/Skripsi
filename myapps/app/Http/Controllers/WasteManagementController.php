<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StockMovement;
use App\Events\StokUpdated;
use App\Events\WasteCreated;
use App\Events\StokRendah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WasteManagementController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $query = Waste::where('id_cabang', $idCabang)
            ->withCount('items')
            ->with(['items.bahan', 'items.produk'])
            ->latest();
        
        // Filter berdasarkan tanggal
        if ($request->has('tanggal') && $request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        // Filter berdasarkan catatan
        if ($request->has('catatan') && $request->catatan) {
            $query->where('catatan', 'like', "%{$request->catatan}%");
        }
        
        // Filter berdasarkan search (untuk backward compatibility)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('catatan', 'like', "%{$search}%");
                
                // Coba parse sebagai tanggal, jika berhasil gunakan whereDate
                try {
                    $date = \Carbon\Carbon::parse($search)->format('Y-m-d');
                    $q->orWhereDate('tanggal', $date);
                } catch (\Exception $e) {
                    // Jika bukan format tanggal yang valid, coba cari dengan like pada format tanggal
                    $q->orWhere('tanggal', 'like', "%{$search}%");
                }
            });
        }
        
        $rows = $query->paginate(10)->withQueryString();
        
        // Hitung total harga untuk setiap waste
        foreach ($rows as $row) {
            $row->total_harga = 0;
            foreach ($row->items as $item) {
                if ($item->tipe === 'bahan_baku' && $item->bahan) {
                    $row->total_harga += $item->qty * ($item->bahan->harga_satuan ?? 0);
                } elseif ($item->tipe === 'produk' && $item->produk) {
                    $row->total_harga += $item->qty * ($item->produk->harga ?? 0);
                }
            }
        }
        
        return view('inventori.waste-management.index', compact('rows'));
    }

    public function create()
    {
        $idCabang = (int) session('id_cabang');
        $bahan = BahanBaku::where('id_cabang', $idCabang)->orderBy('nama_bahan')->get();
        $produks = Produk::where('id_cabang', $idCabang)->orderBy('nama_produk')->get();
        return view('inventori.waste-management.create', compact('bahan', 'produks'));
    }

    public function store(Request $request)
    {
        $rules = [
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.tipe' => ['required', 'in:bahan_baku,produk'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.alasan' => ['nullable', 'string'],
        ];

        // Validasi dinamis berdasarkan tipe
        if ($request->has('items')) {
            foreach ($request->items as $key => $item) {
                if (isset($item['tipe'])) {
                    if ($item['tipe'] === 'bahan_baku') {
                        $rules["items.$key.bahan_baku_id"] = ['required', 'integer', 'exists:bahan_baku,id'];
                        $rules["items.$key.produk_id"] = ['nullable'];
                    } elseif ($item['tipe'] === 'produk') {
                        $rules["items.$key.produk_id"] = ['required', 'integer', 'exists:produk,id'];
                        $rules["items.$key.bahan_baku_id"] = ['nullable'];
                    }
                }
            }
        }

        $validated = $request->validate($rules);

        $validated['id_cabang'] = (int) session('id_cabang');

        DB::transaction(function () use ($validated) {
            $waste = Waste::create([
                'id_cabang' => $validated['id_cabang'],
                'tanggal' => $validated['tanggal'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            foreach ($validated['items'] as $row) {
                $item = $waste->items()->create([
                    'tipe' => $row['tipe'],
                    'bahan_baku_id' => $row['tipe'] === 'bahan_baku' ? $row['bahan_baku_id'] : null,
                    'produk_id' => $row['tipe'] === 'produk' ? $row['produk_id'] : null,
                    'qty' => $row['qty'],
                    'alasan' => $row['alasan'] ?? null,
                ]);

                if ($row['tipe'] === 'bahan_baku') {
                    $bahanBaku = BahanBaku::find($row['bahan_baku_id']);
                    if ($bahanBaku) {
                        $stokLama = $bahanBaku->stok;
                        $bahanBaku->decrement('stok', $row['qty']);
                        // Refresh untuk mendapatkan stok baru
                        $bahanBaku->refresh();
                        
                        // Cek jika stok habis dan trigger event jika perlu
                        if ($stokLama > 0 && $bahanBaku->stok <= 0) {
                            \App\Events\StokHabis::dispatch($bahanBaku);
                        }
                        
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
                        'tipe' => 'out',
                        'qty' => $row['qty'],
                        'ref_type' => 'waste',
                        'ref_id' => $waste->id,
                        'id_cabang' => $validated['id_cabang'],
                        'keterangan' => 'Waste management: ' . ($row['alasan'] ?? 'Tanpa alasan'),
                    ]);
                } elseif ($row['tipe'] === 'produk') {
                    $produk = Produk::find($row['produk_id']);
                    if ($produk) {
                        $stokLama = $produk->stok;
                        $produk->decrement('stok', $row['qty']);
                        // Refresh untuk memastikan observer terpicu
                        $produk->refresh();
                        
                        // Broadcast stok updated
                        try {
                            broadcast(new StokUpdated($validated['id_cabang'], [
                                'tipe' => 'produk',
                                'id' => $produk->id,
                                'nama' => $produk->nama_produk,
                                'stok' => $produk->stok,
                            ]));
                        } catch (\Exception $e) {
                            \Log::warning('Failed to broadcast stock update: ' . $e->getMessage());
                        }
                    }
                }
            }
            
            // Broadcast waste created
            try {
                $waste->load('items.bahan', 'items.produk');
                broadcast(new WasteCreated($validated['id_cabang'], [
                    'id' => $waste->id,
                    'tanggal' => $waste->tanggal->format('d/m/Y'),
                    'catatan' => $waste->catatan,
                ]));
            } catch (\Exception $e) {
                \Log::warning('Failed to broadcast waste: ' . $e->getMessage());
            }
        });

        return redirect()->route('waste-management.index')->with('success', 'Waste management berhasil disimpan');
    }

    public function show(Waste $wasteManagement)
    {
        $wasteManagement->load(['items.bahan', 'items.produk']);
        return view('inventori.waste-management.show', compact('wasteManagement'));
    }

    public function destroy(Waste $wasteManagement)
    {
        // Load items relation
        $wasteManagement->load('items');
        
        DB::transaction(function () use ($wasteManagement) {
            // Kembalikan stock yang sudah dikurangi
            foreach ($wasteManagement->items as $item) {
                if ($item->tipe === 'bahan_baku' && $item->bahan_baku_id) {
                    BahanBaku::where('id', $item->bahan_baku_id)->increment('stok', $item->qty);
                } elseif ($item->tipe === 'produk' && $item->produk_id) {
                    Produk::where('id', $item->produk_id)->increment('stok', $item->qty);
                }
            }

            // Hapus stock movements terkait
            StockMovement::where('ref_type', 'waste')
                ->where('ref_id', $wasteManagement->id)
                ->delete();

            // Hapus waste items (akan otomatis terhapus jika ada cascade, tapi lebih aman delete manual)
            $wasteManagement->items()->delete();

            // Hapus waste record
            $wasteManagement->delete();
        });

        return redirect()->route('waste-management.index')->with('success', 'Waste management berhasil dihapus');
    }
}

