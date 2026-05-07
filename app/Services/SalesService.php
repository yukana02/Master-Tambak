<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesService
{
    /**
     * @param  array<int, array{product_id:int, qty:int}>  $items
     */
    public function checkout(array $items, float $discount, float $paidAmount, string $paymentMethod = 'cash'): Sale
    {
        return DB::transaction(function () use ($items, $discount, $paidAmount, $paymentMethod) {
            $products = Product::whereIn('id', collect($items)->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $product = $products->get((int) $item['product_id']);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak ditemukan atau tidak aktif.']);
                }

                $qty = (int) $item['qty'];

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup. Sisa stok: {$product->stock}.",
                    ]);
                }

                $lineSubtotal = (float) $product->price * $qty;
                $subtotal += $lineSubtotal;

                $preparedItems[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'price' => (float) $product->price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discount = min($discount, $subtotal);
            $total = $subtotal - $discount;

            if ($paidAmount < $total) {
                throw ValidationException::withMessages(['paid_amount' => 'Nominal cash kurang dari total pembayaran.']);
            }

            $sale = Sale::create([
                'invoice_number' => 'TRX-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'paid_amount' => $paidAmount,
                'change_amount' => $paidAmount - $total,
                'sold_at' => now(),
            ]);

            foreach ($preparedItems as $preparedItem) {
                $product = $preparedItem['product'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'qty' => $preparedItem['qty'],
                    'price' => $preparedItem['price'],
                    'subtotal' => $preparedItem['subtotal'],
                ]);

                $product->decrement('stock', $preparedItem['qty']);
            }

            return $sale->load('items.product');
        });
    }
}
