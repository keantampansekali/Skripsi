@extends('layouts.dashboard')

@section('title', 'Edit Produk')
@section('header', 'Edit Produk')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('produk.update', $produk) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">Foto Produk</label>
            @if($produk->foto)
                <div class="mb-2">
                    <img src="{{ asset($produk->foto) }}" alt="{{ $produk->nama_produk }}" class="w-32 h-32 object-cover rounded border" />
                </div>
            @endif
            <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF (Max: 2MB). Kosongkan jika tidak ingin mengubah foto.</p>
            @error('foto')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Nama Produk</label>
            <input name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
            @error('nama_produk')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            @error('deskripsi')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Harga</label>
                <input type="number" step="0.01" name="harga" value="{{ old('harga', $produk->harga) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                @error('harga')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Stok</label>
                <input 
                    type="number" 
                    name="stok" 
                    id="stokInput"
                    value="{{ old('stok', $produk->stok) }}" 
                    max="{{ $maxProducible ?? 999999 }}"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" 
                    required 
                />
                @error('stok')
                    <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                @else
                    @if(isset($maxProducible) && $maxProducible > 0)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="stokInfo">
                            Maksimal stok: <strong>{{ $maxProducible }} unit</strong> (dibatasi oleh ketersediaan bahan baku)
                        </p>
                    @elseif(isset($maxProducible) && $maxProducible == 0)
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                            ⚠️ Bahan baku tidak tersedia. Stok harus 0.
                        </p>
                    @endif
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Cabang</label>
            <select name="id_cabang" disabled class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                <option value="">Gunakan cabang aktif ({{ session('nama_cabang') }})</option>
                @foreach($cabang as $c)
                    <option value="{{ $c->id_cabang }}" @selected(old('id_cabang', $produk->id_cabang) == $c->id_cabang)>{{ $c->nama_cabang }}</option>
                @endforeach
            </select>
            <input type="hidden" name="id_cabang" value="{{ $produk->id_cabang }}">
            <p class="text-xs text-gray-500 mt-1">Cabang tidak dapat diubah setelah produk dibuat.</p>
            @error('id_cabang')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="flex gap-2">
            <a href="{{ route('produk.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Batal
            </a>
            <button class="px-3 py-2 rounded bg-blue-600 text-white flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Update
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stokInput = document.getElementById('stokInput');
    const maxProducible = {{ $maxProducible ?? 0 }};
    
    if (stokInput && maxProducible > 0) {
        stokInput.addEventListener('input', function() {
            const value = parseInt(this.value) || 0;
            const stokInfo = document.getElementById('stokInfo');
            
            if (value > maxProducible) {
                this.classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                this.classList.remove('border-gray-300', 'dark:border-gray-700');
                
                if (stokInfo) {
                    stokInfo.innerHTML = `<span class="text-red-600 dark:text-red-400">⚠️ Stok melebihi kapasitas! Maksimal: <strong>${maxProducible} unit</strong></span>`;
                }
            } else {
                this.classList.remove('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                this.classList.add('border-gray-300', 'dark:border-gray-700');
                
                if (stokInfo) {
                    stokInfo.innerHTML = `Maksimal stok: <strong>${maxProducible} unit</strong> (dibatasi oleh ketersediaan bahan baku)`;
                    stokInfo.classList.remove('text-red-600', 'dark:text-red-400');
                    stokInfo.classList.add('text-gray-500', 'dark:text-gray-400');
                }
            }
        });
        
        // Validasi saat submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const value = parseInt(stokInput.value) || 0;
            if (value > maxProducible) {
                e.preventDefault();
                alert(`Stok tidak boleh melebihi ${maxProducible} unit. Kapasitas produksi terbatas oleh ketersediaan bahan baku.`);
                stokInput.focus();
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection


