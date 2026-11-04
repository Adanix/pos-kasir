@extends('layouts.pos')

@section('title', 'Terminal Kasir POS')

@section('content')
<div class="flex-1 flex flex-col lg:flex-row overflow-hidden" 
     x-data="posApp()" 
     x-init="initPos()" 
     @keydown.window="handleGlobalKey($event)">

    <!-- LEFT SECTION: Product Catalog, Search & Categories -->
    <div class="flex-1 flex flex-col bg-slate-900 border-r border-slate-800 overflow-hidden">
        
        <!-- Search & Filter Bar -->
        <div class="p-3 sm:p-4 bg-slate-900 border-b border-slate-800 space-y-3">
            <div class="flex items-center gap-2">
                
                <!-- Barcode & Search Input -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           x-ref="searchInput"
                           x-model="searchQuery" 
                           @keydown.enter.prevent="handleBarcodeOrEnter()"
                           placeholder="Cari produk / scan barcode (F2)..." 
                           class="w-full pl-9 pr-24 py-2.5 bg-slate-800 border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    
                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center gap-1">
                        <button type="button" @click="handleBarcodeOrEnter()" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold rounded-lg transition">
                            Enter
                        </button>
                    </div>
                </div>

                <!-- Offline Simulator Toggle -->
                <button type="button" 
                        @click="toggleOfflineMode()" 
                        :class="isSimulatedOffline ? 'bg-amber-500/20 text-amber-300 border-amber-500' : 'bg-slate-800 text-slate-300 border-slate-700'"
                        class="px-3 py-2 border rounded-xl text-xs font-semibold flex items-center gap-2 transition hover:bg-slate-700 shrink-0">
                    <span class="w-2 h-2 rounded-full" :class="isSimulatedOffline ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400'"></span>
                    <span x-text="isSimulatedOffline ? 'Mode: Offline Sim' : 'Mode: Online'"></span>
                </button>

            </div>

            <!-- Offline Queue Notification Banner -->
            <div x-show="offlineQueue.length > 0" x-cloak class="p-2.5 bg-amber-950/70 border border-amber-600/50 rounded-xl flex items-center justify-between text-xs text-amber-200">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Ada <strong><span x-text="offlineQueue.length"></span> transaksi offline</strong> tersimpan di lokal browser.</span>
                </div>
                <button type="button" 
                        @click="syncOfflineQueue()" 
                        :disabled="isSyncing || isSimulatedOffline"
                        class="px-3 py-1 bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-slate-950 font-bold text-[11px] rounded-lg shadow transition">
                    <span x-text="isSyncing ? 'Menyinkronkan...' : 'Sinkronkan Sekarang'"></span>
                </button>
            </div>

            <!-- Category Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                <button type="button" 
                        @click="selectedCategory = null"
                        :class="selectedCategory === null ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                    Semua ({{ count($products) }})
                </button>
                @foreach($categories as $cat)
                    <button type="button" 
                            @click="selectedCategory = {{ $cat->id }}"
                            :class="selectedCategory === {{ $cat->id }} ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                        {{ $cat->name }} ({{ $cat->products_count }})
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Products Touch Grid -->
        <div class="flex-1 p-3 sm:p-4 overflow-y-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
                <template x-for="item in filteredProducts" :key="item.id">
                    <button type="button" 
                            @click="addToCart(item)"
                            :disabled="item.stock_qty <= 0"
                            class="group relative flex flex-col text-left bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 hover:border-emerald-500/80 rounded-2xl p-3 transition disabled:opacity-40 disabled:cursor-not-allowed shadow-sm select-none">
                        
                        <!-- Stock Badge -->
                        <div class="flex items-center justify-between gap-1 mb-2">
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md font-semibold"
                                  :class="item.stock_qty <= 0 ? 'bg-rose-900/60 text-rose-300' : (item.stock_qty <= 5 ? 'bg-amber-900/60 text-amber-300' : 'bg-slate-700 text-slate-300')"
                                  x-text="item.stock_qty <= 0 ? 'Habis' : 'Stok: ' + item.stock_qty">
                            </span>
                            <span class="text-[9px] font-mono text-slate-400 truncate max-w-[60px]" x-text="item.sku"></span>
                        </div>

                        <!-- Product Name -->
                        <div class="flex-1">
                            <h3 class="text-xs font-bold text-white group-hover:text-emerald-400 line-clamp-2 transition leading-snug" x-text="item.name"></h3>
                        </div>

                        <!-- Price -->
                        <div class="mt-2 pt-2 border-t border-slate-700/60 flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-emerald-400" x-text="formatRupiah(item.selling_price)"></span>
                            <span class="w-6 h-6 rounded-lg bg-emerald-600/20 text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center text-xs font-bold transition">
                                +
                            </span>
                        </div>
                    </button>
                </template>
            </div>

            <!-- Empty Search Results -->
            <div x-show="filteredProducts.length === 0" x-cloak class="py-16 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm">Tidak ada produk yang cocok dengan pencarian.</p>
            </div>
        </div>

    </div>

    <!-- RIGHT SECTION: Active Cart & Cashier Register -->
    <div class="w-full lg:w-[420px] xl:w-[460px] bg-slate-950 flex flex-col border-t lg:border-t-0 border-slate-800 shrink-0">
        
        <!-- Cart Header -->
        <div class="p-3 sm:p-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Keranjang Transaksi</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-mono" x-text="cartTotalItems + ' item'"></span>
                </h2>
                <div class="text-[11px] text-slate-400">Kasir: {{ Auth::user()->name }}</div>
            </div>
            <button type="button" 
                    @click="clearCart()" 
                    x-show="cart.length > 0"
                    class="px-2.5 py-1 text-rose-400 hover:text-rose-300 text-xs font-semibold hover:bg-rose-950/40 rounded-lg transition">
                Kosongkan
            </button>
        </div>

        <!-- Customer Name / Notes -->
        <div class="px-4 py-2 bg-slate-900/60 border-b border-slate-800 flex items-center gap-2">
            <input type="text" 
                   x-model="customerName" 
                   placeholder="Nama Pelanggan (opsional)" 
                   class="flex-1 py-1 px-2.5 bg-slate-800 border-slate-700 rounded-lg text-xs text-white placeholder-slate-400 focus:border-emerald-500">
            <input type="text" 
                   x-model="customerPhone" 
                   placeholder="No. WhatsApp" 
                   class="w-32 py-1 px-2.5 bg-slate-800 border-slate-700 rounded-lg text-xs text-white placeholder-slate-400 focus:border-emerald-500 font-mono">
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            <template x-for="(cartItem, idx) in cart" :key="cartItem.product_id">
                <div class="p-3 bg-slate-900 border border-slate-800 rounded-xl space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <h4 class="text-xs font-bold text-white leading-tight" x-text="cartItem.name"></h4>
                            <div class="text-[11px] font-mono text-emerald-400 mt-0.5" x-text="formatRupiah(cartItem.selling_price)"></div>
                        </div>
                        <button type="button" @click="removeFromCart(idx)" class="text-slate-500 hover:text-rose-400 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>

                    <!-- Quantity controls and Line Total -->
                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                        <div class="flex items-center gap-1.5 bg-slate-800 rounded-lg p-1">
                            <button type="button" @click="updateQty(idx, -1)" class="w-6 h-6 rounded bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold flex items-center justify-center">-</button>
                            <input type="number" 
                                   :value="cartItem.qty" 
                                   @change="setQty(idx, $event.target.value)"
                                   min="1" 
                                   :max="cartItem.stock_qty"
                                   class="w-10 text-center bg-transparent border-0 text-xs font-mono font-bold text-white p-0 focus:ring-0">
                            <button type="button" @click="updateQty(idx, 1)" class="w-6 h-6 rounded bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold flex items-center justify-center">+</button>
                        </div>

                        <div class="text-right">
                            <div class="text-xs font-mono font-bold text-white" x-text="formatRupiah((cartItem.selling_price * cartItem.qty) - (cartItem.discount || 0))"></div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Cart Empty -->
            <div x-show="cart.length === 0" class="py-16 text-center text-slate-500 space-y-2">
                <svg class="w-12 h-12 mx-auto opacity-30 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <p class="text-xs">Keranjang masih kosong.</p>
                <p class="text-[11px] text-slate-500">Klik produk di katalog atau scan barcode barang.</p>
            </div>
        </div>

        <!-- Totals & Payment Drawer -->
        <div class="p-4 bg-slate-900 border-t border-slate-800 space-y-3">
            
            <!-- Summary Lines -->
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>Subtotal</span>
                    <span class="font-mono text-slate-200" x-text="formatRupiah(subtotal)"></span>
                </div>

                <div class="flex items-center justify-between text-slate-400">
                    <span class="flex items-center gap-1">
                        <span>Diskon Transaksi</span>
                    </span>
                    <input type="number" 
                           x-model.number="orderDiscount" 
                           min="0" 
                           placeholder="0"
                           class="w-24 text-right py-0.5 px-2 bg-slate-800 border-slate-700 rounded text-xs font-mono text-white focus:border-emerald-500">
                </div>

                <div class="flex items-center justify-between text-slate-400">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" x-model="applyTax" class="rounded bg-slate-800 border-slate-700 text-emerald-600 focus:ring-emerald-500">
                        <span>PPN (11%)</span>
                    </label>
                    <span class="font-mono text-slate-200" x-text="formatRupiah(taxAmount)"></span>
                </div>

                <div class="pt-2 border-t border-slate-800 flex justify-between items-baseline">
                    <span class="text-sm font-bold text-white">Total Tagihan</span>
                    <span class="text-xl font-black font-mono text-emerald-400" x-text="formatRupiah(grandTotal)"></span>
                </div>
            </div>

            <!-- Payment Method Tabs -->
            <div class="grid grid-cols-4 gap-1.5 pt-1">
                <button type="button" 
                        @click="paymentMethod = 'cash'" 
                        :class="paymentMethod === 'cash' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                        class="py-2 text-center rounded-xl text-xs transition">
                    Tunai
                </button>
                <button type="button" 
                        @click="paymentMethod = 'qris'" 
                        :class="paymentMethod === 'qris' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                        class="py-2 text-center rounded-xl text-xs transition">
                    QRIS
                </button>
                <button type="button" 
                        @click="paymentMethod = 'bank_transfer'" 
                        :class="paymentMethod === 'bank_transfer' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                        class="py-2 text-center rounded-xl text-xs transition">
                    Transfer
                </button>
                <button type="button" 
                        @click="paymentMethod = 'debit_card'" 
                        :class="paymentMethod === 'debit_card' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                        class="py-2 text-center rounded-xl text-xs transition">
                    Debit
                </button>
            </div>

            <!-- Cash Input & Fast Cash Buttons (If Cash) -->
            <div x-show="paymentMethod === 'cash'" class="space-y-2 pt-1">
                <div class="flex items-center gap-2">
                    <div class="text-xs text-slate-400 shrink-0">Uang Diterima:</div>
                    <input type="number" 
                           x-model.number="cashReceived" 
                           placeholder="0" 
                           class="flex-1 py-1.5 px-3 bg-slate-800 border-slate-700 rounded-xl text-sm font-mono font-bold text-white text-right focus:border-emerald-500">
                </div>

                <!-- Fast Preset Chips -->
                <div class="grid grid-cols-4 gap-1.5 text-xs font-mono">
                    <button type="button" @click="cashReceived = grandTotal" class="py-1 px-1 bg-slate-800 hover:bg-slate-700 text-emerald-400 rounded-lg text-center font-bold">Uang Pas</button>
                    <button type="button" @click="cashReceived = 20000" class="py-1 px-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-center">20.000</button>
                    <button type="button" @click="cashReceived = 50000" class="py-1 px-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-center">50.000</button>
                    <button type="button" @click="cashReceived = 100000" class="py-1 px-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-center">100.000</button>
                </div>

                <!-- Kembalian Display -->
                <div class="p-2 bg-slate-950 rounded-xl border border-slate-800 flex justify-between items-center text-xs">
                    <span class="text-slate-400">Kembalian:</span>
                    <span class="font-mono font-bold text-base" :class="changeAmount < 0 ? 'text-rose-400' : 'text-emerald-400'" x-text="changeAmount < 0 ? 'Kurang ' + formatRupiah(Math.abs(changeAmount)) : formatRupiah(changeAmount)"></span>
                </div>
            </div>

            <!-- Checkout CTA Button -->
            <button type="button" 
                    @click="executeCheckout()" 
                    :disabled="cart.length === 0 || isProcessing || (paymentMethod === 'cash' && changeAmount < 0)"
                    class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-900/30 transition flex items-center justify-center gap-2">
                <svg x-show="!isProcessing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span x-text="isProcessing ? 'Memproses Transaksi...' : (isSimulatedOffline ? 'Simpan Transaksi Offline (F9)' : 'Bayar Transaksi (F9)')"></span>
            </button>

        </div>

    </div>

    <!-- SUCCESS & RECEIPT MODAL -->
    <div x-show="receiptModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="receiptModal = false" class="bg-white text-slate-900 rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4">
            
            <div class="text-center space-y-1">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Transaksi Berhasil!</h3>
                <p class="text-xs text-slate-500 font-mono" x-text="lastOrder ? lastOrder.order_number : ''"></p>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Belanja:</span>
                    <span class="font-bold font-mono" x-text="lastOrder ? formatRupiah(lastOrder.grand_total) : '0'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Uang Diterima:</span>
                    <span class="font-mono" x-text="lastOrder ? formatRupiah(lastOrder.cash_received) : '0'"></span>
                </div>
                <div class="flex justify-between pt-1 border-t border-slate-200 font-bold text-emerald-600">
                    <span>Kembalian:</span>
                    <span class="font-mono text-sm" x-text="lastOrder ? formatRupiah(lastOrder.change_amount) : '0'"></span>
                </div>
            </div>

            <!-- Print Actions -->
            <div class="space-y-2">
                <a :href="lastOrder ? lastOrder.receipt_url + '?width=58' : '#'" target="_blank" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Struk Thermal (58mm)</span>
                </a>

                <a :href="lastOrder ? lastOrder.receipt_url + '?width=80' : '#'" target="_blank" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center justify-center gap-2 transition">
                    <span>Cetak Lebar (80mm)</span>
                </a>

                <button type="button" @click="receiptModal = false; focusSearch();" class="w-full py-2 text-slate-500 hover:text-slate-700 text-xs font-semibold">
                    Tutup / Transaksi Baru (Esc)
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function posApp() {
    return {
        products: @json($products),
        categories: @json($categories),
        searchQuery: '',
        selectedCategory: null,
        cart: [],
        customerName: '',
        customerPhone: '',
        orderDiscount: 0,
        applyTax: false,
        paymentMethod: 'cash',
        cashReceived: 0,
        isProcessing: false,
        isSimulatedOffline: false,
        isSyncing: false,
        offlineQueue: [],
        receiptModal: false,
        lastOrder: null,

        initPos() {
            // Load offline queue from localStorage
            try {
                const storedQueue = localStorage.getItem('omnikasir_offline_queue');
                if (storedQueue) {
                    this.offlineQueue = JSON.parse(storedQueue);
                }
            } catch (e) {
                this.offlineQueue = [];
            }

            // Sync catalog snapshot to local storage for offline use
            this.cacheCatalogLocally();

            this.focusSearch();
        },

        focusSearch() {
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        },

        handleGlobalKey(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                this.focusSearch();
            } else if (e.key === 'F9') {
                e.preventDefault();
                if (this.cart.length > 0 && !this.isProcessing) {
                    this.executeCheckout();
                }
            } else if (e.key === 'Escape') {
                if (this.receiptModal) {
                    this.receiptModal = false;
                    this.focusSearch();
                }
            }
        },

        get filteredProducts() {
            let list = this.products;
            if (this.selectedCategory !== null) {
                list = list.filter(p => p.category_id === this.selectedCategory);
            }
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase().trim();
                list = list.filter(p => 
                    p.name.toLowerCase().includes(q) || 
                    (p.sku && p.sku.toLowerCase().includes(q)) || 
                    (p.barcode && p.barcode.toLowerCase().includes(q))
                );
            }
            return list;
        },

        handleBarcodeOrEnter() {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return;

            // Direct barcode match
            const exactMatch = this.products.find(p => (p.barcode && p.barcode.toLowerCase() === q) || (p.sku && p.sku.toLowerCase() === q));
            if (exactMatch) {
                this.addToCart(exactMatch);
                this.searchQuery = '';
                return;
            }

            // If exactly one match in filtered list
            const currentFiltered = this.filteredProducts;
            if (currentFiltered.length === 1) {
                this.addToCart(currentFiltered[0]);
                this.searchQuery = '';
            }
        },

        addToCart(product) {
            if (product.stock_qty <= 0) {
                alert('Stok produk ' + product.name + ' habis!');
                return;
            }

            const existingIdx = this.cart.findIndex(i => i.product_id === product.id);
            if (existingIdx > -1) {
                if (this.cart[existingIdx].qty >= product.stock_qty) {
                    alert('Kuantitas melebihi sisa stok fisik (' + product.stock_qty + ')!');
                    return;
                }
                this.cart[existingIdx].qty += 1;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    sku: product.sku,
                    selling_price: parseFloat(product.selling_price),
                    stock_qty: product.stock_qty,
                    qty: 1,
                    discount: 0
                });
            }
        },

        updateQty(idx, change) {
            const item = this.cart[idx];
            const newQty = item.qty + change;
            if (newQty <= 0) {
                this.removeFromCart(idx);
            } else if (newQty > item.stock_qty) {
                alert('Stok fisik produk hanya ' + item.stock_qty + '!');
            } else {
                item.qty = newQty;
            }
        },

        setQty(idx, val) {
            const item = this.cart[idx];
            let q = parseInt(val) || 1;
            if (q < 1) q = 1;
            if (q > item.stock_qty) {
                alert('Stok fisik produk hanya ' + item.stock_qty + '!');
                q = item.stock_qty;
            }
            item.qty = q;
        },

        removeFromCart(idx) {
            this.cart.splice(idx, 1);
        },

        clearCart() {
            if (confirm('Kosongkan semua item di keranjang?')) {
                this.cart = [];
                this.orderDiscount = 0;
                this.customerName = '';
                this.customerPhone = '';
                this.cashReceived = 0;
            }
        },

        get cartTotalItems() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.selling_price * item.qty) - (item.discount || 0), 0);
        },

        get taxAmount() {
            if (!this.applyTax) return 0;
            const taxable = Math.max(0, this.subtotal - (this.orderDiscount || 0));
            return taxable * 0.11;
        },

        get grandTotal() {
            const total = Math.max(0, this.subtotal - (this.orderDiscount || 0)) + this.taxAmount;
            return Math.round(total);
        },

        get changeAmount() {
            if (this.paymentMethod !== 'cash') return 0;
            return (this.cashReceived || 0) - this.grandTotal;
        },

        formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        },

        toggleOfflineMode() {
            this.isSimulatedOffline = !this.isSimulatedOffline;
            if (this.isSimulatedOffline) {
                console.log('POS berpindah ke mode OFFLINE simulation.');
            } else {
                console.log('POS kembali ke mode ONLINE.');
            }
        },

        cacheCatalogLocally() {
            try {
                localStorage.setItem('omnikasir_products_cache', JSON.stringify(this.products));
                localStorage.setItem('omnikasir_categories_cache', JSON.stringify(this.categories));
            } catch (e) {
                console.warn('Gagal menyimpan cache katalog ke localStorage', e);
            }
        },

        async executeCheckout() {
            if (this.cart.length === 0) return;
            if (this.paymentMethod === 'cash' && this.changeAmount < 0) {
                alert('Nominal uang tunai kurang dari total tagihan!');
                return;
            }

            this.isProcessing = true;
            const clientTxId = 'TX-OFFLINE-' + Date.now() + '-' + Math.random().toString(36).substr(2, 6).toUpperCase();

            const payload = {
                client_tx_id: clientTxId,
                customer_name: this.customerName || 'Pelanggan Kasir',
                customer_phone: this.customerPhone || null,
                payment_method: this.paymentMethod,
                discount_order: this.orderDiscount || 0,
                apply_tax: this.applyTax,
                cash_received: this.paymentMethod === 'cash' ? this.cashReceived : this.grandTotal,
                items: this.cart.map(i => ({
                    product_id: i.product_id,
                    qty: i.qty,
                    discount: i.discount || 0
                }))
            };

            // IF SIMULATED OFFLINE OR NAVIGATOR OFFLINE
            if (this.isSimulatedOffline || !navigator.onLine) {
                // Save to local offline queue
                this.offlineQueue.push(payload);
                localStorage.setItem('omnikasir_offline_queue', JSON.stringify(this.offlineQueue));

                // Deduct local product stock
                payload.items.forEach(it => {
                    const prod = this.products.find(p => p.id === it.product_id);
                    if (prod) {
                        prod.stock_qty = Math.max(0, prod.stock_qty - it.qty);
                    }
                });

                this.lastOrder = {
                    order_number: 'OFFLINE-' + clientTxId.substr(-8),
                    grand_total: this.grandTotal,
                    cash_received: payload.cash_received,
                    change_amount: this.changeAmount,
                    receipt_url: '#'
                };

                this.isProcessing = false;
                this.cart = [];
                this.receiptModal = true;
                return;
            }

            // ONLINE CHECKOUT
            try {
                const response = await fetch("{{ route('pos.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memproses transaksi di server.');
                }

                // Update local product stock
                payload.items.forEach(it => {
                    const prod = this.products.find(p => p.id === it.product_id);
                    if (prod) {
                        prod.stock_qty = Math.max(0, prod.stock_qty - it.qty);
                    }
                });

                this.lastOrder = data.order;
                this.cart = [];
                this.customerName = '';
                this.customerPhone = '';
                this.orderDiscount = 0;
                this.cashReceived = 0;
                this.receiptModal = true;
            } catch (err) {
                alert('Kesalahan Transaksi: ' + err.message);
            } finally {
                this.isProcessing = false;
            }
        },

        async syncOfflineQueue() {
            if (this.offlineQueue.length === 0 || this.isSyncing) return;
            this.isSyncing = true;

            try {
                const response = await fetch("{{ route('pos.sync.offline') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ transactions: this.offlineQueue })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    this.offlineQueue = [];
                    localStorage.removeItem('omnikasir_offline_queue');
                } else {
                    alert('Sinkronisasi sebagian mengalami kendala.');
                }
            } catch (err) {
                alert('Gagal menyinkronkan data offline: ' + err.message);
            } finally {
                this.isSyncing = false;
            }
        }
    };
}
</script>
@endpush
@endsection
