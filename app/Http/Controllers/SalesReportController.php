<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    public function __invoke(): View
    {
        $sales = Sale::query()
            ->withCount('items')
            ->withSum('items', 'qty')
            ->latest('sold_at')
            ->paginate(15);

        return view('sales.index', [
            'sales' => $sales,
            'totalSales' => Sale::sum('total'),
            'totalDiscount' => Sale::sum('discount'),
            'totalItems' => Sale::query()
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->sum('sale_items.qty'),
        ]);
    }
}
