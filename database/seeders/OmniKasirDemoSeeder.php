<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\CashbookEntry;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\RolePermission;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OmniKasirDemoSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('notification_logs')->truncate();
        DB::table('print_logs')->truncate();
        DB::table('cashbook_entries')->truncate();
        DB::table('attendances')->truncate();
        DB::table('stock_adjustments')->truncate();
        DB::table('stock_movements')->truncate();
        DB::table('payment_transactions')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('pos_shifts')->truncate();
        DB::table('role_permissions')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Seed Users
        $password = Hash::make('password');

        $admin = User::create([
            'name' => 'Budi Santoso',
            'email' => 'owner@omnikasir.demo',
            'employee_code' => 'EMP-001',
            'phone' => '0811-2233-4455',
            'role' => 'admin',
            'default_shift' => 'pagi',
            'password' => $password,
            'is_active' => true,
        ]);

        $cashier = User::create([
            'name' => 'Siti Aminah',
            'email' => 'kasir@omnikasir.demo',
            'employee_code' => 'EMP-002',
            'phone' => '0812-3344-5566',
            'role' => 'cashier',
            'default_shift' => 'pagi',
            'password' => $password,
            'is_active' => true,
        ]);

        $warehouse = User::create([
            'name' => 'Andi Pratama',
            'email' => 'gudang@omnikasir.demo',
            'employee_code' => 'EMP-003',
            'phone' => '0813-4455-6677',
            'role' => 'warehouse',
            'default_shift' => 'pagi',
            'password' => $password,
            'is_active' => true,
        ]);

        $cashier2 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@omnikasir.demo',
            'employee_code' => 'EMP-004',
            'phone' => '0814-5566-7788',
            'role' => 'cashier',
            'default_shift' => 'sore',
            'password' => $password,
            'is_active' => true,
        ]);

        $warehouse2 = User::create([
            'name' => 'Rizky Ramadhan',
            'email' => 'rizky@omnikasir.demo',
            'employee_code' => 'EMP-005',
            'phone' => '0815-6677-8899',
            'role' => 'warehouse',
            'default_shift' => 'sore',
            'password' => $password,
            'is_active' => true,
        ]);

        // 2. Seed Role Permissions Matrix
        $permissions = [
            ['permission_key' => 'pos.checkout', 'permission_label' => 'Transaksi Kasir POS', 'admin' => true, 'cashier' => true, 'warehouse' => false],
            ['permission_key' => 'pos.offline_sync', 'permission_label' => 'Sinkronisasi Mode Offline', 'admin' => true, 'cashier' => true, 'warehouse' => false],
            ['permission_key' => 'pos.petty_cash', 'permission_label' => 'Input Kas Kecil / Pengeluaran Shift', 'admin' => true, 'cashier' => true, 'warehouse' => false],
            ['permission_key' => 'online_orders.process', 'permission_label' => 'Verifikasi & Konfirmasi Pesanan Online', 'admin' => true, 'cashier' => true, 'warehouse' => true],
            ['permission_key' => 'inventory.manage', 'permission_label' => 'Kelola Produk & Kategori', 'admin' => true, 'cashier' => false, 'warehouse' => true],
            ['permission_key' => 'inventory.adjust', 'permission_label' => 'Stock Opname & Restock', 'admin' => true, 'cashier' => false, 'warehouse' => true],
            ['permission_key' => 'inventory.view_cost', 'permission_label' => 'Melihat Harga Modal (HPP)', 'admin' => true, 'cashier' => false, 'warehouse' => true],
            ['permission_key' => 'attendance.personal', 'permission_label' => 'Absen Masuk & Pulang Pribadi', 'admin' => true, 'cashier' => true, 'warehouse' => true],
            ['permission_key' => 'attendance.admin_recap', 'permission_label' => 'Rekap & Koreksi Absensi Seluruh Staf', 'admin' => true, 'cashier' => false, 'warehouse' => false],
            ['permission_key' => 'cashbook.manage', 'permission_label' => 'Buku Kas Harian & Laporan Keuangan', 'admin' => true, 'cashier' => false, 'warehouse' => false],
            ['permission_key' => 'users.manage', 'permission_label' => 'Manajemen Akun Karyawan & Hak Akses', 'admin' => true, 'cashier' => false, 'warehouse' => false],
        ];

        foreach ($permissions as $p) {
            foreach (['admin', 'cashier', 'warehouse'] as $r) {
                RolePermission::create([
                    'role' => $r,
                    'permission_key' => $p['permission_key'],
                    'permission_label' => $p['permission_label'],
                    'is_allowed' => $p[$r],
                ]);
            }
        }

        // 3. Seed Categories
        $categoriesData = [
            ['name' => 'Kopi & Biji Kopi', 'slug' => 'kopi-biji-kopi', 'description' => 'Biji kopi pilihan roasted bean single origin dan espresso blend nusantara.'],
            ['name' => 'Minuman & Susu Barista', 'slug' => 'minuman-susu-barista', 'description' => 'Susu segar pasteurisasi, oat milk, dan racikan minuman siap saji.'],
            ['name' => 'Bahan Kue & Sirup', 'slug' => 'bahan-kue-sirup', 'description' => 'Sirup perasa artisanal, bubuk perisa, dan topping premium.'],
            ['name' => 'Camilan & Roti', 'slug' => 'camilan-roti', 'description' => 'Pastry fresh oven, cookies, dan aneka snack pendamping kopi.'],
            ['name' => 'Perlengkapan Seduh', 'slug' => 'perlengkapan-seduh', 'description' => 'Paper filter V60, tumbler barista, dan aksesoris manual brew.'],
            ['name' => 'Paket Bundling Hemat', 'slug' => 'paket-bundling-hemat', 'description' => 'Paket hemat kombinasi kopi dan camilan dengan harga spesial.'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::create($c);
        }

        // 4. Seed 18 Realistic Products
        $productsData = [
            // Kopi & Biji Kopi
            [
                'cat' => 'kopi-biji-kopi',
                'sku' => 'KOP-001',
                'barcode' => '8991002100011',
                'name' => 'Kopi Arabika Gayo Single Origin 250g',
                'slug' => 'kopi-arabika-gayo-250g',
                'desc' => 'Biji kopi arabika Aceh Gayo grade 1, medium roast dengan notes dark chocolate dan fruity caramel.',
                'unit' => 'Bungkus',
                'cost' => 45000,
                'price' => 65000,
                'stock' => 35,
                'min' => 8,
                'weight' => 270,
                'image' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'kopi-biji-kopi',
                'sku' => 'KOP-002',
                'barcode' => '8991002100028',
                'name' => 'Kopi Arabika Flores Bajawa 250g',
                'slug' => 'kopi-arabika-flores-bajawa-250g',
                'desc' => 'Aroma floral dan nutty khas tanah vulkanik Bajawa, Flores. Full wash process.',
                'unit' => 'Bungkus',
                'cost' => 48000,
                'price' => 70000,
                'stock' => 4, // Low stock on purpose
                'min' => 5,
                'weight' => 270,
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'kopi-biji-kopi',
                'sku' => 'KOP-003',
                'barcode' => '8991002100035',
                'name' => 'Espresso Blend Nusantara 500g',
                'slug' => 'espresso-blend-nusantara-500g',
                'desc' => 'Formula 70% Arabika Temanggung & 30% Robusta Dampit, crema tebal dan bold body.',
                'unit' => 'Bungkus',
                'cost' => 60000,
                'price' => 88000,
                'stock' => 22,
                'min' => 6,
                'weight' => 520,
                'image' => 'https://images.unsplash.com/photo-1587734195503-904fca47e0e9?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'kopi-biji-kopi',
                'sku' => 'KOP-004',
                'barcode' => '8991002100042',
                'name' => 'Kopi Drip Bag Filter Praktis (Isi 5 Sachet)',
                'slug' => 'kopi-drip-bag-filter-isi-5',
                'desc' => 'Kopi tubruk modern siap seduh tanpa ampas, tinggal tuang air panas 150ml.',
                'unit' => 'Kotak',
                'cost' => 28000,
                'price' => 42000,
                'stock' => 40,
                'min' => 10,
                'weight' => 120,
                'image' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?auto=format&fit=crop&w=600&q=80',
                'is_featured' => false,
            ],

            // Minuman & Susu Barista
            [
                'cat' => 'minuman-susu-barista',
                'sku' => 'SUS-001',
                'barcode' => '8991002200018',
                'name' => 'Susu Oat Barista Professional 1 Liter',
                'slug' => 'susu-oat-barista-1l',
                'desc' => 'Plant-based milk creamy khusus latte art, mudah disteam mikrofoam halus.',
                'unit' => 'Kotak',
                'cost' => 29000,
                'price' => 42000,
                'stock' => 28,
                'min' => 6,
                'weight' => 1050,
                'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'minuman-susu-barista',
                'sku' => 'SUS-002',
                'barcode' => '8991002200025',
                'name' => 'Susu Fresh Milk Pasteurisasi 1 Liter',
                'slug' => 'susu-fresh-milk-pasteurisasi-1l',
                'desc' => 'Susu sapi murni segar kualitas kafe, kaya rasa dan gurih alami.',
                'unit' => 'Botol',
                'cost' => 19000,
                'price' => 28000,
                'stock' => 18,
                'min' => 5,
                'weight' => 1040,
                'image' => 'https://images.unsplash.com/photo-1528750997573-59b89d56f4f7?auto=format&fit=crop&w=600&q=80',
                'is_featured' => false,
            ],
            [
                'cat' => 'minuman-susu-barista',
                'sku' => 'MIN-003',
                'barcode' => '8991002200032',
                'name' => 'Matcha Powder Ceremonial Uji 100g',
                'slug' => 'matcha-powder-ceremonial-100g',
                'desc' => 'Bubuk matcha murni kualitas tinggi dari Kyoto, warna hijau pekat dan harum umami.',
                'unit' => 'Pcs',
                'cost' => 65000,
                'price' => 95000,
                'stock' => 3, // Low stock on purpose
                'min' => 5,
                'weight' => 130,
                'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'minuman-susu-barista',
                'sku' => 'MIN-004',
                'barcode' => '8991002200049',
                'name' => 'Sirup Gula Aren Cair Organik 1 Liter',
                'slug' => 'sirup-gula-aren-cair-1l',
                'desc' => 'Gula aren nira kelapa murni tanpa bahan pengawet, aroma smoky pekat pas untuk es kopi susu.',
                'unit' => 'Botol',
                'cost' => 24000,
                'price' => 38000,
                'stock' => 32,
                'min' => 8,
                'weight' => 1300,
                'image' => 'https://images.unsplash.com/photo-1621263764928-df1444c5e859?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],

            // Bahan Kue & Sirup
            [
                'cat' => 'bahan-kue-sirup',
                'sku' => 'SIR-001',
                'barcode' => '8991002300015',
                'name' => 'Sirup Vanilla Madagascar 750ml',
                'slug' => 'sirup-vanilla-madagascar-750ml',
                'desc' => 'Sirup rasa vanilla premium untuk varian latte, frappuccino, dan mocktail.',
                'unit' => 'Botol',
                'cost' => 75000,
                'price' => 110000,
                'stock' => 14,
                'min' => 4,
                'weight' => 1200,
                'image' => 'https://images.unsplash.com/photo-1595981267035-7b04ca84a82d?auto=format&fit=crop&w=600&q=80',
                'is_featured' => false,
            ],
            [
                'cat' => 'bahan-kue-sirup',
                'sku' => 'SIR-002',
                'barcode' => '8991002300022',
                'name' => 'Sirup Salted Caramel Artisanal 750ml',
                'slug' => 'sirup-salted-caramel-750ml',
                'desc' => 'Perpaduan manis gurih karamel bakar dan sea salt asli.',
                'unit' => 'Botol',
                'cost' => 78000,
                'price' => 115000,
                'stock' => 16,
                'min' => 4,
                'weight' => 1200,
                'image' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'bahan-kue-sirup',
                'sku' => 'BAH-003',
                'barcode' => '8991002300039',
                'name' => 'Dark Chocolate Powder 58% Pure 500g',
                'slug' => 'dark-chocolate-powder-500g',
                'desc' => 'Cokelat bubuk Belgia kaya rasa, cocok untuk iced chocolate dan baking.',
                'unit' => 'Bungkus',
                'cost' => 48000,
                'price' => 72000,
                'stock' => 20,
                'min' => 5,
                'weight' => 520,
                'image' => 'https://images.unsplash.com/photo-1548848221-0c2e497ed557?auto=format&fit=crop&w=600&q=80',
                'is_featured' => false,
            ],

            // Camilan & Roti
            [
                'cat' => 'camilan-roti',
                'sku' => 'ROTI-001',
                'barcode' => '8991002400012',
                'name' => 'Butter Croissant Artisanal Freshly Baked',
                'slug' => 'butter-croissant-artisanal',
                'desc' => 'Croissant Prancis renyah di luar, lembut berlapis di dalam dengan pure butter.',
                'unit' => 'Pcs',
                'cost' => 15000,
                'price' => 24000,
                'stock' => 25,
                'min' => 6,
                'weight' => 90,
                'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'camilan-roti',
                'sku' => 'ROTI-002',
                'barcode' => '8991002400029',
                'name' => 'Pain Au Chocolat Double Filling',
                'slug' => 'pain-au-chocolat-double-filling',
                'desc' => 'Pastry puff dengan dua batang cokelat dark meleleh saat dipanaskan.',
                'unit' => 'Pcs',
                'cost' => 18000,
                'price' => 28000,
                'stock' => 2, // Low stock on purpose
                'min' => 5,
                'weight' => 100,
                'image' => 'https://images.unsplash.com/photo-1530610476181-d83430b64dcd?auto=format&fit=crop&w=600&q=80',
                'is_featured' => false,
            ],
            [
                'cat' => 'camilan-roti',
                'sku' => 'SNK-003',
                'barcode' => '8991002400036',
                'name' => 'Cookies Choco Chip Sea Salt Jumbo',
                'slug' => 'cookies-choco-chip-sea-salt-jumbo',
                'desc' => 'Kue kering soft-baked dengan potongan cokelat melimpah dan taburan sea salt.',
                'unit' => 'Pcs',
                'cost' => 12000,
                'price' => 20000,
                'stock' => 30,
                'min' => 8,
                'weight' => 85,
                'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],

            // Perlengkapan Seduh
            [
                'cat' => 'perlengkapan-seduh',
                'sku' => 'SED-001',
                'barcode' => '8991002500019',
                'name' => 'Paper Filter Kopi V60 02 White (100 Lembar)',
                'slug' => 'paper-filter-v60-02-100-lembar',
                'desc' => 'Kertas saring kopi pour-over ukuran 02 bleached bebas bau klorin.',
                'unit' => 'Kotak',
                'cost' => 35000,
                'price' => 52000,
                'stock' => 24,
                'min' => 5,
                'weight' => 180,
                'image' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=600&q=80',
                'is_featured' => false,
            ],
            [
                'cat' => 'perlengkapan-seduh',
                'sku' => 'SED-002',
                'barcode' => '8991002500026',
                'name' => 'Tumbler Stainless Vacuum Insulated 500ml',
                'slug' => 'tumbler-stainless-insulated-500ml',
                'desc' => 'Tumbler tahan panas/dingin hingga 12 jam, food grade 304 anti tumpah.',
                'unit' => 'Pcs',
                'cost' => 85000,
                'price' => 135000,
                'stock' => 15,
                'min' => 3,
                'weight' => 350,
                'image' => 'https://images.unsplash.com/photo-1577937927133-66ef06acdf18?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],

            // Paket Bundling Hemat
            [
                'cat' => 'paket-bundling-hemat',
                'sku' => 'PKT-001',
                'barcode' => '8991002600016',
                'name' => 'Paket Ngopi Santai (Kopi Gayo + 2 Croissant)',
                'slug' => 'paket-ngopi-santai-gayo-croissant',
                'desc' => 'Kombinasi hemat 1 pack Kopi Gayo 250g + 2 Pcs Butter Croissant segar.',
                'unit' => 'Paket',
                'cost' => 70000,
                'price' => 99000,
                'stock' => 12,
                'min' => 3,
                'weight' => 450,
                'image' => 'https://images.unsplash.com/photo-1509785307050-d4066910ec1e?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'cat' => 'paket-bundling-hemat',
                'sku' => 'PKT-002',
                'barcode' => '8991002600023',
                'name' => 'Paket Barista Starter (Blend 500g + Oat Milk 1L)',
                'slug' => 'paket-barista-starter-espresso-oat',
                'desc' => 'Paket lengkap seduh latte di rumah: Espresso Blend 500g + Susu Oat Barista 1L.',
                'unit' => 'Paket',
                'cost' => 84000,
                'price' => 119000,
                'stock' => 10,
                'min' => 2,
                'weight' => 1600,
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $cat = $categories[$p['cat']];
            $prod = Product::create([
                'category_id' => $cat->id,
                'sku' => $p['sku'],
                'barcode' => $p['barcode'],
                'name' => $p['name'],
                'slug' => $p['slug'],
                'description' => $p['desc'],
                'unit' => $p['unit'],
                'cost_price' => $p['cost'],
                'selling_price' => $p['price'],
                'stock_qty' => $p['stock'],
                'min_stock_alert' => $p['min'],
                'weight_grams' => $p['weight'],
                'image_url' => $p['image'],
                'is_active' => true,
                'is_featured_online' => $p['is_featured'],
            ]);

            $products[$p['sku']] = $prod;

            // Initial stock movement
            StockMovement::create([
                'product_id' => $prod->id,
                'user_id' => $warehouse->id,
                'movement_type' => 'initial',
                'qty_before' => 0,
                'qty_change' => $p['stock'],
                'qty_after' => $p['stock'],
                'reference_type' => 'seeder',
                'notes' => 'Saldo awal stok gudang saat setup sistem',
                'created_at' => Carbon::now()->subDays(7),
            ]);
        }

        // 5. Seed POS Shifts
        // Closed shift yesterday
        PosShift::create([
            'shift_code' => 'SHF-' . Carbon::yesterday()->format('Ymd') . '-01',
            'user_id' => $cashier->id,
            'shift_name' => 'Pagi',
            'opening_cash' => 500000,
            'cash_sales_total' => 1250000,
            'non_cash_sales_total' => 840000,
            'petty_cash_out_total' => 85000,
            'closing_cash_expected' => 1665000,
            'closing_cash_actual' => 1665000,
            'difference_amount' => 0,
            'status' => 'closed',
            'notes' => 'Shift kemarin berjalan lancar, saldo kas laci seimbang.',
            'opened_at' => Carbon::yesterday()->setTime(8, 0),
            'closed_at' => Carbon::yesterday()->setTime(16, 0),
            'created_at' => Carbon::yesterday()->setTime(8, 0),
        ]);

        // Active open shift today for Siti Aminah
        $currentShift = PosShift::create([
            'shift_code' => 'SHF-' . Carbon::today()->format('Ymd') . '-01',
            'user_id' => $cashier->id,
            'shift_name' => 'Pagi',
            'opening_cash' => 500000,
            'cash_sales_total' => 380000,
            'non_cash_sales_total' => 245000,
            'petty_cash_out_total' => 25000,
            'closing_cash_expected' => 855000,
            'closing_cash_actual' => null,
            'difference_amount' => null,
            'status' => 'open',
            'notes' => 'Modal awal kasir Rp 500.000 (pecahan 50rb, 20rb, 10rb)',
            'opened_at' => Carbon::today()->setTime(7, 55),
            'created_at' => Carbon::today()->setTime(7, 55),
        ]);

        // 6. Seed Daily Cashbook (Buku Kas) Initial & Balance Ledger
        $runningBalance = 5000000.0; // Saldo Awal Toko
        $cashbookEntries = [
            [
                'entry_number' => 'KAS-INIT-001',
                'entry_date' => Carbon::today()->subDays(3)->toDateString(),
                'transaction_at' => Carbon::today()->subDays(3)->setTime(8, 0),
                'type' => 'in',
                'category' => 'modal_awal',
                'payment_method' => 'cash',
                'amount' => 5000000,
                'description' => 'Saldo awal modal kas operasional toko OmniKasir',
                'ref' => 'MODAL-AWAL',
                'creator' => $admin->id,
            ],
            [
                'entry_number' => 'KAS-OPS-001',
                'entry_date' => Carbon::today()->subDays(2)->toDateString(),
                'transaction_at' => Carbon::today()->subDays(2)->setTime(10, 30),
                'type' => 'out',
                'category' => 'operasional_toko',
                'payment_method' => 'cash',
                'amount' => 85000,
                'description' => 'Pembelian kertas struk thermal 58mm & 80mm (10 roll)',
                'ref' => 'NOTA-KERTAS-01',
                'creator' => $admin->id,
            ],
            [
                'entry_number' => 'KAS-OPS-002',
                'entry_date' => Carbon::yesterday()->toDateString(),
                'transaction_at' => Carbon::yesterday()->setTime(14, 0),
                'type' => 'out',
                'category' => 'operasional_toko',
                'payment_method' => 'bank_transfer',
                'amount' => 250000,
                'description' => 'Pembayaran token listrik toko & wifi indihome',
                'ref' => 'PLN-NOV2026',
                'creator' => $admin->id,
            ],
            [
                'entry_number' => 'KAS-SALE-001',
                'entry_date' => Carbon::yesterday()->toDateString(),
                'transaction_at' => Carbon::yesterday()->setTime(16, 30),
                'type' => 'in',
                'category' => 'penjualan_pos',
                'payment_method' => 'cash',
                'amount' => 1250000,
                'description' => 'Rekap penjualan tunai kasir shift kemarin',
                'ref' => 'SHF-REKAP',
                'creator' => $cashier->id,
            ],
            [
                'entry_number' => 'KAS-SALE-002',
                'entry_date' => Carbon::yesterday()->toDateString(),
                'transaction_at' => Carbon::yesterday()->setTime(17, 0),
                'type' => 'in',
                'category' => 'penjualan_online',
                'payment_method' => 'qris',
                'amount' => 840000,
                'description' => 'Pemasukan pesanan online lunas kemarin via QRIS',
                'ref' => 'QRIS-SETTLE',
                'creator' => $admin->id,
            ],
            [
                'entry_number' => 'KAS-SHIFT-EXP',
                'entry_date' => Carbon::today()->toDateString(),
                'transaction_at' => Carbon::today()->setTime(9, 15),
                'type' => 'out',
                'category' => 'operasional_toko',
                'payment_method' => 'cash',
                'amount' => 25000,
                'description' => 'Beli air galon & plastik kantong kasir shift pagi',
                'ref' => 'PETTY-01',
                'creator' => $cashier->id,
            ],
        ];

        $running = 0.0;
        foreach ($cashbookEntries as $entry) {
            $running = ($entry['type'] === 'in')
                ? $running + $entry['amount']
                : $running - $entry['amount'];

            CashbookEntry::create([
                'entry_number' => $entry['entry_number'],
                'entry_date' => $entry['entry_date'],
                'transaction_at' => $entry['transaction_at'],
                'type' => $entry['type'],
                'category' => $entry['category'],
                'payment_method' => $entry['payment_method'],
                'amount' => $entry['amount'],
                'balance_after' => $running,
                'description' => $entry['description'],
                'reference_number' => $entry['ref'],
                'created_by' => $entry['creator'],
                'created_at' => $entry['transaction_at'],
            ]);
        }

        // 7. Seed 7 Completed POS Orders
        $posOrders = [
            [
                'num' => 'ORD-POS-' . Carbon::today()->format('Ymd') . '-0001',
                'cust' => 'Pak Hendra (Langganan)',
                'method' => 'cash',
                'items' => [['sku' => 'KOP-001', 'qty' => 1], ['sku' => 'ROTI-001', 'qty' => 2]],
                'cash' => 120000,
                'time' => Carbon::today()->setTime(8, 30),
            ],
            [
                'num' => 'ORD-POS-' . Carbon::today()->format('Ymd') . '-0002',
                'cust' => 'Pelanggan Umum',
                'method' => 'qris',
                'items' => [['sku' => 'SUS-001', 'qty' => 1], ['sku' => 'MIN-004', 'qty' => 1]],
                'cash' => 80000,
                'time' => Carbon::today()->setTime(9, 10),
            ],
            [
                'num' => 'ORD-POS-' . Carbon::today()->format('Ymd') . '-0003',
                'cust' => 'Ibu Maya',
                'method' => 'cash',
                'items' => [['sku' => 'KOP-003', 'qty' => 1]],
                'cash' => 100000,
                'time' => Carbon::today()->setTime(10, 0),
            ],
            [
                'num' => 'ORD-POS-' . Carbon::today()->format('Ymd') . '-0004',
                'cust' => 'Pelanggan Umum',
                'method' => 'bank_transfer',
                'items' => [['sku' => 'PKT-002', 'qty' => 1]],
                'cash' => 119000,
                'time' => Carbon::today()->setTime(11, 20),
            ],
            [
                'num' => 'ORD-POS-' . Carbon::today()->format('Ymd') . '-0005',
                'cust' => 'Mas Doni',
                'method' => 'cash',
                'items' => [['sku' => 'SNK-003', 'qty' => 3]],
                'cash' => 60000,
                'time' => Carbon::today()->setTime(12, 45),
            ],
            [
                'num' => 'ORD-POS-' . Carbon::today()->format('Ymd') . '-0006',
                'cust' => 'Pelanggan Umum',
                'method' => 'qris',
                'items' => [['sku' => 'SED-001', 'qty' => 1], ['sku' => 'KOP-004', 'qty' => 1]],
                'cash' => 94000,
                'time' => Carbon::today()->setTime(13, 30),
            ],
            [
                'num' => 'ORD-POS-' . Carbon::today()->format('Ymd') . '-0007',
                'cust' => 'Mbak Anita',
                'method' => 'cash',
                'items' => [['sku' => 'PKT-001', 'qty' => 1]],
                'cash' => 100000,
                'time' => Carbon::today()->setTime(14, 15),
            ],
        ];

        foreach ($posOrders as $po) {
            $subtotal = 0.0;
            $itemsList = [];
            foreach ($po['items'] as $it) {
                $p = $products[$it['sku']];
                $line = (float) $p->selling_price * $it['qty'];
                $subtotal += $line;
                $itemsList[] = ['p' => $p, 'qty' => $it['qty'], 'line' => $line];
            }

            $grandTotal = $subtotal;
            $change = ($po['method'] === 'cash') ? ($po['cash'] - $grandTotal) : 0;

            $order = Order::create([
                'order_number' => $po['num'],
                'channel' => 'pos',
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $po['method'],
                'cashier_id' => $cashier->id,
                'pos_shift_id' => $currentShift->id,
                'customer_name' => $po['cust'],
                'fulfillment_type' => 'walk_in',
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'shipping_cost' => 0,
                'grand_total' => $grandTotal,
                'cash_received' => $po['cash'],
                'change_amount' => $change,
                'print_count' => 1,
                'ordered_at' => $po['time'],
                'completed_at' => $po['time'],
                'created_at' => $po['time'],
            ]);

            foreach ($itemsList as $it) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['p']->id,
                    'product_name_snapshot' => $it['p']->name,
                    'sku_snapshot' => $it['p']->sku,
                    'unit_snapshot' => $it['p']->unit,
                    'cost_price_snapshot' => $it['p']->cost_price,
                    'unit_price' => $it['p']->selling_price,
                    'qty' => $it['qty'],
                    'line_total' => $it['line'],
                ]);

                StockMovement::create([
                    'product_id' => $it['p']->id,
                    'user_id' => $cashier->id,
                    'movement_type' => 'pos_sale',
                    'qty_before' => $it['p']->stock_qty,
                    'qty_change' => -$it['qty'],
                    'qty_after' => $it['p']->stock_qty - $it['qty'],
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'notes' => "Penjualan Kasir POS #{$order->order_number}",
                    'created_at' => $po['time'],
                ]);
            }

            PaymentTransaction::create([
                'order_id' => $order->id,
                'reference_number' => 'PAY-' . strtoupper(Str::random(8)),
                'payment_method' => $po['method'],
                'amount' => $grandTotal,
                'status' => 'paid',
                'paid_at' => $po['time'],
                'processed_by' => $cashier->id,
                'created_at' => $po['time'],
            ]);
        }

        // 8. Seed 5 Online Store Orders (Across multiple statuses for demo evaluation)
        $onlineOrdersData = [
            // Order 1: Menunggu Pembayaran QRIS
            [
                'num' => 'ORD-WEB-' . Carbon::today()->format('Ymd') . '-1001',
                'cust' => 'Rina Wulandari',
                'phone' => '0812-8899-7766',
                'fulfillment' => 'delivery',
                'courier' => 'jne_reg',
                'service' => 'JNE Reguler',
                'ship_cost' => 18000,
                'address' => 'Jl. Pemuda No. 45, Semarang Tengah',
                'status' => 'pending_payment',
                'pay_status' => 'pending',
                'method' => 'qris',
                'items' => [['sku' => 'KOP-001', 'qty' => 1], ['sku' => 'SUS-001', 'qty' => 1]],
                'time' => Carbon::today()->setTime(11, 0),
            ],
            // Order 2: COD Menunggu Konfirmasi Kasir di POS
            [
                'num' => 'ORD-WEB-' . Carbon::today()->format('Ymd') . '-1002',
                'cust' => 'Bambang Sudiro',
                'phone' => '0813-7788-9900',
                'fulfillment' => 'pickup',
                'courier' => 'pickup',
                'service' => 'Ambil Sendiri di Toko',
                'ship_cost' => 0,
                'address' => 'Ambil langsung di counter kasir jam 16:00',
                'status' => 'pending_confirmation',
                'pay_status' => 'unpaid',
                'method' => 'cod',
                'items' => [['sku' => 'KOP-003', 'qty' => 1], ['sku' => 'ROTI-001', 'qty' => 2]],
                'time' => Carbon::today()->setTime(11, 45),
            ],
            // Order 3: Sudah Lunas QRIS, Sedang Diproses / Disiapkan Gudang
            [
                'num' => 'ORD-WEB-' . Carbon::today()->format('Ymd') . '-1003',
                'cust' => 'Dr. Gunawan Wibowo',
                'phone' => '0811-9922-3344',
                'fulfillment' => 'delivery',
                'courier' => 'instant',
                'service' => 'Kurir Instan (Maks. 3 Jam)',
                'ship_cost' => 15000,
                'address' => 'Klinik Medika Pratama, Jl. Pandanaran No. 12',
                'status' => 'processing',
                'pay_status' => 'paid',
                'method' => 'qris',
                'items' => [['sku' => 'MIN-003', 'qty' => 1], ['sku' => 'MIN-004', 'qty' => 1]],
                'time' => Carbon::today()->setTime(12, 10),
            ],
            // Order 4: Lunas VA, Siap Diambil di Kasir
            [
                'num' => 'ORD-WEB-' . Carbon::today()->format('Ymd') . '-1004',
                'cust' => 'Nadia Safitri',
                'phone' => '0857-1122-3344',
                'fulfillment' => 'pickup',
                'courier' => 'pickup',
                'service' => 'Ambil di Toko',
                'ship_cost' => 0,
                'address' => 'Telah dikonfirmasi siap ambil',
                'status' => 'ready_for_pickup',
                'pay_status' => 'paid',
                'method' => 'bank_transfer',
                'items' => [['sku' => 'SED-002', 'qty' => 1]],
                'time' => Carbon::today()->setTime(13, 0),
            ],
            // Order 5: Selesai & Terkirim
            [
                'num' => 'ORD-WEB-' . Carbon::today()->format('Ymd') . '-1005',
                'cust' => 'Farhan Nugraha',
                'phone' => '0878-3344-5566',
                'fulfillment' => 'delivery',
                'courier' => 'sicepat_best',
                'service' => 'SiCepat BEST Express',
                'ship_cost' => 24000,
                'address' => 'Perum Candi Sari Asri Blok B-8',
                'status' => 'completed',
                'pay_status' => 'paid',
                'method' => 'qris',
                'items' => [['sku' => 'PKT-001', 'qty' => 1]],
                'time' => Carbon::yesterday()->setTime(14, 0),
            ],
        ];

        foreach ($onlineOrdersData as $oo) {
            $subtotal = 0.0;
            $itemsList = [];
            foreach ($oo['items'] as $it) {
                $p = $products[$it['sku']];
                $line = (float) $p->selling_price * $it['qty'];
                $subtotal += $line;
                $itemsList[] = ['p' => $p, 'qty' => $it['qty'], 'line' => $line];
            }

            $grandTotal = $subtotal + $oo['ship_cost'];

            $order = Order::create([
                'order_number' => $oo['num'],
                'channel' => 'online',
                'status' => $oo['status'],
                'payment_status' => $oo['pay_status'],
                'payment_method' => $oo['method'],
                'customer_name' => $oo['cust'],
                'customer_phone' => $oo['phone'],
                'fulfillment_type' => $oo['fulfillment'],
                'shipping_courier' => $oo['courier'],
                'shipping_service' => $oo['service'],
                'tracking_number' => ($oo['status'] === 'completed' || $oo['status'] === 'shipped') ? 'RESI-SIC-20261104-987654' : null,
                'customer_address' => $oo['address'],
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'shipping_cost' => $oo['ship_cost'],
                'grand_total' => $grandTotal,
                'ordered_at' => $oo['time'],
                'completed_at' => ($oo['status'] === 'completed') ? $oo['time']->copy()->addHours(3) : null,
                'created_at' => $oo['time'],
            ]);

            foreach ($itemsList as $it) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['p']->id,
                    'product_name_snapshot' => $it['p']->name,
                    'sku_snapshot' => $it['p']->sku,
                    'unit_snapshot' => $it['p']->unit,
                    'cost_price_snapshot' => $it['p']->cost_price,
                    'unit_price' => $it['p']->selling_price,
                    'qty' => $it['qty'],
                    'line_total' => $it['line'],
                ]);

                StockMovement::create([
                    'product_id' => $it['p']->id,
                    'movement_type' => 'online_reserve',
                    'qty_before' => $it['p']->stock_qty,
                    'qty_change' => -$it['qty'],
                    'qty_after' => $it['p']->stock_qty - $it['qty'],
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'notes' => "Reservasi pesanan online #{$order->order_number}",
                    'created_at' => $oo['time'],
                ]);
            }

            PaymentTransaction::create([
                'order_id' => $order->id,
                'reference_number' => 'PAY-ONL-' . strtoupper(Str::random(7)),
                'payment_method' => $oo['method'],
                'amount' => $grandTotal,
                'status' => ($oo['pay_status'] === 'paid') ? 'paid' : 'pending',
                'paid_at' => ($oo['pay_status'] === 'paid') ? $oo['time'] : null,
                'simulator_qr_string' => ($oo['method'] === 'qris') ? '00020101021226600016ID.CO.OMNIKASIR.WWW...' : null,
                'simulator_va_number' => ($oo['method'] === 'bank_transfer') ? '88012000' . $order->id : null,
                'created_at' => $oo['time'],
            ]);
        }

        // 9. Seed 7 Days of Attendance
        $staffMembers = [$admin, $cashier, $warehouse, $cashier2, $warehouse2];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->toDateString();

            foreach ($staffMembers as $idx => $st) {
                // Determine shift timing
                $isPagi = ($st->default_shift === 'pagi');
                $schedStart = $isPagi ? '08:00:00' : '15:00:00';
                $schedEnd = $isPagi ? '16:00:00' : '22:00:00';

                // Skip future clock-outs for today
                if ($i === 0) {
                    // Today: already clocked in
                    $clockIn = $isPagi
                        ? $date->copy()->setTime(7, 50 + $idx)
                        : $date->copy()->setTime(14, 55 + $idx);

                    Attendance::create([
                        'user_id' => $st->id,
                        'attendance_date' => $dateStr,
                        'shift_name' => ucfirst($st->default_shift),
                        'scheduled_start' => $schedStart,
                        'scheduled_end' => $schedEnd,
                        'clock_in_at' => $clockIn,
                        'clock_out_at' => null,
                        'work_duration_minutes' => 0,
                        'status' => 'hadir',
                        'clock_in_ip' => '100.108.222.30',
                        'notes' => 'Shift aktif hari ini',
                    ]);
                    continue;
                }

                // Historical: vary status
                $rand = ($idx + $i) % 10;
                if ($rand === 0) {
                    // Terlambat
                    $clockIn = $isPagi ? $date->copy()->setTime(8, 25) : $date->copy()->setTime(15, 20);
                    $clockOut = $isPagi ? $date->copy()->setTime(16, 10) : $date->copy()->setTime(22, 15);
                    $status = 'terlambat';
                    $notes = 'Terlambat karena kendala hujan deras di jalan';
                } elseif ($rand === 7 && $idx === 3) {
                    // Izin
                    $clockIn = null;
                    $clockOut = null;
                    $status = 'izin';
                    $notes = 'Izin urusan keluarga mendesak';
                } else {
                    // Tepat waktu
                    $clockIn = $isPagi ? $date->copy()->setTime(7, 50) : $date->copy()->setTime(14, 50);
                    $clockOut = $isPagi ? $date->copy()->setTime(16, 5) : $date->copy()->setTime(22, 5);
                    $status = 'hadir';
                    $notes = 'Kehadiran normal tepat waktu';
                }

                $duration = ($clockIn && $clockOut) ? $clockIn->diffInMinutes($clockOut) : 0;

                Attendance::create([
                    'user_id' => $st->id,
                    'attendance_date' => $dateStr,
                    'shift_name' => ucfirst($st->default_shift),
                    'scheduled_start' => $schedStart,
                    'scheduled_end' => $schedEnd,
                    'clock_in_at' => $clockIn,
                    'clock_out_at' => $clockOut,
                    'work_duration_minutes' => $duration,
                    'status' => $status,
                    'clock_in_ip' => '100.108.222.30',
                    'notes' => $notes,
                ]);
            }
        }
    }
}
