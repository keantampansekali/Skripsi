// Real-time updates handler
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for Echo to be initialized
    setTimeout(() => {
        const idCabang = window.idCabang || null;
        
        if (!idCabang) {
            console.warn('idCabang not available');
            return;
        }
        
        if (!window.Echo) {
            console.warn('Echo not available. Make sure Reverb server is running and configured.');
            return;
        }
        
        try {
            const channel = window.Echo.channel(`cabang.${idCabang}`);
            
            // Handle connection errors
            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('WebSocket connection error:', err);
            });
            
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('WebSocket connected successfully');
            });
            
            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.warn('WebSocket disconnected');
            });
    
    // Listen for stock updates
    channel.listen('.stok.updated', (e) => {
        console.log('Stock updated:', e);
        updateStockDisplay(e.data);
        showNotification('Stok diperbarui: ' + e.data.nama, 'info');
    });
    
    // Listen for new transactions
    channel.listen('.transaksi.baru', (e) => {
        console.log('New transaction:', e);
        updateTransactionList(e.transaksi);
        updateDashboardStats(e.stats);
        showNotification('Transaksi baru: ' + e.transaksi.no_transaksi, 'success');
    });
    
    // Listen for restock
    channel.listen('.restock.created', (e) => {
        console.log('Restock created:', e);
        showNotification('Restock baru: ' + e.restock.no_nota, 'info');
        if (window.location.pathname.includes('/inventori/restock')) {
            location.reload();
        }
    });
    
    // Listen for penyesuaian stok
    channel.listen('.penyesuaian.created', (e) => {
        console.log('Penyesuaian created:', e);
        showNotification('Penyesuaian stok dibuat', 'info');
        if (window.location.pathname.includes('/inventori/penyesuaian')) {
            location.reload();
        }
    });
    
    // Listen for waste management
    channel.listen('.waste.created', (e) => {
        console.log('Waste created:', e);
        showNotification('Waste management dibuat', 'info');
        if (window.location.pathname.includes('/inventori/waste-management')) {
            location.reload();
        }
    });
    
    // Listen for produk updates
    channel.listen('.produk.updated', (e) => {
        console.log('Produk updated:', e);
        updateProdukDisplay(e.produk);
        showNotification('Produk diperbarui: ' + e.produk.nama_produk, 'info');
    });
    
    // Listen for bahan baku updates
    channel.listen('.bahan-baku.updated', (e) => {
        console.log('Bahan baku updated:', e);
        updateBahanBakuDisplay(e.bahanBaku);
        showNotification('Bahan baku diperbarui: ' + e.bahanBaku.nama_bahan, 'info');
    });
    
    // Listen for low stock alerts
    channel.listen('.stok.rendah', (e) => {
        console.log('Low stock alert:', e);
        const itemName = e.tipe === 'produk' ? e.item.nama_produk : e.item.nama_bahan;
        showNotification('Peringatan: Stok ' + itemName + ' rendah (' + e.stokBaru + ')', 'warning');
    });
    
    // Listen for stock out alerts
    channel.listen('.stok.habis', (e) => {
        console.log('Stock out alert:', e);
        const itemName = e.bahanBaku?.nama_bahan || 'Item';
        showNotification('Peringatan: Stok ' + itemName + ' habis!', 'error');
    });
    
    // Helper function to update stock display
    function updateStockDisplay(data) {
        // Update stock in kasir page
        if (window.location.pathname.includes('/kasir')) {
            const productCard = document.querySelector(`[data-product-id="${data.id}"]`);
            if (productCard) {
                const stockElement = productCard.querySelector('.stock-display');
                if (stockElement) {
                    const newStock = parseInt(data.stok) || 0;
                    stockElement.setAttribute('data-stock', newStock);
                    
                    if (newStock > 0) {
                        stockElement.textContent = newStock;
                        stockElement.className = 'stock-display text-xs bg-gradient-to-r from-green-600/90 to-green-700/90 text-white px-2.5 py-1 rounded-full font-bold shadow-lg backdrop-blur-sm';
                        if (newStock < 10) {
                            stockElement.className = 'stock-display text-xs bg-gradient-to-r from-yellow-600/90 to-yellow-700/90 text-white px-2.5 py-1 rounded-full font-bold shadow-lg backdrop-blur-sm';
                        }
                    } else {
                        stockElement.textContent = 'Habis';
                        stockElement.className = 'stock-display text-xs bg-gradient-to-r from-gray-600/90 to-gray-700/90 text-white px-2.5 py-1 rounded-full font-bold shadow-lg';
                    }
                    
                    // Update onclick function dengan stok baru
                    const oldOnclick = productCard.getAttribute('onclick');
                    if (oldOnclick) {
                        const newOnclick = oldOnclick.replace(/,\s*\d+\)$/, ', ' + newStock + ')');
                        productCard.setAttribute('onclick', newOnclick);
                    }
                }
            }
        }
        
        // Update stock in master produk page
        if (data.tipe === 'produk' && window.location.pathname.includes('/master/produk')) {
            const row = document.querySelector(`tr[data-produk-id="${data.id}"]`);
            if (row) {
                const stokCell = row.querySelector('[data-field="stok"]');
                if (stokCell) {
                    stokCell.textContent = data.stok;
                    if (data.stok < 10) {
                        stokCell.classList.add('text-red-600', 'font-bold');
                    } else {
                        stokCell.classList.remove('text-red-600', 'font-bold');
                    }
                }
            }
        }
        
        // Update stock in master bahan baku page
        if (data.tipe === 'bahan_baku' && window.location.pathname.includes('/master/bahan-baku')) {
            const row = document.querySelector(`tr[data-bahan-id="${data.id}"]`);
            if (row) {
                const stokCell = row.querySelector('[data-field="stok"]');
                if (stokCell) {
                    const stockValue = parseInt(data.stok) || 0;
                    stokCell.textContent = formatNumber(stockValue);
                    if (stockValue <= 0) {
                        stokCell.classList.add('text-red-600', 'font-bold');
                        row.classList.add('bg-red-50', 'dark:bg-red-900/20');
                    } else if (stockValue < 10) {
                        stokCell.classList.add('text-yellow-600', 'font-semibold');
                        row.classList.add('bg-yellow-50', 'dark:bg-yellow-900/20');
                    } else {
                        stokCell.classList.remove('text-red-600', 'font-bold', 'text-yellow-600', 'font-semibold');
                        row.classList.remove('bg-red-50', 'dark:bg-red-900/20', 'bg-yellow-50', 'dark:bg-yellow-900/20');
                    }
                }
            }
        }
        
        // Update dashboard if on dashboard page
        if (window.location.pathname === '/dashboard') {
            if (typeof fetchDashboardData === 'function') {
                // Small delay to avoid too frequent updates
                setTimeout(() => {
                    fetchDashboardData();
                }, 500);
            }
        }
    }
    
    // Helper function to update transaction list
    function updateTransactionList(transaksi) {
        if (window.location.pathname.includes('/kasir/transaksi')) {
            // Reload the page to show new transaction
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else if (window.location.pathname === '/dashboard') {
            // Update dashboard stats
            if (typeof fetchDashboardData === 'function') {
                fetchDashboardData();
            }
        }
    }
    
    // Helper function to update dashboard stats
    function updateDashboardStats(stats) {
        if (window.location.pathname === '/dashboard') {
            // Update dashboard stats if available
            if (stats.penjualan_hari_ini !== undefined) {
                const element = document.getElementById('penjualan-hari-ini');
                if (element) {
                    element.textContent = formatCurrency(stats.penjualan_hari_ini);
                }
            }
            if (stats.jumlah_transaksi_hari_ini !== undefined) {
                const element = document.getElementById('transaksi-hari-ini');
                if (element) {
                    element.textContent = formatNumber(stats.jumlah_transaksi_hari_ini) + ' transaksi';
                }
            }
            if (stats.penjualan_bulan_ini !== undefined) {
                const element = document.getElementById('penjualan-bulan-ini');
                if (element) {
                    element.textContent = formatCurrency(stats.penjualan_bulan_ini);
                }
            }
            if (stats.jumlah_transaksi_bulan_ini !== undefined) {
                const element = document.getElementById('transaksi-bulan-ini');
                if (element) {
                    element.textContent = formatNumber(stats.jumlah_transaksi_bulan_ini) + ' transaksi';
                }
            }
            
            // Trigger dashboard refresh if function exists
            if (typeof fetchDashboardData === 'function') {
                fetchDashboardData();
            }
        }
    }
    
    // Helper function to update produk display
    function updateProdukDisplay(produk) {
        // Update in master produk index page
        if (window.location.pathname.includes('/master/produk')) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 5) {
                    const namaCell = cells[1];
                    if (namaCell && namaCell.textContent.trim() === produk.nama_produk) {
                        // Update harga (cell index 3)
                        if (cells[3]) {
                            cells[3].textContent = 'Rp ' + formatNumber(produk.harga);
                        }
                        // Update stok (cell index 4)
                        if (cells[4]) {
                            cells[4].textContent = produk.stok;
                            if (produk.stok < 10) {
                                cells[4].classList.add('text-red-600', 'font-bold');
                            } else {
                                cells[4].classList.remove('text-red-600', 'font-bold');
                            }
                        }
                    }
                }
            });
        }
        
        // Update in dashboard table
        if (window.location.pathname === '/dashboard') {
            const rows = document.querySelectorAll('#produk-table-body tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 3) {
                    const namaCell = cells[0];
                    if (namaCell && namaCell.textContent.trim() === produk.nama_produk) {
                        // Update harga (cell index 1)
                        if (cells[1]) {
                            cells[1].textContent = formatCurrency(produk.harga);
                        }
                        // Update stok (cell index 2)
                        if (cells[2]) {
                            const isLowStock = produk.stok < 10;
                            cells[2].textContent = formatNumber(produk.stok) + ' unit';
                            cells[2].className = isLowStock ? 'py-2 px-3 text-red-600 dark:text-red-400 font-semibold' : 'py-2 px-3';
                            row.className = isLowStock ? 'border-b dark:border-gray-700 bg-red-50 dark:bg-red-900/20' : 'border-b dark:border-gray-700';
                        }
                    }
                }
            });
        }
    }
    
    // Helper function to update bahan baku display
    function updateBahanBakuDisplay(bahanBaku) {
        // Update in master bahan baku index page
        if (window.location.pathname.includes('/master/bahan-baku')) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 4) {
                    const namaCell = cells[0];
                    if (namaCell && namaCell.textContent.trim() === bahanBaku.nama_bahan) {
                        // Update stok (cell index 2)
                        if (cells[2]) {
                            const stockValue = parseInt(bahanBaku.stok) || 0;
                            cells[2].textContent = formatNumber(stockValue);
                            if (stockValue <= 0) {
                                cells[2].classList.add('text-red-600', 'font-bold');
                                row.classList.add('bg-red-50', 'dark:bg-red-900/20');
                            } else if (stockValue < 10) {
                                cells[2].classList.add('text-yellow-600', 'font-semibold');
                                row.classList.add('bg-yellow-50', 'dark:bg-yellow-900/20');
                            } else {
                                cells[2].classList.remove('text-red-600', 'font-bold', 'text-yellow-600', 'font-semibold');
                                row.classList.remove('bg-red-50', 'dark:bg-red-900/20', 'bg-yellow-50', 'dark:bg-yellow-900/20');
                            }
                        }
                        // Update harga satuan (cell index 3)
                        if (cells[3]) {
                            cells[3].textContent = 'Rp ' + formatNumber(bahanBaku.harga_satuan || 0);
                        }
                    }
                }
            });
        }
        
        // Update in dashboard table
        if (window.location.pathname === '/dashboard') {
            const rows = document.querySelectorAll('#bahan-baku-table-body tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 4) {
                    const namaCell = cells[0];
                    if (namaCell && namaCell.textContent.trim() === bahanBaku.nama_bahan) {
                        // Update harga satuan (cell index 2)
                        if (cells[2]) {
                            cells[2].textContent = formatCurrency(bahanBaku.harga_satuan || 0);
                        }
                        // Update stok (cell index 3)
                        if (cells[3]) {
                            const isOutOfStock = bahanBaku.stok <= 0;
                            const isLowStock = bahanBaku.stok > 0 && bahanBaku.stok < 10;
                            cells[3].textContent = formatNumber(bahanBaku.stok);
                            
                            if (isOutOfStock) {
                                cells[3].className = 'py-2 px-3 text-red-700 dark:text-red-300 font-bold';
                                row.className = 'border-b dark:border-gray-700 bg-red-100 dark:bg-red-900/30';
                            } else if (isLowStock) {
                                cells[3].className = 'py-2 px-3 text-yellow-600 dark:text-yellow-400 font-semibold';
                                row.className = 'border-b dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/20';
                            } else {
                                cells[3].className = 'py-2 px-3';
                                row.className = 'border-b dark:border-gray-700';
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Helper function to show notifications
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
    
    // Helper function to format number with thousand separators
    function formatNumber(value) {
        if (value === null || value === undefined) return '0';
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }
    
    // Helper function to format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    }
        } catch (error) {
            console.error('Error setting up real-time listeners:', error);
        }
    }, 100);
});

