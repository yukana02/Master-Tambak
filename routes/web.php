<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PondController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SalesReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/roles/assign', [RolePermissionController::class, 'assign'])->name('roles.assign');
    Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'syncPermissions'])->name('roles.permissions');
});

Route::middleware(['auth', 'role:Super Admin|Admin'])->group(function () {
    Route::resource('ponds', PondController::class);
    Route::post('/ponds-layout', [PondController::class, 'layout'])->name('ponds.layout');

    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance', [FinanceController::class, 'store'])->name('finance.store');
    Route::delete('/finance/{transaction}', [FinanceController::class, 'destroy'])->name('finance.destroy');
    Route::post('/finance/categories', [FinanceController::class, 'storeCategory'])->name('finance.categories.store');
    Route::get('/exports/finance', [ExportController::class, 'finance'])->name('exports.finance');
});

Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::post('/product-categories', [ProductController::class, 'storeCategory'])->name('product-categories.store');
    Route::get('/sales', SalesReportController::class)->name('sales.index');
    Route::get('/exports/sales', [ExportController::class, 'sales'])->name('exports.sales');
});

Route::middleware(['auth', 'role:Super Admin|Kasir'])->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
});

require __DIR__.'/auth.php';
