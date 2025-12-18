@extends('layouts.kasir')

@section('title', 'Sistem Kasir')

@section('content')
<style>
    @keyframes gradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    
    .modern-gradient {
        background: linear-gradient(135deg, #1f1f1f 0%, #2d1b1b 25%, #3a2222 50%, #2d1b1b 75%, #1f1f1f 100%);
        background-size: 400% 400%;
        animation: gradient 20s ease infinite;
    }
    
    .glass-modern {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px) saturate(120%);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }
    
    .product-card-modern {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .product-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
        z-index: 5;
    }
    
    .product-card-modern:hover::before {
        left: 100%;
    }
    
    .product-card-modern:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        border-color: rgba(255, 255, 255, 0.4);
    }
    
    .product-card-modern:hover .absolute.inset-0 {
        background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.7) 100%);
    }
    
    .cart-item-modern {
        transition: all 0.3s ease;
    }
    
    .cart-item-modern:hover {
        transform: translateX(4px);
    }
    
    .input-modern {
        transition: all 0.3s ease;
    }
    
    .input-modern:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
    }
    
    .button-modern {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .button-modern::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .button-modern:hover::after {
        width: 300px;
        height: 300px;
    }
    
    .scrollbar-modern::-webkit-scrollbar {
        width: 8px;
    }
    
    .scrollbar-modern::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    
    .scrollbar-modern::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }
    
    .scrollbar-modern::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    .badge-modern {
        animation: float 3s ease-in-out infinite;
    }
</style>

