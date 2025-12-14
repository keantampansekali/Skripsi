@extends('layouts.dashboard')

@section('title', 'Laporan Penjualan')
@section('header', 'Laporan Penjualan - ' . session('nama_cabang', 'Tidak ada cabang'))

@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-4">
    <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Cabang aktif:</span>
        <span class="font-medium">{{ $namaCabang }}</span>
    </div>
    <div class="flex gap-2 items-end flex-wrap">
        <form action="{{ route('laporan-penjualan.index') }}" method="GET" class="flex gap-2 items-end flex-wrap" id="filterForm">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Jenis Laporan</label>
                <select name="filter_type" id="filter_type" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" onchange="updateFilterInput(); this.form.submit();">
                    <option value="harian" {{ $filterType === 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="bulanan" {{ $filterType === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $filterType === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            <div id="filter_input_container">
                @if($filterType === 'harian')
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal</label>
                        <input type="date" name="filter_value" value="{{ $filterValue }}" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" onchange="this.form.submit()" />
                    </div>
                @elseif($filterType === 'bulanan')
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
                        <input type="month" name="filter_value" value="{{ $filterValue }}" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" onchange="this.form.submit()" />
                    </div>
                @else
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
                        <input type="number" name="filter_value" value="{{ $filterValue }}" min="2000" max="2100" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" onchange="this.form.submit()" />
                    </div>
                @endif
            </div>
        </form>
        <a href="{{ route('laporan-penjualan.export', ['filter_type' => $filterType, 'filter_value' => $filterValue]) }}" class="px-3 py-2 rounded bg-green-600 text-white text-sm hover:bg-green-700 whitespace-nowrap h-[34px] inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export CSV
        </a>
        <button onclick="window.print()" class="px-3 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700 whitespace-nowrap h-[34px] inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print
        </button>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden print:shadow-none">
    <div class="p-4 print:hidden">
        <h2 class="text-lg font-semibold">Laporan Penjualan {{ ucfirst($filterType) }}</h2>
        <p class="text-sm text-gray-500">Cabang: {{ $namaCabang }}</p>
        <p class="text-sm text-gray-500">Periode: {{ $periodLabel }}</p>
        <p class="text-sm text-gray-500">Dicetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="hidden print:block p-4">
        <h2 class="text-lg font-semibold">LAPORAN PENJUALAN {{ strtoupper($filterType) }}</h2>
        <p class="text-sm">Cabang: {{ $namaCabang }}</p>
        <p class="text-sm">Periode: {{ $periodLabel }}</p>
        <p class="text-sm">Dicetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="p-4 grid grid-cols-1 md:grid-cols-4 gap-4 border-b dark:border-gray-700">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Transaksi</div>
            <div class="text-xl font-semibold">{{ number_format($summary['total_transaksi'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Subtotal</div>
            <div class="text-xl font-semibold">Rp {{ number_format($summary['total_subtotal'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Diskon</div>
            <div class="text-xl font-semibold">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Penjualan</div>
            <div class="text-xl font-semibold">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Toggle Button -->
    @if(count($data) > 0)
    <div class="p-4 border-b dark:border-gray-700 print:hidden flex justify-end">
        <div class="flex gap-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
            <button id="btnTabel" onclick="showTabel()" class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium transition">
                📊 Tabel
            </button>
            <button id="btnGrafik" onclick="showGrafik()" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium transition hover:bg-gray-300 dark:hover:bg-gray-500">
                📈 Grafik
            </button>
        </div>
    </div>
    @endif

    <!-- Chart Section -->
    @if(count($data) > 0)
    <div id="chartSection" class="p-4 border-b dark:border-gray-700 print:hidden hidden">
        <h3 class="text-lg font-semibold mb-4">Grafik Penjualan</h3>
        <div class="space-y-4">
            <!-- Line Chart - Total Penjualan -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg">
                <h4 class="text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">Total Penjualan per Periode</h4>
                <div class="relative" style="height: 300px;">
                    <canvas id="chartTotal"></canvas>
                </div>
            </div>
            <!-- Bar Chart - Perbandingan -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg">
                <h4 class="text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">Perbandingan Subtotal, Diskon & Total</h4>
                <div class="relative" style="height: 300px;">
                    <canvas id="chartComparison"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Table Section -->
    <div id="tableSection" class="p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm print:text-xs">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700 print:bg-gray-100">
                        @if($filterType === 'harian')
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Waktu</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">No Transaksi</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Subtotal</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Diskon</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Tax</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Total</th>
                        @elseif($filterType === 'bulanan')
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Jumlah Transaksi</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Subtotal</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Diskon</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Tax</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Total</th>
                        @else
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Bulan</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Jumlah Transaksi</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Subtotal</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Diskon</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Tax</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        @if($filterType === 'harian')
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $row['waktu'] }}</td>
                            <td class="py-3 px-4 font-medium text-gray-800 dark:text-white">{{ $row['no_transaksi'] }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['diskon'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['tax'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-800 dark:text-white">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                        @elseif($filterType === 'bulanan')
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $row['tanggal'] }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">{{ number_format($row['jumlah_transaksi'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['diskon'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['tax'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-800 dark:text-white">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                        @else
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $row['bulan'] }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">{{ number_format($row['jumlah_transaksi'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['diskon'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($row['tax'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-800 dark:text-white">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 print:bg-gray-100 font-semibold">
                        <td class="py-3 px-4 text-right" colspan="{{ $filterType === 'harian' ? '2' : '1' }}">TOTAL:</td>
                        <td class="py-3 px-4 text-right">{{ $filterType !== 'harian' ? number_format($summary['total_transaksi'], 0, ',', '.') : '' }}</td>
                        <td class="py-3 px-4 text-right">Rp {{ number_format($summary['total_subtotal'], 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right">Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print\:shadow-none, .print\:shadow-none * {
            visibility: visible;
        }
        .print\:shadow-none {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:hidden {
            display: none;
        }
        .print\:block {
            display: block;
        }
        .print\:bg-gray-100 {
            background-color: #f3f4f6;
        }
        .print\:text-xs {
            font-size: 0.75rem;
        }
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Toggle Tabel/Grafik
function showTabel() {
    document.getElementById('tableSection').classList.remove('hidden');
    document.getElementById('chartSection').classList.add('hidden');
    document.getElementById('btnTabel').classList.add('bg-blue-600', 'text-white');
    document.getElementById('btnTabel').classList.remove('bg-gray-200', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
    document.getElementById('btnGrafik').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('btnGrafik').classList.add('bg-gray-200', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
}

function showGrafik() {
    document.getElementById('tableSection').classList.add('hidden');
    document.getElementById('chartSection').classList.remove('hidden');
    document.getElementById('btnGrafik').classList.add('bg-blue-600', 'text-white');
    document.getElementById('btnGrafik').classList.remove('bg-gray-200', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
    document.getElementById('btnTabel').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('btnTabel').classList.add('bg-gray-200', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
    
    // Initialize charts when showing grafik
    if (typeof window.chartTotalInstance === 'undefined' || typeof window.chartComparisonInstance === 'undefined') {
        initializeCharts();
    }
}

function updateFilterInput() {
    const filterType = document.getElementById('filter_type').value;
    const container = document.getElementById('filter_input_container');
    
    let html = '';
    if (filterType === 'harian') {
        html = `
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal</label>
                <input type="date" name="filter_value" value="{{ date('Y-m-d') }}" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" onchange="this.form.submit()" />
            </div>
        `;
    } else if (filterType === 'bulanan') {
        html = `
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
                <input type="month" name="filter_value" value="{{ date('Y-m') }}" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" onchange="this.form.submit()" />
            </div>
        `;
    } else {
        html = `
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
                <input type="number" name="filter_value" value="{{ date('Y') }}" min="2000" max="2100" class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm" onchange="this.form.submit()" />
            </div>
        `;
    }
    
    container.innerHTML = html;
}

// Chart initialization function
function initializeCharts() {

    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#e5e7eb' : '#374151';
    const gridColor = isDarkMode ? '#374151' : '#e5e7eb';
    
    // Chart data
    const chartLabels = @json($chartLabels);
    const chartTotals = @json($chartTotals);
    const chartSubtotals = @json($chartSubtotals);
    const chartDiskons = @json($chartDiskons);
    
    // Destroy existing charts if they exist
    if (window.chartTotalInstance) {
        window.chartTotalInstance.destroy();
    }
    if (window.chartComparisonInstance) {
        window.chartComparisonInstance.destroy();
    }
    
    // Line Chart - Total Penjualan
    const ctxTotal = document.getElementById('chartTotal');
    if (ctxTotal) {
        window.chartTotalInstance = new Chart(ctxTotal, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Total Penjualan',
                    data: chartTotals,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                animation: {
                    duration: 0 // Disable animation
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: textColor,
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Total: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            maxTicksLimit: 8,
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'J';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                }
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: textColor,
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 15
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Bar Chart - Comparison
    const ctxComparison = document.getElementById('chartComparison');
    if (ctxComparison) {
        window.chartComparisonInstance = new Chart(ctxComparison, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Subtotal',
                        data: chartSubtotals,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    },
                    {
                        label: 'Diskon',
                        data: chartDiskons,
                        backgroundColor: 'rgba(234, 179, 8, 0.7)',
                        borderColor: 'rgb(234, 179, 8)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total',
                        data: chartTotals,
                        backgroundColor: 'rgba(147, 51, 234, 0.7)',
                        borderColor: 'rgb(147, 51, 234)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                animation: {
                    duration: 0 // Disable animation
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: textColor,
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            maxTicksLimit: 8,
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'J';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                }
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: textColor,
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 15
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}

// Initialize on page load
@if(count($data) > 0)
document.addEventListener('DOMContentLoaded', function() {
    // Default show tabel
    showTabel();
});
@endif
</script>
@endpush
@endsection

