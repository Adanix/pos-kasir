@extends('layouts.public')

@section('title', 'OmniKasir — POS Offline-First & Toko Online Terpadu')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-20 bg-gradient-to-b from-white via-slate-50 to-slate-100 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Sistem Kasir Offline-First & Toko Online Satu Database</span>
            </div>

            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Satukan <span class="text-emerald-600">Kasir Toko Fisik</span>, Gudang, dan <span class="text-indigo-600">Toko Online</span> dalam Satu Sistem Terpadu.
            </h1>

            <!-- Subtitle -->
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
                Kelola transaksi kasir super cepat dengan cetak struk thermal 58mm/80mm, pantau stok otomatis tanpa takut selisih, terima pesanan dari katalog online langsung di layar kasir, catat absensi staf, hingga rekap Buku Kas harian dalam Rupiah secara real-time.
            </p>

            <!-- CTAs -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="{{ route('demo.login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 text-base font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-lg shadow-emerald-600/30 transition hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Coba Demo Aplikasi
                </a>
                <a href="{{ route('store.catalog') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Lihat Toko Online Publik
                </a>
                <a href="{{ route('public.docs') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-slate-700 hover:text-slate-900 transition">
                    Baca Dokumentasi &rarr;
                </a>
            </div>

            <!-- Live Database Stats Strip -->
            <div class="pt-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                    <div class="text-center p-2">
                        <div class="text-2xl font-extrabold text-slate-900 font-mono">{{ $stats['active_products'] ?? 18 }}</div>
                        <div class="text-xs text-slate-500 font-medium">Produk Aktif di Katalog</div>
                    </div>
                    <div class="text-center p-2 border-l border-slate-100">
                        <div class="text-2xl font-extrabold text-slate-900 font-mono">{{ $stats['categories_count'] ?? 6 }}</div>
                        <div class="text-xs text-slate-500 font-medium">Kategori Ritel</div>
                    </div>
                    <div class="text-center p-2 border-t md:border-t-0 md:border-l border-slate-100">
                        <div class="text-2xl font-extrabold text-emerald-600 font-mono">{{ $stats['total_orders'] ?? 12 }}</div>
                        <div class="text-xs text-slate-500 font-medium">Transaksi Terproses</div>
                    </div>
                    <div class="text-center p-2 border-t md:border-t-0 md:border-l border-slate-100">
                        <div class="text-2xl font-extrabold text-indigo-600 font-mono">100%</div>
                        <div class="text-xs text-slate-500 font-medium">Sinkronisasi Real-Time</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3-Layout Architecture Showcase Section -->
