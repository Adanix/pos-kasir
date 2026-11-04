<?php

namespace Tests\Feature;

use App\Models\CashbookEntry;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\OmniKasirDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAttendanceAndCashbookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OmniKasirDemoSeeder::class);
    }

    public function test_warehouse_inventory_and_stock_adjustment(): void
    {
        $warehouse = User::where('role', 'warehouse')->first();
        $this->actingAs($warehouse);

        $inventoryPage = $this->get(route('warehouse.inventory.index'));
        $inventoryPage->assertStatus(200);
        $inventoryPage->assertSee('Ringkasan Stok');

        $product = Product::first();
        $stockBefore = $product->stock_qty;

        // Record restock adjustment
        $response = $this->post(route('warehouse.adjustments.store'), [
            'product_id' => $product->id,
            'adjustment_type' => 'restock',
            'qty_change' => 25,
            'unit_cost' => $product->cost_price,
            'reason' => 'Penerimaan kontainer supplier utama',
        ]);
        $response->assertRedirect(route('warehouse.adjustments.index'));

        $this->assertEquals($stockBefore + 25, $product->fresh()->stock_qty);

        // Movement card must reflect this
        $movementsPage = $this->get(route('warehouse.movements.index', ['product_id' => $product->id]));
        $movementsPage->assertStatus(200);
        $movementsPage->assertSee('RESTOCK');
    }

    public function test_personal_attendance_clock_in_and_clock_out(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        // Delete any existing attendance for today for a clean test
        \App\Models\Attendance::where('user_id', $cashier->id)->whereDate('attendance_date', today())->delete();

        $page = $this->get(route('attendance.index'));
        $page->assertStatus(200);
        $page->assertSee('Absensi Kehadiran');

        // Clock In
        $inResponse = $this->post(route('attendance.clock-in'), [
            'notes' => 'Masuk shift pagi tepat waktu',
        ]);
        $inResponse->assertSessionHas('success');

        $attendance = \App\Models\Attendance::where('user_id', $cashier->id)->whereDate('attendance_date', today())->first();
        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->clock_in_at);

        // Clock Out
        $outResponse = $this->post(route('attendance.clock-out'), [
            'notes' => 'Pulang setelah tutup kasir',
        ]);
        $outResponse->assertSessionHas('success');

        $attendance->refresh();
        $this->assertNotNull($attendance->clock_out_at);
        $this->assertGreaterThanOrEqual(0, $attendance->work_duration_minutes);
    }

    public function test_admin_cashbook_and_financial_reports(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Cashbook page
        $cashbookPage = $this->get(route('admin.cashbook.index'));
        $cashbookPage->assertStatus(200);
        $cashbookPage->assertSee('Buku Kas Harian');

        // Record manual cashbook entry
        $entryResponse = $this->post(route('admin.cashbook.store'), [
            'type' => 'out',
            'category' => 'listrik_air',
            'amount' => 150000,
            'description' => 'Pembayaran token PLN toko bulan ini',
            'payment_method' => 'cash',
        ]);
        $entryResponse->assertSessionHas('success');

        $this->assertDatabaseHas('cashbook_entries', [
            'category' => 'listrik_air',
            'amount' => 150000,
            'type' => 'out',
        ]);

        // Financial report
        $reportPage = $this->get(route('admin.reports.index'));
        $reportPage->assertStatus(200);
        $reportPage->assertSee('Laporan Omzet');
        $reportPage->assertSee('Laba Kotor');
    }
}
