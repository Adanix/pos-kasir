# OmniKasir — Unified Offline-Capable POS & Online Store Web Application

<p align="center">
  <img src="https://images.unsplash.com/photo-1556742049-0a67c5574f73?auto=format&fit=crop&w=1200&q=80" alt="OmniKasir Hero Banner" width="100%" style="border-radius: 16px; max-height: 380px; object-fit: cover;" />
</p>

<p align="center">
  <a href="https://omnikasir.ramadadawah.my.id"><img src="https://img.shields.io/badge/Live%20Demo-omnikasir.ramadadawah.my.id-059669?style=for-the-badge&logo=cloudflare" alt="Live Demo"></a>
  <img src="https://img.shields.io/badge/Laravel-12%20%2F%2013-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Tailwind-CSS%204-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-Reactive%20POS-8BC0D0?style=for-the-badge&logo=alpinedotjs" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Tests-22%20Passed%20(160%20assertions)-emerald?style=for-the-badge" alt="Tests">
</p>

---

## 📌 Tentang OmniKasir

**OmniKasir** adalah sistem web ritel terintegrasi yang menyatukan operasional kasir toko fisik (**Point of Sale / POS**), manajemen stok multi-kategori di **Gudang (Warehouse)**, dan penjualan e-commerce melalui **Toko Online Publik (Storefront)** dalam satu kesatuan database relasional MySQL.

Sistem ini didesain dengan konsep **Offline-First Resilience** menggunakan IndexedDB di sisi peramban kasir untuk menjamin operasional penjualan tetap berjalan mulus meskipun koneksi internet terputus, lalu otomatis tersinkronisasi kembali ke server secara aman tanpa risiko duplikasi atau selisih stok.

