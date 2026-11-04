<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class P0PublicAndDemoAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\OmniKasirDemoSeeder::class);
    }

    public function test_public_landing_page_renders_with_ctas_and_contact(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('OmniKasir');
        $response->assertSee('Coba Demo Aplikasi');
        $response->assertSee('Lihat Toko Online Publik');
        $response->assertSee('mailto:nixresponding@gmail.com', false);
    }

    public function test_public_docs_page_renders_with_contact_and_modules(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertSee('Dokumentasi Produk OmniKasir');
        $response->assertSee('Butuh Sistem yang Disesuaikan Lebih Lanjut?');
        $response->assertSee('mailto:nixresponding@gmail.com', false);
        $response->assertSee('Layar Kasir (POS Terminal)');
    }

    public function test_demo_login_screen_renders_personas_without_passwords(): void
    {
        $response = $this->get('/demo/login');

        $response->assertStatus(200);
        $response->assertSee('Pilih Peran untuk Menjelajahi Demo OmniKasir');
        $response->assertSee('Pemilik / Admin');
        $response->assertSee('Kasir');
        $response->assertSee('Staf Gudang');
        $response->assertDontSee('type="password"', false);
    }

    public function test_quick_login_authenticates_each_allowed_persona(): void
    {
        // 1. Admin
        $resAdmin = $this->post('/demo/quick-login', ['persona' => 'admin']);
        $resAdmin->assertRedirect('/admin/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals('admin', Auth::user()->role);
        $this->assertEquals('owner@omnikasir.demo', Auth::user()->email);

        // 2. Cashier
        $resCashier = $this->post('/demo/quick-login', ['persona' => 'cashier']);
        $resCashier->assertRedirect('/pos');
        $this->assertTrue(Auth::check());
        $this->assertEquals('cashier', Auth::user()->role);
        $this->assertEquals('kasir@omnikasir.demo', Auth::user()->email);

        // 3. Warehouse
        $resWarehouse = $this->post('/demo/quick-login', ['persona' => 'warehouse']);
        $resWarehouse->assertRedirect('/warehouse/inventory');
        $this->assertTrue(Auth::check());
        $this->assertEquals('warehouse', Auth::user()->role);
        $this->assertEquals('gudang@omnikasir.demo', Auth::user()->email);
    }

    public function test_quick_login_rejects_invalid_persona_key(): void
    {
        $response = $this->post('/demo/quick-login', ['persona' => 'superadmin']);
        $response->assertSessionHasErrors('persona');
        $this->assertFalse(Auth::check());
    }

    public function test_logout_and_demo_mode_disabled_guard(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $logoutRes = $this->post('/logout');
        $logoutRes->assertRedirect(route('demo.login'));
        $this->assertFalse(Auth::check());

        // Test demo mode disabled
        config(['demo.enabled' => false]);
        $disabledLogin = $this->get('/demo/login');
        $disabledLogin->assertStatus(404);

        $disabledQuickLogin = $this->post('/demo/quick-login', ['persona' => 'admin']);
        $disabledQuickLogin->assertStatus(404);
    }

    public function test_role_authorization_boundaries(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $warehouse = User::where('role', 'warehouse')->first();

        // Cashier cannot access admin cashbook
        $this->actingAs($cashier);
        $this->get('/admin/cashbook')->assertStatus(403);

        // Warehouse cannot access pos terminal
        $this->actingAs($warehouse);
        $this->get('/pos')->assertStatus(403);
    }
}
