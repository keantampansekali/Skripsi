<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\PenyesuaianStokController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\WasteManagementController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\LaporanPembelianController;
use App\Http\Controllers\LaporanStokReportController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    });
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Sistem Kasir
    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('index');
        Route::post('/process', [KasirController::class, 'process'])->name('process');
        Route::get('/transaksi', [KasirController::class, 'transaksi'])->name('transaksi');
        Route::get('/transaksi/print', [KasirController::class, 'printTransaksi'])->name('transaksi.print');
        Route::get('/produk/{produk}/availability', [KasirController::class, 'checkAvailability'])->name('produk.availability');
    });

    // Group routes for Data Master
    Route::prefix('master')->group(function () {
        // CRUD Produk
        Route::resource('produk', ProdukController::class);
        // CRUD Bahan Baku
        Route::resource('bahan-baku', BahanBakuController::class);
        // CRUD Resep
        Route::resource('resep', ResepController::class);
        // CRUD Supplier
        Route::resource('supplier', SupplierController::class);
        // CRUD Pegawai
        Route::resource('pegawai', PegawaiController::class);
    });

    // Inventori
    Route::prefix('inventori')->group(function () {
        Route::resource('restock', RestockController::class)->only(['index','create','store','show']);
        Route::resource('penyesuaian', PenyesuaianStokController::class)->only(['index','create','store','show']);
        Route::resource('waste-management', WasteManagementController::class)->only(['index','create','store','show','destroy']);
        Route::get('laporan-stok', [LaporanStokController::class, 'index'])->name('laporan-stok.index');
        Route::get('laporan-stok/export', [LaporanStokController::class, 'export'])->name('laporan-stok.export');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan-penjualan.index');
        Route::get('penjualan/export', [LaporanPenjualanController::class, 'export'])->name('laporan-penjualan.export');
        Route::get('pembelian', [LaporanPembelianController::class, 'index'])->name('laporan-pembelian.index');
        Route::get('stok', [LaporanStokReportController::class, 'index'])->name('laporan-stok-report.index');
        Route::get('stok/export', [LaporanStokReportController::class, 'export'])->name('laporan-stok-report.export');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