🌐 **Demo Publik Langsung:** [https://omnikasir.ramadadawah.my.id](https://omnikasir.ramadadawah.my.id)  
📖 **Dokumentasi Lengkap:** [https://omnikasir.ramadadawah.my.id/docs](https://omnikasir.ramadadawah.my.id/docs)  
📬 **Kontak & Kustomisasi Bisnis:** [nixresponding@gmail.com](mailto:nixresponding@gmail.com)

---

## ✨ 3 Layout Antarmuka Khusus (Three Distinct Layouts)

Aplikasi dibangun dengan 3 layout antarmuka yang dirancang khusus sesuai alur kerja operasional:

### 1. Terminal Kasir POS (`/pos`)
* **Touch-Friendly & Zero-Overflow Layout:** Tata letak khusus layar kasir dengan katalog produk sentuh, shortcut pencarian barcode EAN-13 (Hotkey `F2`), dan register keranjang transaksi reaktif (Alpine.js).
* **Mode Offline (IndexedDB Outbox):** Toggle simulasi offline yang menyimpan transaksi lokal saat koneksi terputus dan tombol *batch synchronization* dengan token idempotensi `client_tx_id`.
* **Manajemen Shift Kasir (`/pos/shift`):** Buka shift dengan modal awal (*opening cash float*), pencatatan kas kecil laci (*petty cash*), kalkulasi selisih fisik kas laci, dan tutup shift harian.
* **Cetak Struk Thermal & ESC/POS Bridge (`/pos/orders/{id}/receipt`):** Format struk thermal standar **58mm** dan **80mm** siap cetak (`window.print()`), dilengkapi modul simulasi *ESC/POS Hardware Bridge Driver*.
* **Antrean Pesanan Online (`/pos/online-orders`):** Notifikasi pesanan web masuk secara real-time, verifikasi pembayaran, pencetakan struk pengiriman, dan pembaruan status pengiriman.

### 2. Backoffice & Manajemen Gudang (`/admin/*`, `/warehouse/*`)
* **Dashboard Owner & Keuangan (`/admin/dashboard`):** Monitoring omzet harian, ringkasan transaksi, grafik penjualan, dan status operasional.
* **Buku Kas Harian (`/admin/cashbook`):** Pencatatan mutasi kas masuk (*inflow*) dan kas keluar (*outflow*) dengan saldo berjalan (*running balance*).
* **Laporan Laba / Rugi (`/admin/reports`):** Laporan laba kotor, HPP (Harga Pokok Penjualan), margin laba, dan laba operasional bersih.
* **Inventori & Kartu Stok (`/warehouse/inventory`, `/warehouse/movements`):** Peringatan stok menipis (*low-stock alerts*), nilai aset modal, opname fisik, mutasi restock supplier, dan buku mutasi stok (*immutable stock ledger*).
* **Presensi & Absensi Karyawan (`/attendance`, `/admin/attendance`):** Catatan absensi masuk dan pulang real-time dengan kalkulasi otomatis status hadir tepat waktu atau terlambat.

### 3. Toko Online Publik (`/store/*`)
* **Katalog Belanja Responsif:** Tampilan modern *mobile-first* dengan filter kategori, pengurutan harga, dan detail produk.
* **Keranjang & Checkout:** Validasi batas stok fisik gudang, tombol *Isi Otomatis Data Pelanggan Demo*, dan opsi pemenuhan pesanan:
  * **Ambil di Toko Fisik (BOPIS):** Bebas ongkos kirim, pesanan disiapkan di meja kasir.
  * **Pengiriman Ekspedisi:** Kalkulasi ongkir otomatis untuk JNE, J&T, SiCepat, dan Kurir Instan.
* **Simulator Gateway Pembayaran (`/store/payment/{order_number}`):** Render dinamis QRIS Standar Nasional dan Virtual Account Bank (BCA/Mandiri) dengan kontrol simulasi: *Bayar Sukses*, *Gagal Bayar*, atau *Batal (Otomatis Release Stok)*.
* **Pelacakan Pesanan & E-Receipt (`/store/orders/*`):** Progres pesanan 5 tahap (*Dibuat &rarr; Terbayar &rarr; Diproses &rarr; Dikirim &rarr; Selesai*) dengan fitur cetak faktur digital.

---

## 👥 Persona Akun Demo (1-Click Login)

Aplikasi dilengkapi tombol cepat **Demo Role Switcher** di bagian atas layar untuk pengujian instan tanpa perlu memasukkan password:

| Peran (Role) | Email Login | Hak Akses Utama |
| :--- | :--- | :--- |
| **Owner / Admin** | `owner@omnikasir.demo` | Akses penuh ke Dashboard Keuangan, Buku Kas, Laporan Laba Rugi, Manajemen Pengguna, dan POS |
| **Kasir (Cashier)** | `kasir@omnikasir.demo` | Terminal POS, Manajemen Shift Kas Laci, Antrean Pesanan Online, dan Riwayat Struk |
| **Staf Gudang (Warehouse)** | `gudang@omnikasir.demo` | Ringkasan Stok Gudang, Penambahan SKU Produk, Opname Stok, dan Kartu Mutasi |

---

## 🛠️ Stack Teknologi & Dependensi

* **Backend:** PHP 8.2+ / PHP 8.3, Laravel 12 / Laravel 13
* **Database:** MySQL 8.0+ / MariaDB (Mendukung SQLite untuk Unit Testing)
* **Frontend:** Tailwind CSS v4, Flowbite, Alpine.js 3.x, Vite
* **Printing & Hardware Simulation:** ESC/POS Command Generator, Viewport Thermal CSS (58mm/80mm)
* **Testing:** PHPUnit, Laravel Feature & Unit Test Suites

---

## 🚀 Panduan Instalasi Lokal

### 1. Kloning Repositori
```bash
git clone git@github.com:Adanix/pos-kasir.git
cd pos-kasir
```

### 2. Instalasi Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (.env)
Salin berkas konfigurasi sampel dan sesuaikan kredensial basis data:
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan variabel mode demo aktif pada `.env`:
```env
APP_NAME="OmniKasir"
APP_LOCALE=id
APP_DEMO_MODE=true
DEMO_CONTACT_EMAIL=nixresponding@gmail.com
```

### 4. Migrasi & Seeder Database
Jalankan migrasi database beserta dataset demo realistis:
```bash
php artisan migrate --seed
```

### 5. Kompilasi Aset Frontend & Jalankan Server
```bash
npm run build
php artisan serve
```
Aplikasi dapat diakses melalui peramban di `http://localhost:8000`.

---

## 🧪 Menjalankan Pengujian Otomatis (Testing)

Proyek ini dilengkapi pengujian fitur dan unit komprehensif yang menguji otentikasi role, boundary middleware, transaksi POS, sinkronisasi offline, toko online, mutasi stok, hingga buku kas:

```bash
php artisan test
```

**Hasil Uji Otomatis:**
```
   PASS  Tests\Unit\ExampleTest (1 test)
   PASS  Tests\Feature\ExampleTest (1 test)
   PASS  Tests\Feature\P0PublicAndDemoAuthTest (7 tests, 41 assertions)
   PASS  Tests\Feature\RolePermissionBoundaryTest (4 tests, 39 assertions)
   PASS  Tests\Feature\PosTransactionAndOfflineSyncTest (4 tests, 28 assertions)
   PASS  Tests\Feature\OnlineStoreAndPosSyncTest (2 tests, 27 assertions)
   PASS  Tests\Feature\InventoryAttendanceAndCashbookTest (3 tests, 23 assertions)

  Tests:    22 passed (160 assertions)
  Duration: 4.59s
```

---

## 📄 Lisensi & Hak Cipta

Hak Cipta © 2026 **OmniKasir** & **Adanix**. Seluruh hak dilindungi undang-undang.  
Untuk keperluan komersial, implementasi kustom POS ritel, atau integrasi hardware thermal, hubungi [nixresponding@gmail.com](mailto:nixresponding@gmail.com).
