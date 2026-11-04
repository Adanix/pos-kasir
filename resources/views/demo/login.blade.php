@extends('layouts.public')

@section('title', 'Pilih Peran Demo — OmniKasir')

@section('content')
<div class="py-12 bg-slate-50 min-h-[calc(100vh-16rem)] flex items-center justify-center">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                Akses Instan 1-Klik Tanpa Kata Sandi
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                Pilih Peran untuk Menjelajahi Demo OmniKasir
            </h1>
            <p class="text-sm text-slate-600">
                Pilih salah satu profil peran di bawah ini untuk menguji antarmuka, hak akses, dan alur kerja aplikasi secara langsung. Seluruh perubahan stok dan buku kas tersimpan di database MySQL.
            </p>

            @auth
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center justify-between">
                <span>Saat ini Anda sedang masuk sebagai: <strong>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="font-bold underline hover:text-rose-600">Keluar</button>
                </form>
            </div>
            @endauth
        </div>

        <!-- 3 Persona Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Persona 1: Admin / Owner -->
            <div class="rounded-2xl bg-white border-2 border-slate-200 hover:border-indigo-500 shadow-sm hover:shadow-md p-6 flex flex-col justify-between transition group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl">
                            👑
                        </div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                            Akses Penuh
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition">Pemilik / Admin</h3>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">owner@omnikasir.demo</div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Melihat seluruh gambaran bisnis: grafik omzet penjualan, laba kotor, Buku Kas Harian (Kas Masuk & Keluar), rekap absensi karyawan, hingga pengaturan hak akses.
                    </p>

                    <div class="space-y-1.5 pt-2 text-xs text-slate-700 border-t border-slate-100">
                        <div class="font-semibold text-slate-900 text-[11px] uppercase tracking-wider">Hak Akses Utama:</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Dashboard Penjualan & Laba</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Buku Kas Harian & Saldo Berjalan</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Rekap Absensi Seluruh Karyawan</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Akses Bebas ke POS & Gudang</div>
                    </div>
                </div>

                <div class="pt-6">
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="admin">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                            Masuk sebagai Pemilik / Admin &rarr;
                        </button>
                    </form>
                </div>
            </div>

            <!-- Persona 2: Cashier -->
            <div class="rounded-2xl bg-white border-2 border-emerald-500 shadow-sm hover:shadow-md p-6 flex flex-col justify-between transition group relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white shadow-sm uppercase tracking-wider">
                    Paling Direkomendasikan
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">
                            💳
                        </div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Kasir POS
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition">Kasir (Siti Aminah)</h3>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">kasir@omnikasir.demo</div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Melayani transaksi walk-in super cepat dengan sentuhan atau keyboard, menerima notifikasi antrean pesanan dari toko online, cetak struk thermal, dan menguji mode offline.
                    </p>

                    <div class="space-y-1.5 pt-2 text-xs text-slate-700 border-t border-slate-100">
                        <div class="font-semibold text-slate-900 text-[11px] uppercase tracking-wider">Hak Akses Utama:</div>
                        <div class="flex items-center gap-1.5 text-emerald-700">✓ Terminal Kasir Cepat & Numpad</div>
                        <div class="flex items-center gap-1.5 text-emerald-700">✓ Antrean Pesanan Toko Online</div>
                        <div class="flex items-center gap-1.5 text-emerald-700">✓ Cetak Struk 58mm / 80mm</div>
                        <div class="flex items-center gap-1.5 text-emerald-700">✓ Mode Offline & Buka/Tutup Shift</div>
                    </div>
                </div>

                <div class="pt-6">
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="cashier">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                            Masuk sebagai Kasir POS &rarr;
                        </button>
                    </form>
                </div>
            </div>

            <!-- Persona 3: Warehouse -->
            <div class="rounded-2xl bg-white border-2 border-slate-200 hover:border-amber-500 shadow-sm hover:shadow-md p-6 flex flex-col justify-between transition group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl">
                            📦
                        </div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                            Staf Gudang
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-amber-600 transition">Staf Gudang (Andi)</h3>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">gudang@omnikasir.demo</div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Mengelola data produk, kategori, barcode, menginput stok masuk supplier (restock), melakukan koreksi stock opname, dan memantau audit mutasi di Kartu Stok.
                    </p>

                    <div class="space-y-1.5 pt-2 text-xs text-slate-700 border-t border-slate-100">
                        <div class="font-semibold text-slate-900 text-[11px] uppercase tracking-wider">Hak Akses Utama:</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Manajemen Produk, Barcode, & Kategori</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Stok Masuk (Restock Supplier)</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Stock Opname / Penyesuaian</div>
                        <div class="flex items-center gap-1.5 text-slate-600">✓ Kartu Stok (Audit Mutasi Lengkap)</div>
                    </div>
                </div>

                <div class="pt-6">
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="warehouse">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition">
                            Masuk sebagai Staf Gudang &rarr;
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- 4th Option: Public Storefront Link -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl shrink-0">
                    🛍️
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ingin Menguji Sisi Pelanggan Toko Online?</h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Buka katalog publik toko online tanpa perlu login. Masukkan barang ke keranjang, buat pesanan dengan simulasi QRIS, dan saksikan pesanan Anda muncul seketika di layar Kasir POS!
                    </p>
                </div>
            </div>
            <a href="{{ route('store.catalog') }}" class="shrink-0 inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition">
                Buka Toko Online (/store) &rarr;
            </a>
        </div>

    </div>
</div>
@endsection