<div class="min-h-screen relative overflow-hidden">
    <!-- Modern Gradient Background -->
    <div class="fixed inset-0 modern-gradient"></div>
    
    <!-- Soft Red Accent Overlay -->
    <div class="fixed inset-0 bg-gradient-to-br from-red-900/20 via-red-800/15 to-transparent"></div>
    
    <!-- Animated Pattern Overlay -->
    <div class="fixed inset-0 opacity-3" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.1) 1px, transparent 0); background-size: 50px 50px; animation: shimmer 20s linear infinite;"></div>
    
    <!-- Floating Shapes -->
    <div class="fixed top-10 left-10 w-72 h-72 bg-red-900/8 rounded-full blur-3xl animate-pulse"></div>
    <div class="fixed bottom-10 right-10 w-96 h-96 bg-red-900/8 rounded-full blur-3xl animate-pulse delay-1000"></div>
    
    <div class="relative z-10 container mx-auto p-6">
        <!-- Modern Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="glass-modern rounded-3xl px-8 py-6 shadow-2xl">
                        <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-white/25 to-white/10 rounded-2xl flex items-center justify-center backdrop-blur-lg border border-white/15">
                            <span class="text-3xl">💳</span>
                        </div>
                        <div>
                            <h1 class="text-4xl font-extrabold text-white/95 mb-1">
                                Sistem Kasir
                            </h1>
                            <p class="text-sm text-white/70 font-medium flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500/70 rounded-full animate-pulse"></span>
                                {{ session('nama_cabang', 'Cabang') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('kasir.transaksi') }}" class="glass-modern px-6 py-3 text-white/90 rounded-2xl hover:bg-white/15 transition-all shadow-xl button-modern font-semibold flex items-center gap-2">
                        <span>📊</span>
                        <span>Transaksi</span>
                    </a>
                    <a href="/dashboard" class="bg-white/90 backdrop-blur-lg text-gray-700 rounded-2xl hover:bg-white/95 transition-all shadow-xl px-6 py-3 font-semibold button-modern flex items-center gap-2">
                        <span>←</span>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Daftar Produk -->
            <div class="lg:col-span-2">
                <div class="glass-modern rounded-3xl shadow-2xl p-8">
                    <!-- Search Bar -->
                    <div class="mb-6 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Cari produk..." 
                            class="input-modern w-full pl-12 pr-5 py-4 glass-modern border-2 border-white/20 rounded-2xl focus:border-white/40 focus:outline-none text-white placeholder-white/60 text-lg shadow-xl"
                            autocomplete="off"
                        />
                    </div>

                    <!-- Products Grid -->
                    <div id="productsGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[calc(100vh-300px)] overflow-y-auto px-1 scrollbar-modern">
                        @foreach($produks as $produk)
                        @php
                            $stokProduk = $produk->stok ?? 0;
                            $isDisabled = $stokProduk <= 0;
                        @endphp
                        <button 
                            onclick="addToCart({{ $produk->id }}, '{{ $produk->nama_produk }}', {{ $produk->harga }}, {{ $stokProduk }})"
                            class="product-card-modern p-5 rounded-2xl text-left shadow-xl relative overflow-hidden group min-h-[200px] flex flex-col {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                            style="@if($produk->foto) background-image: url('{{ asset($produk->foto) }}'); background-size: cover; background-position: center; @else background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%); @endif"
                            data-name="{{ strtolower($produk->nama_produk) }}"
                            data-product-id="{{ $produk->id }}"
                            data-stok="{{ $stokProduk }}"
                            {{ $isDisabled ? 'disabled' : '' }}
                        >
                            <!-- Overlay untuk readability -->
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/80 rounded-2xl {{ $isDisabled ? 'bg-red-900/40' : '' }}"></div>
                            
                            @if($isDisabled)
                            <!-- Badge Stok Habis -->
                            <div class="absolute top-2 left-2 z-20 bg-red-600/90 text-white text-xs px-2 py-1 rounded-lg font-bold shadow-lg backdrop-blur-sm">
                                ⚠️ Stok Habis
                            </div>
                            @endif
                            
                            <!-- Content -->
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="flex items-start justify-between mb-2">
                                    @if(!$produk->foto)
                                    <div class="w-12 h-12 rounded-xl overflow-hidden shadow-lg backdrop-blur-sm flex items-center justify-center bg-white/10">
                                        <span class="text-xl">🛍️</span>
                                    </div>
                                    @endif
                                    @if($stokProduk > 0)
                                        @php
                                            $stokRendahThreshold = config('whatsapp.stok_rendah_threshold', 10);
                                            $isStokRendah = $stokProduk < $stokRendahThreshold;
                                        @endphp
                                        <span class="stock-display text-xs {{ $isStokRendah ? 'bg-gradient-to-r from-yellow-600/90 to-yellow-700/90' : 'bg-gradient-to-r from-green-600/90 to-green-700/90' }} text-white px-2.5 py-1 rounded-full font-bold shadow-lg backdrop-blur-sm" data-stock="{{ $stokProduk }}" data-threshold="{{ $stokRendahThreshold }}">
                                            {{ $stokProduk }}
                                        </span>
                                    @else
                                    <span class="stock-display text-xs bg-gradient-to-r from-red-600/90 to-red-700/90 text-white px-2.5 py-1 rounded-full font-bold shadow-lg" data-stock="0">
                                        Tidak Tersedia
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-end">
                                    <h3 class="font-bold text-white mb-1 text-sm leading-tight group-hover:text-white drop-shadow-lg">{{ $produk->nama_produk }}</h3>
                                    <p class="text-xs text-white/80 mb-2 line-clamp-1 leading-relaxed drop-shadow">
                                        @if($isDisabled)
                                        Stok tidak tersedia
                                        @else
                                        {{ $produk->deskripsi ?? 'Tersedia' }}
                                        @endif
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <p class="text-lg font-extrabold text-white drop-shadow-lg">
                                            Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                        </p>
                                        @if(!$isDisabled)
                                        <div class="w-8 h-8 rounded-full bg-white/30 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                            <span class="text-white text-sm font-bold">+</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </button>
                        @endforeach
                    </div>

                    @if($produks->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada produk tersedia</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right: Keranjang & Payment -->
            <div class="lg:col-span-1">
                <div class="glass-modern rounded-3xl shadow-2xl p-6 sticky top-4">
                        <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-white/25 to-white/10 rounded-2xl flex items-center justify-center backdrop-blur-lg border border-white/15">
                            <span class="text-2xl">🛒</span>
                        </div>
                        <h2 class="text-2xl font-extrabold text-white/95 drop-shadow-lg">
                            Keranjang
                        </h2>
                    </div>
                    
                    <!-- Cart Items -->
                    <div id="cartItems" class="space-y-3 mb-6 max-h-[400px] overflow-y-auto px-1 scrollbar-modern">
                        <div class="text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center">
                                <span class="text-4xl">🛍️</span>
                            </div>
                            <p class="text-white/60 font-medium">Keranjang kosong</p>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="border-t border-white/20 pt-6 space-y-4">
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-white/80 text-sm">Subtotal</span>
                            <span class="font-bold text-white text-lg" id="subtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-white/80 text-sm">Diskon</span>
                            <span class="font-bold text-white text-lg" id="discountAmount">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-white/80 text-sm">Tax (10%)</span>
                            <span class="font-bold text-white text-lg" id="taxAmount">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center glass-modern rounded-2xl p-5 border border-white/20 mt-4 bg-white/10">
                            <span class="font-extrabold text-white/95 text-lg">Total</span>
                            <span class="font-extrabold text-3xl text-white/95" id="total">Rp 0</span>
                        </div>

                        <!-- Discount Input -->
                        <div class="space-y-3 pt-4 border-t border-white/10">
                            <label class="block text-sm font-bold text-white mb-3 flex items-center gap-2">
                                <span>🎫</span>
                                <span>Diskon</span>
                            </label>
                            <div class="flex gap-2 mb-3">
                                <button 
                                    type="button"
                                    id="discountTypeRp"
                                    onclick="setDiscountType('rp')"
                                    class="flex-1 px-4 py-2.5 bg-white/95 text-gray-800 rounded-xl hover:bg-white transition-all shadow-lg font-bold button-modern"
                                >
                                    Rp
                                </button>
                                <button 
                                    type="button"
                                    id="discountTypePercent"
                                    onclick="setDiscountType('percent')"
                                    class="flex-1 px-4 py-2.5 glass-modern text-white rounded-xl hover:bg-white/20 transition-all shadow-lg font-bold button-modern"
                                >
                                    %
                                </button>
                            </div>
                            <input 
                                type="number" 
                                id="discountInput"
                                placeholder="0" 
                                min="0"
                                class="input-modern w-full px-5 py-4 glass-modern border-2 border-white/20 rounded-2xl focus:border-white/40 focus:outline-none text-white text-lg shadow-xl placeholder-white/50"
                                oninput="calculateTotals()"
                                value="0"
                            />
                            <span class="text-xs text-white/60 block mt-2" id="discountLabel">Masukkan nominal diskon dalam Rupiah</span>
                        </div>

                        <!-- Payment Input -->
                        <div class="space-y-3 pt-4 border-t border-white/10">
                            <label class="block text-sm font-bold text-white mb-2 flex items-center gap-2">
                                <span>💵</span>
                                <span>Bayar</span>
                            </label>
                            <input 
                                type="number" 
                                id="paymentInput"
                                placeholder="0" 
                                min="0"
                                class="input-modern w-full px-5 py-4 glass-modern border-2 border-white/20 rounded-2xl focus:border-white/40 focus:outline-none text-white text-lg shadow-xl placeholder-white/50"
                                oninput="limitPaymentInput(); calculateChange();"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                            />
                            <label class="block text-sm font-bold text-white mt-4 mb-2 flex items-center gap-2">
                                <span>💰</span>
                                <span>Kembalian</span>
                            </label>
                            <input 
                                type="text" 
                                id="changeInput"
                                readonly
                                value="Rp 0" 
                                class="w-full px-5 py-4 bg-white/10 backdrop-blur-md border-2 border-white/15 rounded-2xl text-white/90 text-lg shadow-lg"
                            />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-6">
                            <button 
                                onclick="clearCart()"
                                class="flex-1 px-6 py-4 bg-white/20 backdrop-blur-lg text-white rounded-2xl hover:bg-white/30 transition-all shadow-xl font-bold text-lg button-modern"
                            >
                                🗑️ Hapus
                            </button>
                            <button 
                                onclick="processPayment()"
                                id="checkoutBtn"
                                class="flex-1 px-6 py-4 bg-white/90 text-gray-800 rounded-2xl hover:bg-white transition-all shadow-xl font-bold text-lg button-modern disabled:bg-gray-400/50 disabled:text-gray-500 disabled:cursor-not-allowed disabled:hover:bg-gray-400/50"
                                disabled
                            >
                                ✅ Bayar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];
