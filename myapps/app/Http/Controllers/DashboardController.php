<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\TransaksiKasir;
use App\Models\Restock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }
    
    public function getStats(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        
        // Statistik Produk
        $totalProduk = Produk::where('id_cabang', $idCabang)->count();
        $totalStok = Produk::where('id_cabang', $idCabang)->sum('stok');
        
        // Statistik Bahan Baku
        $totalBahanBaku = BahanBaku::where('id_cabang', $idCabang)->count();
        $totalStokBahan = BahanBaku::where('id_cabang', $idCabang)->sum('stok');
        
        // Statistik Penjualan Hari Ini
        $penjualanHariIni = TransaksiKasir::where('id_cabang', $idCabang)
            ->whereDate('created_at', Carbon::today())
            ->sum('total');
        $jumlahTransaksiHariIni = TransaksiKasir::where('id_cabang', $idCabang)
            ->whereDate('created_at', Carbon::today())
            ->count();
        
        // Statistik Penjualan Bulan Ini
        $penjualanBulanIni = TransaksiKasir::where('id_cabang', $idCabang)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');
        $jumlahTransaksiBulanIni = TransaksiKasir::where('id_cabang', $idCabang)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Statistik Pembelian Bulan Ini
        $pembelianBulanIni = Restock::where('id_cabang', $idCabang)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('total');
        
        // Produk dengan stok rendah
        $produkStokRendah = Produk::where('id_cabang', $idCabang)
            ->where('stok', '<', 10)
            ->orderBy('stok')
            ->get(['id', 'nama_produk', 'harga', 'stok']);
        
        // Semua produk untuk ditampilkan di tabel (dengan pagination)
        $produkPage = $request->get('produk_page', 1);
        $produkPerPage = 10;
        $produkQuery = Produk::where('id_cabang', $idCabang)
            ->orderBy('nama_produk', 'asc')
            ->orderBy('id', 'asc');
        $produkListPaginated = $produkQuery->paginate($produkPerPage, ['id', 'nama_produk', 'harga', 'stok'], 'produk_page', $produkPage);
        
        // Bahan baku dengan stok habis
        $bahanBakuStokHabis = BahanBaku::where('id_cabang', $idCabang)
            ->where('stok', '<=', 0)
            ->orderBy('stok')
            ->get(['id', 'nama_bahan', 'satuan', 'stok', 'harga_satuan']);
        
        // Bahan baku dengan stok rendah (kurang dari 10)
        $bahanBakuStokRendah = BahanBaku::where('id_cabang', $idCabang)
            ->where('stok', '>', 0)
            ->where('stok', '<', 10)
            ->orderBy('stok')
            ->get(['id', 'nama_bahan', 'satuan', 'stok', 'harga_satuan']);
        
        // Semua bahan baku untuk ditampilkan di tabel (dengan pagination)
        $bahanBakuPage = $request->get('bahan_baku_page', 1);
        $bahanBakuPerPage = 10;
        $bahanBakuQuery = BahanBaku::where('id_cabang', $idCabang)
            ->orderBy('nama_bahan', 'asc')
            ->orderBy('id', 'asc');
        $bahanBakuListPaginated = $bahanBakuQuery->paginate($bahanBakuPerPage, ['id', 'nama_bahan', 'satuan', 'stok', 'harga_satuan'], 'bahan_baku_page', $bahanBakuPage);
        
        // Transaksi terbaru
        $transaksiTerbaru = TransaksiKasir::where('id_cabang', $idCabang)
            ->withCount('items')
            ->latest()
            ->take(5)
            ->get(['id', 'no_transaksi', 'total', 'created_at']);
        
        // Nama cabang
        $namaCabang = session('nama_cabang', 'Tidak ada cabang');
        
        return response()->json([
            'total_produk' => $totalProduk,
            'total_stok' => $totalStok,
            'total_bahan_baku' => $totalBahanBaku,
            'total_stok_bahan' => $totalStokBahan,
            'penjualan_hari_ini' => $penjualanHariIni,
            'jumlah_transaksi_hari_ini' => $jumlahTransaksiHariIni,
            'penjualan_bulan_ini' => $penjualanBulanIni,
            'jumlah_transaksi_bulan_ini' => $jumlahTransaksiBulanIni,
            'pembelian_bulan_ini' => $pembelianBulanIni,
            'produk_stok_rendah' => $produkStokRendah,
            'jumlah_produk_stok_rendah' => $produkStokRendah->count(),
            'produk_list' => $produkListPaginated->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama_produk' => $p->nama_produk,
                    'harga' => $p->harga,
                    'stok' => $p->stok,
                ];
            }),
            'produk_pagination' => [
                'current_page' => $produkListPaginated->currentPage(),
                'last_page' => $produkListPaginated->lastPage(),
                'per_page' => $produkListPaginated->perPage(),
                'total' => $produkListPaginated->total(),
                'from' => $produkListPaginated->firstItem(),
                'to' => $produkListPaginated->lastItem(),
            ],
            'transaksi_terbaru' => $transaksiTerbaru->map(function($t) {
                return [
                    'id' => $t->id,
                    'no_transaksi' => $t->no_transaksi,
                    'total' => $t->total,
                    'created_at' => Carbon::parse($t->created_at)->format('d/m/Y H:i'),
                ];
            }),
            'bahan_baku_stok_habis' => $bahanBakuStokHabis->map(function($b) {
                return [
                    'id' => $b->id,
                    'nama_bahan' => $b->nama_bahan,
                    'satuan' => $b->satuan,
                    'stok' => $b->stok,
                    'harga_satuan' => $b->harga_satuan,
                ];
            }),
            'bahan_baku_stok_rendah' => $bahanBakuStokRendah->map(function($b) {
                return [
                    'id' => $b->id,
                    'nama_bahan' => $b->nama_bahan,
                    'satuan' => $b->satuan,
                    'stok' => $b->stok,
                    'harga_satuan' => $b->harga_satuan,
                ];
            }),
            'jumlah_bahan_baku_stok_habis' => $bahanBakuStokHabis->count(),
            'jumlah_bahan_baku_stok_rendah' => $bahanBakuStokRendah->count(),
            'bahan_baku_list' => $bahanBakuListPaginated->map(function($b) {
                return [
                    'id' => $b->id,
                    'nama_bahan' => $b->nama_bahan,
                    'satuan' => $b->satuan,
                    'stok' => $b->stok,
                    'harga_satuan' => $b->harga_satuan,
                ];
            }),
            'bahan_baku_pagination' => [
                'current_page' => $bahanBakuListPaginated->currentPage(),
                'last_page' => $bahanBakuListPaginated->lastPage(),
                'per_page' => $bahanBakuListPaginated->perPage(),
                'total' => $bahanBakuListPaginated->total(),
                'from' => $bahanBakuListPaginated->firstItem(),
                'to' => $bahanBakuListPaginated->lastItem(),
            ],
            'nama_cabang' => $namaCabang,
        ]);
    }
}

