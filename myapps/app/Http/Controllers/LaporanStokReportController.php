<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StockMovement;
use App\Models\RestockItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class LaporanStokReportController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $namaCabang = session('nama_cabang', 'Cabang');
        
        // Get filter type and value
        $filterType = $request->get('filter_type', 'bulanan'); // bulanan, tahunan
        $filterValue = $request->get('filter_value');
        
        // Set default values
        if (!$filterValue) {
            if ($filterType === 'bulanan') {
                $filterValue = date('Y-m');
            } else {
                $filterValue = date('Y');
            }
        }
        
        $data = [];
        $summary = [
            'total_bahan_baku' => 0,
            'total_produk' => 0,
            'total_nilai_bahan_baku' => 0,
            'total_nilai_produk' => 0,
        ];
        
        if ($filterType === 'bulanan') {
            // Laporan Bulanan - group by hari
            $bulan = Carbon::createFromFormat('Y-m', $filterValue);
            $startDate = $bulan->copy()->startOfMonth()->startOfDay();
            $endDate = $bulan->copy()->endOfMonth()->endOfDay();
            
            // Get all bahan baku and produk
            $bahanBaku = BahanBaku::where('id_cabang', $idCabang)->get();
            $produk = Produk::where('id_cabang', $idCabang)->get();
            
            // Group by date
            $groupedByDate = [];
            
            // Get dates that have actual transactions (restock, waste, penyesuaian)
            // Hanya ambil tanggal yang benar-benar ada transaksi, bukan semua hari
            $datesWithActivity = StockMovement::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as tanggal')
                ->distinct()
                ->pluck('tanggal')
                ->map(function($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();
            
            // Hanya tampilkan tanggal yang benar-benar ada transaksi
            // Tidak menambahkan tanggal pertama/terakhir secara otomatis
            $datesWithActivity = array_unique($datesWithActivity);
            sort($datesWithActivity);
            
            // Loop through dates with activity - hanya tanggal yang benar-benar ada transaksi
            foreach ($datesWithActivity as $dateStr) {
                $currentDate = Carbon::parse($dateStr);
                $dateKey = $currentDate->format('Y-m-d');
                $dateStart = $currentDate->copy()->startOfDay();
                $dateEnd = $currentDate->copy()->endOfDay();
                
                // Cek apakah benar-benar ada transaksi pada tanggal ini
                $hasTransaction = StockMovement::where('id_cabang', $idCabang)
                    ->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->exists();
                
                if (!$hasTransaction) {
                    continue; // Skip jika tidak ada transaksi
                }
                
                // Calculate stock for bahan baku on this date (hanya yang ada transaksi)
                $totalBahanBaku = 0;
                $totalNilaiBahanBaku = 0;
                
                // Ambil bahan baku yang ada transaksi pada tanggal ini
                $bahanBakuIdsWithTransaction = StockMovement::where('id_cabang', $idCabang)
                    ->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->distinct()
                    ->pluck('bahan_baku_id')
                    ->toArray();
                
                foreach ($bahanBaku as $b) {
                    // Hanya hitung jika ada transaksi untuk bahan baku ini pada tanggal ini
                    if (!in_array($b->id, $bahanBakuIdsWithTransaction) && $b->stok <= 0) {
                        continue;
                    }
                    
                    $stokSaatIni = $b->stok;
                    
                    // Get movements after this date
                    $movementsAfter = StockMovement::where('bahan_baku_id', $b->id)
                        ->where('id_cabang', $idCabang)
                        ->where('created_at', '>', $dateEnd)
                        ->get();
                    
                    $perubahanSetelah = 0;
                    foreach ($movementsAfter as $mov) {
                        if ($mov->tipe === 'in') {
                            $perubahanSetelah += $mov->qty;
                        } elseif ($mov->tipe === 'out') {
                            $perubahanSetelah -= $mov->qty;
                        } elseif ($mov->tipe === 'adj') {
                            $perubahanSetelah += $mov->qty;
                        }
                    }
                    
                    $stokTanggal = $stokSaatIni - $perubahanSetelah;
                    if ($stokTanggal > 0) {
                        $totalBahanBaku += $stokTanggal;
                        $totalNilaiBahanBaku += $stokTanggal * $b->harga_satuan;
                    }
                }
                
                // Calculate stock for produk on this date (hanya yang ada stok)
                $totalProduk = 0;
                $totalNilaiProduk = 0;
                foreach ($produk as $p) {
                    if ($p->stok > 0) {
                        $totalProduk += $p->stok;
                        $totalNilaiProduk += $p->stok * $p->harga;
                    }
                }
                
                // Hanya tambahkan jika ada transaksi pada tanggal ini
                if ($hasTransaction && ($totalBahanBaku > 0 || $totalProduk > 0)) {
                    $groupedByDate[$dateKey] = [
                        'tanggal' => $currentDate->locale('id')->format('d M Y'),
                        'total_bahan_baku' => $totalBahanBaku,
                        'total_produk' => $totalProduk,
                        'total_nilai_bahan_baku' => $totalNilaiBahanBaku,
                        'total_nilai_produk' => $totalNilaiProduk,
                        'total_nilai' => $totalNilaiBahanBaku + $totalNilaiProduk,
                    ];
                }
            }
            
            // Sort by date key
            ksort($groupedByDate);
            $data = array_values($groupedByDate);
            
            // Calculate summary
            foreach ($data as $d) {
                $summary['total_bahan_baku'] += $d['total_bahan_baku'];
                $summary['total_produk'] += $d['total_produk'];
                $summary['total_nilai_bahan_baku'] += $d['total_nilai_bahan_baku'];
                $summary['total_nilai_produk'] += $d['total_nilai_produk'];
            }
            
            // Average values
            $daysCount = count($data);
            if ($daysCount > 0) {
                $summary['avg_bahan_baku'] = $summary['total_bahan_baku'] / $daysCount;
                $summary['avg_produk'] = $summary['total_produk'] / $daysCount;
                $summary['avg_nilai_bahan_baku'] = $summary['total_nilai_bahan_baku'] / $daysCount;
                $summary['avg_nilai_produk'] = $summary['total_nilai_produk'] / $daysCount;
            }
            
            $summary['total_nilai'] = $summary['total_nilai_bahan_baku'] + $summary['total_nilai_produk'];
            
            $periodLabel = $bulan->locale('id')->format('F Y');
            
        } else {
            // Laporan Tahunan - group by bulan
            $tahun = (int) $filterValue;
            $startDate = Carbon::create($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 12, 31)->endOfDay();
            
            // Get all bahan baku and produk
            $bahanBaku = BahanBaku::where('id_cabang', $idCabang)->get();
            $produk = Produk::where('id_cabang', $idCabang)->get();
            
            // Group by month
            $groupedByMonth = [];
            
            // Loop through each month
            for ($month = 1; $month <= 12; $month++) {
                $monthStart = Carbon::create($tahun, $month, 1)->startOfMonth()->startOfDay();
                $monthEnd = Carbon::create($tahun, $month, 1)->endOfMonth()->endOfDay();
                $monthKey = sprintf('%04d-%02d', $tahun, $month);
                
                // Calculate average stock for this month (using end of month)
                $totalBahanBaku = 0;
                $totalNilaiBahanBaku = 0;
                
                foreach ($bahanBaku as $b) {
                    $stokSaatIni = $b->stok;
                    
                    // Get movements after month end
                    $movementsAfter = StockMovement::where('bahan_baku_id', $b->id)
                        ->where('id_cabang', $idCabang)
                        ->where('created_at', '>', $monthEnd)
                        ->get();
                    
                    $perubahanSetelah = 0;
                    foreach ($movementsAfter as $mov) {
                        if ($mov->tipe === 'in') {
                            $perubahanSetelah += $mov->qty;
                        } elseif ($mov->tipe === 'out') {
                            $perubahanSetelah -= $mov->qty;
                        } elseif ($mov->tipe === 'adj') {
                            $perubahanSetelah += $mov->qty;
                        }
                    }
                    
                    $stokTanggal = $stokSaatIni - $perubahanSetelah;
                    $totalBahanBaku += $stokTanggal;
                    $totalNilaiBahanBaku += $stokTanggal * $b->harga_satuan;
                }
                
                // Calculate stock for produk (simplified - using current stock)
                $totalProduk = $produk->sum('stok');
                $totalNilaiProduk = $produk->sum(function($p) {
                    return $p->stok * $p->harga;
                });
                
                $groupedByMonth[$monthKey] = [
                    'bulan' => $monthStart->locale('id')->format('F Y'),
                    'total_bahan_baku' => $totalBahanBaku,
                    'total_produk' => $totalProduk,
                    'total_nilai_bahan_baku' => $totalNilaiBahanBaku,
                    'total_nilai_produk' => $totalNilaiProduk,
                    'total_nilai' => $totalNilaiBahanBaku + $totalNilaiProduk,
                ];
            }
            
            // Sort by month key
            ksort($groupedByMonth);
            $data = array_values($groupedByMonth);
            
            // Calculate summary
            foreach ($data as $d) {
                $summary['total_bahan_baku'] += $d['total_bahan_baku'];
                $summary['total_produk'] += $d['total_produk'];
                $summary['total_nilai_bahan_baku'] += $d['total_nilai_bahan_baku'];
                $summary['total_nilai_produk'] += $d['total_nilai_produk'];
            }
            
            // Average values
            $monthsCount = count($data);
            if ($monthsCount > 0) {
                $summary['avg_bahan_baku'] = $summary['total_bahan_baku'] / $monthsCount;
                $summary['avg_produk'] = $summary['total_produk'] / $monthsCount;
                $summary['avg_nilai_bahan_baku'] = $summary['total_nilai_bahan_baku'] / $monthsCount;
                $summary['avg_nilai_produk'] = $summary['total_nilai_produk'] / $monthsCount;
            }
            
            $summary['total_nilai'] = $summary['total_nilai_bahan_baku'] + $summary['total_nilai_produk'];
            
            $periodLabel = $tahun;
        }
        
        // Prepare chart data
        $chartLabels = [];
        $chartBahanBaku = [];
        $chartProduk = [];
        $chartNilaiBahanBaku = [];
        $chartNilaiProduk = [];
        $chartTotalNilai = [];
        
        foreach ($data as $row) {
            if ($filterType === 'bulanan') {
                $chartLabels[] = $row['tanggal'];
            } else {
                $chartLabels[] = $row['bulan'];
            }
            $chartBahanBaku[] = $row['total_bahan_baku'];
            $chartProduk[] = $row['total_produk'];
            $chartNilaiBahanBaku[] = $row['total_nilai_bahan_baku'];
            $chartNilaiProduk[] = $row['total_nilai_produk'];
            $chartTotalNilai[] = $row['total_nilai'];
        }
        
        // Get detail per bahan baku - HANYA yang ada aktivitas dalam periode
        $detailBahanBaku = [];
        
        // Get bahan baku IDs that have activity in the period
        $bahanBakuIdsWithActivity = StockMovement::where('id_cabang', $idCabang)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct()
            ->pluck('bahan_baku_id')
            ->toArray();
        
        // Hanya ambil bahan baku yang benar-benar ada aktivitas dalam periode
        // Tidak menampilkan bahan baku yang hanya punya stok tapi tidak ada aktivitas
        if (empty($bahanBakuIdsWithActivity)) {
            $bahanBakuList = collect([]);
        } else {
            $bahanBakuList = BahanBaku::where('id_cabang', $idCabang)
                ->whereIn('id', $bahanBakuIdsWithActivity)
                ->orderBy('nama_bahan')
                ->get();
        }
        
        foreach ($bahanBakuList as $bahan) {
            // Stok awal = nilai stok yang ada di data bahan baku pada awal periode
            // Kita hitung mundur dari stok saat ini
            $stokSaatIni = $bahan->stok;
            
            // Hitung semua perubahan yang terjadi setelah awal periode
            $movementsAfterStart = StockMovement::where('bahan_baku_id', $bahan->id)
                ->where('id_cabang', $idCabang)
                ->where('created_at', '>=', $startDate)
                ->get();
            
            $perubahanSetelahStart = 0;
            foreach ($movementsAfterStart as $mov) {
                if ($mov->tipe === 'in') {
                    $perubahanSetelahStart += $mov->qty;
                } elseif ($mov->tipe === 'out') {
                    $perubahanSetelahStart -= $mov->qty;
                } elseif ($mov->tipe === 'adj') {
                    $perubahanSetelahStart += $mov->qty;
                }
            }
            
            // Stok awal = stok saat ini - perubahan setelah awal periode
            // Ini adalah stok yang ada di data bahan baku pada awal periode
            $stokAwal = $stokSaatIni - $perubahanSetelahStart;
            
            // Pastikan stok awal tidak negatif
            if ($stokAwal < 0) {
                $stokAwal = 0;
            }
            
            // Hitung masuk (restock) dalam periode
            $masuk = StockMovement::where('bahan_baku_id', $bahan->id)
                ->where('id_cabang', $idCabang)
                ->where('tipe', 'in')
                ->where('ref_type', 'restock')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('qty');
            
            // Hitung keluar (waste) dalam periode
            $keluar = StockMovement::where('bahan_baku_id', $bahan->id)
                ->where('id_cabang', $idCabang)
                ->where('tipe', 'out')
                ->where('ref_type', 'waste')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('qty');
            
            // Hitung adjustment dalam periode
            $adjustment = StockMovement::where('bahan_baku_id', $bahan->id)
                ->where('id_cabang', $idCabang)
                ->where('tipe', 'adj')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('qty');
            
            // Stok akhir = stok awal + masuk - keluar + adjustment
            $stokAkhir = $stokAwal + $masuk - $keluar + $adjustment;
            
            // Hanya tambahkan jika benar-benar ada aktivitas dalam periode
            // (masuk, keluar, atau adjustment)
            if ($masuk > 0 || $keluar > 0 || $adjustment != 0) {
                $detailBahanBaku[] = [
                    'nama_bahan' => $bahan->nama_bahan,
                    'satuan' => $bahan->satuan,
                    'stok_awal' => $stokAwal,
                    'masuk' => $masuk,
                    'keluar' => $keluar,
                    'adjustment' => $adjustment,
                    'stok_akhir' => $stokAkhir,
                    'harga_satuan' => $bahan->harga_satuan,
                    'total_nilai' => $stokAkhir * $bahan->harga_satuan,
                ];
            }
        }
        
        return view('reports.stok.index', compact(
            'data',
            'summary',
            'filterType',
            'filterValue',
            'periodLabel',
            'namaCabang',
            'chartLabels',
            'chartBahanBaku',
            'chartProduk',
            'chartNilaiBahanBaku',
            'chartNilaiProduk',
            'chartTotalNilai',
            'detailBahanBaku',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $namaCabang = session('nama_cabang', 'Cabang');
        
        // Get filter type and value
        $filterType = $request->get('filter_type', 'bulanan');
        $filterValue = $request->get('filter_value');
        
        // Set default values
        if (!$filterValue) {
            if ($filterType === 'bulanan') {
                $filterValue = date('Y-m');
            } else {
                $filterValue = date('Y');
            }
        }
        
        $data = [];
        $summary = [
            'total_bahan_baku' => 0,
            'total_produk' => 0,
            'total_nilai_bahan_baku' => 0,
            'total_nilai_produk' => 0,
        ];
        
        if ($filterType === 'bulanan') {
            $bulan = Carbon::createFromFormat('Y-m', $filterValue);
            $startDate = $bulan->copy()->startOfMonth()->startOfDay();
            $endDate = $bulan->copy()->endOfMonth()->endOfDay();
            
            $bahanBaku = BahanBaku::where('id_cabang', $idCabang)->get();
            $produk = Produk::where('id_cabang', $idCabang)->get();
            
            $groupedByDate = [];
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateKey = $currentDate->format('Y-m-d');
                $dateEnd = $currentDate->copy()->endOfDay();
                
                $totalBahanBaku = 0;
                $totalNilaiBahanBaku = 0;
                
                foreach ($bahanBaku as $b) {
                    $stokSaatIni = $b->stok;
                    
                    $movementsAfter = StockMovement::where('bahan_baku_id', $b->id)
                        ->where('id_cabang', $idCabang)
                        ->where('created_at', '>', $dateEnd)
                        ->get();
                    
                    $perubahanSetelah = 0;
                    foreach ($movementsAfter as $mov) {
                        if ($mov->tipe === 'in') {
                            $perubahanSetelah += $mov->qty;
                        } elseif ($mov->tipe === 'out') {
                            $perubahanSetelah -= $mov->qty;
                        } elseif ($mov->tipe === 'adj') {
                            $perubahanSetelah += $mov->qty;
                        }
                    }
                    
                    $stokTanggal = $stokSaatIni - $perubahanSetelah;
                    $totalBahanBaku += $stokTanggal;
                    $totalNilaiBahanBaku += $stokTanggal * $b->harga_satuan;
                }
                
                $totalProduk = $produk->sum('stok');
                $totalNilaiProduk = $produk->sum(function($p) {
                    return $p->stok * $p->harga;
                });
                
                $groupedByDate[$dateKey] = [
                    'tanggal' => $currentDate->locale('id')->format('d M Y'),
                    'total_bahan_baku' => $totalBahanBaku,
                    'total_produk' => $totalProduk,
                    'total_nilai_bahan_baku' => $totalNilaiBahanBaku,
                    'total_nilai_produk' => $totalNilaiProduk,
                    'total_nilai' => $totalNilaiBahanBaku + $totalNilaiProduk,
                ];
                
                $currentDate->addDay();
            }
            
            ksort($groupedByDate);
            $data = array_values($groupedByDate);
            
            foreach ($data as $d) {
                $summary['total_bahan_baku'] += $d['total_bahan_baku'];
                $summary['total_produk'] += $d['total_produk'];
                $summary['total_nilai_bahan_baku'] += $d['total_nilai_bahan_baku'];
                $summary['total_nilai_produk'] += $d['total_nilai_produk'];
            }
            
            $daysCount = count($data);
            if ($daysCount > 0) {
                $summary['avg_bahan_baku'] = $summary['total_bahan_baku'] / $daysCount;
                $summary['avg_produk'] = $summary['total_produk'] / $daysCount;
                $summary['avg_nilai_bahan_baku'] = $summary['total_nilai_bahan_baku'] / $daysCount;
                $summary['avg_nilai_produk'] = $summary['total_nilai_produk'] / $daysCount;
            }
            
            $summary['total_nilai'] = $summary['total_nilai_bahan_baku'] + $summary['total_nilai_produk'];
            
            $periodLabel = $bulan->locale('id')->format('F Y');
            
        } else {
            $tahun = (int) $filterValue;
            $startDate = Carbon::create($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 12, 31)->endOfDay();
            
            $bahanBaku = BahanBaku::where('id_cabang', $idCabang)->get();
            $produk = Produk::where('id_cabang', $idCabang)->get();
            
            $groupedByMonth = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $monthStart = Carbon::create($tahun, $month, 1)->startOfMonth()->startOfDay();
                $monthEnd = Carbon::create($tahun, $month, 1)->endOfMonth()->endOfDay();
                $monthKey = sprintf('%04d-%02d', $tahun, $month);
                
                $totalBahanBaku = 0;
                $totalNilaiBahanBaku = 0;
                
                foreach ($bahanBaku as $b) {
                    $stokSaatIni = $b->stok;
                    
                    $movementsAfter = StockMovement::where('bahan_baku_id', $b->id)
                        ->where('id_cabang', $idCabang)
                        ->where('created_at', '>', $monthEnd)
                        ->get();
                    
                    $perubahanSetelah = 0;
                    foreach ($movementsAfter as $mov) {
                        if ($mov->tipe === 'in') {
                            $perubahanSetelah += $mov->qty;
                        } elseif ($mov->tipe === 'out') {
                            $perubahanSetelah -= $mov->qty;
                        } elseif ($mov->tipe === 'adj') {
                            $perubahanSetelah += $mov->qty;
                        }
                    }
                    
                    $stokTanggal = $stokSaatIni - $perubahanSetelah;
                    $totalBahanBaku += $stokTanggal;
                    $totalNilaiBahanBaku += $stokTanggal * $b->harga_satuan;
                }
                
                $totalProduk = $produk->sum('stok');
                $totalNilaiProduk = $produk->sum(function($p) {
                    return $p->stok * $p->harga;
                });
                
                $groupedByMonth[$monthKey] = [
                    'bulan' => $monthStart->locale('id')->format('F Y'),
                    'total_bahan_baku' => $totalBahanBaku,
                    'total_produk' => $totalProduk,
                    'total_nilai_bahan_baku' => $totalNilaiBahanBaku,
                    'total_nilai_produk' => $totalNilaiProduk,
                    'total_nilai' => $totalNilaiBahanBaku + $totalNilaiProduk,
                ];
            }
            
            ksort($groupedByMonth);
            $data = array_values($groupedByMonth);
            
            foreach ($data as $d) {
                $summary['total_bahan_baku'] += $d['total_bahan_baku'];
                $summary['total_produk'] += $d['total_produk'];
                $summary['total_nilai_bahan_baku'] += $d['total_nilai_bahan_baku'];
                $summary['total_nilai_produk'] += $d['total_nilai_produk'];
            }
            
            $monthsCount = count($data);
            if ($monthsCount > 0) {
                $summary['avg_bahan_baku'] = $summary['total_bahan_baku'] / $monthsCount;
                $summary['avg_produk'] = $summary['total_produk'] / $monthsCount;
                $summary['avg_nilai_bahan_baku'] = $summary['total_nilai_bahan_baku'] / $monthsCount;
                $summary['avg_nilai_produk'] = $summary['total_nilai_produk'] / $monthsCount;
            }
            
            $summary['total_nilai'] = $summary['total_nilai_bahan_baku'] + $summary['total_nilai_produk'];
            
            $periodLabel = $tahun;
        }
        
        // Generate CSV
        $filename = 'Laporan_Stok_' . ucfirst($filterType) . '_' . str_replace([' ', '/'], '_', $periodLabel) . '_' . date('YmdHis') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data, $summary, $filterType, $namaCabang, $periodLabel) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, "\xEF\xBB\xBF");
            
            // Header Information
            fputcsv($file, ['LAPORAN STOK ' . strtoupper($filterType)]);
            fputcsv($file, ['Cabang: ' . $namaCabang]);
            fputcsv($file, ['Periode: ' . $periodLabel]);
            fputcsv($file, ['Dicetak: ' . date('d/m/Y H:i:s')]);
            fputcsv($file, []); // Empty row
            
            // Summary
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Bahan Baku (Rata-rata)', number_format($summary['avg_bahan_baku'] ?? 0, 2, ',', '.')]);
            fputcsv($file, ['Total Produk (Rata-rata)', number_format($summary['avg_produk'] ?? 0, 2, ',', '.')]);
            fputcsv($file, ['Nilai Bahan Baku (Rata-rata)', 'Rp ' . number_format($summary['avg_nilai_bahan_baku'] ?? 0, 0, ',', '.')]);
            fputcsv($file, ['Nilai Produk (Rata-rata)', 'Rp ' . number_format($summary['avg_nilai_produk'] ?? 0, 0, ',', '.')]);
            fputcsv($file, ['Total Nilai', 'Rp ' . number_format($summary['total_nilai'] ?? 0, 0, ',', '.')]);
            fputcsv($file, []); // Empty row
            
            // Table Header
            if ($filterType === 'bulanan') {
                fputcsv($file, ['Tanggal', 'Total Bahan Baku', 'Total Produk', 'Nilai Bahan Baku', 'Nilai Produk', 'Total Nilai']);
            } else {
                fputcsv($file, ['Bulan', 'Total Bahan Baku', 'Total Produk', 'Nilai Bahan Baku', 'Nilai Produk', 'Total Nilai']);
            }
            
            // Table Data
            foreach ($data as $row) {
                if ($filterType === 'bulanan') {
                    fputcsv($file, [
                        $row['tanggal'],
                        number_format($row['total_bahan_baku'], 2, ',', '.'),
                        number_format($row['total_produk'], 2, ',', '.'),
                        number_format($row['total_nilai_bahan_baku'], 0, ',', '.'),
                        number_format($row['total_nilai_produk'], 0, ',', '.'),
                        number_format($row['total_nilai'], 0, ',', '.')
                    ]);
                } else {
                    fputcsv($file, [
                        $row['bulan'],
                        number_format($row['total_bahan_baku'], 2, ',', '.'),
                        number_format($row['total_produk'], 2, ',', '.'),
                        number_format($row['total_nilai_bahan_baku'], 0, ',', '.'),
                        number_format($row['total_nilai_produk'], 0, ',', '.'),
                        number_format($row['total_nilai'], 0, ',', '.')
                    ]);
                }
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
}

