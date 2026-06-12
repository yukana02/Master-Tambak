<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PondController;
use App\Http\Controllers\PondFeedingController;
use App\Http\Controllers\PondHarvestController;
use App\Http\Controllers\PondHarvestInputController;
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
    Route::post('/roles/users', [RolePermissionController::class, 'storeUser'])->name('roles.users.store');
    Route::post('/roles/assign', [RolePermissionController::class, 'assign'])->name('roles.assign');
    Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'syncPermissions'])->name('roles.permissions');
});

Route::middleware(['auth', 'role:Super Admin|Admin'])->group(function () {
    Route::get('/feed-categories', [FeedController::class, 'categoriesIndex'])->name('feed-categories.index');
    Route::post('/feed-categories', [FeedController::class, 'storeCategory'])->name('feed-categories.store');
    Route::put('/feed-categories/{category}', [FeedController::class, 'updateCategory'])->name('feed-categories.update');
    Route::delete('/feed-categories/{category}', [FeedController::class, 'destroyCategory'])->name('feed-categories.destroy');
    Route::resource('feeds', FeedController::class);

    Route::resource('ponds', PondController::class);
    Route::post('/ponds/{pond}/feedings', [PondFeedingController::class, 'store'])->name('ponds.feedings.store');
    Route::delete('/ponds/{pond}/feedings/{feeding}', [PondFeedingController::class, 'destroy'])->name('ponds.feedings.destroy');
    Route::post('/ponds/{pond}/harvests', [PondHarvestController::class, 'store'])->name('ponds.harvests.store');
    Route::get('/ponds/{pond}/harvests/{harvest}/export', [PondHarvestController::class, 'export'])->name('ponds.harvests.export');
    Route::post('/ponds/{pond}/inputs', [PondHarvestInputController::class, 'store'])->name('ponds.inputs.store');
    Route::put('/ponds/{pond}/inputs/{input}', [PondHarvestInputController::class, 'update'])->name('ponds.inputs.update');
    Route::delete('/ponds/{pond}/inputs/{input}', [PondHarvestInputController::class, 'destroy'])->name('ponds.inputs.destroy');
    Route::get('/ponds/{pond}/inputs/export', [PondHarvestInputController::class, 'export'])->name('ponds.inputs.export');
    Route::post('/ponds-layout', [PondController::class, 'layout'])->name('ponds.layout');

    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance', [FinanceController::class, 'store'])->name('finance.store');
    Route::delete('/finance/{transaction}', [FinanceController::class, 'destroy'])->name('finance.destroy');
    Route::post('/finance/categories', [FinanceController::class, 'storeCategory'])->name('finance.categories.store');
    Route::get('/exports/finance', [ExportController::class, 'finance'])->name('exports.finance');
});

Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/deploy', \App\Http\Controllers\DeployController::class)->name('deploy');
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
