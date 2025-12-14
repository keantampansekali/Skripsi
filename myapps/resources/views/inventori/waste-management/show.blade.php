@extends('layouts.dashboard')

@section('title', 'Detail Waste Management')
@section('header', 'Detail Waste Management')

@section('content')
<div class="max-w-4xl space-y-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-4">Detail Waste Management</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-gray-500 dark:text-gray-400 mb-1">Tanggal</div>
                <div class="font-medium">{{ \Carbon\Carbon::parse($wasteManagement->tanggal)->format('d M Y') }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-gray-500 dark:text-gray-400 mb-1">Catatan</div>
                <div class="font-medium">{{ $wasteManagement->catatan ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-4">Item Waste</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-left py-2 px-3">Kategori</th>
                        <th class="text-left py-2 px-3">Item</th>
                        <th class="text-left py-2 px-3">Qty</th>
                        <th class="text-left py-2 px-3">Harga Satuan</th>
                        <th class="text-left py-2 px-3">Subtotal</th>
                        <th class="text-left py-2 px-3">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalHarga = 0;
                    @endphp
                    @forelse($wasteManagement->items as $item)
                    @php
                        $hargaSatuan = 0;
                        if ($item->tipe === 'bahan_baku' && $item->bahan) {
                            $hargaSatuan = $item->bahan->harga_satuan ?? 0;
                        } elseif ($item->tipe === 'produk' && $item->produk) {
                            $hargaSatuan = $item->produk->harga ?? 0;
                        }
                        $subtotal = $item->qty * $hargaSatuan;
                        $totalHarga += $subtotal;
                    @endphp
                    <tr class="border-b dark:border-gray-700">
                        <td class="py-2 px-3">
                            <span class="px-2 py-1 rounded text-xs {{ $item->tipe === 'bahan_baku' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                {{ $item->tipe === 'bahan_baku' ? 'Bahan Baku' : 'Produk' }}
                            </span>
                        </td>
                        <td class="py-2 px-3">
                            @if($item->tipe === 'bahan_baku' && $item->bahan)
                                {{ $item->bahan->nama_bahan }} ({{ $item->bahan->satuan }})
                            @elseif($item->tipe === 'produk' && $item->produk)
                                {{ $item->produk->nama_produk }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-2 px-3">{{ number_format($item->qty, 2) }}</td>
                        <td class="py-2 px-3">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                        <td class="py-2 px-3">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        <td class="py-2 px-3">{{ $item->alasan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-3 text-center text-gray-500 dark:text-gray-400">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($wasteManagement->items->count() > 0)
                <tfoot>
                    <tr class="border-t-2 dark:border-gray-700 font-semibold">
                        <td colspan="4" class="py-2 px-3 text-right">Total Harga:</td>
                        <td class="py-2 px-3">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('waste-management.index') }}" class="px-3 py-2 rounded border dark:border-gray-700">Kembali</a>
    </div>
</div>
@endsection

