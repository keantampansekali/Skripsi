@extends('layouts.dashboard')

@section('title', 'Tambah Produk')
@section('header', 'Tambah Produk')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf

        <div>
            <label class="block text-sm mb-1">Foto Produk</label>
            <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF (Max: 2MB)</p>
            @error('foto')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Nama Produk</label>
            <input name="nama_produk" value="{{ old('nama_produk') }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
            @error('nama_produk')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Harga</label>
                <input type="number" step="0.01" name="harga" value="{{ old('harga') }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                @error('harga')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Stok</label>
                <input type="number" name="stok" value="{{ old('stok', 0) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                @error('stok')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Cabang</label>
            <div class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-900 dark:border-gray-700">
                {{ session('nama_cabang') }}
            </div>
            <input type="hidden" name="id_cabang" value="{{ session('id_cabang') }}" />
        </div>

        <div class="flex gap-2">
            <a href="{{ route('produk.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Batal
            </a>
            <button class="px-3 py-2 rounded bg-blue-600 text-white flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection


