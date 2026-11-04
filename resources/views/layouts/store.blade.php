<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toko Online') — OmniKasir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full flex flex-col antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Demo Banner -->
    @include('components.demo-banner')

    <!-- Top Store Header -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-sm/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Logo & Brand -->
                <a href="{{ route('store.catalog') }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <span class="text-base font-black tracking-tight text-slate-900 block leading-tight">OmniKasir Store</span>
                        <span class="text-[10px] text-emerald-600 font-semibold tracking-wider uppercase leading-none">Belanja Online & Ambil di Toko</span>
                    </div>
                </a>

                <!-- Search Bar -->
                <div class="hidden sm:block flex-1 max-w-md mx-4">
                    <form method="GET" action="{{ route('store.catalog') }}" class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari produk di katalog online..." 
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border-slate-200 rounded-xl text-xs focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </form>
                </div>

                <!-- Right Navigation -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('store.orders.lookup') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Lacak Pesanan</span>
                    </a>

                    @php
                        $cart = session()->get('cart', []);
                        $cartCount = array_sum(array_column($cart, 'qty'));
                    @endphp

                    <a href="{{ route('store.cart.index') }}" class="relative inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm shadow-emerald-600/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Keranjang</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-white text-emerald-800 text-[10px] font-mono font-black">
                            {{ $cartCount }}
                        </span>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-10 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <div class="space-y-3 md:col-span-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-black text-xs">OK</div>
                        <span class="text-sm font-bold text-slate-900">OmniKasir Storefront</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                        Platform e-commerce terintegrasi dengan terminal kasir fisik POS dan manajemen gudang backoffice. Sinkronisasi stok otomatis dan verifikasi pembayaran instan.
                    </p>
                    <p class="text-xs font-semibold text-slate-700">
                        Kontak Developer & Pemesanan: <a href="mailto:nixresponding@gmail.com" class="text-emerald-600 hover:underline">nixresponding@gmail.com</a>
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-900">Simulasi Pembayaran</div>
                    <ul class="space-y-1.5 text-slate-500">
                        <li>&bull; QRIS Standar Nasional (Simulator)</li>
                        <li>&bull; BCA / Mandiri Virtual Account</li>
                        <li>&bull; COD (Bayar Saat Barang Sampai)</li>
                        <li>&bull; Ambil Sendiri di Kasir Toko (BOPIS)</li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-900">Akses Cepat Demo</div>
                    <ul class="space-y-1.5 text-slate-500">
                        <li><a href="{{ route('public.home') }}" class="hover:text-emerald-600">Beranda Publik</a></li>
                        <li><a href="{{ route('public.docs') }}" class="hover:text-emerald-600">Dokumentasi Fitur & API</a></li>
                        <li><a href="{{ route('demo.login') }}" class="hover:text-emerald-600">Ganti Persona Role Demo</a></li>
                        <li><a href="{{ route('store.orders.lookup') }}" class="hover:text-emerald-600">Lacak Status Pesanan</a></li>
                    </ul>
                </div>

            </div>

            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-slate-400 text-[11px]">
                <p>&copy; {{ date('Y') }} OmniKasir. Hak Cipta Dilindungi Undang-Undang.</p>
                <p>Demo Interaktif &bull; Lingkungan Uji Coba</p>
            </div>
        </div>
    </footer>

</body>
</html>