let discountType = 'rp'; // 'rp' or 'percent'

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const products = document.querySelectorAll('.product-card-modern');
    
    products.forEach(product => {
        const name = product.getAttribute('data-name');
        if (name.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
});

// Fungsi untuk menghitung total (untuk validasi minimum pembayaran)
function getTotalAmount() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discountInput = document.getElementById('discountInput');
    let discountValue = discountInput ? parseFloat(discountInput.value) || 0 : 0;
    let discount = 0;
    
    // Hitung discount berdasarkan tipe (sama seperti calculateTotals)
    if (discountType === 'percent') {
        // Validasi: persentase tidak boleh lebih dari 100
        if (discountValue > 100) {
            discountValue = 100;
        }
        discount = (subtotal * discountValue) / 100;
    } else {
        // Validasi: diskon tidak boleh lebih besar dari subtotal
        if (discountValue > subtotal) {
            discountValue = subtotal;
        }
        discount = discountValue;
    }
    
    const subtotalAfterDiscount = Math.max(0, subtotal - discount);
    const tax = subtotalAfterDiscount * 0.1; // 10% tax
    const total = subtotalAfterDiscount + tax;
    return total;
}


// Payment input validation
document.addEventListener('DOMContentLoaded', function() {
    const paymentInput = document.getElementById('paymentInput');
    if (paymentInput) {
        // Set minimum berdasarkan total (tidak ada batasan maksimal)
        function updateMinLimit() {
            const total = getTotalAmount();
            paymentInput.setAttribute('min', total);
            // Hapus max attribute jika ada
            paymentInput.removeAttribute('max');
        }
        
        // Update saat halaman dimuat
        updateMinLimit();
        
        // Tambahkan event listener untuk paste
        paymentInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
            const numericValue = pastedData.replace(/[^\d]/g, '');
            const finalValue = parseInt(numericValue) || 0;
            
            paymentInput.value = finalValue;
            calculateChange();
        });
    }
});

