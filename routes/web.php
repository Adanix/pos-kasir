<?php

use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashbookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DemoAuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PosOnlineOrderController;
use App\Http\Controllers\PosReceiptController;
use App\Http\Controllers\PosShiftController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StoreCartController;
use App\Http\Controllers\StoreCheckoutController;
use App\Http\Controllers\StoreOrderController;
use App\Http\Controllers\StorePaymentController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WarehouseInventoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| P0 — Public & Demo Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/docs', [PublicPageController::class, 'docs'])->name('public.docs');

Route::middleware('demo.mode')->group(function () {
    Route::get('/demo/login', [DemoAuthController::class, 'showLogin'])->name('demo.login');
    Route::get('/login', fn () => redirect()->route('demo.login'))->name('login');
    Route::post('/demo/quick-login', [DemoAuthController::class, 'quickLogin'])->name('demo.quick-login');
});

Route::post('/logout', [DemoAuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('/demo/reset', [DemoAuthController::class, 'resetDemoData'])->name('demo.reset')->middleware(['auth', 'demo.mode']);

/*
|--------------------------------------------------------------------------
| Toko Online Publik (Public Storefront)
|--------------------------------------------------------------------------
*/
Route::prefix('store')->name('store.')->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('catalog');
    Route::get('/products/{slug}', [StorefrontController::class, 'show'])->name('product.show');

    // Cart
    Route::get('/cart', [StoreCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [StoreCartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update', [StoreCartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [StoreCartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [StoreCheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [StoreCheckoutController::class, 'store'])->name('checkout.store');

    // Payment Simulation
    Route::get('/payment/{order_number}', [StorePaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order_number}/simulate', [StorePaymentController::class, 'simulate'])->name('payment.simulate');

    // Order Tracking & E-Receipt
    Route::get('/orders', [StoreOrderController::class, 'lookup'])->name('orders.lookup');
    Route::get('/orders/{order_number}', [StoreOrderController::class, 'show'])->name('orders.show');
});

/*
|--------------------------------------------------------------------------
| Layout 1: POS / Kasir Terminal (role: admin, cashier)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,cashier'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/catalog-snapshot', [PosController::class, 'catalogSnapshot'])->name('catalog.snapshot');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
    Route::post('/sync-offline', [PosController::class, 'syncOffline'])->name('sync.offline');
    Route::get('/transactions', [PosController::class, 'transactions'])->name('transactions.index');

    // Thermal Receipt & Bridge Print
    Route::get('/orders/{order}/receipt', [PosReceiptController::class, 'show'])->name('orders.receipt');
    Route::post('/orders/{order}/print-bridge', [PosReceiptController::class, 'printBridge'])->name('orders.print-bridge');

    // Shift Management
    Route::get('/shift', [PosShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift/open', [PosShiftController::class, 'open'])->name('shift.open');
    Route::post('/shift/close', [PosShiftController::class, 'close'])->name('shift.close');
    Route::post('/shift/expense', [PosShiftController::class, 'recordPettyExpense'])->name('shift.expense');

    // Online Order Queue
    Route::get('/online-orders', [PosOnlineOrderController::class, 'index'])->name('online-orders.index');
    Route::post('/online-orders/{order}/confirm-payment', [PosOnlineOrderController::class, 'confirmPayment'])->name('online-orders.confirm-payment');
    Route::post('/online-orders/{order}/status', [PosOnlineOrderController::class, 'updateStatus'])->name('online-orders.status');
});

/*
|--------------------------------------------------------------------------
| Layout 2: Warehouse / Gudang (role: admin, warehouse)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,warehouse'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/inventory', [WarehouseInventoryController::class, 'index'])->name('inventory.index');

    // Products & Categories
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

    // Stock Adjustments & Movements (Kartu Stok)
    Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
    Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('movements.index');
});

/*
|--------------------------------------------------------------------------
| Employee Attendance (Personal: admin, cashier, warehouse)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,cashier,warehouse'])->prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
});

/*
|--------------------------------------------------------------------------
| Layout 2: Admin Dashboard & Reports (role: admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Cashbook (Buku Kas)
    Route::get('/cashbook', [CashbookController::class, 'index'])->name('cashbook.index');
    Route::post('/cashbook', [CashbookController::class, 'store'])->name('cashbook.store');

    // Financial Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // All-Staff Attendance Monitoring & Recap
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AdminAttendanceController::class, 'store'])->name('attendance.store');

    // Users & Roles
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
    Route::put('/roles', [AdminRoleController::class, 'update'])->name('roles.update');
});
