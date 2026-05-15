<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', [
            'products' => Product::with('category')->latest()->get(),
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product(['is_active' => true, 'unit' => 'pcs']),
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create($request->safe()->merge([
            'is_active' => $request->boolean('is_active'),
        ])->all());

        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->safe()->merge([
            'is_active' => $request->boolean('is_active'),
        ])->all());

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $productName = $product->name;
            $product->delete();

            return redirect()->route('products.index')->with('success', "Produk '{$productName}' berhasil dihapus secara permanen.");
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus produk: ' . $th->getMessage());
        }
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        ProductCategory::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'],
            'description' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Kategori produk berhasil dibuat.');
    }
}