function addToCart(id, name, price, stock) {
    if (stock <= 0) {
        alert('❌ Produk tidak tersedia!\n\nStok produk habis.\nSilakan hubungi admin untuk restock produk.');
        return;
    }

    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        if (existingItem.quantity >= stock) {
            alert(`❌ Maksimal ${stock} unit!\n\nStok produk tersedia: ${stock} unit.`);
            return;
        }
        existingItem.quantity++;
    } else {
        cart.push({ id, name, price, stock, quantity: 1 });
    }
    
    renderCart();
    calculateTotals();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    renderCart();
    calculateTotals();
}

function updateQuantity(id, change) {
    const item = cart.find(item => item.id === id);
    if (!item) return;
    
    const newQuantity = item.quantity + change;
    if (newQuantity <= 0) {
        removeFromCart(id);
    } else if (newQuantity > item.stock) {
        alert('Stok tidak mencukupi!');
    } else {
        item.quantity = newQuantity;
        renderCart();
        calculateTotals();
    }
}

function setQuantity(id, value) {
    const item = cart.find(item => item.id === id);
    if (!item) return;
    
    const newQuantity = parseInt(value) || 1;
    
    if (newQuantity <= 0) {
        removeFromCart(id);
        return;
    }
    
    if (newQuantity > item.stock) {
        alert('Stok tidak mencukupi! Stok tersedia: ' + item.stock);
        item.quantity = item.stock;
    } else {
        item.quantity = newQuantity;
    }
    
    renderCart();
    calculateTotals();
}

