@extends('layouts.dashboard')

@section('title', 'Tambah Bahan Baku')
@section('header', 'Tambah Bahan Baku')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('bahan-baku.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf

        <div>
            <label class="block text-sm mb-1">Nama Bahan</label>
            <input name="nama_bahan" value="{{ old('nama_bahan') }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
            @error('nama_bahan')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
                <label class="block text-sm mb-1">Satuan</label>
                <select name="jenis_satuan" id="jenis_satuan" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required>
                    @php($jenis = old('jenis_satuan', 'pcs'))
                    <option value="pcs" @selected($jenis==='pcs')>pcs</option>
                    <option value="gram" @selected($jenis==='gram')>gram</option>
                    <option value="kg" @selected($jenis==='kg')>kg</option>
                    <option value="ml" @selected($jenis==='ml')>ml</option>
                    <option value="liter" @selected($jenis==='liter')>liter</option>
                    <option value="ons" @selected($jenis==='ons')>ons</option>
                    <option value="bungkus" @selected($jenis==='bungkus')>bungkus</option>
                    <option value="botol" @selected($jenis==='botol')>botol</option>
                    <option value="kaleng" @selected($jenis==='kaleng')>kaleng</option>
                </select>
                <input type="hidden" name="satuan" id="satuan_hidden" value="{{ old('satuan', '1 pcs') }}" />
                @error('satuan')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Stok</label>
                <input type="number" name="stok" value="{{ old('stok', 0) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                @error('stok')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Harga / Unit</label>
                <input type="number" step="0.01" name="harga_satuan" value="{{ old('harga_satuan', 0) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                @error('harga_satuan')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Cabang</label>
            <div class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-900 dark:border-gray-700">
                {{ session('nama_cabang') }}
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('bahan-baku.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
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

@push('scripts')
<script>
    function updateSatuan() {
        const jenis = document.getElementById('jenis_satuan').value;
        document.getElementById('satuan_hidden').value = '1 ' + jenis;
    }
    
    document.getElementById('jenis_satuan').addEventListener('change', updateSatuan);
    updateSatuan(); // Initialize
</script>
@endpush
@endsection


