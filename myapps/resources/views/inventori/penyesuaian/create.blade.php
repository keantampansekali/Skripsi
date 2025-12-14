@extends('layouts.dashboard')

@section('title', 'Buat Penyesuaian Stok')
@section('header', 'Buat Penyesuaian Stok')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('penyesuaian.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
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
            <label class="block text-sm font-medium">Item Penyesuaian</label>
            <div id="rows" class="space-y-2">
                <div class="grid grid-cols-1 md:grid-cols-10 gap-2">
                    <div class="md:col-span-4">
                        <select name="items[0][bahan_baku_id]" class="select-bahan w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" data-index="0">
                            <option value="">- Pilih Bahan -</option>
                            @foreach($bahan as $b)
                                <option value="{{ $b->id }}" data-stok="{{ $b->stok }}">{{ $b->nama_bahan }} (Stok: {{ $b->stok }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" step="0.01" min="0" name="items[0][stok_lama]" readonly placeholder="Stok Lama" class="stok-lama w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-700" />
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" step="0.01" min="0" name="items[0][stok_baru]" placeholder="Stok Baru" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <input name="items[0][keterangan]" placeholder="Keterangan" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
                        <button type="button" class="px-2 py-1 text-xs rounded border dark:border-gray-700 remove">Hapus</button>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <button type="button" id="addRow" class="px-2 py-1 text-xs rounded border dark:border-gray-700">Tambah baris</button>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('penyesuaian.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
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
        function bindRemove(btn, wrapper){ btn.addEventListener('click', () => wrapper.remove()); }
        rows.querySelectorAll('.remove').forEach(btn => bindRemove(btn, btn.closest('.grid')));
        
        function updateStokLama(select) {
            const index = select.dataset.index;
            const selectedOption = select.options[select.selectedIndex];
            const stokLama = selectedOption.dataset.stok || '0';
            const row = select.closest('.grid');
            const stokLamaInput = row.querySelector('.stok-lama');
            if (stokLamaInput) {
                stokLamaInput.value = stokLama;
            }
        }
        
        rows.addEventListener('change', (e) => {
            if (e.target.classList.contains('select-bahan')) {
                updateStokLama(e.target);
            }
        });
        
        let index = 1;
        add?.addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'grid grid-cols-1 md:grid-cols-10 gap-2';
            wrapper.innerHTML = `
                <div class=\"md:col-span-4\">
                    <select name=\"items[${index}][bahan_baku_id]\" class=\"select-bahan w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" data-index=\"${index}\">
                        <option value=\"\">- Pilih Bahan -</option>
                        ${`@foreach($bahan as $b)<option value=\"{{$b->id}}\" data-stok=\"{{$b->stok}}\">{{$b->nama_bahan}} (Stok: {{$b->stok}})</option>@endforeach`}
                    </select>
                </div>
                <div class=\"md:col-span-2\">
                    <input type=\"number\" step=\"0.01\" min=\"0\" name=\"items[${index}][stok_lama]\" readonly placeholder=\"Stok Lama\" class=\"stok-lama w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-700\" />
                </div>
                <div class=\"md:col-span-2\">
                    <input type=\"number\" step=\"0.01\" min=\"0\" name=\"items[${index}][stok_baru]\" placeholder=\"Stok Baru\" class=\"w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" required />
                </div>
                <div class=\"md:col-span-2 flex gap-2\">
                    <input name=\"items[${index}][keterangan]\" placeholder=\"Keterangan\" class=\"w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700\" />
                    <button type=\"button\" class=\"px-2 py-1 text-xs rounded border dark:border-gray-700 remove\">Hapus</button>
                </div>`;
            rows.appendChild(wrapper);
            bindRemove(wrapper.querySelector('.remove'), wrapper);
            wrapper.querySelector('.select-bahan').addEventListener('change', function() {
                updateStokLama(this);
            });
            index++;
        });
    })();
</script>
@endpush
@endsection

