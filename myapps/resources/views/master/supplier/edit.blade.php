@extends('layouts.dashboard')

@section('title', 'Edit Supplier')
@section('header', 'Edit Supplier')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('supplier.update', $supplier) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">Nama Supplier</label>
            <input name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
            @error('nama_supplier')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Alamat (opsional)</label>
            <textarea name="alamat" rows="3" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">{{ old('alamat', $supplier->alamat) }}</textarea>
            @error('alamat')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Kontak</label>
            <div id="rows" class="space-y-2">
                @php($i=0)
                @forelse($supplier->contacts as $row)
                <div class="grid grid-cols-1 md:grid-cols-6 gap-2">
                    <div class="md:col-span-2">
                        <select name="kontak[{{ $i }}][tipe]" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">
                            <option value="telp" @selected($row->tipe==='telp')>Telepon</option>
                            <option value="wa" @selected($row->tipe==='wa')>WhatsApp</option>
                            <option value="email" @selected($row->tipe==='email')>Email</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex gap-2">
                        <input name="kontak[{{ $i }}][nilai]" value="{{ $row->nilai }}" placeholder="Nomor / Email" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                        <button type="button" class="px-2 py-1 text-xs rounded border dark:border-gray-700 remove">Hapus</button>
                    </div>
                </div>
                @php($i++)
                @empty
                <div class="grid grid-cols-1 md:grid-cols-6 gap-2">
                    <div class="md:col-span-2">
                        <select name="kontak[0][tipe]" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">
                            <option value="telp">Telepon</option>
                            <option value="wa">WhatsApp</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex gap-2">
                        <input name="kontak[0][nilai]" placeholder="Nomor / Email" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                        <button type="button" class="px-2 py-1 text-xs rounded border dark:border-gray-700 remove">Hapus</button>
                    </div>
                </div>
                @endforelse
            </div>
            <div class="mt-2 flex gap-2">
                <button type="button" id="addRow" class="px-2 py-1 text-xs rounded border dark:border-gray-700">Tambah kontak</button>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('supplier.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
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
    (function(){
        const rows = document.getElementById('rows');
        const add = document.getElementById('addRow');
        
        if (!rows || !add) {
            console.error('Required elements not found');
            return;
        }
        
        function bindRemove(btn, wrapper){ 
            btn.addEventListener('click', () => wrapper.remove()); 
        }
        
        rows.querySelectorAll('.remove').forEach(btn => bindRemove(btn, btn.closest('.grid')));
        
        // Hitung index berdasarkan jumlah kontak yang ada
        let index = rows.querySelectorAll('.grid').length;
        
        add.addEventListener('click', function() {
            const wrapper = document.createElement('div');
            wrapper.className = 'grid grid-cols-1 md:grid-cols-6 gap-2';
            wrapper.innerHTML = `
                <div class="md:col-span-2">
                    <select name="kontak[${index}][tipe]" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">
                        <option value="telp">Telepon</option>
                        <option value="wa">WhatsApp</option>
                        <option value="email">Email</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <input name="kontak[${index}][nilai]" placeholder="Nomor / Email" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                    <button type="button" class="px-2 py-1 text-xs rounded border dark:border-gray-700 remove">Hapus</button>
                </div>`;
            rows.appendChild(wrapper);
            bindRemove(wrapper.querySelector('.remove'), wrapper);
            index++;
        });
    })();
</script>
@endpush
@endsection


