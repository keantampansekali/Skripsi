@extends('layouts.dashboard')

@section('title', 'Detail Penyesuaian Stok')
@section('header', 'Detail Penyesuaian Stok')

@section('content')
<div class="max-w-5xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
        <!-- Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b dark:border-gray-700 pb-4">
            <div>
                <h2 class="text-xl font-bold mb-4">Informasi Penyesuaian</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Tanggal:</span>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($penyesuaian->tanggal)->format('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Cabang:</span>
                        <span class="font-semibold">{{ session('nama_cabang', 'Tidak ada cabang') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Jumlah Item:</span>
                        <span class="font-semibold">{{ $penyesuaian->items->count() }} item</span>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-xl font-bold mb-4">Catatan</h2>
                <div class="text-sm">
                    <p class="text-gray-600 dark:text-gray-400">{{ $penyesuaian->catatan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div>
            <h2 class="text-xl font-bold mb-4">Daftar Item Penyesuaian</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left py-2 px-3">No</th>
                            <th class="text-left py-2 px-3">Nama Bahan Baku</th>
                            <th class="text-left py-2 px-3">Satuan</th>
                            <th class="text-right py-2 px-3">Stok Lama</th>
                            <th class="text-right py-2 px-3">Stok Baru</th>
                            <th class="text-right py-2 px-3">Selisih</th>
                            <th class="text-left py-2 px-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penyesuaian->items as $index => $item)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 px-3">{{ $index + 1 }}</td>
                            <td class="py-2 px-3">{{ $item->bahan->nama_bahan ?? '-' }}</td>
                            <td class="py-2 px-3">{{ $item->bahan->satuan ?? '-' }}</td>
                            <td class="py-2 px-3 text-right">{{ number_format($item->stok_lama, 2, ',', '.') }}</td>
                            <td class="py-2 px-3 text-right font-semibold">{{ number_format($item->stok_baru, 2, ',', '.') }}</td>
                            <td class="py-2 px-3 text-right">
                                <span class="font-semibold {{ $item->selisih >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $item->selisih >= 0 ? '+' : '' }}{{ number_format($item->selisih, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-2 px-3">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary -->
        <div class="border-t dark:border-gray-700 pt-4">
            <div class="flex justify-end">
                <div class="w-full md:w-1/2 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total Item:</span>
                        <span class="font-semibold">{{ $penyesuaian->items->count() }} item</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total Selisih Positif:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            +{{ number_format($penyesuaian->items->where('selisih', '>', 0)->sum('selisih'), 2, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total Selisih Negatif:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">
                            {{ number_format($penyesuaian->items->where('selisih', '<', 0)->sum('selisih'), 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2 pt-4 border-t dark:border-gray-700">
            <a href="{{ route('penyesuaian.index') }}" class="px-4 py-2 rounded border dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection

