<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\OmniKasirDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionBoundaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OmniKasirDemoSeeder::class);
    }

    public function test_unauthenticated_users_are_redirected_to_demo_login(): void
    {
        $this->get('/pos')->assertRedirect(route('demo.login'));
        $this->get('/admin/dashboard')->assertRedirect(route('demo.login'));
        $this->get('/warehouse/inventory')->assertRedirect(route('demo.login'));
        $this->get('/attendance')->assertRedirect(route('demo.login'));
    }

    public function test_cashier_can_access_pos_and_attendance_but_not_admin_or_warehouse(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        // Allowed
        $this->get('/pos')->assertStatus(200);
        $this->get('/pos/shift')->assertStatus(200);
        $this->get('/pos/online-orders')->assertStatus(200);
        $this->get('/attendance')->assertStatus(200);

        // Forbidden
        $this->get('/admin/dashboard')->assertStatus(403);
        $this->get('/admin/cashbook')->assertStatus(403);
        $this->get('/admin/reports')->assertStatus(403);
        $this->get('/admin/users')->assertStatus(403);
        $this->get('/warehouse/inventory')->assertStatus(403);
        $this->get('/warehouse/products')->assertStatus(403);
        $this->get('/warehouse/stock-adjustments')->assertStatus(403);
    }

    public function test_warehouse_can_access_inventory_but_not_pos_or_admin_finance(): void
    {
        $warehouse = User::where('role', 'warehouse')->first();
        $this->actingAs($warehouse);

        // Allowed
        $this->get('/warehouse/inventory')->assertStatus(200);
        $this->get('/warehouse/products')->assertStatus(200);
        $this->get('/warehouse/stock-adjustments')->assertStatus(200);
        $this->get('/warehouse/stock-movements')->assertStatus(200);
        $this->get('/attendance')->assertStatus(200);

        // Forbidden
        $this->get('/pos')->assertStatus(403);
        $this->get('/pos/shift')->assertStatus(403);
        $this->get('/admin/dashboard')->assertStatus(403);
        $this->get('/admin/cashbook')->assertStatus(403);
        $this->get('/admin/reports')->assertStatus(403);
        $this->get('/admin/users')->assertStatus(403);
    }

    public function test_admin_can_access_all_modules(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $this->get('/admin/dashboard')->assertStatus(200);
        $this->get('/admin/cashbook')->assertStatus(200);
        $this->get('/admin/reports')->assertStatus(200);
        $this->get('/admin/attendance')->assertStatus(200);
        $this->get('/admin/users')->assertStatus(200);
        $this->get('/admin/roles')->assertStatus(200);
        $this->get('/pos')->assertStatus(200);
        $this->get('/warehouse/inventory')->assertStatus(200);
        $this->get('/warehouse/products')->assertStatus(200);
    }
}