<section id="tampilan" class="py-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">Arsitektur UI Berorientasi Fungsi</h2>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Tiga Tampilan Berbeda Sesuai Kebutuhan Nyata</h3>
            <p class="text-sm text-slate-600 mt-3">Tidak memaksakan satu tampilan admin kaku untuk semua orang. OmniKasir membagi antarmuka menjadi 3 ruang kerja terpisah namun terhubung ke database yang sama.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: POS Kasir -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">1. Layar Kasir (POS Terminal)</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Desain 2-kolom full-screen bebas sidebar. Tombol sentuh besar (min. 44px), shortcut keyboard (F2/F4/F8), antrean pesanan online masuk, simulasi mode offline dengan IndexedDB, dan cetak struk thermal 58mm/80mm.
                    </p>
                    <ul class="text-xs text-slate-700 space-y-1.5 pt-2">
                        <li class="flex items-center gap-1.5 text-emerald-700 font-medium">✓ Touch-friendly & Numpad cepat</li>
                        <li class="flex items-center gap-1.5 text-emerald-700 font-medium">✓ Cetak Struk 58mm / 80mm & ESC/POS</li>
                        <li class="flex items-center gap-1.5 text-emerald-700 font-medium">✓ Bekerja saat internet terputus (Offline)</li>
                    </ul>
                </div>
                <div class="pt-6">
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="cashier">
                        <button type="submit" class="w-full text-center py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition">
                            Coba Layar Kasir (/pos)
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card 2: Backoffice Admin & Gudang -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-800 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">2. Dashboard Pemilik & Gudang</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Layout dashboard Flowbite responsif dengan sidebar navigasi. Memantau Buku Kas Harian, analisa laba kotor, mutasi Kartu Stok, Stock Opname, absensi kehadiran staf, dan manajemen hak akses karyawan.
                    </p>
                    <ul class="text-xs text-slate-700 space-y-1.5 pt-2">
                        <li class="flex items-center gap-1.5 text-slate-800 font-medium">✓ Buku Kas Harian & Saldo Berjalan</li>
                        <li class="flex items-center gap-1.5 text-slate-800 font-medium">✓ Mutasi Stok Real-Time & Opname</li>
                        <li class="flex items-center gap-1.5 text-slate-800 font-medium">✓ Rekap Absensi & Kontrol Hak Akses</li>
                    </ul>
                </div>
                <div class="pt-6">
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="admin">
                        <button type="submit" class="w-full text-center py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition">
                            Buka Dashboard Pemilik (/admin)
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card 3: Toko Online Publik -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">3. Toko Online Publik (Storefront)</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Katalog produk e-commerce mobile-first untuk pelanggan umum. Filter kategori, keranjang belanja, checkout instan tanpa ribet daftar, simulasi pembayaran QRIS/VA, dan pelacakan pesanan otomatis ke kasir.
                    </p>
                    <ul class="text-xs text-slate-700 space-y-1.5 pt-2">
                        <li class="flex items-center gap-1.5 text-indigo-700 font-medium">✓ Checkout Mudah + Contoh Data Cepat</li>
                        <li class="flex items-center gap-1.5 text-indigo-700 font-medium">✓ Simulasi QRIS & Virtual Account</li>
                        <li class="flex items-center gap-1.5 text-indigo-700 font-medium">✓ Pelacakan Pesanan & E-Struk Real-Time</li>
                    </ul>
                </div>
                <div class="pt-6">
                    <a href="{{ route('store.catalog') }}" class="block w-full text-center py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        Jelajahi Toko Online (/store)
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Features Grid Section -->
<section id="fitur" class="py-16 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">Solusi Lengkap Bisnis Ritel</h2>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Tujuh Modul Andal Tanpa Aplikasi Terpisah</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">1</div>
                <h4 class="font-bold text-slate-900 text-base">Kasir POS & Cetak Struk Thermal</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Dukungan pencarian barcode instan, diskon per item / nota, pembayaran tunai kembalian, QRIS, dan cetak struk thermal 58mm/80mm serta bridge ESC/POS.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">2</div>
                <h4 class="font-bold text-slate-900 text-base">Mode Offline Cerdas (IndexedDB)</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Jual beli tetap berjalan lancar saat internet mati. Transaksi ditampung di antrean lokal browser dan disinkronkan aman tanpa duplikasi saat online kembali.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">3</div>
                <h4 class="font-bold text-slate-900 text-base">Manajemen Stok & Kartu Mutasi</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap item yang terjual di kasir maupun dipesan online langsung memotong stok. Riwayat mutasi tercatat abadi di Kartu Stok lengkap dengan peringatan stok menipis.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">4</div>
                <h4 class="font-bold text-slate-900 text-base">Toko Online & Integrasi Antrean POS</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pesanan dari pelanggan web otomatis muncul di layar kasir dengan badge notifikasi berkedip. Kasir tinggal konfirmasi, cetak struk packing, atau kirim kurir.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">5</div>
                <h4 class="font-bold text-slate-900 text-base">Buku Kas Harian (Daily Cashbook)</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Otomatis mencatat pemasukan dari kasir dan toko online, serta mencatat pengeluaran operasional toko (beli plastik, galon, listrik) dengan saldo berjalan akurat.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">6</div>
                <h4 class="font-bold text-slate-900 text-base">Absensi Karyawan & Pembagian Shift</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Fitur Absen Masuk dan Pulang untuk kasir dan staf gudang dengan pencatatan status tepat waktu atau terlambat, serta rekap lengkap untuk pemilik toko.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Interactive End-to-End Workflow Section -->
