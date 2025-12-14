@extends('layouts.dashboard')

@section('title', 'Detail Nota Pembelian')
@section('header', 'Detail Nota Pembelian')

@section('content')
<div class="max-w-5xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
        <!-- Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b dark:border-gray-700 pb-4">
            <div>
                <h2 class="text-xl font-bold mb-4">Informasi Nota</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">No. Nota:</span>
                        <span class="font-semibold">{{ $restock->no_nota ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Tanggal:</span>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($restock->tanggal)->format('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Cabang:</span>
                        <span class="font-semibold">{{ session('nama_cabang', 'Tidak ada cabang') }}</span>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-xl font-bold mb-4">Informasi Supplier</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Nama Supplier:</span>
                        <span class="font-semibold">{{ optional($restock->supplier)->nama_supplier ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Alamat:</span>
                        <span class="font-semibold text-right">{{ optional($restock->supplier)->alamat ?? '-' }}</span>
                    </div>
                    @if($restock->supplier && $restock->supplier->contacts->count() > 0)
                    <div class="mt-2">
                        <span class="text-gray-500 dark:text-gray-400 block mb-1">Kontak:</span>
                        @foreach($restock->supplier->contacts as $contact)
                        <div class="text-xs">
                            <span class="capitalize">{{ $contact->tipe }}:</span> 
                            <span class="font-semibold">{{ $contact->nilai }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div>
            <h2 class="text-xl font-bold mb-4">Daftar Bahan Baku</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left py-2 px-3">No</th>
                            <th class="text-left py-2 px-3">Nama Bahan Baku</th>
                            <th class="text-left py-2 px-3">Satuan</th>
                            <th class="text-right py-2 px-3">Jumlah</th>
                            <th class="text-right py-2 px-3">Harga Satuan</th>
                            <th class="text-right py-2 px-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($restock->items as $index => $item)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 px-3">{{ $index + 1 }}</td>
                            <td class="py-2 px-3">{{ $item->bahan->nama_bahan ?? '-' }}</td>
                            <td class="py-2 px-3">{{ $item->bahan->satuan ?? '-' }}</td>
                            <td class="py-2 px-3 text-right">{{ number_format($item->qty, 2, ',', '.') }}</td>
                            <td class="py-2 px-3 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-2 px-3 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
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
                        <span class="text-gray-500 dark:text-gray-400">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($restock->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Diskon:</span>
                        <span class="font-semibold">Rp {{ number_format($restock->diskon ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">PPN:</span>
                        <span class="font-semibold">Rp {{ number_format($restock->ppn ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t dark:border-gray-700 pt-2 mt-2">
                        <span>Total Pembayaran:</span>
                        <span>Rp {{ number_format($restock->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        @if($restock->catatan)
        <div class="border-t dark:border-gray-700 pt-4">
            <h3 class="text-sm font-semibold mb-2">Catatan:</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $restock->catatan }}</p>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-2 pt-4 border-t dark:border-gray-700">
            <a href="{{ route('restock.index') }}" class="px-4 py-2 rounded border dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection

