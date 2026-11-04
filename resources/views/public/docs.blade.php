@extends('layouts.public')

@section('title', 'Dokumentasi Produk & Panduan Sistem — OmniKasir')

@section('content')
<div class="py-10 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 mb-2">
                    Dokumentasi Resmi & Panduan Pengguna
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dokumentasi Produk OmniKasir</h1>
                <p class="text-sm text-slate-600 mt-1">Panduan lengkap arsitektur, hak akses pengguna, alur operasional per modul, dan integrasi sistem.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('demo.login') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition">
                    Coba Demo Sekarang &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Desktop Sticky Table of Contents (Left 3 cols) -->
        <aside class="hidden lg:block lg:col-span-3 sticky top-24 space-y-4">
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm text-xs space-y-2">
                <div class="font-bold uppercase tracking-wider text-slate-400 text-[10px] mb-2">Daftar Isi Dokumentasi</div>
                <nav class="space-y-1 font-medium text-slate-600">
                    <a href="#ringkasan" class="block py-1.5 px-2 rounded hover:bg-slate-100 hover:text-emerald-700 transition">1. Ringkasan & Arsitektur</a>
                    <a href="#panduan-masuk" class="block py-1.5 px-2 rounded hover:bg-slate-100 hover:text-emerald-700 transition">2. Cara Masuk & Persona Demo</a>
                    <a href="#hak-akses" class="block py-1.5 px-2 rounded hover:bg-slate-100 hover:text-emerald-700 transition">3. Matriks Hak Akses (RBAC)</a>
                    <a href="#modul-aplikasi" class="block py-1.5 px-2 rounded hover:bg-slate-100 hover:text-emerald-700 transition">4. Panduan 7 Modul Inti</a>
                    <a href="#simulasi-batasan" class="block py-1.5 px-2 rounded hover:bg-slate-100 hover:text-emerald-700 transition">5. Layanan Simulasi & Batasan</a>
                    <a href="#kustomisasi-kontak" class="block py-1.5 px-2 rounded hover:bg-emerald-50 text-emerald-800 font-bold transition">6. Kustomisasi & Kontak</a>
                </nav>
            </div>

            <div class="p-4 bg-gradient-to-br from-slate-900 to-indigo-950 rounded-2xl text-white text-xs space-y-2">
                <div class="font-semibold text-emerald-400">Butuh Bantuan?</div>
                <p class="text-slate-300 text-[11px] leading-relaxed">Hubungi tim pengembang untuk diskusi teknis atau kustomisasi fitur:</p>
                <a href="mailto:{{ config('demo.contact_email', 'nixresponding@gmail.com') }}" class="block font-mono text-emerald-300 hover:underline text-[11px]">
                    {{ config('demo.contact_email', 'nixresponding@gmail.com') }}
                </a>
            </div>
        </aside>

        <!-- Main Documentation Body (Right 9 cols) -->
        <div class="lg:col-span-9 space-y-12 text-slate-800 leading-relaxed text-sm">

            <!-- Section 1: Ringkasan -->
            <section id="ringkasan" class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Bab 1</div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Ringkasan Produk & Arsitektur</h2>
                <p>
                    <strong>OmniKasir</strong> adalah platform web ritel terintegrasi yang menyatukan operasional kasir toko fisik (POS), manajemen inventori gudang, dan e-commerce toko online publik dalam satu database relasional MySQL yang sama.
                </p>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                    <div class="font-bold text-slate-900">Prinsip Desain Utama:</div>
                    <ul class="list-disc list-inside space-y-1 text-slate-600">
                        <li><strong>Tiga Layout Khusus:</strong> Menggunakan antarmuka terpisah untuk Kasir POS (ramah sentuh & shortcut), Dashboard Admin/Gudang (sidebar Flowbite), dan Toko Online Publik (mobile-first).</li>
                        <li><strong>Integritas Database Atomik:</strong> Setiap penjualan POS dan pesanan online langsung memotong stok dengan penguncian baris (<code>lockForUpdate</code>) dan mencatat mutasi di Kartu Stok serta Buku Kas Harian.</li>
                        <li><strong>Mode Offline Tangguh:</strong> Menggunakan IndexedDB di browser kasir untuk mencatat transaksi saat internet mati, lalu sinkronisasi batch aman tanpa duplikasi dengan token UUID.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 2: Panduan Masuk -->
            <section id="panduan-masuk" class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Bab 2</div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Cara Masuk & Eksplorasi Demo</h2>
                <p>
                    Aplikasi dilengkapi dengan mekanisme <strong>Quick Login 1-Klik</strong> di halaman <a href="{{ route('demo.login') }}" class="text-emerald-600 font-bold hover:underline">/demo/login</a>. Anda tidak perlu mengetikkan username atau password:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                        <div class="font-bold text-slate-900">👑 Pemilik / Admin</div>
                        <div class="text-[11px] text-slate-500 font-mono">owner@omnikasir.demo</div>
                        <p class="text-slate-600 mt-1">Akses penuh ke dashboard, Buku Kas, analitik, dan absensi.</p>
                    </div>
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                        <div class="font-bold text-emerald-700">💳 Kasir POS</div>
                        <div class="text-[11px] text-slate-500 font-mono">kasir@omnikasir.demo</div>
                        <p class="text-slate-600 mt-1">Layar terminal POS kasir, antrean online, struk thermal, dan shift.</p>
                    </div>
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                        <div class="font-bold text-amber-700">📦 Staf Gudang</div>
                        <div class="text-[11px] text-slate-500 font-mono">gudang@omnikasir.demo</div>
                        <p class="text-slate-600 mt-1">Katalog produk, stok masuk, stock opname, dan Kartu Stok.</p>
                    </div>
                </div>
            </section>

            <!-- Section 3: Hak Akses RBAC -->
            <section id="hak-akses" class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Bab 3</div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Pembagian Hak Akses (Role-Permission Matrix)</h2>
                <p>Sistem menerapkan pengamanan di sisi server menggunakan middleware <code>role:admin,cashier,warehouse</code>:</p>
                
                <div class="overflow-x-auto w-full rounded-xl border border-slate-200">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-100 text-slate-700 uppercase font-semibold">
                            <tr>
                                <th class="p-3">Fitur / Modul</th>
                                <th class="p-3 text-center">Pemilik (Admin)</th>
                                <th class="p-3 text-center">Kasir (Cashier)</th>
                                <th class="p-3 text-center">Staf Gudang</th>
                                <th class="p-3 text-center">Pelanggan Publik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr>
                                <td class="p-3 font-medium">Terminal POS & Checkout (/pos)</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Penuh</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Penuh</td>
                                <td class="p-3 text-center text-rose-500 font-bold">✗ (403)</td>
                                <td class="p-3 text-center text-slate-400">✗ Login</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">Antrean Pesanan Online (/pos/online-orders)</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Penuh</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Konfirmasi & Cetak</td>
                                <td class="p-3 text-center text-amber-600 font-bold">✓ Status Packing</td>
                                <td class="p-3 text-center text-slate-400">✗ Login</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">Manajemen Produk & Kategori</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Inkl. Harga Modal</td>
                                <td class="p-3 text-center text-slate-400">Read-Only di POS</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Penuh</td>
                                <td class="p-3 text-center text-slate-400">Katalog Publik</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">Buku Kas Harian (Cashbook) (/admin/cashbook)</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Penuh</td>
                                <td class="p-3 text-center text-amber-600 font-bold">Kas Shift Kasir</td>
                                <td class="p-3 text-center text-rose-500 font-bold">✗ (403)</td>
                                <td class="p-3 text-center text-rose-500 font-bold">✗ (403)</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">Rekap Absensi Karyawan (/admin/attendance)</td>
                                <td class="p-3 text-center text-emerald-600 font-bold">✓ Rekap Seluruh Staf</td>
                                <td class="p-3 text-center text-slate-500">Absen Pribadi</td>
                                <td class="p-3 text-center text-slate-500">Absen Pribadi</td>
                                <td class="p-3 text-center text-rose-500 font-bold">✗ (403)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Section 4: Panduan 7 Modul Inti -->
            <section id="modul-aplikasi" class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-6">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Bab 4</div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Panduan Operasional 7 Modul Inti</h2>

                <div class="space-y-6">
                    <div class="border-l-4 border-emerald-500 pl-4 space-y-2">
                        <h3 class="text-base font-bold text-slate-900">4.1 Layar Kasir (POS Terminal) & Cetak Struk Thermal</h3>
                        <p class="text-xs text-slate-600">
                            Kasir dapat mencari barang melalui ketikan nama, scan barcode EAN-13, atau memilih kategori. Pembayaran mendukung Tunai (dengan kalkulator kembalian dan tombol nominal cepat), simulasi QRIS Dinamis, dan Transfer Bank. Setelah selesai, dialog cetak struk menyediakan pilihan format <strong>58mm</strong> dan <strong>80mm</strong> serta simulasi bridge ESC/POS.
                        </p>
                    </div>

                    <div class="border-l-4 border-amber-500 pl-4 space-y-2">
                        <h3 class="text-base font-bold text-slate-900">4.2 Mode Offline Kasir (IndexedDB Outbox)</h3>
                        <p class="text-xs text-slate-600">
                            Di bagian atas layar kasir terdapat tombol toggle <strong>Simulasi Mode Offline</strong>. Saat mode ini diaktifkan, transaksi tunai tetap dapat dilayani dan disimpan ke IndexedDB browser kasir. Ketika koneksi pulih, tombol <strong>Sinkronkan Sekarang</strong> akan mengirimkan transaksi tertunda secara aman ke server tanpa risiko dobel stok atau duplikasi ID.
                        </p>
                    </div>

                    <div class="border-l-4 border-indigo-500 pl-4 space-y-2">
                        <h3 class="text-base font-bold text-slate-900">4.3 Toko Online Publik & Integrasi Antrean Kasir</h3>
                        <p class="text-xs text-slate-600">
                            Pelanggan web dapat membuka <a href="{{ route('store.catalog') }}" class="text-indigo-600 font-bold hover:underline">/store</a>, memilih produk, mengisi alamat pengiriman/ambil di toko, dan membayar via QRIS simulator. Begitu pesanan dibuat, stok langsung di-reservasi di database dan muncul seketika di menu <strong>Antrean Pesanan Online</strong> di layar kasir.
                        </p>
                    </div>

                    <div class="border-l-4 border-slate-500 pl-4 space-y-2">
                        <h3 class="text-base font-bold text-slate-900">4.4 Buku Kas Harian (Daily Cashbook)</h3>
                        <p class="text-xs text-slate-600">
                            Setiap transaksi kasir yang lunas dan pesanan online yang terverifikasi otomatis membukukan transaksi <strong>Kas Masuk</strong>. Pemilik toko juga dapat mencatat transaksi <strong>Kas Keluar</strong> untuk pengeluaran operasional (seperti beli kertas struk, plastik, token listrik, dsb.), dan sistem akan menghitung saldo berjalan secara real-time.
                        </p>
                    </div>

                    <div class="border-l-4 border-purple-500 pl-4 space-y-2">
                        <h3 class="text-base font-bold text-slate-900">4.5 Absensi Karyawan</h3>
                        <p class="text-xs text-slate-600">
                            Karyawan dapat melakukan Absen Masuk dan Absen Pulang dengan jam WIB otomatis. Keterlambatan dihitung otomatis jika absensi dilakukan lewat dari 15 menit setelah shift dimulai. Pemilik dapat melihat rekap bulanan seluruh karyawan di menu Rekap Absensi.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 5: Layanan Simulasi & Batasan Demo -->
            <section id="simulasi-batasan" class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Bab 5</div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Layanan Simulasi & Batasan Demo</h2>
                <p>
                    Untuk keperluan presentasi dan pengujian yang aman, komponen eksternal pihak ketiga disimulasikan secara deterministik di tingkat servis lokal:
                </p>
                <ul class="list-disc list-inside space-y-2 text-xs text-slate-600">
                    <li><strong>Payment Gateway:</strong> Pembayaran QRIS dan Virtual Account menggunakan emulator lokal yang dapat diklik "Simulasikan Pembayaran Berhasil" atau "Gagal". Tidak ada perpindahan uang sungguhan.</li>
                    <li><strong>Kurir & Ongkir:</strong> Biaya pengiriman dihitung secara deterministik berdasarkan berat gram produk dan zona kurir (JNE, SiCepat, Instan).</li>
                    <li><strong>Notifikasi WhatsApp:</strong> Pesan struk WhatsApp diformat dan disimpan ke <code>notification_logs</code> di database lokal.</li>
                    <li><strong>Thermal Printer Bridge:</strong> Menggunakan browser print standar 58mm/80mm ditambah generator raw ESC/POS commands (seperti QZ Tray).</li>
                </ul>
            </section>

            <!-- Section 6: Mandatory Customization Notice -->
            <section id="kustomisasi-kontak" class="p-6 sm:p-8 rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl space-y-4">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    Kustomisasi Khusus Bisnis Anda
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                    Butuh Sistem yang Disesuaikan Lebih Lanjut? (Need something more tailored?)
                </h2>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Demo ini menampilkan fitur dan alur kerja aplikasi saat ini. Aplikasi web ini dapat dikembangkan lebih lanjut, diperluas, dan dikustomisasi agar sesuai dengan proses bisnis serta kebutuhan spesifik Anda. Fitur, alur kerja, antarmuka, maupun integrasi pihak ketiga (Payment Gateway live, kurir ekspedisi, WhatsApp API, atau printer hardware) dapat didiskusikan dan disesuaikan dengan kebutuhan Anda.
                </p>
                <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <span class="text-xs text-slate-400">Kontak Pengembangan & Kustomisasi:</span>
                    <a href="mailto:{{ config('demo.contact_email', 'nixresponding@gmail.com') }}" class="font-mono text-emerald-300 hover:text-emerald-200 font-bold bg-white/10 px-3 py-1.5 rounded-lg border border-white/20 transition text-xs">
                        {{ config('demo.contact_email', 'nixresponding@gmail.com') }}
                    </a>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection
