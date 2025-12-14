<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <!-- Set theme early to prevent FOUC -->
    <script>
        (function() {
            try {
                const stored = localStorage.getItem('theme') || 'dark';
                const root = document.documentElement;
                
                // Remove all theme classes
                root.classList.remove('dark');
                
                // Apply selected theme
                if (stored === 'dark') {
                    root.classList.add('dark');
                }
                // 'light' is default, no class needed
            } catch (e) {}
        })();
    </script>
    <style>
        /* Light Theme (White) Styles - Improved visibility and contrast */
        html:not(.dark) body {
            background-color: #f3f4f6 !important;
            color: #111827 !important;
        }
        
        html:not(.dark) aside,
        html:not(.dark) header {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
        }
        
        html:not(.dark) .bg-white,
        html:not(.dark) .dark\:bg-gray-800 {
            background-color: #ffffff !important;
        }
        
        html:not(.dark) .bg-gray-100,
        html:not(.dark) .dark\:bg-gray-900 {
            background-color: #f3f4f6 !important;
        }
        
        html:not(.dark) main {
            background-color: #f3f4f6 !important;
        }
        
        html:not(.dark) .text-gray-900,
        html:not(.dark) .dark\:text-gray-100 {
            color: #111827 !important;
            font-weight: 500 !important;
        }
        
        html:not(.dark) .text-gray-500,
        html:not(.dark) .dark\:text-gray-400 {
            color: #6b7280 !important;
        }
        
        html:not(.dark) .border-gray-200,
        html:not(.dark) .dark\:border-gray-700 {
            border-color: #d1d5db !important;
        }
        
        html:not(.dark) .hover\:bg-gray-100:hover,
        html:not(.dark) .dark\:hover\:bg-gray-700:hover {
            background-color: #e5e7eb !important;
        }
        
        html:not(.dark) input,
        html:not(.dark) select,
        html:not(.dark) textarea {
            background-color: #ffffff !important;
            color: #111827 !important;
            border-color: #d1d5db !important;
            border-width: 1px !important;
        }
        
        html:not(.dark) input::placeholder,
        html:not(.dark) textarea::placeholder {
            color: #9ca3af !important;
        }
        
        /* Enhanced shadow for cards */
        html:not(.dark) .shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        }
        
        html:not(.dark) .bg-white.shadow,
        html:not(.dark) .dark\:bg-gray-800.shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            border: 1px solid #e5e7eb !important;
        }
        
        html:not(.dark) .bg-blue-50,
        html:not(.dark) .dark\:bg-blue-900\/20 {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
        }
        
        html:not(.dark) .text-blue-800,
        html:not(.dark) .dark\:text-blue-200 {
            color: #1e40af !important;
            font-weight: 600 !important;
        }
        
        html:not(.dark) .border-blue-200,
        html:not(.dark) .dark\:border-blue-800 {
            border-color: #bfdbfe !important;
        }
        
        /* Role Badge Colors for Light Theme - Better Contrast */
        /* Owner Badge - Purple */
        html:not(.dark) .bg-purple-100 {
            background-color: #f3e8ff !important;
        }
        
        html:not(.dark) .text-purple-800 {
            color: #6b21a8 !important;
            font-weight: 600 !important;
        }
        
        html:not(.dark) span.bg-purple-100.text-purple-800,
        html:not(.dark) .bg-purple-100.text-purple-800 {
            background-color: #f3e8ff !important;
            color: #6b21a8 !important;
            font-weight: 600 !important;
        }
        
        /* Admin Badge - Red */
        html:not(.dark) .bg-red-100 {
            background-color: #fee2e2 !important;
        }
        
        html:not(.dark) .text-red-800 {
            color: #991b1b !important;
            font-weight: 600 !important;
        }
        
        html:not(.dark) span.bg-red-100.text-red-800,
        html:not(.dark) .bg-red-100.text-red-800 {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            font-weight: 600 !important;
        }
        
        /* Pegawai Badge - Blue */
        html:not(.dark) .bg-blue-100 {
            background-color: #dbeafe !important;
        }
        
        html:not(.dark) .text-blue-800 {
            color: #1e40af !important;
            font-weight: 600 !important;
        }
        
        html:not(.dark) span.bg-blue-100.text-blue-800,
        html:not(.dark) .bg-blue-100.text-blue-800 {
            background-color: #dbeafe !important;
            color: #1e40af !important;
            font-weight: 600 !important;
        }
        
        /* Sidebar Menu - Better visibility */
        html:not(.dark) aside {
            background-color: #ffffff !important;
            border-right: 2px solid #d1d5db !important;
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.05) !important;
        }
        
        html:not(.dark) aside a {
            color: #374151 !important;
        }
        
        html:not(.dark) aside a:hover {
            background-color: #f3f4f6 !important;
            color: #111827 !important;
        }
        
        html:not(.dark) aside .text-gray-500 {
            color: #6b7280 !important;
            font-weight: 600 !important;
        }
        
        /* Header visibility */
        html:not(.dark) header {
            background-color: #ffffff !important;
            border-bottom: 2px solid #d1d5db !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }
        
        html:not(.dark) header h1 {
            color: #111827 !important;
            font-weight: 600 !important;
        }
        
        /* Table visibility - Enhanced */
        html:not(.dark) table {
            border-color: #d1d5db !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        
        html:not(.dark) table th {
            background-color: #f9fafb !important;
            color: #111827 !important;
            border-color: #d1d5db !important;
            border-width: 1px !important;
            font-weight: 600 !important;
        }
        
        html:not(.dark) table td {
            color: #374151 !important;
            border-color: #e5e7eb !important;
            border-width: 1px !important;
        }
        
        html:not(.dark) table tr {
            background-color: #ffffff !important;
        }
        
        html:not(.dark) table tr:hover {
            background-color: #f9fafb !important;
        }
        
        /* Card visibility - Enhanced */
        html:not(.dark) .bg-white.dark\:bg-gray-800 {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        
        /* KPI Cards - Enhanced visibility */
        html:not(.dark) .bg-white.rounded-lg.shadow {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        
        /* Text colors for better contrast */
        html:not(.dark) .text-gray-700 {
            color: #374151 !important;
        }
        
        html:not(.dark) .text-gray-600 {
            color: #4b5563 !important;
        }
        
        html:not(.dark) .text-gray-400 {
            color: #9ca3af !important;
        }
        
        /* Link colors */
        html:not(.dark) a.text-blue-600,
        html:not(.dark) a.text-blue-400 {
            color: #2563eb !important;
        }
        
        html:not(.dark) a.text-blue-600:hover {
            color: #1d4ed8 !important;
        }
        
        /* Button colors */
        html:not(.dark) .bg-blue-600 {
            background-color: #2563eb !important;
        }
        
        html:not(.dark) .bg-blue-600:hover {
            background-color: #1d4ed8 !important;
        }
        
        /* Warning/Alert colors - Enhanced */
        html:not(.dark) .bg-yellow-50,
        html:not(.dark) .bg-yellow-100 {
            background-color: #fef3c7 !important;
            border: 1px solid #fbbf24 !important;
        }
        
        html:not(.dark) .text-yellow-800 {
            color: #92400e !important;
            font-weight: 600 !important;
        }
        
        html:not(.dark) .bg-red-50,
        html:not(.dark) .bg-red-100 {
            background-color: #fee2e2 !important;
            border: 1px solid #f87171 !important;
        }
        
        html:not(.dark) .text-red-800 {
            color: #991b1b !important;
            font-weight: 600 !important;
        }
        
        /* Red text for low stock */
        html:not(.dark) .text-red-600 {
            color: #dc2626 !important;
            font-weight: 600 !important;
        }
        
        html:not(.dark) .text-red-400 {
            color: #f87171 !important;
        }
        
        /* Pagination buttons */
        html:not(.dark) button.border {
            border-color: #d1d5db !important;
            background-color: #ffffff !important;
            color: #374151 !important;
        }
        
        html:not(.dark) button.border:hover {
            background-color: #f3f4f6 !important;
        }
        
        html:not(.dark) button.border:disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }
        
        /* Sidebar brand text */
        html:not(.dark) aside .text-lg.font-semibold {
            color: #111827 !important;
        }
        
        /* Logout button */
        html:not(.dark) button.text-gray-700 {
            color: #374151 !important;
        }
        
        html:not(.dark) button.text-gray-700:hover {
            background-color: #f3f4f6 !important;
            color: #111827 !important;
        }
    </style>
    <!-- Tailwind CSS via CDN with dark mode class strategy -->
    <script>
        // Tailwind CDN expects `tailwind.config`, not `window.tailwind.config`
        tailwind = window.tailwind || {};
        tailwind.config = { darkMode: 'class' };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700 hidden md:flex md:flex-col fixed left-0 top-0 h-screen z-30">
            <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <span class="text-lg font-semibold">My App</span>
            </div>
            <nav class="flex-1 overflow-y-auto p-4 space-y-1 min-h-0">
                @php
                    $isPegawai = \App\Helpers\BranchHelper::isPegawai();
                    $isAdmin = \App\Helpers\BranchHelper::isAdmin();
                    $isOwner = \App\Helpers\BranchHelper::isOwner();
                    $currentPath = request()->path();
                    $isDashboard = $currentPath === 'dashboard' || str_starts_with($currentPath, 'dashboard/');
                    $isKasir = str_starts_with($currentPath, 'kasir');
                    
                    // Helper function untuk check active menu
                    $isActive = function($path) use ($currentPath) {
                        if (is_array($path)) {
                            foreach ($path as $p) {
                                if ($currentPath === $p || str_starts_with($currentPath, $p . '/')) {
                                    return true;
                                }
                            }
                            return false;
                        }
                        return $currentPath === $path || str_starts_with($currentPath, $path . '/');
                    };
                @endphp

                <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-md {{ $isDashboard ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('kasir.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ $isKasir ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Sistem Kasir</span>
                </a>

                @if(!$isPegawai)
                <div class="mt-4">
                    <div class="px-3 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Data Master</div>
                    <a href="/master/produk" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/produk') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Data Menu / Produk</span>
                    </a>
                    <a href="/master/bahan-baku" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/bahan-baku') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Data Bahan Baku</span>
                    </a>
                    <a href="/master/resep" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/resep') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Data Resep</span>
                    </a>
                    <a href="/master/supplier" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/supplier') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Data Supplier</span>
                    </a>
                    @if($isOwner)
                    <a href="/master/pegawai" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/pegawai') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Data Pegawai / User</span>
                    </a>
                    @endif
                </div>

                <div class="mt-4">
                    <div class="px-3 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Stok & Inventori</div>
                    <a href="/inventori/restock" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('inventori/restock') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Restock</span>
                    </a>
                    <a href="/inventori/penyesuaian" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('inventori/penyesuaian') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Penyesuaian Stok</span>
                    </a>
                    <a href="/inventori/waste-management" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('inventori/waste-management') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Waste Management</span>
                    </a>
                </div>

                <div class="mt-4">
                    <div class="px-3 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Reports</div>
                    <a href="/reports/penjualan" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('reports/penjualan') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Laporan Penjualan</span>
                    </a>
                    <a href="/reports/stok" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('reports/stok') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Laporan Stok</span>
                    </a>
                    <a href="/reports/pembelian" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('reports/pembelian') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Laporan Pembelian</span>
                    </a>
                    <a href="{{ route('kasir.transaksi') }}" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('kasir/transaksi') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Transaksi Kasir</span>
                    </a>
                </div>
                @endif
            </nav>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full text-left px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile sidebar (toggleable) -->
        <div x-data="{ open: false }" class="md:hidden">
            <div class="fixed inset-0 z-40" x-show="open" x-transition.opacity @click="open = false"></div>
            <div class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 z-50 transform transition-transform" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700 justify-between">
                    <span class="text-lg font-semibold">My App</span>
                    <button class="p-2" @click="open = false">✕</button>
                </div>
                <nav class="p-4 space-y-1">
                    @php
                        $isPegawai = \App\Helpers\BranchHelper::isPegawai();
                        $isAdmin = \App\Helpers\BranchHelper::isAdmin();
                        $isOwner = \App\Helpers\BranchHelper::isOwner();
                        $currentPath = request()->path();
                        $isDashboard = $currentPath === 'dashboard' || str_starts_with($currentPath, 'dashboard/');
                        $isKasir = str_starts_with($currentPath, 'kasir');
                        
                        // Helper function untuk check active menu
                        $isActive = function($path) use ($currentPath) {
                            if (is_array($path)) {
                                foreach ($path as $p) {
                                    if ($currentPath === $p || str_starts_with($currentPath, $p . '/')) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                            return $currentPath === $path || str_starts_with($currentPath, $path . '/');
                        };
                    @endphp

                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-md {{ $isDashboard ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('kasir.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ $isKasir ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Sistem Kasir</span>
                    </a>

                    @if(!$isPegawai)
                    <div class="mt-4">
                        <div class="px-3 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Data Master</div>
                        <a href="/master/produk" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/produk') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Data Menu / Produk</span>
                        </a>
                        <a href="/master/bahan-baku" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/bahan-baku') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Data Bahan Baku</span>
                        </a>
                        <a href="/master/resep" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/resep') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Data Resep</span>
                        </a>
                        <a href="/master/supplier" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/supplier') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Data Supplier</span>
                        </a>
                        @if($isOwner)
                        <a href="/master/pegawai" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('master/pegawai') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Data Pegawai / User</span>
                        </a>
                        @endif
                    </div>

                    <div class="mt-4">
                        <div class="px-3 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Stok & Inventori</div>
                        <a href="/inventori/restock" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('inventori/restock') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Restock</span>
                        </a>
                        <a href="/inventori/penyesuaian" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('inventori/penyesuaian') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Penyesuaian Stok</span>
                        </a>
                        <a href="/inventori/waste-management" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('inventori/waste-management') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Waste Management</span>
                        </a>
                    </div>

                    <div class="mt-4">
                        <div class="px-3 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Reports</div>
                        <a href="/reports/penjualan" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('reports/penjualan') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Laporan Penjualan</span>
                        </a>
                        <a href="/reports/stok" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('reports/stok') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Laporan Stok</span>
                        </a>
                        <a href="/reports/pembelian" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('reports/pembelian') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Laporan Pembelian</span>
                        </a>
                        <a href="{{ route('kasir.transaksi') }}" class="flex items-center gap-3 px-5 py-2 rounded-md {{ $isActive('kasir/transaksi') ? 'bg-blue-600 text-white hover:bg-blue-700 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Laporan Transaksi Kasir</span>
                        </a>
                    </div>
                    @endif
                    <form class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-700" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-left px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0 md:ml-64">
            <!-- Topbar -->
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 md:px-6">
                <div class="flex items-center gap-2">
                    <button class="md:hidden p-2 rounded hover:bg-gray-100" onclick="document.dispatchEvent(new CustomEvent('toggle-sidebar'))">☰</button>
                    <h1 class="text-xl font-semibold">@yield('header', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $user = \Illuminate\Support\Facades\Auth::user();
                        $pegawai = null;
                        if ($user) {
                            // Cari pegawai berdasarkan username atau email
                            $pegawai = \App\Models\Pegawai::where('username', $user->username)
                                ->orWhere(function($q) use ($user) {
                                    if ($user->email) {
                                        $q->where('email', $user->email);
                                    }
                                })
                                ->first();
                        }
                    @endphp
                    <div class="flex items-center gap-2">
                        @if($pegawai)
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:block">
                                {{ $pegawai->nama }}
                            </span>
                        @endif
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors flex items-center justify-center cursor-pointer">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                             style="display: none;">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span class="text-white font-semibold text-lg">
                                            {{ $pegawai ? strtoupper(substr($pegawai->nama, 0, 1)) : strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $pegawai ? $pegawai->nama : ($user->name ?? 'User') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ $pegawai ? $pegawai->username : ($user->username ?? '-') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 space-y-3">
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nama Karyawan</label>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                        {{ $pegawai ? $pegawai->nama : ($user->name ?? '-') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Username</label>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                        {{ $pegawai ? $pegawai->username : ($user->username ?? '-') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Role</label>
                                    <p class="text-sm font-medium mt-1">
                                        @if($pegawai)
                                            @if($pegawai->role === 'owner')
                                                <span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    Owner
                                                </span>
                                            @elseif($pegawai->role === 'admin')
                                                <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    Pegawai
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                User
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 block">Setelan Tema</label>
                                <div class="space-y-2">
                                    <button onclick="setTheme('light')" class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors theme-option" data-theme="light">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span>Tema Putih</span>
                                        </div>
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 theme-check hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button onclick="setTheme('dark')" class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors theme-option" data-theme="dark">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                            </svg>
                                            <span>Tema Hitam</span>
                                        </div>
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 theme-check hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </header>

            <main class="p-4 md:p-6">
                @yield('content')
            </main>

            <footer class="p-4 text-sm text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} My App. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Alpine.js (for simple sidebar toggle on mobile) -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('toggle-sidebar', () => {
            const root = document.querySelector('[x-data]');
            if (root && root.__x) {
                root.__x.$data.open = !root.__x.$data.open;
            }
        });
        
        // Theme management
        function setTheme(theme) {
            const root = document.documentElement;
            
            // Remove all theme classes
            root.classList.remove('dark');
            
            // Apply selected theme
            if (theme === 'dark') {
                root.classList.add('dark');
            }
            // 'light' is default, no class needed
            
            // Save to localStorage
            localStorage.setItem('theme', theme);
            
            // Update checkmarks
            document.querySelectorAll('.theme-option').forEach(btn => {
                const check = btn.querySelector('.theme-check');
                if (btn.dataset.theme === theme) {
                    check.classList.remove('hidden');
                } else {
                    check.classList.add('hidden');
                }
            });
        }
        
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            setTheme(currentTheme);
        });
    </script>
    @vite(['resources/js/app.js'])
    <script>
        // Set idCabang for real-time updates
        window.idCabang = {{ session('id_cabang', 0) }};
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('scripts')
@yield('modals')
</body>
</html>