function renderCart() {
    // Update min limit setelah render cart (tidak ada batasan maksimal)
    setTimeout(function() {
        const paymentInput = document.getElementById('paymentInput');
        if (paymentInput) {
            const total = getTotalAmount();
            paymentInput.setAttribute('min', total);
            paymentInput.removeAttribute('max');
        }
    }, 0);
    const cartItems = document.getElementById('cartItems');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center">
                    <span class="text-4xl">🛍️</span>
                </div>
                <p class="text-white/60 font-medium">Keranjang kosong</p>
            </div>
        `;
        checkoutBtn.disabled = true;
    } else {
        cartItems.innerHTML = cart.map(item => `
            <div class="cart-item-modern glass-modern p-4 rounded-2xl border border-white/20 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-white text-sm flex-1 truncate pr-2">${item.name}</h4>
                    <button onclick="removeFromCart(${item.id})" class="w-8 h-8 bg-white/20 hover:bg-red-700/90 rounded-xl flex items-center justify-center text-white font-bold transition-all hover:scale-110 backdrop-blur-sm flex-shrink-0">
                        ✕
                    </button>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <button onclick="updateQuantity(${item.id}, -1)" class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white font-bold shadow-lg transition-all backdrop-blur-sm hover:scale-110">−</button>
                        <input 
                            type="number" 
                            min="1" 
                            max="${item.stock}"
                            value="${item.quantity}"
                            onchange="setQuantity(${item.id}, this.value)"
                            onblur="setQuantity(${item.id}, this.value)"
                            class="w-16 text-white font-bold text-base text-center glass-modern px-2 rounded-xl border border-white/20 focus:border-white/40 focus:outline-none"
                        />
                        <button onclick="updateQuantity(${item.id}, 1)" class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white font-bold shadow-lg transition-all backdrop-blur-sm hover:scale-110">+</button>
                    </div>
                    <span class="font-extrabold text-lg bg-gradient-to-r from-white to-white/80 bg-clip-text text-transparent">Rp ${formatNumber(item.price * item.quantity)}</span>
                </div>
                <p class="text-xs text-white/50 mt-1">Rp ${formatNumber(item.price)}/unit</p>
            </div>
        `).join('');
        checkoutBtn.disabled = false;
    }
    
    updateTotals();
}

function setDiscountType(type) {
    discountType = type;
    const rpBtn = document.getElementById('discountTypeRp');
    const percentBtn = document.getElementById('discountTypePercent');
    const discountLabel = document.getElementById('discountLabel');
    
    if (type === 'rp') {
        rpBtn.classList.remove('glass-modern', 'text-white');
        rpBtn.classList.add('bg-white/95', 'text-gray-800');
        percentBtn.classList.remove('bg-white/95', 'text-gray-800');
        percentBtn.classList.add('glass-modern', 'text-white');
        discountLabel.textContent = 'Masukkan nominal diskon dalam Rupiah';
        document.getElementById('discountInput').setAttribute('max', '');
    } else {
        percentBtn.classList.remove('glass-modern', 'text-white');
        percentBtn.classList.add('bg-white/95', 'text-gray-800');
        rpBtn.classList.remove('bg-white/95', 'text-gray-800');
        rpBtn.classList.add('glass-modern', 'text-white');
        discountLabel.textContent = 'Masukkan persentase diskon (0-100)';
        document.getElementById('discountInput').setAttribute('max', '100');
    }
    
    calculateTotals();
}

function calculateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discountValue = parseFloat(document.getElementById('discountInput').value) || 0;
    let discount = 0;
    
    if (discountType === 'percent') {
        // Validasi: persentase tidak boleh lebih dari 100
        if (discountValue > 100) {
            discountValue = 100;
            document.getElementById('discountInput').value = 100;
        }
        discount = (subtotal * discountValue) / 100;
    } else {
        // Validasi: diskon tidak boleh lebih besar dari subtotal
        if (discountValue > subtotal) {
            discountValue = subtotal;
            document.getElementById('discountInput').value = subtotal;
        }
        discount = discountValue;
    }
    
    const subtotalAfterDiscount = Math.max(0, subtotal - discount);
    const tax = subtotalAfterDiscount * 0.1; // 10% tax
    const total = subtotalAfterDiscount + tax;
    
    document.getElementById('subtotal').textContent = 'Rp ' + formatNumber(subtotal);
    document.getElementById('discountAmount').textContent = 'Rp ' + formatNumber(discount);
    document.getElementById('taxAmount').textContent = 'Rp ' + formatNumber(tax);
    document.getElementById('total').textContent = 'Rp ' + formatNumber(total);
    
    // Update min limit pada payment input (tidak ada batasan maksimal)
    const paymentInput = document.getElementById('paymentInput');
    if (paymentInput) {
        paymentInput.setAttribute('min', total);
        paymentInput.removeAttribute('max');
    }
    
    calculateChange();
}

function updateTotals() {
    calculateTotals();
}

function limitPaymentInput() {
    const paymentInput = document.getElementById('paymentInput');
    let value = paymentInput.value.toString();
    
    // Hapus karakter non-numerik saja, tidak ada batasan maksimal
    value = value.replace(/[^\d]/g, '');
    
    // Set nilai yang sudah dibersihkan
    if (value !== paymentInput.value) {
        paymentInput.value = value;
    }
    
    // Hapus max attribute jika ada (tidak ada batasan maksimal)
    paymentInput.removeAttribute('max');
}

function calculateChange() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discountValue = parseFloat(document.getElementById('discountInput').value) || 0;
    let discount = 0;
    
    // Hitung discount berdasarkan tipe (sama seperti calculateTotals)
    if (discountType === 'percent') {
        // Validasi: persentase tidak boleh lebih dari 100
        if (discountValue > 100) {
            discountValue = 100;
        }
        discount = (subtotal * discountValue) / 100;
    } else {
        // Validasi: diskon tidak boleh lebih besar dari subtotal
        if (discountValue > subtotal) {
            discountValue = subtotal;
        }
        discount = discountValue;
    }
    
    const subtotalAfterDiscount = Math.max(0, subtotal - discount);
    const tax = subtotalAfterDiscount * 0.1; // 10% tax
    const total = subtotalAfterDiscount + tax;
    const paymentInput = document.getElementById('paymentInput');
    const originalValue = paymentInput.value.trim(); // Simpan nilai asli sebelum parsing, hapus spasi
    // Hapus semua karakter non-numerik (termasuk titik dan koma sebagai pemisah ribuan)
    const numericValue = originalValue.replace(/[^\d]/g, '');
    let payment = parseFloat(numericValue) || 0;
    
    const change = payment - total;
    
    const changeInput = document.getElementById('changeInput');
    if (change < 0) {
        changeInput.value = `Kurang: Rp ${formatNumber(Math.abs(change))}`;
        changeInput.classList.add('text-red-600', 'dark:text-red-400');
        changeInput.classList.remove('text-green-600', 'dark:text-green-400');
    } else if (change === 0) {
        changeInput.value = 'Rp 0';
        changeInput.classList.remove('text-red-600', 'dark:text-red-400', 'text-green-600', 'dark:text-green-400');
    } else {
        changeInput.value = `Rp ${formatNumber(change)}`;
        changeInput.classList.add('text-green-600', 'dark:text-green-400');
        changeInput.classList.remove('text-red-600', 'dark:text-red-400');
    }
}

function clearCart() {
    if (cart.length === 0) return;
    
    if (confirm('Hapus semua item dari keranjang?')) {
    cart = [];
    renderCart();
    document.getElementById('paymentInput').value = '';
    document.getElementById('discountInput').value = '0';
    calculateTotals();
    }
}

function processPayment() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discountValue = parseFloat(document.getElementById('discountInput').value) || 0;
    let discount = 0;
    
    // Hitung discount berdasarkan tipe
    if (discountType === 'percent') {
        if (discountValue > 100) discountValue = 100;
        discount = (subtotal * discountValue) / 100;
    } else {
        if (discountValue > subtotal) discountValue = subtotal;
        discount = discountValue;
    }
    
    const subtotalAfterDiscount = Math.max(0, subtotal - discount);
    const tax = subtotalAfterDiscount * 0.1; // 10% tax
    const total = subtotalAfterDiscount + tax;
    const paymentInput = document.getElementById('paymentInput');
    const originalValue = paymentInput.value.trim(); // Simpan nilai asli sebelum parsing, hapus spasi
    // Hapus semua karakter non-numerik (termasuk titik dan koma sebagai pemisah ribuan)
    const numericValue = originalValue.replace(/[^\d]/g, '');
    let payment = parseFloat(numericValue) || 0;
    
    if (payment < total) {
        alert('Jumlah pembayaran tidak mencukupi!');
        return;
    }
    
    if (cart.length === 0) {
        alert('Keranjang kosong!');
        return;
    }
    
    // Generate struk
    const change = payment - total;
    const cartCopy = JSON.parse(JSON.stringify(cart)); // Copy cart before reset
    
    // Simpan transaksi ke database
    saveTransaction(subtotal, discount, discountType, discountValue, tax, total, payment, change, cartCopy);
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function saveTransaction(subtotal, discount, discountTypeParam, discountValue, tax, total, payment, change, cartItems) {
    const transactionData = {
        items: cartItems.map(item => ({
            id: item.id,
            name: item.name,
            price: item.price,
            quantity: item.quantity
        })),
        subtotal: subtotal,
        diskon: discount,
        tipe_diskon: discountTypeParam,
        nilai_diskon: discountValue,
        tax: tax,
        total: total,
        bayar: payment,
        kembalian: change
    };

    fetch('{{ route("kasir.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(transactionData)
    })
    .then(async response => {
        // Cek content type
        const contentType = response.headers.get('content-type');
        let data;
        
        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
        } else {
            // Jika bukan JSON, coba parse sebagai text
            const text = await response.text();
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Response tidak valid: ' + text.substring(0, 100));
            }
        }
        
        if (!response.ok) {
            throw new Error(data.message || data.error || 'Server error: ' + response.status);
        }
        
        return data;
    })
    .then(data => {
        if (data.success) {
            // Update stock di cart items setelah transaksi berhasil
            cartItems.forEach(item => {
                const productCard = document.querySelector(`[data-product-id="${item.id}"]`);
                if (productCard) {
                    const stockElement = productCard.querySelector('.stock-display');
                    if (stockElement) {
                        const currentStock = parseInt(stockElement.getAttribute('data-stock') || stockElement.textContent) || 0;
                        const newStock = Math.max(0, currentStock - item.quantity);
                        stockElement.setAttribute('data-stock', newStock);
                        
                        if (newStock > 0) {
                            stockElement.textContent = newStock;
                            // Ambil threshold dari attribute atau default 10
                            const threshold = parseInt(stockElement.getAttribute('data-threshold')) || 10;
                            
                            // Cek threshold dulu, baru set warna
                            if (newStock < threshold) {
                                // Stok rendah: kuning
                                stockElement.className = 'stock-display text-xs bg-gradient-to-r from-yellow-600/90 to-yellow-700/90 text-white px-2.5 py-1 rounded-full font-bold shadow-lg backdrop-blur-sm';
                            } else {
                                // Stok cukup: hijau
                                stockElement.className = 'stock-display text-xs bg-gradient-to-r from-green-600/90 to-green-700/90 text-white px-2.5 py-1 rounded-full font-bold shadow-lg backdrop-blur-sm';
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
            });
            
            // Generate struk setelah transaksi disimpan (dilakukan setelah update stock)
            try {
                generateStruk(cartItems, subtotal, discount, discountTypeParam, discountValue, tax, total, payment, change);
            } catch (strukError) {
                console.error('Error generating struk:', strukError);
                // Jangan ganggu transaksi jika struk gagal
            }
            
            // Reset after payment
            cart = [];
            renderCart();
            document.getElementById('paymentInput').value = '';
            document.getElementById('discountInput').value = '0';
            discountType = 'rp';
            setDiscountType('rp');
            calculateTotals();
        } else {
            const errorMsg = data.message || 'Terjadi kesalahan saat memproses pembayaran';
            alert('Pembayaran error: ' + errorMsg);
            console.error('Payment error:', data);
        }
    })
    .catch(error => {
        console.error('Error detail:', error);
        const errorMessage = error.message || 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.';
        alert('Pembayaran error: ' + errorMessage);
    });
}

function generateStruk(cartItems, subtotal, discount, discountTypeParam, discountValue, tax, total, payment, change) {
    const now = new Date();
    const dateTime = now.toLocaleString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    const cabang = "{{ session('nama_cabang', 'Cabang') }}";
    const discountLabel = discountTypeParam === 'percent' ? discountValue + '%' : 'Rp ' + formatNumber(discountValue);
    
    let strukHTML = `
<!DOCTYPE html>
<html>
<head>
    <title>Struk Transaksi</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            color: black;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .item-name {
            flex: 2;
            font-weight: bold;
        }
        .item-qty {
            flex: 1;
            text-align: center;
        }
        .item-price {
            flex: 2;
            text-align: right;
        }
        .summary {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #000;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-row {
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #000;
            font-size: 0.9em;
        }
        .buttons {
            margin-top: 20px;
            text-align: center;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            border: 2px solid #dc2626;
            border-radius: 5px;
            background: white;
            color: #dc2626;
        }
        button:hover {
            background: #dc2626;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>WING CHICKEN</h2>
        <p style="font-size: 0.9em; margin: 5px 0;">Jl. Kebun Cengkeh, Samping Dedes Minimarket</p>
        <p style="font-size: 0.9em; margin: 5px 0;">Batu Merah, Kota Ambon, Maluku</p>
        <p style="margin-top: 10px; font-size: 0.85em;">${cabang}</p>
        <p style="font-size: 0.85em;">${dateTime}</p>
    </div>
    
    <div class="items">
        ${cartItems.map(item => `
            <div class="item-row">
                <div class="item-name">${item.name}</div>
                <div class="item-qty">${item.quantity}x</div>
                <div class="item-price">Rp ${formatNumber(item.price * item.quantity)}</div>
            </div>
            <div style="font-size: 0.85em; color: #666; padding-left: 10px;">
                Rp ${formatNumber(item.price)}/unit
            </div>
        `).join('')}
    </div>
    
    <div class="summary">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>Rp ${formatNumber(subtotal)}</span>
        </div>
        <div class="summary-row">
            <span>Diskon (${discountLabel}):</span>
            <span>Rp ${formatNumber(discount)}</span>
        </div>
        <div class="summary-row">
            <span>Tax (10%):</span>
            <span>Rp ${formatNumber(tax)}</span>
        </div>
        <div class="summary-row total-row">
            <span>TOTAL:</span>
            <span>Rp ${formatNumber(total)}</span>
        </div>
        <div class="summary-row">
            <span>Bayar:</span>
            <span>Rp ${formatNumber(payment)}</span>
        </div>
        <div class="summary-row total-row">
            <span>Kembalian:</span>
            <span>Rp ${formatNumber(change)}</span>
        </div>
    </div>
    
    <div class="footer">
        <p>Terima Kasih</p>
        <p>Selamat Menikmati</p>
    </div>
    
    <div class="buttons no-print">
        <button onclick="window.print()">🖨️ Print</button>
        <button onclick="window.close()">✕ Tutup</button>
    </div>
</body>
</html>`;
    
    try {
        const strukWindow = window.open('', '_blank', 'width=400,height=800');
        
        if (!strukWindow) {
            // Popup diblokir, tampilkan struk di console atau alert
            console.warn('Popup diblokir oleh browser. Struk HTML:', strukHTML);
            // Alternatif: tampilkan struk di alert atau modal
            if (confirm('Popup diblokir. Ingin melihat struk di console? (F12 untuk membuka Developer Tools)')) {
                console.log('Struk HTML:', strukHTML);
            }
            return;
        }
        
        // Tunggu sebentar untuk memastikan window sudah terbuka
        setTimeout(() => {
            try {
                if (strukWindow.document) {
                    strukWindow.document.write(strukHTML);
                    strukWindow.document.close();
                } else {
                    console.error('Tidak dapat mengakses document dari window baru');
                }
            } catch (e) {
                console.error('Error menulis ke struk window:', e);
                // Fallback: coba lagi atau tampilkan error
                alert('Gagal membuka struk. Silakan coba lagi atau cek popup blocker.');
            }
        }, 100);
    } catch (e) {
        console.error('Error membuka window struk:', e);
        alert('Gagal membuka struk. Pastikan popup tidak diblokir oleh browser.');
    }
}
</script>
@endpush
@endsection
