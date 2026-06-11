<?php

namespace App\Exports;

use App\Models\PondHarvestInput;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PondHarvestInputExport implements FromQuery, WithHeadings, WithMapping
{
    private int $pondId;

    public function __construct(int $pondId)
    {
        $this->pondId = $pondId;
    }

    public function query()
    {
        return PondHarvestInput::where('pond_id', $this->pondId)
            ->orderByDesc('harvested_at');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Nama Bakul', 'Kg', 'Harga/Kg', 'Total', 'Status', 'Catatan'];
    }

    public function map($row): array
    {
        return [
            $row->harvested_at?->format('d/m/Y'),
            $row->bucket_name,
            $row->kg,
            $row->price_per_kg,
            $row->total_price,
            $row->status === 'draft' ? 'Draft' : 'Final',
            $row->notes ?? '-',
        ];
    }
}
