<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class LaporanStokController extends Controller
{
    public function index(Request $request)
    {
        $idCabang = session('id_cabang');
        $namaCabang = session('nama_cabang', 'Cabang');
        
        // Default tanggal hari ini
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $tanggalCarbon = Carbon::parse($tanggal)->endOfDay();
        
        $bahan = BahanBaku::where('id_cabang', $idCabang)
            ->orderBy('nama_bahan')
            ->get();
        
        // Hitung stok per tanggal berdasarkan movements
        $stokByDate = [];
        foreach ($bahan as $b) {
            // Hitung stok awal
            $stokAwal = 0;
            
            // Hitung perubahan stok dari movements hingga tanggal yang dipilih
            $movements = StockMovement::where('bahan_baku_id', $b->id)
                ->where('id_cabang', $idCabang)
                ->where('created_at', '<=', $tanggalCarbon)
                ->get();
            
            $stokPerubahan = 0;
            foreach ($movements as $mov) {
                if ($mov->tipe === 'in') {
                    $stokPerubahan += $mov->qty;
                } elseif ($mov->tipe === 'out') {
                    $stokPerubahan -= $mov->qty;
                } elseif ($mov->tipe === 'adj') {
                    // Untuk adjustment, kita hitung selisihnya
                    $stokPerubahan += $mov->qty; // qty sudah berupa selisih
                }
            }
            
            // Stok pada tanggal yang dipilih = stok awal + perubahan
            // Untuk sederhana, kita gunakan stok saat ini sebagai referensi
            // dan hitung mundur berdasarkan movements setelah tanggal
            $stokSaatIni = $b->stok;
            
            // Hitung movements setelah tanggal
            $movementsAfter = StockMovement::where('bahan_baku_id', $b->id)
                ->where('id_cabang', $idCabang)
                ->where('created_at', '>', $tanggalCarbon)
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
            
            // Stok pada tanggal = stok saat ini - perubahan setelah tanggal
            $stokByDate[$b->id] = $stokSaatIni - $perubahanSetelah;
        }
        
        return view('inventori.laporan-stok.index', compact('bahan', 'tanggal', 'stokByDate', 'namaCabang'));
    }

    public function export(Request $request)
    {
        $idCabang = session('id_cabang');
        $namaCabang = session('nama_cabang', 'Cabang');
        
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $tanggalCarbon = Carbon::parse($tanggal)->endOfDay();
        $tanggalFormatted = Carbon::parse($tanggal)->format('d/m/Y');
        
        $bahan = BahanBaku::where('id_cabang', $idCabang)
            ->orderBy('nama_bahan')
            ->get();

        $filename = 'laporan-stok-harian-' . $tanggal . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($bahan, $namaCabang, $tanggalFormatted, $tanggalCarbon, $idCabang) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk UTF-8 agar Excel membaca dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header CSV
            fputcsv($file, ['LAPORAN STOK HARIAN - ' . strtoupper($namaCabang)]);
            fputcsv($file, ['Tanggal', $tanggalFormatted]);
            fputcsv($file, ['Generated', date('d/m/Y H:i:s')]);
            fputcsv($file, []); // Blank line
            
            // Kolom header
            fputcsv($file, ['No', 'Nama Bahan', 'Satuan', 'Stok', 'Harga Satuan', 'Total Nilai']);
            
            $no = 1;
            $totalNilai = 0;
            
            foreach ($bahan as $b) {
                // Hitung stok per tanggal
                $stokSaatIni = $b->stok;
                
                $movementsAfter = StockMovement::where('bahan_baku_id', $b->id)
                    ->where('id_cabang', $idCabang)
                    ->where('created_at', '>', $tanggalCarbon)
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
                $nilai = $stokTanggal * $b->harga_satuan;
                $totalNilai += $nilai;
                
                fputcsv($file, [
                    $no++,
                    $b->nama_bahan,
                    $b->satuan,
                    number_format($stokTanggal, 2, ',', '.'),
                    'Rp ' . number_format($b->harga_satuan, 0, ',', '.'),
                    'Rp ' . number_format($nilai, 0, ',', '.'),
                ]);
            }
            
            // Footer
            fputcsv($file, []); // Blank line
            fputcsv($file, ['TOTAL NILAI STOK', '', '', '', '', 'Rp ' . number_format($totalNilai, 0, ',', '.')]);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

