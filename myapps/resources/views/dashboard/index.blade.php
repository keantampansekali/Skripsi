@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('header', 'Dashboard - ' . session('nama_cabang', 'Tidak ada cabang'))

@section('content')
<div id="dashboard-container">
    <!-- Loading State -->
    <div id="loading-state" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Memuat data...</p>
    </div>

    <!-- Content (hidden initially) -->
    <div id="dashboard-content" class="hidden">
        <!-- Active Branch Indicator -->
        <div id="branch-indicator" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>Cabang Aktif:</strong> <span id="branch-name">-</span>
            </p>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Penjualan Hari Ini -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Penjualan Hari Ini</div>
                        <div class="mt-2 text-2xl font-semibold" id="penjualan-hari-ini">Rp 0</div>
                        <div class="text-xs text-gray-400 mt-1" id="transaksi-hari-ini">0 transaksi</div>
                    </div>
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            
            <!-- Penjualan Bulan Ini -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Penjualan Bulan Ini</div>
                        <div class="mt-2 text-2xl font-semibold" id="penjualan-bulan-ini">Rp 0</div>
                        <div class="text-xs text-gray-400 mt-1" id="transaksi-bulan-ini">0 transaksi</div>
                    </div>
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            
            <!-- Total Produk -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Produk</div>
                        <div class="mt-2 text-2xl font-semibold" id="total-produk">0</div>
                        <div class="text-xs text-gray-400 mt-1" id="total-stok">0 unit tersedia</div>
                    </div>
                    <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            
            <!-- Bahan Baku -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Bahan Baku</div>
                        <div class="mt-2 text-2xl font-semibold" id="total-bahan-baku">0</div>
                        <div class="text-xs text-gray-400 mt-1" id="total-stok-bahan">0 unit tersedia</div>
                    </div>
                    <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Alert Stok Rendah Produk -->
        <div id="alert-stok-rendah" class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 hidden">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <strong>Peringatan:</strong> Ada <span id="jumlah-stok-rendah">0</span> produk dengan stok rendah (kurang dari 10 unit)
                </p>
            </div>
        </div>

        <!-- Alert Stok Habis/Rendah Bahan Baku -->
        <div id="alert-bahan-baku-stok-habis" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 hidden">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm text-red-800 dark:text-red-200">
                    <strong>Peringatan Kritis:</strong> Ada <span id="jumlah-bahan-baku-stok-habis">0</span> bahan baku dengan stok habis (stok = 0)
                </p>
            </div>
        </div>

        <div id="alert-bahan-baku-stok-rendah" class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 hidden">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <strong>Peringatan:</strong> Ada <span id="jumlah-bahan-baku-stok-rendah">0</span> bahan baku dengan stok rendah (kurang dari 10 unit)
                </p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-4">Daftar Produk - <span id="table-branch-name">-</span></h2>
            <div id="produk-table-container">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 px-3">Nama Produk</th>
                                <th class="text-left py-2 px-3">Harga</th>
                                <th class="text-left py-2 px-3">Stok</th>
                            </tr>
                        </thead>
                        <tbody id="produk-table-body">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="produk-empty-state" class="hidden">
                <p class="text-gray-500 dark:text-gray-400">Tidak ada produk untuk cabang ini.</p>
            </div>
            <!-- Pagination Produk -->
            <div id="produk-pagination" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span id="produk-from">0</span> - <span id="produk-to">0</span> dari <span id="produk-total">0</span> produk
                </div>
                <div class="flex items-center gap-1">
                    <button id="produk-prev" onclick="changeProdukPage(-1)" class="px-3 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>
                        &lt;
                    </button>
                    <div id="produk-page-numbers" class="flex gap-1">
                        <!-- Page numbers will be inserted here -->
                    </div>
                    <button id="produk-next" onclick="changeProdukPage(1)" class="px-3 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>
                        &gt;
                    </button>
                </div>
            </div>
        </div>

        <!-- Bahan Baku List Table -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-4">Daftar Bahan Baku - <span id="table-branch-name-bahan">-</span></h2>
            <div id="bahan-baku-table-container">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 px-3">Nama Bahan Baku</th>
                                <th class="text-left py-2 px-3">Satuan</th>
                                <th class="text-left py-2 px-3">Harga Satuan</th>
                                <th class="text-left py-2 px-3">Stok</th>
                            </tr>
                        </thead>
                        <tbody id="bahan-baku-table-body">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="bahan-baku-empty-state" class="hidden">
                <p class="text-gray-500 dark:text-gray-400">Tidak ada bahan baku untuk cabang ini.</p>
            </div>
            <!-- Pagination Bahan Baku -->
            <div id="bahan-baku-pagination" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span id="bahan-baku-from">0</span> - <span id="bahan-baku-to">0</span> dari <span id="bahan-baku-total">0</span> bahan baku
                </div>
                <div class="flex items-center gap-1">
                    <button id="bahan-baku-prev" onclick="changeBahanBakuPage(-1)" class="px-3 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>
                        &lt;
                    </button>
                    <div id="bahan-baku-page-numbers" class="flex gap-1">
                        <!-- Page numbers will be inserted here -->
                    </div>
                    <button id="bahan-baku-next" onclick="changeBahanBakuPage(1)" class="px-3 py-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>
                        &gt;
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let refreshInterval = null;
    const REFRESH_INTERVAL_MS = 5000; // Refresh setiap 5 detik
    
    // Pagination state
    let currentProdukPage = 1;
    let currentBahanBakuPage = 1;

    // Format currency
    function formatCurrency(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    // Format number
    function formatNumber(value) {
        return new Intl.NumberFormat('id-ID').format(value);
    }
    
    // Extract unit from satuan (remove number prefix)
    function extractUnit(satuan) {
        if (!satuan) return 'unit';
        const parts = satuan.split(' ');
        // If there's a space and second part exists, return it; otherwise return the whole string
        return parts.length > 1 ? parts.slice(1).join(' ') : satuan;
    }

    // Fetch dashboard data
    async function fetchDashboardData() {
        try {
            const url = new URL('{{ route("dashboard.stats") }}', window.location.origin);
            url.searchParams.set('produk_page', currentProdukPage);
            url.searchParams.set('bahan_baku_page', currentBahanBakuPage);
            
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Failed to fetch dashboard data');
            }
            const data = await response.json();
            updateDashboard(data);
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
            // Show error state
            document.getElementById('loading-state').innerHTML = `
                <div class="text-red-500">
                    <p>Gagal memuat data. Mencoba lagi...</p>
                </div>
            `;
        }
    }

    // Store last page info for validation
    let produkLastPage = 1;
    let bahanBakuLastPage = 1;

    // Change produk page (exposed to global scope for onclick)
    window.changeProdukPage = function(delta) {
        const newPage = currentProdukPage + delta;
        if (newPage >= 1 && newPage <= produkLastPage) {
            currentProdukPage = newPage;
            fetchDashboardData();
        }
    };

    // Go to specific produk page
    window.goToProdukPage = function(page) {
        if (page >= 1 && page <= produkLastPage) {
            currentProdukPage = page;
            fetchDashboardData();
        }
    };

    // Change bahan baku page (exposed to global scope for onclick)
    window.changeBahanBakuPage = function(delta) {
        const newPage = currentBahanBakuPage + delta;
        if (newPage >= 1 && newPage <= bahanBakuLastPage) {
            currentBahanBakuPage = newPage;
            fetchDashboardData();
        }
    };

    // Go to specific bahan baku page
    window.goToBahanBakuPage = function(page) {
        if (page >= 1 && page <= bahanBakuLastPage) {
            currentBahanBakuPage = page;
            fetchDashboardData();
        }
    };

    // Generate page numbers array
    function generatePageNumbers(currentPage, lastPage) {
        const pages = [];
        const maxVisible = 5; // Maximum number of page buttons to show
        
        if (lastPage <= maxVisible) {
            // Show all pages if total pages <= maxVisible
            for (let i = 1; i <= lastPage; i++) {
                pages.push(i);
            }
        } else {
            // Show pages around current page
            let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let end = Math.min(lastPage, start + maxVisible - 1);
            
            // Adjust start if we're near the end
            if (end - start < maxVisible - 1) {
                start = Math.max(1, end - maxVisible + 1);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
        }
        
        return pages;
    }

    // Update dashboard with new data
    function updateDashboard(data) {
        // Hide loading, show content
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('dashboard-content').classList.remove('hidden');

        // Update branch name
        document.getElementById('branch-name').textContent = data.nama_cabang || 'Tidak ada cabang';
        document.getElementById('table-branch-name').textContent = data.nama_cabang || 'Tidak ada cabang';
        document.getElementById('table-branch-name-bahan').textContent = data.nama_cabang || 'Tidak ada cabang';

        // Update KPI cards
        document.getElementById('penjualan-hari-ini').textContent = formatCurrency(data.penjualan_hari_ini || 0);
        document.getElementById('transaksi-hari-ini').textContent = formatNumber(data.jumlah_transaksi_hari_ini || 0) + ' transaksi';
        
        document.getElementById('penjualan-bulan-ini').textContent = formatCurrency(data.penjualan_bulan_ini || 0);
        document.getElementById('transaksi-bulan-ini').textContent = formatNumber(data.jumlah_transaksi_bulan_ini || 0) + ' transaksi';
        
        document.getElementById('total-produk').textContent = formatNumber(data.total_produk || 0);
        document.getElementById('total-stok').textContent = formatNumber(data.total_stok || 0) + ' unit tersedia';
        
        document.getElementById('total-bahan-baku').textContent = formatNumber(data.total_bahan_baku || 0);
        document.getElementById('total-stok-bahan').textContent = formatNumber(data.total_stok_bahan || 0) + ' unit tersedia';

        // Update stok rendah alert produk
        const alertElement = document.getElementById('alert-stok-rendah');
        if (data.jumlah_produk_stok_rendah > 0) {
            document.getElementById('jumlah-stok-rendah').textContent = formatNumber(data.jumlah_produk_stok_rendah);
            alertElement.classList.remove('hidden');
        } else {
            alertElement.classList.add('hidden');
        }

        // Update alert bahan baku stok habis
        const alertBahanBakuHabis = document.getElementById('alert-bahan-baku-stok-habis');
        if (data.jumlah_bahan_baku_stok_habis > 0) {
            document.getElementById('jumlah-bahan-baku-stok-habis').textContent = formatNumber(data.jumlah_bahan_baku_stok_habis);
            alertBahanBakuHabis.classList.remove('hidden');
        } else {
            alertBahanBakuHabis.classList.add('hidden');
        }

        // Update alert bahan baku stok rendah
        const alertBahanBakuRendah = document.getElementById('alert-bahan-baku-stok-rendah');
        if (data.jumlah_bahan_baku_stok_rendah > 0) {
            document.getElementById('jumlah-bahan-baku-stok-rendah').textContent = formatNumber(data.jumlah_bahan_baku_stok_rendah);
            alertBahanBakuRendah.classList.remove('hidden');
        } else {
            alertBahanBakuRendah.classList.add('hidden');
        }

        // Update produk table
        const tableBody = document.getElementById('produk-table-body');
        const emptyState = document.getElementById('produk-empty-state');
        const tableContainer = document.getElementById('produk-table-container');
        const produkPagination = document.getElementById('produk-pagination');

        if (data.produk_list && data.produk_list.length > 0) {
            // Highlight produk dengan stok rendah
            tableBody.innerHTML = data.produk_list.map(produk => {
                const isLowStock = produk.stok < 10;
                const rowClass = isLowStock ? 'border-b dark:border-gray-700 bg-red-50 dark:bg-red-900/20' : 'border-b dark:border-gray-700';
                const stockClass = isLowStock ? 'text-red-600 dark:text-red-400 font-semibold' : '';
                return `
                <tr class="${rowClass}">
                    <td class="py-2 px-3">${produk.nama_produk}</td>
                    <td class="py-2 px-3">${formatCurrency(produk.harga)}</td>
                    <td class="py-2 px-3 ${stockClass}">${formatNumber(produk.stok)} unit</td>
                </tr>
            `;
            }).join('');
            tableContainer.classList.remove('hidden');
            emptyState.classList.add('hidden');
            
            // Update pagination info
            if (data.produk_pagination) {
                const pagination = data.produk_pagination;
                produkLastPage = pagination.last_page || 1;
                currentProdukPage = pagination.current_page || 1;
                
                document.getElementById('produk-from').textContent = formatNumber(pagination.from || 0);
                document.getElementById('produk-to').textContent = formatNumber(pagination.to || 0);
                document.getElementById('produk-total').textContent = formatNumber(pagination.total || 0);
                
                // Enable/disable buttons
                document.getElementById('produk-prev').disabled = pagination.current_page <= 1;
                document.getElementById('produk-next').disabled = pagination.current_page >= pagination.last_page;
                
                // Generate and render page numbers
                const pageNumbers = generatePageNumbers(pagination.current_page, pagination.last_page);
                const pageNumbersContainer = document.getElementById('produk-page-numbers');
                pageNumbersContainer.innerHTML = pageNumbers.map(page => {
                    const isActive = page === pagination.current_page;
                    const activeClass = isActive 
                        ? 'bg-blue-600 text-white dark:bg-blue-500' 
                        : 'dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600';
                    return `
                        <button 
                            onclick="goToProdukPage(${page})" 
                            class="px-3 py-1 text-sm border rounded transition ${activeClass} ${isActive ? 'cursor-default' : 'cursor-pointer'}"
                            ${isActive ? 'disabled' : ''}
                        >
                            ${page}
                        </button>
                    `;
                }).join('');
                
                produkPagination.classList.remove('hidden');
            }
        } else {
            tableContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            produkPagination.classList.add('hidden');
        }

        // Update bahan baku table
        const bahanBakuTableBody = document.getElementById('bahan-baku-table-body');
        const bahanBakuEmptyState = document.getElementById('bahan-baku-empty-state');
        const bahanBakuTableContainer = document.getElementById('bahan-baku-table-container');
        const bahanBakuPagination = document.getElementById('bahan-baku-pagination');

        if (data.bahan_baku_list && data.bahan_baku_list.length > 0) {
            // Highlight bahan baku dengan stok habis atau rendah
            bahanBakuTableBody.innerHTML = data.bahan_baku_list.map(bahan => {
                const isOutOfStock = bahan.stok <= 0;
                const isLowStock = bahan.stok > 0 && bahan.stok < 10;
                let rowClass = 'border-b dark:border-gray-700';
                let stockClass = '';
                
                if (isOutOfStock) {
                    rowClass = 'border-b dark:border-gray-700 bg-red-100 dark:bg-red-900/30';
                    stockClass = 'text-red-700 dark:text-red-300 font-bold';
                } else if (isLowStock) {
                    rowClass = 'border-b dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/20';
                    stockClass = 'text-yellow-600 dark:text-yellow-400 font-semibold';
                }
                
                return `
                <tr class="${rowClass}">
                    <td class="py-2 px-3">${bahan.nama_bahan}</td>
                    <td class="py-2 px-3">${extractUnit(bahan.satuan)}</td>
                    <td class="py-2 px-3">${formatCurrency(bahan.harga_satuan || 0)}</td>
                    <td class="py-2 px-3 ${stockClass}">${formatNumber(bahan.stok)}</td>
                </tr>
            `;
            }).join('');
            bahanBakuTableContainer.classList.remove('hidden');
            bahanBakuEmptyState.classList.add('hidden');
            
            // Update pagination info
            if (data.bahan_baku_pagination) {
                const pagination = data.bahan_baku_pagination;
                bahanBakuLastPage = pagination.last_page || 1;
                currentBahanBakuPage = pagination.current_page || 1;
                
                document.getElementById('bahan-baku-from').textContent = formatNumber(pagination.from || 0);
                document.getElementById('bahan-baku-to').textContent = formatNumber(pagination.to || 0);
                document.getElementById('bahan-baku-total').textContent = formatNumber(pagination.total || 0);
                
                // Enable/disable buttons
                document.getElementById('bahan-baku-prev').disabled = pagination.current_page <= 1;
                document.getElementById('bahan-baku-next').disabled = pagination.current_page >= pagination.last_page;
                
                // Generate and render page numbers
                const pageNumbers = generatePageNumbers(pagination.current_page, pagination.last_page);
                const pageNumbersContainer = document.getElementById('bahan-baku-page-numbers');
                pageNumbersContainer.innerHTML = pageNumbers.map(page => {
                    const isActive = page === pagination.current_page;
                    const activeClass = isActive 
                        ? 'bg-blue-600 text-white dark:bg-blue-500' 
                        : 'dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600';
                    return `
                        <button 
                            onclick="goToBahanBakuPage(${page})" 
                            class="px-3 py-1 text-sm border rounded transition ${activeClass} ${isActive ? 'cursor-default' : 'cursor-pointer'}"
                            ${isActive ? 'disabled' : ''}
                        >
                            ${page}
                        </button>
                    `;
                }).join('');
                
                bahanBakuPagination.classList.remove('hidden');
            }
        } else {
            bahanBakuTableContainer.classList.add('hidden');
            bahanBakuEmptyState.classList.remove('hidden');
            bahanBakuPagination.classList.add('hidden');
        }
    }

    // Start auto-refresh
    function startAutoRefresh() {
        // Clear existing interval if any
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        
        // Fetch immediately
        fetchDashboardData();
        
        // Then set interval
        refreshInterval = setInterval(fetchDashboardData, REFRESH_INTERVAL_MS);
    }

    // Stop auto-refresh
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startAutoRefresh();
        
        // Stop refresh when page is hidden (save resources)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });

        // Stop refresh when page is unloaded
        window.addEventListener('beforeunload', stopAutoRefresh);
    });
})();
</script>
@endsection
