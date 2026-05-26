<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WastageController;
use App\Http\Controllers\PosController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth Routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customer CRUD routes
    Route::resource('customers', CustomerController::class);

    // Product (Inventory) CRUD routes
    Route::resource('products', ProductController::class);
    Route::get('/inventory', function () {
        $user = Auth::user();
        $modules = $user->role->modules()->get();
        return app(ProductController::class)->index();
    })->name('inventory.index');

    // Placeholder for employees
    Route::get('/employees', function () {
        $modules = auth()->user()->role->modules()->get();
        return view('modules.employees', ['modules' => $modules]);
    })->name('employees.index');

    // Wastage CRUD routes
    Route::resource('wastages', WastageController::class);
    Route::get('/wastage', function () {
        return app(WastageController::class)->index();
    })->name('wastage.index');

    // Placeholder routes for other modules
    Route::get('/suppliers', function () {
        $modules = auth()->user()->role->modules()->get();
        return view('modules.suppliers', ['modules' => $modules]);
    })->name('suppliers.index');

    // POS routes
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/order', [PosController::class, 'createOrder'])->name('pos.order.create');
    Route::get('/pos/order/{order}', [PosController::class, 'getOrder'])->name('pos.order.show');
    Route::post('/pos/order/{order}/item', [PosController::class, 'addItem'])->name('pos.item.add');
    Route::delete('/pos/order/{order}/item/{item}', [PosController::class, 'removeItem'])->name('pos.item.remove');
    Route::put('/pos/order/{order}/item/{item}', [PosController::class, 'updateItem'])->name('pos.item.update');
    Route::post('/pos/order/{order}/hold', [PosController::class, 'holdOrder'])->name('pos.order.hold');
    Route::post('/pos/order/{order}/complete', [PosController::class, 'completeOrder'])->name('pos.order.complete');
    Route::post('/pos/order/{order}/kot', [PosController::class, 'printKot'])->name('pos.order.kot');
    Route::post('/pos/order/{order}/bot', [PosController::class, 'printBot'])->name('pos.order.bot');
    Route::post('/pos/order/{order}/customer', [PosController::class, 'updateCustomer'])->name('pos.order.customer');
    Route::post('/pos/order/{order}/waiter-bill', [PosController::class, 'printWaiterBill'])->name('pos.order.waiter_bill');
    Route::post('/pos/order/{order}/live-bill', [PosController::class, 'toggleLiveBill'])->name('pos.order.live_bill');
    Route::post('/pos/order/{order}/close-table', [PosController::class, 'closeTable'])->name('pos.order.close_table');
    Route::get('/pos/table/{table}/orders', [PosController::class, 'getTableOrders'])->name('pos.table.orders');
    Route::get('/pos/tables', [PosController::class, 'getTables'])->name('pos.tables');
    Route::get('/pos/products', [PosController::class, 'getProducts'])->name('pos.products');
    Route::get('/pos/held-orders', [PosController::class, 'getHeldOrders'])->name('pos.held');

    Route::get('/reports', function () {
        $modules = auth()->user()->role->modules()->get();
        return view('modules.reports', ['modules' => $modules]);
    })->name('reports.index');

    Route::get('/settings', function () {
        $modules = auth()->user()->role->modules()->get();
        return view('modules.settings', ['modules' => $modules]);
    })->name('settings.index');
});
