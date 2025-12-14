<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanPembelianController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = (int) session('id_cabang');
        $namaCabang = session('nama_cabang', 'Cabang');
        
        // Get filter value (bulan)
        $filterValue = $request->get('filter_value', date('Y-m'));
        
        $data = [];
        $summary = [
            'total_pembelian' => 0,
            'total_subtotal' => 0,
            'total_diskon' => 0,
            'total_ppn' => 0,
            'total_pembayaran' => 0,
        ];
        
        // Laporan Bulanan - group by hari
        $bulan = Carbon::createFromFormat('Y-m', $filterValue);
        $startDate = $bulan->copy()->startOfMonth();
        $endDate = $bulan->copy()->endOfMonth();
        
        $restocks = Restock::where('id_cabang', $idCabang)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('tanggal')
            ->get();
        
        // Group by date
        $groupedByDate = [];
        foreach ($restocks as $r) {
            $dateKey = Carbon::parse($r->tanggal)->format('Y-m-d');
            if (!isset($groupedByDate[$dateKey])) {
                $groupedByDate[$dateKey] = [
                    'tanggal' => Carbon::parse($r->tanggal)->locale('id')->format('d M Y'),
                    'jumlah_pembelian' => 0,
                    'subtotal' => 0,
                    'diskon' => 0,
                    'ppn' => 0,
                    'total' => 0,
                ];
            }
            
            $groupedByDate[$dateKey]['jumlah_pembelian']++;
            $groupedByDate[$dateKey]['subtotal'] += $r->subtotal ?? 0;
            $groupedByDate[$dateKey]['diskon'] += $r->diskon ?? 0;
            $groupedByDate[$dateKey]['ppn'] += $r->ppn ?? 0;
            $groupedByDate[$dateKey]['total'] += $r->total ?? 0;
        }
        
        // Sort by date key
        ksort($groupedByDate);
        $data = array_values($groupedByDate);
        
        foreach ($restocks as $r) {
            $summary['total_pembelian']++;
            $summary['total_subtotal'] += $r->subtotal ?? 0;
            $summary['total_diskon'] += $r->diskon ?? 0;
            $summary['total_ppn'] += $r->ppn ?? 0;
            $summary['total_pembayaran'] += $r->total ?? 0;
        }
        
        $periodLabel = $bulan->locale('id')->format('F Y');
        
        // Prepare chart data
        $chartLabels = [];
        $chartTotals = [];
        $chartSubtotals = [];
        $chartDiskons = [];
        $chartPPNs = [];
        
        foreach ($data as $row) {
            $chartLabels[] = $row['tanggal'];
            $chartTotals[] = $row['total'];
            $chartSubtotals[] = $row['subtotal'];
            $chartDiskons[] = $row['diskon'];
            $chartPPNs[] = $row['ppn'];
        }
        
        return view('reports.pembelian.index', compact(
            'data',
            'summary',
            'filterValue',
            'periodLabel',
            'namaCabang',
            'chartLabels',
            'chartTotals',
            'chartSubtotals',
            'chartDiskons',
            'chartPPNs'
        ));
    }
}

