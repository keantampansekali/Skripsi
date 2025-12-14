@extends('layouts.dashboard')

@section('title', 'Detail Produk')
@section('header', 'Detail Produk')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        
        <div>
            <label class="block text-sm mb-1">Foto Produk</label>
            @if($produk->foto)
                <img src="{{ asset($produk->foto) }}" alt="{{ $produk->nama_produk }}" class="w-48 h-48 object-cover rounded border" />
            @else
                <div class="w-48 h-48 bg-gray-200 dark:bg-gray-700 rounded border flex items-center justify-center">
                    <span class="text-gray-400">No Image</span>
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm mb-1">Nama Produk</label>
            <input value="{{ $produk->nama_produk }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
        </div>

        <div>
            <label class="block text-sm mb-1">Deskripsi</label>
            <textarea rows="3" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed">{{ $produk->deskripsi }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Harga</label>
                <input type="number" step="0.01" value="{{ $produk->harga }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
            </div>
            <div>
                <label class="block text-sm mb-1">Stok</label>
                <input type="number" value="{{ $produk->stok }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Cabang</label>
            <div class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-900 dark:border-gray-700">
                {{ optional($produk->cabang)->nama_cabang ?? session('nama_cabang') }}
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('produk.index') }}" class="px-3 py-2 rounded border dark:border-gray-700">Kembali</a>
            <a href="{{ route('produk.edit', $produk) }}" class="px-3 py-2 rounded bg-amber-500 text-white">Edit</a>
        </div>
    </div>
</div>
@endsection

