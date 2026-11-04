<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OmniKasir — POS Offline-Capable & Toko Online Terpadu')</title>
    <meta name="description" content="Sistem Kasir (POS) Offline-First dan Toko Online Terpadu untuk UMKM Indonesia. Satu database real-time untuk kasir, gudang, dan e-commerce.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full flex flex-col bg-slate-50 text-slate-900 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Demo Switcher Bar -->
    @include('components.demo-banner')

    <!-- Public Header Navigation -->
    <header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <a href="{{ route('public.home') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold tracking-tight text-slate-900 flex items-center gap-1.5">
                            OmniKasir
                            <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">Demo</span>
                        </div>
                        <div class="text-[11px] text-slate-500 hidden sm:block">POS Offline-First & Toko Online</div>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('public.home') }}#fitur" class="hover:text-emerald-600 transition">Fitur Utama</a>
                    <a href="{{ route('public.home') }}#tampilan" class="hover:text-emerald-600 transition">Arsitektur 3 Tampilan</a>
                    <a href="{{ route('public.home') }}#alur-kerja" class="hover:text-emerald-600 transition">Alur Kerja</a>
                    <a href="{{ route('store.catalog') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold transition flex items-center gap-1">
                        <span>Toko Online</span>
                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1 rounded">Publik</span>
                    </a>
                    <a href="{{ route('public.docs') }}" class="hover:text-emerald-600 transition">Dokumentasi</a>
                </nav>

                <!-- Action CTA & Mobile Button -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('demo.login') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm shadow-emerald-600/30 transition hover:shadow-md">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Masuk Demo
                    </a>

                    <!-- Hamburger button for mobile -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-cloak class="md:hidden py-3 border-t border-slate-200 space-y-2 text-sm">
                <a href="{{ route('public.home') }}#fitur" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md hover:bg-slate-100">Fitur Utama</a>
                <a href="{{ route('public.home') }}#tampilan" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md hover:bg-slate-100">Arsitektur 3 Tampilan</a>
                <a href="{{ route('public.home') }}#alur-kerja" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md hover:bg-slate-100">Alur Kerja Terpadu</a>
                <a href="{{ route('store.catalog') }}" class="block px-3 py-2 rounded-md text-indigo-600 font-semibold hover:bg-indigo-50">Toko Online Publik</a>
                <a href="{{ route('public.docs') }}" class="block px-3 py-2 rounded-md hover:bg-slate-100">Dokumentasi Produk & Modul</a>
                <a href="{{ route('demo.login') }}" class="block px-3 py-2 rounded-md text-emerald-700 font-semibold hover:bg-emerald-50">Pilih Peran Demo</a>
            </div>
        </div>
    </header>

    <!-- Global Flash Messages -->
    @if(session('success'))
    <div class="bg-emerald-50 border-b border-emerald-200 px-4 py-2.5 text-sm text-emerald-800 flex items-center justify-center space-x-2">
        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="bg-rose-50 border-b border-rose-200 px-4 py-2.5 text-sm text-rose-800 flex items-center justify-center space-x-2">
        <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        <span>{{ session('error') ?? $errors->first() }}</span>
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-50 border-b border-blue-200 px-4 py-2.5 text-sm text-blue-800 flex items-center justify-center space-x-2">
        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    <!-- Main Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">OmniKasir</span>
                    </div>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                        Aplikasi Kasir (POS) Offline-Capable dan Toko Online Terpadu dalam satu database MySQL. Dibangun khusus untuk mempermudah operasional ritel, kafe, butik, dan minimarket di Indonesia.
                    </p>
                    <div class="text-xs text-slate-500">
                        Kontak Pengembang & Kustomisasi: 
                        <a href="mailto:{{ config('demo.contact_email', 'nixresponding@gmail.com') }}" class="text-emerald-400 hover:underline font-mono">
                            {{ config('demo.contact_email', 'nixresponding@gmail.com') }}
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigasi Demo -->
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Navigasi Demo</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('public.home') }}" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="{{ route('demo.login') }}" class="hover:text-emerald-400 transition font-medium">Pilih Peran Demo</a></li>
                        <li><a href="{{ route('store.catalog') }}" class="hover:text-white transition">Toko Online Publik</a></li>
                        <li><a href="{{ route('public.docs') }}" class="hover:text-white transition">Dokumentasi Produk</a></li>
                    </ul>
                </div>

                <!-- Col 3: Peran Akses -->
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Peran Akses</h3>
                    <ul class="space-y-2 text-sm">
                        <li><span class="text-slate-300">👑 Pemilik / Admin</span> <span class="text-xs text-slate-500">(/admin)</span></li>
                        <li><span class="text-slate-300">💳 Kasir POS</span> <span class="text-xs text-slate-500">(/pos)</span></li>
                        <li><span class="text-slate-300">📦 Staf Gudang</span> <span class="text-xs text-slate-500">(/warehouse)</span></li>
                        <li><span class="text-slate-300">🛍️ Pelanggan Web</span> <span class="text-xs text-slate-500">(/store)</span></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} OmniKasir Nusantara. Demo Aplikasi Web Ritel Terpadu.</p>
                <p>
                    Dibuat dengan Laravel 13 &bull; Tailwind CSS 4 &bull; MySQL &bull; Flowbite &bull; Alpine.js
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
