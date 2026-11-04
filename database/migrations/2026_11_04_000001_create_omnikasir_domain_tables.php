<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnUpdate();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('unit')->default('Pcs');
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('stock_qty')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->integer('weight_grams')->default(250);
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured_online')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index(['stock_qty', 'min_stock_alert']);
        });

        // 3. Role Permissions
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role')->index();
            $table->string('permission_key');
            $table->string('permission_label');
            $table->boolean('is_allowed')->default(true);
            $table->timestamps();

            $table->unique(['role', 'permission_key']);
        });

        // 4. POS Shifts
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->string('shift_name')->default('Pagi');
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('cash_sales_total', 15, 2)->default(0);
            $table->decimal('non_cash_sales_total', 15, 2)->default(0);
            $table->decimal('petty_cash_out_total', 15, 2)->default(0);
            $table->decimal('closing_cash_expected', 15, 2)->default(0);
            $table->decimal('closing_cash_actual', 15, 2)->nullable();
            $table->decimal('difference_amount', 15, 2)->nullable();
            $table->string('status')->default('open')->index();
            $table->text('notes')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // 5. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('client_tx_id')->nullable()->unique();
            $table->string('channel')->default('pos')->index(); // pos, online
            $table->string('status')->default('completed')->index(); // pending_payment, pending_confirmation, paid, processing, ready_for_pickup, shipped, completed, cancelled, refunded
            $table->string('payment_status')->default('paid')->index(); // unpaid, pending, paid, failed, cancelled, refunded
            $table->string('payment_method')->default('cash'); // cash, qris, bank_transfer, cod
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pos_shift_id')->nullable()->constrained('pos_shifts')->nullOnDelete();
            $table->string('customer_name')->default('Pelanggan Umum');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('fulfillment_type')->default('walk_in'); // walk_in, pickup, delivery
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('customer_address')->nullable();
            $table->text('customer_notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('cash_received', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->integer('print_count')->default(0);
            $table->boolean('is_offline_synced')->default(false);
            $table->timestamp('ordered_at')->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 6. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate();
            $table->string('product_name_snapshot');
            $table->string('sku_snapshot');
            $table->string('unit_snapshot')->default('Pcs');
            $table->decimal('cost_price_snapshot', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });

        // 7. Payment Transactions
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->string('payment_method');
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending, paid, failed, cancelled, refunded
            $table->text('simulator_qr_string')->nullable();
            $table->string('simulator_va_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Stock Movements (Kartu Stok)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('movement_type')->index(); // initial, pos_sale, online_reserve, online_release, restock, opname_adjustment, damaged, return_refund
            $table->integer('qty_before');
            $table->integer('qty_change'); // signed (+/-)
            $table->integer('qty_after');
            $table->string('reference_type')->nullable(); // order, adjustment, seeder
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 9. Stock Adjustments
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->string('adjustment_type')->default('restock'); // restock, opname, damaged, return
            $table->integer('qty_before');
            $table->integer('qty_change');
            $table->integer('qty_after');
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->text('reason')->nullable();
            $table->timestamp('adjusted_at');
            $table->timestamps();
        });

        // 10. Attendances
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->date('attendance_date')->index();
            $table->string('shift_name')->default('Pagi');
            $table->time('scheduled_start')->default('08:00:00');
            $table->time('scheduled_end')->default('16:00:00');
            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->integer('work_duration_minutes')->default(0);
            $table->string('status')->default('hadir')->index(); // hadir, terlambat, izin, sakit, alpha
            $table->string('clock_in_ip')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'attendance_date']);
        });

        // 11. Daily Cashbook Entries (Buku Kas)
        Schema::create('cashbook_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique();
            $table->date('entry_date')->index();
            $table->timestamp('transaction_at')->index();
            $table->string('type')->index(); // in, out
            $table->string('category')->index(); // modal_awal, penjualan_pos, penjualan_online, operasional_toko, pembelian_stok, gaji_staf, refund_penjualan, lainnya
            $table->string('payment_method')->default('cash'); // cash, qris, bank_transfer
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->text('description');
            $table->string('reference_number')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('pos_shift_id')->nullable()->constrained('pos_shifts')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 12. Print Logs
        Schema::create('print_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('paper_size')->default('58mm'); // 58mm, 80mm
            $table->string('printer_mode')->default('browser_print'); // browser_print, escpos_bridge
            $table->string('status')->default('printed'); // printed, failed
            $table->text('raw_payload')->nullable();
            $table->timestamp('printed_at');
            $table->timestamps();
        });

        // 13. Notification Logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('channel')->default('whatsapp_sim'); // whatsapp_sim, email_sim
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('message_body');
            $table->string('status')->default('sent_simulated');
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('print_logs');
        Schema::dropIfExists('cashbook_entries');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('pos_shifts');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
