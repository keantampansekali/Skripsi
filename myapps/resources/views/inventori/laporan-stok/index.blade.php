@extends('layouts.dashboard')

@section('title', 'Laporan Stok Harian')
@section('header', 'Laporan Stok Harian - ' . session('nama_cabang', 'Tidak ada cabang'))

@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-4">
    <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Cabang aktif:</span>
        <span class="font-medium">{{ $namaCabang }}</span>
    </div>
    <div class="flex gap-2 items-end">
        <form action="{{ route('laporan-stok.index') }}" method="GET" class="flex gap-2 items-end">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Filter Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" />
            </div>
            <button type="submit" class="px-3 py-2 rounded bg-gray-600 text-white text-sm hover:bg-gray-700 whitespace-nowrap h-[34px]">Tampilkan</button>
        </form>
        <a href="{{ route('laporan-stok.export', ['tanggal' => $tanggal]) }}" class="px-3 py-2 rounded bg-green-600 text-white text-sm hover:bg-green-700 whitespace-nowrap h-[34px] flex items-center">Export CSV</a>
        <button onclick="window.print()" class="px-3 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700 whitespace-nowrap h-[34px]">Print</button>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden print:shadow-none">
    <div class="p-4 print:hidden">
        <h2 class="text-lg font-semibold">Laporan Stok Harian</h2>
        <p class="text-sm text-gray-500">Cabang: {{ $namaCabang }}</p>
        <p class="text-sm text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</p>
        <p class="text-sm text-gray-500">Dicetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="hidden print:block p-4">
        <h2 class="text-lg font-semibold">LAPORAN STOK HARIAN</h2>
        <p class="text-sm">Cabang: {{ $namaCabang }}</p>
        <p class="text-sm">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</p>
        <p class="text-sm">Dicetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm print:text-xs">
            <thead>
                <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700 print:bg-gray-100">
                    <th class="text-left py-2 px-3">No</th>
                    <th class="text-left py-2 px-3">Nama Bahan</th>
                    <th class="text-left py-2 px-3">Satuan</th>
                    <th class="text-right py-2 px-3">Stok</th>
                    <th class="text-right py-2 px-3">Harga Satuan</th>
                    <th class="text-right py-2 px-3">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalNilai = 0;
                    $no = 1;
                @endphp
                @forelse($bahan as $b)
                @php
                    $stokTanggal = $stokByDate[$b->id] ?? $b->stok;
                    $nilai = $stokTanggal * $b->harga_satuan;
                    $totalNilai += $nilai;
                @endphp
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2 px-3">{{ $no++ }}</td>
                    <td class="py-2 px-3">{{ $b->nama_bahan }}</td>
                    <td class="py-2 px-3">{{ $b->satuan }}</td>
                    <td class="py-2 px-3 text-right">{{ number_format($stokTanggal, 2, ',', '.') }}</td>
                    <td class="py-2 px-3 text-right">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
                    <td class="py-2 px-3 text-right font-medium">Rp {{ number_format($nilai, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-4 px-3 text-center text-gray-500 dark:text-gray-400">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 print:bg-gray-100 font-semibold">
                    <td colspan="5" class="py-2 px-3 text-right">TOTAL NILAI STOK:</td>
                    <td class="py-2 px-3 text-right">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('head')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print\:shadow-none, .print\:shadow-none * {
            visibility: visible;
        }
        .print\:shadow-none {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:hidden {
            display: none;
        }
        .print\:block {
            display: block;
        }
        .print\:bg-gray-100 {
            background-color: #f3f4f6;
        }
        .print\:text-xs {
            font-size: 0.75rem;
        }
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
    }
</style>
@endpush
@endsection

