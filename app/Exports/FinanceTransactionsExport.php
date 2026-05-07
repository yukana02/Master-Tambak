<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinanceTransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Transaction::query()->with('category')->latest('transaction_date');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi', 'Nominal'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date?->format('Y-m-d'),
            $transaction->type,
            $transaction->category?->name,
            $transaction->description,
            $transaction->amount,
        ];
    }
}
