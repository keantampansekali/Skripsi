@extends('layouts.dashboard')

@section('title', 'Buat Waste Management')
@section('header', 'Buat Waste Management')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('waste-management.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                @error('tanggal')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Catatan</label>
                <input name="catatan" value="{{ old('catatan') }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Item Waste</label>
            @php
                // Pre-process bahan to extract units
                $bahanWithUnits = $bahan->map(function($b) {
                    $satuanParts = explode(' ', $b->satuan, 2);
                    $b->unit = count($satuanParts) > 1 ? $satuanParts[1] : $b->satuan;
                    return $b;
                });
            @endphp
            <div id="rows" class="space-y-2">
                <div class="grid grid-cols-1 md:grid-cols-10 gap-2 item-row">
                    <div class="md:col-span-2">
                        <select name="items[0][tipe]" class="tipe-select w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" data-index="0">
                            <option value="bahan_baku">Bahan</option>
                            <option value="produk">Produk</option>
                        </select>
                    </div>
                    <div class="md:col-span-3 item-select-container">
                        <select name="items[0][bahan_baku_id]" class="item-select w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" data-index="0">
                            <option value="">- Pilih Bahan -</option>
                            @foreach($bahanWithUnits as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_bahan }} ({{ $b->unit }})</option>
                            @endforeach
                        </select>
                        <select name="items[0][produk_id]" class="item-select hidden w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" data-index="0">
                            <option value="">- Pilih Produk -</option>
                            @foreach($produks as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="number" step="0.01" min="0" name="items[0][qty]" placeholder="Qty" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                    </div>
                    <div class="md:col-span-3">
                        <input name="items[0][alasan]" placeholder="Alasan Waste" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                    </div>
                    <div class="flex items-center">
                        <button type="button" class="px-2 py-1 text-xs rounded border dark:border-gray-700 remove">Hapus</button>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <button type="button" id="addRow" class="px-2 py-1 text-xs rounded border dark:border-gray-700">Tambah baris</button>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('waste-management.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
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
    (function(){
        const rows = document.getElementById('rows');
        const add = document.getElementById('addRow');
        const bahanOptions = `@foreach($bahanWithUnits as $b)<option value="{{$b->id}}">{{$b->nama_bahan}} ({{$b->unit}})</option>@endforeach`;
        const produkOptions = `@foreach($produks as $p)<option value="{{$p->id}}">{{$p->nama_produk}}</option>@endforeach`;
        
        function bindRemove(btn, wrapper){ btn.addEventListener('click', () => wrapper.remove()); }
        rows.querySelectorAll('.remove').forEach(btn => bindRemove(btn, btn.closest('.grid')));
        
        function toggleItemSelect(select) {
            const row = select.closest('.item-row');
            const index = select.dataset.index;
            const tipe = select.value;
            const containers = row.querySelectorAll('.item-select');
            
            containers.forEach(c => {
                if (c.name.includes(tipe === 'bahan_baku' ? 'bahan_baku_id' : 'produk_id')) {
                    c.classList.remove('hidden');
                    c.removeAttribute('disabled');
                    c.required = true;
                } else {
                    c.classList.add('hidden');
                    c.setAttribute('disabled', 'disabled');
                    c.required = false;
                    c.value = '';
                }
            });
        }
        
        // Disable hidden selects on form submit
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.item-select.hidden').forEach(sel => {
                sel.setAttribute('disabled', 'disabled');
            });
        });
        
        rows.addEventListener('change', (e) => {
            if (e.target.classList.contains('tipe-select')) {
                toggleItemSelect(e.target);
            }
        });
        
        // Initialize first row
        const firstRow = rows.querySelector('.item-row');
        if (firstRow) {
            const firstTipeSelect = firstRow.querySelector('.tipe-select');
            if (firstTipeSelect) {
                toggleItemSelect(firstTipeSelect);
            }
        }
        
        let index = 1;
        add?.addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'grid grid-cols-1 md:grid-cols-10 gap-2 item-row';
            wrapper.innerHTML = `
                <div class=\"md:col-span-2\">
                    <select name=\"items[${index}][tipe]\" class=\"tipe-select w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" data-index=\"${index}\">
                        <option value=\"bahan_baku\">Bahan</option>
                        <option value=\"produk\">Produk</option>
                    </select>
                </div>
                <div class=\"md:col-span-3 item-select-container\">
                    <select name=\"items[${index}][bahan_baku_id]\" class=\"item-select w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" data-index=\"${index}\">
                        <option value=\"\">- Pilih Bahan -</option>
                        ${bahanOptions}
                    </select>
                    <select name=\"items[${index}][produk_id]\" class=\"item-select hidden w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" data-index=\"${index}\">
                        <option value=\"\">- Pilih Produk -</option>
                        ${produkOptions}
                    </select>
                </div>
                <div>
                    <input type=\"number\" step=\"0.01\" min=\"0\" name=\"items[${index}][qty]\" placeholder=\"Qty\" class=\"w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" />
                </div>
                <div class=\"md:col-span-3\">
                    <input name=\"items[${index}][alasan]\" placeholder=\"Alasan Waste\" class=\"w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" />
                </div>
                <div class=\"flex items-center\">
                    <button type=\"button\" class=\"px-2 py-1 text-xs rounded border dark:border-gray-700 remove\">Hapus</button>
                </div>`;
            rows.appendChild(wrapper);
            bindRemove(wrapper.querySelector('.remove'), wrapper);
            wrapper.querySelector('.tipe-select').addEventListener('change', function() {
                toggleItemSelect(this);
            });
            index++;
        });
    })();
</script>
@endpush
@endsection

