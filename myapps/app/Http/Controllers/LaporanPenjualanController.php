<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKasir;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $namaCabang = session('nama_cabang', 'Cabang');
        
        // Get filter type and value
        $filterType = $request->get('filter_type', 'harian'); // harian, bulanan, tahunan
        $filterValue = $request->get('filter_value');
        
        // Set default values
        if (!$filterValue) {
            if ($filterType === 'harian') {
                $filterValue = date('Y-m-d');
            } elseif ($filterType === 'bulanan') {
                $filterValue = date('Y-m');
            } else {
                $filterValue = date('Y');
            }
        }
        
        $data = [];
        $summary = [
            'total_transaksi' => 0,
            'total_subtotal' => 0,
            'total_diskon' => 0,
            'total_tax' => 0,
            'total_penjualan' => 0,
        ];
        
        if ($filterType === 'harian') {
            // Laporan Harian
            $tanggal = Carbon::parse($filterValue);
            $startDate = $tanggal->copy()->startOfDay();
            $endDate = $tanggal->copy()->endOfDay();
            
            $transaksi = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            foreach ($transaksi as $t) {
                $data[] = [
                    'waktu' => Carbon::parse($t->created_at)->format('H:i:s'),
                    'no_transaksi' => $t->no_transaksi,
                    'subtotal' => $t->subtotal,
                    'diskon' => $t->diskon,
                    'tax' => $t->tax,
                    'total' => $t->total,
                ];
                
                $summary['total_transaksi']++;
                $summary['total_subtotal'] += $t->subtotal;
                $summary['total_diskon'] += $t->diskon;
                $summary['total_tax'] += $t->tax;
                $summary['total_penjualan'] += $t->total;
            }
            
            $periodLabel = $tanggal->locale('id')->format('d M Y');
            
        } elseif ($filterType === 'bulanan') {
            // Laporan Bulanan - group by hari
            $bulan = Carbon::createFromFormat('Y-m', $filterValue);
            $startDate = $bulan->copy()->startOfMonth()->startOfDay();
            $endDate = $bulan->copy()->endOfMonth()->endOfDay();
            
            $transaksi = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            // Group by date
            $groupedByDate = [];
            foreach ($transaksi as $t) {
                $dateKey = Carbon::parse($t->created_at)->format('Y-m-d');
                if (!isset($groupedByDate[$dateKey])) {
                    $groupedByDate[$dateKey] = [
                        'tanggal' => Carbon::parse($t->created_at)->locale('id')->format('d M Y'),
                        'jumlah_transaksi' => 0,
                        'subtotal' => 0,
                        'diskon' => 0,
                        'tax' => 0,
                        'total' => 0,
                    ];
                }
                
                $groupedByDate[$dateKey]['jumlah_transaksi']++;
                $groupedByDate[$dateKey]['subtotal'] += $t->subtotal;
                $groupedByDate[$dateKey]['diskon'] += $t->diskon;
                $groupedByDate[$dateKey]['tax'] += $t->tax;
                $groupedByDate[$dateKey]['total'] += $t->total;
            }
            
            // Sort by date key
            ksort($groupedByDate);
            $data = array_values($groupedByDate);
            
            foreach ($transaksi as $t) {
                $summary['total_transaksi']++;
                $summary['total_subtotal'] += $t->subtotal;
                $summary['total_diskon'] += $t->diskon;
                $summary['total_tax'] += $t->tax;
                $summary['total_penjualan'] += $t->total;
            }
            
            $periodLabel = $bulan->locale('id')->format('F Y');
            
        } else {
            // Laporan Tahunan - group by bulan
            $tahun = (int) $filterValue;
            $startDate = Carbon::create($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 12, 31)->endOfDay();
            
            $transaksi = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            // Group by month
            $groupedByMonth = [];
            foreach ($transaksi as $t) {
                $monthKey = Carbon::parse($t->created_at)->format('Y-m');
                $monthName = Carbon::parse($t->created_at)->locale('id')->format('F Y');
                
                if (!isset($groupedByMonth[$monthKey])) {
                    $groupedByMonth[$monthKey] = [
                        'bulan' => $monthName,
                        'jumlah_transaksi' => 0,
                        'subtotal' => 0,
                        'diskon' => 0,
                        'tax' => 0,
                        'total' => 0,
                    ];
                }
                
                $groupedByMonth[$monthKey]['jumlah_transaksi']++;
                $groupedByMonth[$monthKey]['subtotal'] += $t->subtotal;
                $groupedByMonth[$monthKey]['diskon'] += $t->diskon;
                $groupedByMonth[$monthKey]['tax'] += $t->tax;
                $groupedByMonth[$monthKey]['total'] += $t->total;
            }
            
            // Sort by month key
            ksort($groupedByMonth);
            $data = array_values($groupedByMonth);
            
            foreach ($transaksi as $t) {
                $summary['total_transaksi']++;
                $summary['total_subtotal'] += $t->subtotal;
                $summary['total_diskon'] += $t->diskon;
                $summary['total_tax'] += $t->tax;
                $summary['total_penjualan'] += $t->total;
            }
            
            $periodLabel = $tahun;
        }
        
        // Prepare chart data
        $chartLabels = [];
        $chartTotals = [];
        $chartSubtotals = [];
        $chartDiskons = [];
        
        foreach ($data as $row) {
            if ($filterType === 'harian') {
                $chartLabels[] = $row['waktu'];
            } elseif ($filterType === 'bulanan') {
                $chartLabels[] = $row['tanggal'];
            } else {
                $chartLabels[] = $row['bulan'];
            }
            $chartTotals[] = $row['total'];
            $chartSubtotals[] = $row['subtotal'];
            $chartDiskons[] = $row['diskon'];
        }
        
        return view('reports.penjualan.index', compact(
            'data',
            'summary',
            'filterType',
            'filterValue',
            'periodLabel',
            'namaCabang',
            'chartLabels',
            'chartTotals',
            'chartSubtotals',
            'chartDiskons'
        ));
    }

    public function export(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $namaCabang = session('nama_cabang', 'Cabang');
        
        // Get filter type and value
        $filterType = $request->get('filter_type', 'harian');
        $filterValue = $request->get('filter_value');
        
        // Set default values
        if (!$filterValue) {
            if ($filterType === 'harian') {
                $filterValue = date('Y-m-d');
            } elseif ($filterType === 'bulanan') {
                $filterValue = date('Y-m');
            } else {
                $filterValue = date('Y');
            }
        }
        
        $data = [];
        $summary = [
            'total_transaksi' => 0,
            'total_subtotal' => 0,
            'total_diskon' => 0,
            'total_tax' => 0,
            'total_penjualan' => 0,
        ];
        
        if ($filterType === 'harian') {
            $tanggal = Carbon::parse($filterValue);
            $startDate = $tanggal->copy()->startOfDay();
            $endDate = $tanggal->copy()->endOfDay();
            
            $transaksi = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            foreach ($transaksi as $t) {
                $data[] = [
                    'waktu' => Carbon::parse($t->created_at)->format('H:i:s'),
                    'no_transaksi' => $t->no_transaksi,
                    'subtotal' => $t->subtotal,
                    'diskon' => $t->diskon,
                    'tax' => $t->tax,
                    'total' => $t->total,
                ];
                
                $summary['total_transaksi']++;
                $summary['total_subtotal'] += $t->subtotal;
                $summary['total_diskon'] += $t->diskon;
                $summary['total_tax'] += $t->tax;
                $summary['total_penjualan'] += $t->total;
            }
            
            $periodLabel = $tanggal->locale('id')->format('d M Y');
            
        } elseif ($filterType === 'bulanan') {
            $bulan = Carbon::createFromFormat('Y-m', $filterValue);
            $startDate = $bulan->copy()->startOfMonth()->startOfDay();
            $endDate = $bulan->copy()->endOfMonth()->endOfDay();
            
            $transaksi = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            $groupedByDate = [];
            foreach ($transaksi as $t) {
                $dateKey = Carbon::parse($t->created_at)->format('Y-m-d');
                if (!isset($groupedByDate[$dateKey])) {
                    $groupedByDate[$dateKey] = [
                        'tanggal' => Carbon::parse($t->created_at)->locale('id')->format('d M Y'),
                        'jumlah_transaksi' => 0,
                        'subtotal' => 0,
                        'diskon' => 0,
                        'tax' => 0,
                        'total' => 0,
                    ];
                }
                
                $groupedByDate[$dateKey]['jumlah_transaksi']++;
                $groupedByDate[$dateKey]['subtotal'] += $t->subtotal;
                $groupedByDate[$dateKey]['diskon'] += $t->diskon;
                $groupedByDate[$dateKey]['tax'] += $t->tax;
                $groupedByDate[$dateKey]['total'] += $t->total;
            }
            
            ksort($groupedByDate);
            $data = array_values($groupedByDate);
            
            foreach ($transaksi as $t) {
                $summary['total_transaksi']++;
                $summary['total_subtotal'] += $t->subtotal;
                $summary['total_diskon'] += $t->diskon;
                $summary['total_tax'] += $t->tax;
                $summary['total_penjualan'] += $t->total;
            }
            
            $periodLabel = $bulan->locale('id')->format('F Y');
            
        } else {
            $tahun = (int) $filterValue;
            $startDate = Carbon::create($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 12, 31)->endOfDay();
            
            $transaksi = TransaksiKasir::where('id_cabang', $idCabang)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            $groupedByMonth = [];
            foreach ($transaksi as $t) {
                $monthKey = Carbon::parse($t->created_at)->format('Y-m');
                $monthName = Carbon::parse($t->created_at)->locale('id')->format('F Y');
                
                if (!isset($groupedByMonth[$monthKey])) {
                    $groupedByMonth[$monthKey] = [
                        'bulan' => $monthName,
                        'jumlah_transaksi' => 0,
                        'subtotal' => 0,
                        'diskon' => 0,
                        'tax' => 0,
                        'total' => 0,
                    ];
                }
                
                $groupedByMonth[$monthKey]['jumlah_transaksi']++;
                $groupedByMonth[$monthKey]['subtotal'] += $t->subtotal;
                $groupedByMonth[$monthKey]['diskon'] += $t->diskon;
                $groupedByMonth[$monthKey]['tax'] += $t->tax;
                $groupedByMonth[$monthKey]['total'] += $t->total;
            }
            
            ksort($groupedByMonth);
            $data = array_values($groupedByMonth);
            
            foreach ($transaksi as $t) {
                $summary['total_transaksi']++;
                $summary['total_subtotal'] += $t->subtotal;
                $summary['total_diskon'] += $t->diskon;
                $summary['total_tax'] += $t->tax;
                $summary['total_penjualan'] += $t->total;
            }
            
            $periodLabel = $tahun;
        }
        
        // Generate CSV
        $filename = 'Laporan_Penjualan_' . ucfirst($filterType) . '_' . str_replace([' ', '/'], '_', $periodLabel) . '_' . date('YmdHis') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data, $summary, $filterType, $namaCabang, $periodLabel) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, "\xEF\xBB\xBF");
            
            // Header Information
            fputcsv($file, ['LAPORAN PENJUALAN ' . strtoupper($filterType)]);
            fputcsv($file, ['Cabang: ' . $namaCabang]);
            fputcsv($file, ['Periode: ' . $periodLabel]);
            fputcsv($file, ['Dicetak: ' . date('d/m/Y H:i:s')]);
            fputcsv($file, []); // Empty row
            
            // Summary
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Transaksi', number_format($summary['total_transaksi'], 0, ',', '.')]);
            fputcsv($file, ['Total Subtotal', 'Rp ' . number_format($summary['total_subtotal'], 0, ',', '.')]);
            fputcsv($file, ['Total Diskon', 'Rp ' . number_format($summary['total_diskon'], 0, ',', '.')]);
            fputcsv($file, ['Total Tax', 'Rp ' . number_format($summary['total_tax'], 0, ',', '.')]);
            fputcsv($file, ['Total Penjualan', 'Rp ' . number_format($summary['total_penjualan'], 0, ',', '.')]);
            fputcsv($file, []); // Empty row
            
            // Table Header
            if ($filterType === 'harian') {
                fputcsv($file, ['Waktu', 'No Transaksi', 'Subtotal', 'Diskon', 'Tax', 'Total']);
            } elseif ($filterType === 'bulanan') {
                fputcsv($file, ['Tanggal', 'Jumlah Transaksi', 'Subtotal', 'Diskon', 'Tax', 'Total']);
            } else {
                fputcsv($file, ['Bulan', 'Jumlah Transaksi', 'Subtotal', 'Diskon', 'Tax', 'Total']);
            }
            
            // Table Data
            foreach ($data as $row) {
                if ($filterType === 'harian') {
                    fputcsv($file, [
                        $row['waktu'],
                        $row['no_transaksi'],
                        number_format($row['subtotal'], 0, ',', '.'),
                        number_format($row['diskon'], 0, ',', '.'),
                        number_format($row['tax'], 0, ',', '.'),
                        number_format($row['total'], 0, ',', '.')
                    ]);
                } elseif ($filterType === 'bulanan') {
                    fputcsv($file, [
                        $row['tanggal'],
                        number_format($row['jumlah_transaksi'], 0, ',', '.'),
                        number_format($row['subtotal'], 0, ',', '.'),
                        number_format($row['diskon'], 0, ',', '.'),
                        number_format($row['tax'], 0, ',', '.'),
                        number_format($row['total'], 0, ',', '.')
                    ]);
                } else {
                    fputcsv($file, [
                        $row['bulan'],
                        number_format($row['jumlah_transaksi'], 0, ',', '.'),
                        number_format($row['subtotal'], 0, ',', '.'),
                        number_format($row['diskon'], 0, ',', '.'),
                        number_format($row['tax'], 0, ',', '.'),
                        number_format($row['total'], 0, ',', '.')
                    ]);
                }
            }
            
            // Total Row
            fputcsv($file, []); // Empty row
            fputcsv($file, [
                'TOTAL',
                $filterType !== 'harian' ? number_format($summary['total_transaksi'], 0, ',', '.') : '',
                number_format($summary['total_subtotal'], 0, ',', '.'),
                number_format($summary['total_diskon'], 0, ',', '.'),
                number_format($summary['total_tax'], 0, ',', '.'),
                number_format($summary['total_penjualan'], 0, ',', '.')
            ]);
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
}

