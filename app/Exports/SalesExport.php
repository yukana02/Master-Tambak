<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Sale::query()->with('items')->latest('sold_at');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Invoice', 'Jumlah Item', 'Subtotal', 'Diskon', 'Total', 'Cash', 'Kembalian'];
    }

    public function map($sale): array
    {
        return [
            $sale->sold_at?->format('Y-m-d H:i'),
            $sale->invoice_number,
            $sale->items->sum('qty'),
            $sale->subtotal,
            $sale->discount,
            $sale->total,
            $sale->paid_amount,
            $sale->change_amount,
        ];
    }
}
