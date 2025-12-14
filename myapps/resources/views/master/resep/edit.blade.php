@extends('layouts.dashboard')

@section('title', 'Edit Resep')
@section('header', 'Edit Resep')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('resep.update', $resep) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">Nama Resep</label>
            <input name="nama_resep" value="{{ old('nama_resep', $resep->nama_resep) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
            @error('nama_resep')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Produk (opsional)</label>
            <select name="produk_id" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">
                <option value="">-</option>
                @foreach($produks as $p)
                    <option value="{{ $p->id }}" @selected(old('produk_id', $resep->produk_id)==$p->id)>{{ $p->nama_produk }}</option>
                @endforeach
            </select>
            @error('produk_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Bahan</label>
            @php
                // Pre-process bahan to extract units
                $bahanWithUnits = $bahan->map(function($b) {
                    $satuanParts = explode(' ', $b->satuan, 2);
                    $b->unit = count($satuanParts) > 1 ? $satuanParts[1] : $b->satuan;
                    return $b;
                });
            @endphp
            <div id="rows" class="space-y-2">
                @php($i=0)
                @forelse($resep->items as $row)
                <div class="grid grid-cols-1 md:grid-cols-6 gap-2">
                    <div class="md:col-span-4">
                        <select name="items[{{ $i }}][bahan_baku_id]" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">
                            <option value="">- Pilih Bahan -</option>
                            @foreach($bahanWithUnits as $b)
                                <option value="{{ $b->id }}" @selected($row->bahan_baku_id==$b->id)>{{ $b->nama_bahan }} ({{ $b->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2 items-start">
                        <div class="flex-1">
                            <input type="number" step="0.01" min="0" name="items[{{ $i }}][qty]" value="{{ $row->qty }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                            <p class="text-xs text-gray-400 mt-0.5">ml/gram/pcs</p>
                        </div>
                        <button type="button" class="px-3 py-2 text-xs rounded bg-gray-600 text-white hover:bg-gray-700 flex items-center justify-center remove h-[42px]" title="Hapus baris">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @php($i++)
                @empty
                <div class="grid grid-cols-1 md:grid-cols-6 gap-2">
                    <div class="md:col-span-4">
                        <select name="items[0][bahan_baku_id]" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700">
                            <option value="">- Pilih Bahan -</option>
                            @foreach($bahanWithUnits as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_bahan }} ({{ $b->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2 items-start">
                        <div class="flex-1">
                            <input type="number" step="0.01" min="0" name="items[0][qty]" placeholder="Qty" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                            <p class="text-xs text-gray-400 mt-0.5">ml/gram/pcs</p>
                        </div>
                        <button type="button" class="px-3 py-2 text-xs rounded bg-gray-600 text-white hover:bg-gray-700 flex items-center justify-center remove h-[42px]" title="Hapus baris">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforelse
            </div>
            <div class="mt-2 flex gap-2">
                <button type="button" id="addRow" class="px-2 py-1 text-xs rounded border dark:border-gray-700">Tambah baris</button>
            </div>
        </div>

        @push('scripts')
        <script>
            (function(){
                const rows = document.getElementById('rows');
                const add = document.getElementById('addRow');
                let index = {{ max(1, $resep->items->count()) }};
                
                // Prepare bahan baku options from server
                const bahanOptions = @json($bahanWithUnits->map(function($b) {
                    return [
                        'id' => $b->id,
                        'nama' => $b->nama_bahan,
                        'satuan' => $b->unit
                    ];
                })->values());
                
                function createBahanSelect(name, selectedId = null) {
                    const select = document.createElement('select');
                    select.name = name;
                    select.className = 'w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700';
                    
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '- Pilih Bahan -';
                    select.appendChild(defaultOption);
                    
                    bahanOptions.forEach(bahan => {
                        const option = document.createElement('option');
                        option.value = bahan.id;
                        option.textContent = `${bahan.nama} (${bahan.satuan})`;
                        if (selectedId && bahan.id == selectedId) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                    
                    return select;
                }
                
                function bindRemove(btn, wrapper){ 
                    btn.addEventListener('click', () => wrapper.remove()); 
                }
                
                rows.querySelectorAll('.remove').forEach(btn => bindRemove(btn, btn.closest('.grid')));
                
                add?.addEventListener('click', () => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'grid grid-cols-1 md:grid-cols-6 gap-2';
                    
                    const selectDiv = document.createElement('div');
                    selectDiv.className = 'md:col-span-4';
                    selectDiv.appendChild(createBahanSelect(`items[${index}][bahan_baku_id]`));
                    
                    const inputDiv = document.createElement('div');
                    inputDiv.className = 'md:col-span-2 flex gap-2 items-start';
                    
                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'number';
                    qtyInput.step = '0.01';
                    qtyInput.min = '0';
                    qtyInput.name = `items[${index}][qty]`;
                    qtyInput.placeholder = 'Qty';
                    qtyInput.className = 'w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700';
                    
                    const qtyWrapper = document.createElement('div');
                    qtyWrapper.className = 'flex-1';
                    
                    const qtyLabel = document.createElement('p');
                    qtyLabel.className = 'text-xs text-gray-400 mt-0.5';
                    qtyLabel.textContent = 'ml/gram/pcs';
                    
                    qtyWrapper.appendChild(qtyInput);
                    qtyWrapper.appendChild(qtyLabel);
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'px-3 py-2 text-xs rounded bg-gray-600 text-white hover:bg-gray-700 flex items-center justify-center remove h-[42px]';
                    removeBtn.title = 'Hapus baris';
                    removeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                    
                    inputDiv.appendChild(qtyWrapper);
                    inputDiv.appendChild(removeBtn);
                    
                    wrapper.appendChild(selectDiv);
                    wrapper.appendChild(inputDiv);
                    rows.appendChild(wrapper);
                    bindRemove(removeBtn, wrapper);
                    index++;
                });
            })();
        </script>
        @endpush

        <div class="flex gap-2">
            <a href="{{ route('resep.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
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
@endsection



