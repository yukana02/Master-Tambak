<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Services\SalesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))
            ->when($request->category, fn ($query, $category) => $query->where('product_category_id', $category))
            ->orderBy('name')
            ->get();

        return view('pos.index', [
            'products' => $products,
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function store(SaleRequest $request, SalesService $salesService): RedirectResponse
    {
        $sale = $salesService->checkout(
            $request->validated('items'),
            (float) $request->input('discount', 0),
            (float) $request->input('paid_amount'),
            $request->input('payment_method', 'cash')
        );

        return redirect()->route('pos.receipt', $sale)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function receipt(Sale $sale): View
    {
        return view('pos.receipt', ['sale' => $sale->load('items')]);
    }
}