<section id="alur-kerja" class="py-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-2">Sinkronisasi Satu Database</h2>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Bagaimana Pesanan Online Mengalir Otomatis ke Kasir</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative">
                <div class="text-xs font-bold text-indigo-600 uppercase">Langkah 1</div>
                <h4 class="font-bold text-slate-900 text-sm">Pelanggan Checkout di Toko Online</h4>
                <p class="text-xs text-slate-600">Pelanggan memilih kopi dan camilan, memilih kurir atau ambil di toko, lalu membayar via simulasi QRIS.</p>
                <div class="text-[11px] font-mono text-emerald-600 bg-emerald-50 p-2 rounded-lg border border-emerald-100">
                    Stok langsung di-reservasi aman di MySQL
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative">
                <div class="text-xs font-bold text-indigo-600 uppercase">Langkah 2</div>
                <h4 class="font-bold text-slate-900 text-sm">Muncul di Layar Kasir POS</h4>
                <p class="text-xs text-slate-600">Layar kasir menampilkan badge antrean pesanan online masuk tanpa perlu me-refresh halaman.</p>
                <div class="text-[11px] font-mono text-indigo-600 bg-indigo-50 p-2 rounded-lg border border-indigo-100">
                    Status: Siap Disiapkan & Dipacking
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative">
                <div class="text-xs font-bold text-indigo-600 uppercase">Langkah 3</div>
                <h4 class="font-bold text-slate-900 text-sm">Gudang & Kasir Cetak Struk</h4>
                <p class="text-xs text-slate-600">Kasir memverifikasi pesanan, mencetak struk thermal 58mm/80mm untuk packing barang.</p>
                <div class="text-[11px] font-mono text-purple-600 bg-purple-50 p-2 rounded-lg border border-purple-100">
                    Nomor Resi kurir dibuat otomatis
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative">
                <div class="text-xs font-bold text-indigo-600 uppercase">Langkah 4</div>
                <h4 class="font-bold text-slate-900 text-sm">Masuk Otomatis ke Buku Kas</h4>
                <p class="text-xs text-slate-600">Uang hasil penjualan online langsung masuk ke pembukuan Buku Kas Harian toko sebagai Kas Masuk.</p>
                <div class="text-[11px] font-mono text-emerald-600 bg-emerald-50 p-2 rounded-lg border border-emerald-100">
                    Saldo Berjalan Rupiah terupdate otomatis
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Customization & Contact Callout Banner -->
<section class="py-14 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
            Kustomisasi & Integrasi Lanjutan
        </div>
        <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
            Butuh Sistem Kasir & Toko Online yang Disesuaikan untuk Bisnis Anda?
        </h3>
        <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl mx-auto">
            Aplikasi demo ini dapat dikembangkan lebih lanjut, diperluas, dan disesuaikan dengan alur bisnis Anda: integrasi payment gateway live (Midtrans/Xendit), sinkronisasi kurir real-time, integrasi WhatsApp API otomatis, atau cetak silent USB thermal printer.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="mailto:{{ config('demo.contact_email', 'nixresponding@gmail.com') }}" class="inline-flex items-center px-6 py-3.5 rounded-xl font-bold text-slate-900 bg-emerald-400 hover:bg-emerald-300 shadow-lg shadow-emerald-500/20 transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Hubungi Pengembang ({{ config('demo.contact_email', 'nixresponding@gmail.com') }})
            </a>
            <a href="{{ route('public.docs') }}" class="inline-flex items-center px-6 py-3.5 rounded-xl font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 transition text-sm">
                Baca Dokumentasi Lengkap
            </a>
        </div>
    </div>
</section>
@endsection
