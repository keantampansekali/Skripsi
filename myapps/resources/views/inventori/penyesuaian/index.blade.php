@extends('layouts.dashboard')

@section('title', 'Penyesuaian Stok')
@section('header', 'Penyesuaian Stok - ' . session('nama_cabang', 'Tidak ada cabang'))

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200">
        {{ session('success') }}
    </div>
@endif

<div class="mb-4 flex items-center justify-between flex-wrap gap-4">
    <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Cabang aktif:</span>
        <span class="font-medium">{{ session('nama_cabang', 'Tidak ada cabang') }}</span>
    </div>
    <div class="flex gap-3 items-end flex-wrap">
        <form action="{{ route('penyesuaian.index') }}" method="GET" id="filterForm" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal</label>
                <input 
                    type="date" 
                    name="tanggal" 
                    value="{{ request('tanggal') }}" 
                    class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm w-full"
                    id="tanggalFilter"
                />
            </div>
            @if(request('tanggal'))
                <a href="{{ route('penyesuaian.index') }}" class="px-4 py-2 rounded border dark:border-gray-700 text-sm whitespace-nowrap h-[34px] flex items-center hover:bg-gray-100 dark:hover:bg-gray-700">
                    Reset
                </a>
            @endif
        </form>
        <a href="{{ route('penyesuaian.create') }}" class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700 whitespace-nowrap h-[34px] flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Penyesuaian
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="text-left py-2 px-3">Tanggal</th>
                    <th class="text-left py-2 px-3">Jumlah Item</th>
                    <th class="text-left py-2 px-3">Catatan</th>
                    <th class="text-center py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                    <td class="py-2 px-3">{{ $r->items_count }}</td>
                    <td class="py-2 px-3">{{ $r->catatan ?? '-' }}</td>
                    <td class="py-2 px-3 text-center">
                        <a href="{{ route('penyesuaian.show', $r) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Detail
                        </a>
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
    <div class="p-3">{{ $rows->links() }}</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tanggalFilter = document.getElementById('tanggalFilter');
        const filterForm = document.getElementById('filterForm');
        
        if (tanggalFilter) {
            tanggalFilter.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });
</script>
@endpush
@endsection

