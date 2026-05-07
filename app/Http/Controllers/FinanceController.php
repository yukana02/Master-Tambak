<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinanceTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::with('category')->latest('transaction_date')->paginate(15);
        $income = Transaction::where('type', 'income')->sum('amount');
        $expense = Transaction::where('type', 'expense')->sum('amount');

        return view('finance.index', [
            'transactions' => $transactions,
            'categories' => Category::orderBy('type')->orderBy('name')->get(),
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'monthlyChart' => $this->monthlyChart(),
            'categoryChart' => $this->categoryChart(),
        ]);
    }

    public function store(FinanceTransactionRequest $request): RedirectResponse
    {
        Transaction::create($request->validated());

        return back()->with('success', 'Transaksi keuangan berhasil disimpan.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return back()->with('success', 'Transaksi keuangan berhasil dihapus.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        Category::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Kategori keuangan berhasil dibuat.');
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

    private function categoryChart(): array
    {
        $rows = Transaction::query()
            ->selectRaw('categories.name as name, sum(transactions.amount) as total')
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->groupBy('categories.name')
            ->orderBy('categories.name')
            ->get();

        return [
            'labels' => $rows->pluck('name'),
            'data' => $rows->pluck('total')->map(fn ($total) => (float) $total),
        ];
    }
}
