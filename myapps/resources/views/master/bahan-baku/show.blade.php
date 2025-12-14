@extends('layouts.dashboard')

@section('title', 'Detail Bahan Baku')
@section('header', 'Detail Bahan Baku')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        
        <div>
            <label class="block text-sm mb-1 font-medium">Nama Bahan</label>
            <input value="{{ $item->nama_bahan }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1 font-medium">Satuan</label>
                <input value="{{ $item->satuan }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium">Stok</label>
                <input type="number" value="{{ $item->stok }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium">Harga / Unit</label>
                <input type="number" step="0.01" value="{{ $item->harga_satuan }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1 font-medium">Cabang</label>
            <div class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-900 dark:border-gray-700">
                {{ session('nama_cabang', 'Tidak ada cabang') }}
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('bahan-baku.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Kembali</a>
            <a href="{{ route('bahan-baku.edit', $item) }}" class="px-3 py-2 rounded bg-amber-500 text-white hover:bg-amber-600 transition-colors">Edit</a>
        </div>
    </div>
</div>
@endsection

