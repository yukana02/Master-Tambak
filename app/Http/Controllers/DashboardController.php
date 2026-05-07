<?php

namespace App\Http\Controllers;

use App\Models\Pond;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $income = Transaction::where('type', 'income')->sum('amount');
        $expense = Transaction::where('type', 'expense')->sum('amount');
        $sales = Sale::sum('total');

        return view('dashboard', [
            'pondCount' => Pond::count(),
            'productCount' => Product::count(),
            'lowStockCount' => Product::where('stock', '<=', 10)->count(),
            'saleCount' => Sale::count(),
            'income' => $income,
            'expense' => $expense,
            'balance' => $income + $sales - $expense,
            'monthlyChart' => $this->monthlyChart(),
        ]);
    }

    private function monthlyChart(): array
    {
        $months = collect(range(5, 0))->map(fn (int $offset) => now()->subMonths($offset));

        return [
            'labels' => $months->map->format('M Y')->values(),
            'income' => $months->map(fn ($month) => (float) Transaction::where('type', 'income')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount'))->values(),
            'expense' => $months->map(fn ($month) => (float) Transaction::where('type', 'expense')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount'))->values(),
        ];
    }
}
