<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\TransaksiKasir;
use App\Services\ResepService;
use App\Events\TransaksiBaru;
use App\Events\StokUpdated;
use App\Events\StokRendah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query()
            ->where('id_cabang', session('id_cabang'))
            ->where('stok', '>', 0)
            ->where(function ($q) {
                $q->where('nama_produk', 'not like', '%Nasi Goreng Spesial%')
                  ->where('nama_produk', 'not like', '%Sate%')
                  ->where('nama_produk', 'not like', '%Ikan Bakar%');
            });

        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $query->where('nama_produk', 'like', "%{$request->search}%");
        }

        $produks = $query->orderBy('nama_produk')->get();
        
        return view('kasir.index', compact('produks'));
    }

    public function process(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => ['required', 'array', 'min:1'],
                'items.*.id' => ['required', 'integer', 'exists:produk,id'],
                'items.*.name' => ['required', 'string'],
                'items.*.price' => ['required', 'numeric', 'min:0'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'subtotal' => ['required', 'numeric', 'min:0'],
                'diskon' => ['required', 'numeric', 'min:0'],
                'tipe_diskon' => ['required', 'in:rp,percent'],
                'nilai_diskon' => ['required', 'numeric', 'min:0'],
                'tax' => ['required', 'numeric', 'min:0'],
                'total' => ['required', 'numeric', 'min:0'],
                'bayar' => ['required', 'numeric', 'min:0'],
                'kembalian' => ['required', 'numeric', 'min:0'],
            ]);

            $idCabang = (int) session('id_cabang');
            $pegawaiId = session('pegawai_id');
            
            if (!$idCabang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cabang tidak ditemukan. Silakan login ulang.'
                ], 400);
            }
            
            // Ambil pegawai dari session atau dari user yang login
            $user = \Illuminate\Support\Facades\Auth::user();
            $pegawai = null;
            
            // Cek apakah pegawai_id di session valid
            if ($pegawaiId) {
                $pegawai = \App\Models\Pegawai::find($pegawaiId);
            }
            
            // Jika pegawai tidak ditemukan dari session, cari berdasarkan user yang login
            if (!$pegawai && $user) {
                $pegawai = \App\Models\Pegawai::where('username', $user->username)
                    ->orWhere('email', $user->email)
                    ->first();
                
                if ($pegawai) {
                    // Update session dengan pegawai_id yang valid
                    session(['pegawai_id' => $pegawai->id]);
                    $pegawaiId = $pegawai->id;
                }
            }
            
            // Validasi pegawai harus ada
            if (!$pegawai || !$pegawaiId) {
                \Log::error('Pegawai tidak ditemukan untuk transaksi', [
                    'session_pegawai_id' => session('pegawai_id'),
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? $user->username : null,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan. Silakan login ulang.'
                ], 400);
            }
            
            // Pastikan pegawai_id yang akan digunakan valid
            $pegawaiId = $pegawai->id;
            
            DB::transaction(function () use ($validated, $idCabang, $pegawaiId) {
                // Generate nomor transaksi
                $noTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(TransaksiKasir::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
                
                $transaksi = TransaksiKasir::create([
                    'id_cabang' => $idCabang,
                    'pegawai_id' => $pegawaiId,
                    'no_transaksi' => $noTransaksi,
                    'subtotal' => $validated['subtotal'],
                    'diskon' => $validated['diskon'],
                    'tipe_diskon' => $validated['tipe_diskon'],
                    'nilai_diskon' => $validated['nilai_diskon'],
                    'tax' => $validated['tax'],
                    'total' => $validated['total'],
                    'bayar' => $validated['bayar'],
                    'kembalian' => $validated['kembalian'],
                ]);

                // Simpan items dan kurangi stok
                $resepService = new ResepService();
                
                foreach ($validated['items'] as $item) {
                    $produk = Produk::find($item['id']);
                    
                    if (!$produk) {
                        throw new \Exception('Produk tidak ditemukan: ' . $item['id']);
                    }
                    
                    if ($produk->stok < $item['quantity']) {
                        throw new \Exception('Stok tidak mencukupi untuk produk: ' . $produk->nama_produk);
                    }
                    
                    // Cek ketersediaan bahan baku jika produk punya resep
                    $checkBahanBaku = $resepService->checkBahanBakuAvailability($produk, $item['quantity']);
                    if (!$checkBahanBaku['available']) {
                        $missingList = collect($checkBahanBaku['missing'])
                            ->map(function($m) {
                                return "{$m['nama_bahan']}: butuh {$m['kebutuhan']} {$m['satuan']}, tersedia {$m['stok_tersedia']} {$m['satuan']}";
                            })
                            ->implode(', ');
                        throw new \Exception('Bahan baku tidak mencukupi untuk produk ' . $produk->nama_produk . '. ' . $missingList);
                    }
                    
                    $transaksi->items()->create([
                        'produk_id' => $item['id'],
                        'nama_produk' => $item['name'],
                        'harga' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Kurangi stok produk
                    $stokLama = $produk->stok;
                    $produk->decrement('stok', $item['quantity']);
                    // Refresh untuk memastikan observer terpicu
                    $produk->refresh();
                    
                    // Kurangi stok bahan baku berdasarkan resep
                    $resepService->reduceBahanBakuFromResep($produk, $item['quantity'], $transaksi->id);
                    
                    // Broadcast events (optional - tidak mengganggu transaksi jika gagal)
                    try {
                        $produk->refresh();
                        // Broadcast stok updated
                        broadcast(new StokUpdated($idCabang, [
                            'tipe' => 'produk',
                            'id' => $produk->id,
                            'nama' => $produk->nama_produk,
                            'stok' => $produk->stok,
                        ]));
                    } catch (\Exception $e) {
                        // Broadcast events optional - log error but don't fail transaction
                        \Log::warning('Failed to broadcast stock update: ' . $e->getMessage(), [
                            'exception' => get_class($e),
                            'produk_id' => $produk->id,
                            'trace' => $e->getTraceAsString()
                        ]);
                        // Jangan throw exception - transaksi sudah berhasil
                    }
                }
                
                // Hitung statistik baru (untuk broadcast jika diperlukan)
                $penjualanHariIni = TransaksiKasir::where('id_cabang', $idCabang)
                    ->whereDate('created_at', Carbon::today())
                    ->sum('total');
                $jumlahTransaksiHariIni = TransaksiKasir::where('id_cabang', $idCabang)
                    ->whereDate('created_at', Carbon::today())
                    ->count();
                $penjualanBulanIni = TransaksiKasir::where('id_cabang', $idCabang)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('total');
                $jumlahTransaksiBulanIni = TransaksiKasir::where('id_cabang', $idCabang)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count();
                
                // Broadcast transaksi baru (optional - tidak mengganggu transaksi jika gagal)
                try {
                    $transaksi->load('items');
                    broadcast(new TransaksiBaru($idCabang, [
                        'id' => $transaksi->id,
                        'no_transaksi' => $transaksi->no_transaksi,
                        'total' => $transaksi->total,
                        'created_at' => $transaksi->created_at->format('d/m/Y H:i'),
                    ], [
                        'penjualan_hari_ini' => $penjualanHariIni,
                        'jumlah_transaksi_hari_ini' => $jumlahTransaksiHariIni,
                        'penjualan_bulan_ini' => $penjualanBulanIni,
                        'jumlah_transaksi_bulan_ini' => $jumlahTransaksiBulanIni,
                    ]));
                } catch (\Exception $e) {
                    // Broadcast events optional - log error but don't fail transaction
                    \Log::warning('Failed to broadcast transaction: ' . $e->getMessage(), [
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Jangan throw exception - transaksi sudah berhasil disimpan
                }
            });

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil disimpan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Kasir validation error', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Kasir process error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transaksi(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $filterType = $request->get('filter_type', 'harian'); // harian atau bulanan
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $bulan = $request->get('bulan', date('Y-m'));
        $filterPegawaiId = $request->get('pegawai_id');
        
        // Get current user and pegawai
        $user = \Illuminate\Support\Facades\Auth::user();
        $currentPegawai = null;
        $isOwner = false;
        
        if ($user) {
            // Cari pegawai berdasarkan username atau email
            $currentPegawai = \App\Models\Pegawai::where('username', $user->username)
                ->orWhere(function($q) use ($user) {
                    if ($user->email) {
                        $q->where('email', $user->email);
                    }
                })
                ->first();
            
            if ($currentPegawai) {
                $isOwner = \App\Helpers\BranchHelper::isOwner();
            }
        }
        
        // Get list of pegawai untuk dropdown
        // Jika bukan owner, hanya tampilkan pegawai yang sedang login
        if ($isOwner) {
            // Owner: tampilkan semua pegawai yang terkait dengan cabang ini
            // 1. Pegawai yang terkait dengan cabang melalui pegawai_cabang
            // 2. Pegawai yang pernah melakukan transaksi di cabang ini
            // 3. Semua owner (karena owner bisa akses semua cabang)
            // 4. Admin yang terkait dengan cabang atau pernah melakukan transaksi
            $pegawaiList = \App\Models\Pegawai::where(function($q) use ($idCabang) {
                $q->whereHas('cabangs', function($query) use ($idCabang) {
                    $query->where('pegawai_cabang.id_cabang', $idCabang);
                })
                ->orWhereHas('transaksiKasir', function($query) use ($idCabang) {
                    $query->where('transaksi_kasir.id_cabang', $idCabang);
                })
                ->orWhere('role', 'owner');
            })->orderBy('nama')->get();
        } else {
            // Jika bukan owner, hanya tampilkan dirinya sendiri
            if ($currentPegawai) {
                $pegawaiList = collect([$currentPegawai]);
            } else {
                $pegawaiList = collect();
            }
        }
        
        $transaksis = collect();
        $totalHari = 0;
        $periodLabel = '';
        $groupedData = [];
        
        if ($filterType === 'harian') {
            $query = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereDate('created_at', $tanggal)
                ->with(['items', 'pegawai'])
                ->orderBy('created_at', 'desc');
            
            // Jika bukan owner, hanya tampilkan transaksi milik pegawai yang login
            if (!$isOwner && $currentPegawai) {
                $query->where('pegawai_id', $currentPegawai->id);
            } else {
                // Filter by pegawai jika dipilih (hanya untuk owner)
                if ($filterPegawaiId) {
                    $query->where('pegawai_id', $filterPegawaiId);
                }
            }

            $transaksis = $query->get();
            $totalHari = $transaksis->sum('total');
            $periodLabel = Carbon::parse($tanggal)->locale('id')->format('d M Y');
        } else {
            // Filter bulanan
            $bulanStart = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $bulanEnd = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
            
            $query = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$bulanStart, $bulanEnd])
                ->with(['items', 'pegawai'])
                ->orderBy('created_at', 'desc');
            
            // Jika bukan owner, hanya tampilkan transaksi milik pegawai yang login
            if (!$isOwner && $currentPegawai) {
                $query->where('pegawai_id', $currentPegawai->id);
            } else {
                // Filter by pegawai jika dipilih (hanya untuk owner)
                if ($filterPegawaiId) {
                    $query->where('pegawai_id', $filterPegawaiId);
                }
            }

            $transaksis = $query->get();
            $totalHari = $transaksis->sum('total');
            $periodLabel = $bulanStart->locale('id')->format('F Y');
            
            // Group by date untuk tampilan bulanan
            foreach ($transaksis as $trx) {
                $dateKey = Carbon::parse($trx->created_at)->format('Y-m-d');
                if (!isset($groupedData[$dateKey])) {
                    $groupedData[$dateKey] = [
                        'tanggal' => Carbon::parse($trx->created_at)->locale('id')->format('d M Y'),
                        'transaksis' => collect(),
                        'total' => 0,
                        'jumlah' => 0
                    ];
                }
                $groupedData[$dateKey]['transaksis']->push($trx);
                $groupedData[$dateKey]['total'] += $trx->total;
                $groupedData[$dateKey]['jumlah']++;
            }
            
            // Sort by date key
            ksort($groupedData);
        }
        
        return view('kasir.transaksi', compact('transaksis', 'tanggal', 'bulan', 'totalHari', 'filterType', 'periodLabel', 'groupedData', 'pegawaiList', 'filterPegawaiId', 'isOwner'));
    }

    public function printTransaksi(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $filterPegawaiId = $request->get('pegawai_id');
        $noTransaksi = $request->get('no');
        
        // Get current user and pegawai
        $user = \Illuminate\Support\Facades\Auth::user();
        $currentPegawai = null;
        $isOwner = false;
        
        if ($user) {
            // Cari pegawai berdasarkan username atau email
            $currentPegawai = \App\Models\Pegawai::where('username', $user->username)
                ->orWhere(function($q) use ($user) {
                    if ($user->email) {
                        $q->where('email', $user->email);
                    }
                })
                ->first();
            
            if ($currentPegawai) {
                $isOwner = \App\Helpers\BranchHelper::isOwner();
            }
        }
        
        $query = TransaksiKasir::where('id_cabang', $idCabang)
            ->whereDate('created_at', $tanggal)
            ->with('items')
            ->orderBy('created_at', 'asc');
        
        // Jika ada filter no transaksi spesifik
        if ($noTransaksi) {
            $query->where('no_transaksi', $noTransaksi);
        }
        
        // Jika bukan owner, hanya tampilkan transaksi milik pegawai yang login
        if (!$isOwner && $currentPegawai) {
            $query->where('pegawai_id', $currentPegawai->id);
        } else {
            // Filter by pegawai jika dipilih (hanya untuk owner)
            if ($filterPegawaiId) {
                $query->where('pegawai_id', $filterPegawaiId);
            }
        }
        
        $transaksis = $query->get();
        
        $totalHari = $transaksis->sum('total');
        $totalTransaksi = $transaksis->count();
        $cabang = session('nama_cabang', 'Cabang');
        
        return view('kasir.print-transaksi', compact('transaksis', 'tanggal', 'totalHari', 'totalTransaksi', 'cabang'));
    }
}
