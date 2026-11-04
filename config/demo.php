<?php

return [
    'enabled' => (bool) env('APP_DEMO_MODE', true),
    'contact_email' => env('DEMO_CONTACT_EMAIL', 'nixresponding@gmail.com'),
    'personas' => [
        'admin' => [
            'key' => 'admin',
            'name' => 'Budi Santoso',
            'role_label' => 'Pemilik / Admin',
            'email' => 'owner@omnikasir.demo',
            'redirect' => '/admin/dashboard',
            'description' => 'Akses penuh ke analitik penjualan, Buku Kas Harian, Laporan Laba/Rugi, Manajemen Produk & Gudang, Rekap Absensi Karyawan, dan Layar Kasir POS.',
            'capabilities' => [
                'Dashboard Ringkasan Penjualan & Laba Kotor',
                'Buku Kas Harian (Kas Masuk, Kas Keluar, Saldo Berjalan)',
                'Manajemen Karyawan & Matriks Hak Akses',
                'Rekap Absensi Karyawan Seluruh Staf',
                'Akses Penuh ke POS Kasir & Stok Gudang',
            ],
        ],
        'cashier' => [
            'key' => 'cashier',
            'name' => 'Siti Aminah',
            'role_label' => 'Kasir',
            'email' => 'kasir@omnikasir.demo',
            'redirect' => '/pos',
            'description' => 'Layar Kasir POS Touch-Friendly, Antrean Pesanan Toko Online, Cetak Struk 58mm/80mm, Mode Offline & Shift Kas.',
            'capabilities' => [
                'Terminal Kasir Cepat dengan Scanner Barcode & Tombol Sentuh',
                'Antrean & Konfirmasi Pesanan dari Toko Online',
                'Cetak Struk Thermal 58mm & 80mm + Simulasi ESC/POS',
                'Simulasi Mode Offline (IndexedDB) & Sinkronisasi',
                'Buka & Tutup Shift Register Kasir',
            ],
        ],
        'warehouse' => [
            'key' => 'warehouse',
            'name' => 'Andi Pratama',
            'role_label' => 'Staf Gudang',
            'email' => 'gudang@omnikasir.demo',
            'redirect' => '/warehouse/inventory',
            'description' => 'Katalog Produk & Kategori, Stok Masuk (Restock), Stock Opname, Kartu Stok (Mutasi), dan Penyiapan Pesanan Online.',
            'capabilities' => [
                'Manajemen Produk, Kategori, Barcode & Satuan',
                'Pencatatan Stok Masuk (Restock Supplier)',
                'Stock Opname / Penyesuaian Selisih Fisik',
                'Kartu Stok (Audit Mutasi Keluar/Masuk Lengkap)',
                'Status Penyiapan (Packing) Barang Pesanan Online',
            ],
        ],
    ],
];
