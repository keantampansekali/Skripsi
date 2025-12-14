@extends('layouts.dashboard')

@section('title', 'Data Resep')
@section('header', 'Data Resep - ' . session('nama_cabang', 'Tidak ada cabang'))

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200">
        {{ session('success') }}
    </div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Cabang aktif:</span>
        <span class="font-medium">{{ session('nama_cabang', 'Tidak ada cabang') }}</span>
    </div>
    <div class="flex items-center gap-2">
        <form action="{{ route('resep.index') }}" method="GET" class="flex items-center gap-2" id="searchForm">
            <input 
                type="text" 
                name="search" 
                id="searchInput"
                value="{{ request('search') }}" 
                placeholder="Cari nama resep..." 
                class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm"
                autocomplete="off"
            />
        </form>
        <a href="{{ route('resep.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Tambah Resep</a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="text-left py-2 px-3">Nama Resep</th>
                    <th class="text-left py-2 px-3">Produk</th>
                    <th class="text-left py-2 px-3">Jumlah Bahan</th>
                    <th class="text-center py-2 px-3 w-56">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reseps as $r)
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2 px-3">{{ $r->nama_resep }}</td>
                    <td class="py-2 px-3">{{ optional($r->produk)->nama_produk ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $r->items_count }}</td>
                    <td class="py-2 px-3 text-center">
                        <div class="inline-flex gap-2 justify-center">
                            <a href="{{ route('resep.edit', $r) }}" class="px-2 py-1 text-xs rounded bg-amber-500 text-white hover:bg-amber-600 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit
                            </a>
                            <form action="{{ route('resep.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus resep ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-4 px-3 text-center text-gray-500 dark:text-gray-400">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $reseps->links() }}</div>
</div>

@push('scripts')
<script>
(function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;
    
    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            // Jika input kosong, langsung reset tanpa delay
            if (this.value.trim() === '') {
                window.location.href = "{{ route('resep.index') }}";
                return;
            }
            
            // Delay submit untuk menghindari terlalu banyak request
            searchTimeout = setTimeout(function() {
                searchForm.submit();
            }, 500); // 500ms delay
        });
    }
})();
</script>
@endpush
@endsection


