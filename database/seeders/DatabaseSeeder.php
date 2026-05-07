<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Pond;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage roles',
            'manage ponds',
            'manage finance',
            'manage products',
            'use pos',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $superAdmin = Role::findOrCreate('Super Admin')->givePermissionTo($permissions);
        $admin = Role::findOrCreate('Admin')->givePermissionTo(['manage ponds', 'manage finance', 'export reports']);
        $kasir = Role::findOrCreate('Kasir')->givePermissionTo(['use pos']);

        User::updateOrCreate(
            ['email' => 'superadmin@tambak.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        )->syncRoles([$superAdmin]);

        User::updateOrCreate(
            ['email' => 'admin@tambak.test'],
            ['name' => 'Admin Tambak', 'password' => Hash::make('password')]
        )->syncRoles([$admin]);

        User::updateOrCreate(
            ['email' => 'kasir@tambak.test'],
            ['name' => 'Kasir Tambak', 'password' => Hash::make('password')]
        )->syncRoles([$kasir]);

        $incomeCategory = Category::firstOrCreate(['name' => 'Penjualan Ikan', 'type' => 'income']);
        $feedCategory = Category::firstOrCreate(['name' => 'Pakan', 'type' => 'expense']);
        Category::firstOrCreate(['name' => 'Perawatan Kolam', 'type' => 'expense']);

        Pond::insertOrIgnore([
            ['name' => 'Kolam A1', 'fish_type' => 'Lele', 'fish_count' => 1500, 'stocking_date' => now()->subDays(35), 'harvest_date' => now()->addDays(25), 'x' => 0, 'y' => 0, 'width' => 3, 'height' => 2, 'notes' => 'Pertumbuhan stabil.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kolam B1', 'fish_type' => 'Nila', 'fish_count' => 900, 'stocking_date' => now()->subDays(80), 'harvest_date' => now()->addDays(8), 'x' => 3, 'y' => 0, 'width' => 3, 'height' => 2, 'notes' => 'Mendekati panen.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kolam C1', 'fish_type' => 'Gurame', 'fish_count' => 400, 'stocking_date' => now()->subDays(150), 'harvest_date' => now()->subDays(3), 'x' => 6, 'y' => 0, 'width' => 3, 'height' => 2, 'notes' => 'Perlu jadwal panen.', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $categories = collect(['Ikan', 'Pakan', 'Alat', 'Pupuk'])->mapWithKeys(fn (string $name) => [
            $name => ProductCategory::firstOrCreate(['name' => $name]),
        ]);

        Product::insertOrIgnore([
            ['product_category_id' => $categories['Ikan']->id, 'name' => 'Lele Konsumsi', 'sku' => 'IKN-LELE', 'price' => 25000, 'stock' => 120, 'unit' => 'kg', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['product_category_id' => $categories['Ikan']->id, 'name' => 'Nila Segar', 'sku' => 'IKN-NILA', 'price' => 32000, 'stock' => 80, 'unit' => 'kg', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['product_category_id' => $categories['Pakan']->id, 'name' => 'Pakan Pelet 781', 'sku' => 'PKN-781', 'price' => 14500, 'stock' => 250, 'unit' => 'kg', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['product_category_id' => $categories['Alat']->id, 'name' => 'Serok Ikan', 'sku' => 'ALT-SRK', 'price' => 45000, 'stock' => 12, 'unit' => 'pcs', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['product_category_id' => $categories['Pupuk']->id, 'name' => 'Pupuk Kolam Organik', 'sku' => 'PPK-ORG', 'price' => 30000, 'stock' => 35, 'unit' => 'sak', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Transaction::insertOrIgnore([
            ['category_id' => $incomeCategory->id, 'type' => 'income', 'amount' => 2500000, 'description' => 'Penjualan ikan minggu pertama', 'transaction_date' => now()->subDays(10)->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $feedCategory->id, 'type' => 'expense', 'amount' => 750000, 'description' => 'Pembelian pakan', 'transaction_date' => now()->subDays(6)->toDateString(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
