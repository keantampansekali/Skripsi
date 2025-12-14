@extends('layouts.kasir')

@section('title', 'Transaksi Kasir')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-red-100 dark:from-gray-900 dark:to-gray-800 p-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">📋 Transaksi Kasir</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ session('nama_cabang', 'Cabang') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <a href="{{ route('kasir.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Kasir
                    </a>
                    <a href="/dashboard" class="hidden md:flex px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                <form action="{{ route('kasir.transaksi') }}" method="GET" class="flex items-end gap-3 flex-wrap" id="filterForm">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Jenis Laporan</label>
                        <select 
                            name="filter_type" 
                            id="filter_type"
                            class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                            onchange="updateFilterInput(); this.form.submit();"
                        >
                            <option value="harian" {{ $filterType === 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="bulanan" {{ $filterType === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]" id="filter_input_container">
                        @if($filterType === 'harian')
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Tanggal</label>
                            <input 
                                type="date" 
                                name="tanggal" 
                                value="{{ $tanggal }}" 
                                class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                onchange="this.form.submit()"
                            />
                        @else
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Bulan</label>
                            <input 
                                type="month" 
                                name="bulan" 
                                value="{{ $bulan }}" 
                                class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                onchange="this.form.submit()"
                            />
                        @endif
                    </div>
                    @if($isOwner)
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Nama Kasir</label>
                        <select 
                            name="pegawai_id" 
                            id="pegawai_id"
                            class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                            onchange="this.form.submit()"
                        >
                            <option value="">Semua Kasir</option>
                            @foreach($pegawaiList as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ $filterPegawaiId == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $filterType === 'harian' ? 'Tanggal' : 'Periode' }}</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $periodLabel }}</div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Jumlah Transaksi</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $transaksis->count() }}</div>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-lg p-4 border border-red-200 dark:border-red-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Pendapatan</div>
                    <div class="text-xl font-bold text-red-700 dark:text-red-400">Rp {{ number_format($totalHari, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Transaksi List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                @if($filterType === 'harian')
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">No Transaksi</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Waktu</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Kasir</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Items</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Total</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $trx)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="py-3 px-4">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $trx->no_transaksi }}</span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($trx->created_at)->format('H:i:s') }}
                            </td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                {{ $trx->pegawai ? $trx->pegawai->nama : '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="space-y-1">
                                    @foreach($trx->items as $item)
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $item->nama_produk }} ({{ $item->quantity }}x)
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-800 dark:text-white">
                                Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button 
                                    onclick="printSingle('{{ $trx->no_transaksi }}')"
                                    class="px-3 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700"
                                >
                                    Print
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada transaksi pada tanggal ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @else
                <!-- Tampilan Bulanan -->
                @forelse($groupedData as $dateKey => $group)
                <div class="mb-6 border-b dark:border-gray-700 pb-4 last:border-b-0">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $group['tanggal'] }}</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">{{ $group['jumlah'] }}</span> transaksi | 
                            <span class="font-semibold text-red-600 dark:text-red-400">Rp {{ number_format($group['total'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="text-left py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">No Transaksi</th>
                                <th class="text-left py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">Waktu</th>
                                <th class="text-left py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">Kasir</th>
                                <th class="text-left py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">Items</th>
                                <th class="text-right py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">Total</th>
                                <th class="text-center py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['transaksis'] as $trx)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-2 px-4">
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $trx->no_transaksi }}</span>
                                </td>
                                <td class="py-2 px-4 text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('H:i:s') }}
                                </td>
                                <td class="py-2 px-4 text-gray-600 dark:text-gray-400">
                                    {{ $trx->pegawai ? $trx->pegawai->nama : '-' }}
                                </td>
                                <td class="py-2 px-4">
                                    <div class="space-y-1">
                                        @foreach($trx->items as $item)
                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $item->nama_produk }} ({{ $item->quantity }}x)
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-2 px-4 text-right font-semibold text-gray-800 dark:text-white">
                                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <button 
                                        onclick="printSingle('{{ $trx->no_transaksi }}')"
                                        class="px-3 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700"
                                    >
                                        Print
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @empty
                <div class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                    Tidak ada transaksi pada bulan ini
                </div>
                @endforelse
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none;
    }
    body {
        background: white;
    }
}
</style>

<script>
function updateFilterInput() {
    const filterType = document.getElementById('filter_type').value;
    const container = document.getElementById('filter_input_container');
    
    let html = '';
    if (filterType === 'harian') {
        html = `
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Tanggal</label>
            <input 
                type="date" 
                name="tanggal" 
                value="{{ $tanggal }}" 
                class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                onchange="this.form.submit()"
            />
        `;
    } else {
        html = `
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Bulan</label>
            <input 
                type="month" 
                name="bulan" 
                value="{{ $bulan }}" 
                class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                onchange="this.form.submit()"
            />
        `;
    }
    
    container.innerHTML = html;
}

function printSingle(noTransaksi) {
    // Redirect to print page with filter
    const filterType = '{{ $filterType }}';
    const tanggal = '{{ $tanggal }}';
    const bulan = '{{ $bulan }}';
    const pegawaiId = '{{ $filterPegawaiId }}';
    
    let url = '{{ route("kasir.transaksi.print") }}?';
    if (filterType === 'harian') {
        url += 'tanggal=' + tanggal;
    } else {
        url += 'bulan=' + bulan;
    }
    if (pegawaiId) {
        url += '&pegawai_id=' + pegawaiId;
    }
    url += '&no=' + noTransaksi;
    
    window.open(url, '_blank');
}
</script>
@endsection

