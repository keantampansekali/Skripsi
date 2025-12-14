@extends('layouts.dashboard')

@section('title', 'Nota Pembelian')
@section('header', 'Nota Pembelian dari Supplier')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('restock.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Supplier <span class="text-red-500">*</span></label>
                <select name="supplier_id" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required>
                    <option value="">- Pilih Supplier -</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Tanggal Pembelian <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                @error('tanggal')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">No Nota Pembelian</label>
            <input type="text" name="no_nota" value="{{ old('no_nota') }}" placeholder="Masukkan nomor nota dari supplier" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            @error('no_nota')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Daftar Bahan Baku yang Dibeli</label>
            @php
                // Pre-process bahan to extract units
                $bahanWithUnits = $bahan->map(function($b) {
                    $satuanParts = explode(' ', $b->satuan, 2);
                    $b->unit = count($satuanParts) > 1 ? $satuanParts[1] : $b->satuan;
                    return $b;
                });
            @endphp
            <div id="rows" class="space-y-2">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-start border-b dark:border-gray-700 pb-2">
                    <div class="md:col-span-5">
                        <label class="block text-xs text-gray-500 mb-1">Bahan Baku <span class="text-red-500">*</span></label>
                        <select name="items[0][bahan_baku_id]" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required>
                            <option value="">- Pilih Bahan -</option>
                            @foreach($bahanWithUnits as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_bahan }} ({{ $b->unit }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-0.5 opacity-0">Placeholder</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="items[0][qty]" placeholder="Qty" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                        <p class="text-xs text-gray-400 mt-0.5">Dalam satuan terkecil (ml/gram/pcs)</p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs text-gray-500 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="items[0][harga_satuan]" placeholder="Harga" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                        <p class="text-xs text-gray-400 mt-0.5 opacity-0">Placeholder</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1 opacity-0">Placeholder</label>
                        <button type="button" class="px-2 py-1 text-xs rounded border dark:border-gray-700 remove w-full h-[42px]">Hapus</button>
                        <p class="text-xs text-gray-400 mt-0.5 opacity-0">Placeholder</p>
                    </div>
                </div>
            </div>
            <div class="mt-2 flex gap-2">
                <button type="button" id="addRow" class="px-3 py-1 text-xs rounded bg-gray-600 text-white hover:bg-gray-700">+ Tambah Bahan Baku</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t dark:border-gray-700 pt-4">
            <div>
                <label class="block text-sm mb-1">Subtotal</label>
                <input type="text" id="subtotal_display" value="Rp 0" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed font-semibold" />
            </div>
            <div>
                <label class="block text-sm mb-1">Diskon</label>
                <input type="number" step="0.01" min="0" name="diskon" value="{{ old('diskon', 0) }}" id="diskon" placeholder="0" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            </div>
            <div>
                <label class="block text-sm mb-1">PPN</label>
                <input type="number" step="0.01" min="0" name="ppn" value="{{ old('ppn', 0) }}" id="ppn" placeholder="0" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Total Pembayaran</label>
            <input type="text" id="total_display" value="Rp 0" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed font-bold text-lg" />
        </div>

        <div>
            <label class="block text-sm mb-1">Catatan</label>
            <textarea name="catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">{{ old('catatan') }}</textarea>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('restock.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
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
        
        function bindRemove(btn, wrapper){ 
            btn.addEventListener('click', () => {
                wrapper.remove();
                calculateTotal();
            }); 
        }
        
        function calculateTotal() {
            let subtotal = 0;
            const rowItems = rows.querySelectorAll('.grid');
            
            rowItems.forEach(row => {
                const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
                const harga = parseFloat(row.querySelector('input[name*="[harga_satuan]"]')?.value || 0);
                subtotal += qty * harga;
            });
            
            const diskon = parseFloat(document.getElementById('diskon').value || 0);
            const ppn = parseFloat(document.getElementById('ppn').value || 0);
            const subtotalSetelahDiskon = subtotal - diskon;
            const total = subtotalSetelahDiskon + ppn;
            
            document.getElementById('subtotal_display').value = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('total_display').value = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        rows.querySelectorAll('.remove').forEach(btn => bindRemove(btn, btn.closest('.grid')));
        
        // Bind calculate on input changes
        rows.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[qty]"], input[name*="[harga_satuan]"]')) {
                calculateTotal();
            }
        });
        
        document.getElementById('diskon').addEventListener('input', calculateTotal);
        document.getElementById('ppn').addEventListener('input', calculateTotal);
        
        let index = 1;
        
        // Prepare bahan baku options from server
        const bahanOptions = @json($bahanWithUnits->map(function($b) {
            return [
                'id' => $b->id,
                'nama' => $b->nama_bahan,
                'satuan' => $b->unit
            ];
        })->values());
        
        function createBahanSelect(name) {
            const select = document.createElement('select');
            select.name = name;
            select.className = 'w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700';
            select.required = true;
            
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '- Pilih Bahan -';
            select.appendChild(defaultOption);
            
            bahanOptions.forEach(bahan => {
                const option = document.createElement('option');
                option.value = bahan.id;
                option.textContent = `${bahan.nama} (${bahan.satuan})`;
                select.appendChild(option);
            });
            
            return select;
        }
        
        add?.addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 items-start border-b dark:border-gray-700 pb-2';
            
            const bahanDiv = document.createElement('div');
            bahanDiv.className = 'md:col-span-5';
            const bahanLabel = document.createElement('label');
            bahanLabel.className = 'block text-xs text-gray-500 mb-1';
            bahanLabel.innerHTML = 'Bahan Baku <span class="text-red-500">*</span>';
            bahanDiv.appendChild(bahanLabel);
            bahanDiv.appendChild(createBahanSelect(`items[${index}][bahan_baku_id]`));
            const bahanPlaceholder = document.createElement('p');
            bahanPlaceholder.className = 'text-xs text-gray-400 mt-0.5 opacity-0';
            bahanPlaceholder.textContent = 'Placeholder';
            bahanDiv.appendChild(bahanPlaceholder);
            
            const qtyDiv = document.createElement('div');
            qtyDiv.className = 'md:col-span-2';
            const qtyLabel = document.createElement('label');
            qtyLabel.className = 'block text-xs text-gray-500 mb-1';
            qtyLabel.innerHTML = 'Jumlah <span class="text-red-500">*</span>';
            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.step = '0.01';
            qtyInput.min = '0.01';
            qtyInput.name = `items[${index}][qty]`;
            qtyInput.placeholder = 'Qty';
            qtyInput.className = 'w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700';
            qtyInput.required = true;
            const qtyHelper = document.createElement('p');
            qtyHelper.className = 'text-xs text-gray-400 mt-0.5';
            qtyHelper.textContent = 'Dalam satuan terkecil (ml/gram/pcs)';
            qtyDiv.appendChild(qtyLabel);
            qtyDiv.appendChild(qtyInput);
            qtyDiv.appendChild(qtyHelper);
            
            const hargaDiv = document.createElement('div');
            hargaDiv.className = 'md:col-span-3';
            const hargaLabel = document.createElement('label');
            hargaLabel.className = 'block text-xs text-gray-500 mb-1';
            hargaLabel.innerHTML = 'Harga Satuan <span class="text-red-500">*</span>';
            const hargaInput = document.createElement('input');
            hargaInput.type = 'number';
            hargaInput.step = '0.01';
            hargaInput.min = '0';
            hargaInput.name = `items[${index}][harga_satuan]`;
            hargaInput.placeholder = 'Harga';
            hargaInput.className = 'w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700';
            hargaInput.required = true;
            const hargaPlaceholder = document.createElement('p');
            hargaPlaceholder.className = 'text-xs text-gray-400 mt-0.5 opacity-0';
            hargaPlaceholder.textContent = 'Placeholder';
            hargaDiv.appendChild(hargaLabel);
            hargaDiv.appendChild(hargaInput);
            hargaDiv.appendChild(hargaPlaceholder);
            
            const removeDiv = document.createElement('div');
            removeDiv.className = 'md:col-span-2';
            const removeLabel = document.createElement('label');
            removeLabel.className = 'block text-xs text-gray-500 mb-1 opacity-0';
            removeLabel.textContent = 'Placeholder';
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'px-2 py-1 text-xs rounded border dark:border-gray-700 remove w-full h-[42px]';
            removeBtn.textContent = 'Hapus';
            const removePlaceholder = document.createElement('p');
            removePlaceholder.className = 'text-xs text-gray-400 mt-0.5 opacity-0';
            removePlaceholder.textContent = 'Placeholder';
            removeDiv.appendChild(removeLabel);
            removeDiv.appendChild(removeBtn);
            removeDiv.appendChild(removePlaceholder);
            
            wrapper.appendChild(bahanDiv);
            wrapper.appendChild(qtyDiv);
            wrapper.appendChild(hargaDiv);
            wrapper.appendChild(removeDiv);
            
            rows.appendChild(wrapper);
            bindRemove(removeBtn, wrapper);
            
            // Bind calculate on new inputs
            qtyInput.addEventListener('input', calculateTotal);
            hargaInput.addEventListener('input', calculateTotal);
            
            index++;
        });
        
        // Initial calculation
        calculateTotal();
    })();
</script>
@endpush
@endsection


