<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_sales_report(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Super Admin']));
        $category = ProductCategory::create(['name' => 'Ikan']);
        $product = Product::create([
            'product_category_id' => $category->id,
            'name' => 'Lele Konsumsi',
            'sku' => 'IKN-TEST',
            'price' => 50000,
            'stock' => 10,
            'unit' => 'kg',
            'is_active' => true,
        ]);

        Sale::create([
            'invoice_number' => 'INV-TEST-001',
            'subtotal' => 100000,
            'discount' => 5000,
            'total' => 95000,
            'payment_method' => 'cash',
            'paid_amount' => 100000,
            'change_amount' => 5000,
            'sold_at' => now(),
        ])->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Lele Konsumsi',
            'qty' => 2,
            'price' => 50000,
            'subtotal' => 100000,
        ]);

        $this->actingAs($user)
            ->get(route('sales.index'))
            ->assertOk()
            ->assertSee('Laporan Penjualan')
            ->assertSee('INV-TEST-001')
            ->assertSee('Rp 95.000');
    }

    public function test_kasir_cannot_view_sales_report(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Kasir']));

        $this->actingAs($user)
            ->get(route('sales.index'))
            ->assertForbidden();
    }
}
