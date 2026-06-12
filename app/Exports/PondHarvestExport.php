<?php

namespace App\Exports;

use App\Models\PondHarvestInput;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PondHarvestExport implements FromQuery, WithHeadings, WithMapping
{
    private int $harvestId;

    public function __construct(int $harvestId)
    {
        $this->harvestId = $harvestId;
    }

    public function query()
    {
        return PondHarvestInput::where('pond_harvest_id', $this->harvestId)
            ->orderBy('harvested_at');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Nama Bakul', 'Kg', 'Harga/Kg', 'Total', 'Catatan'];
    }

    public function map($row): array
    {
        return [
            $row->harvested_at?->format('d/m/Y'),
            $row->bucket_name,
            $row->kg,
            $row->price_per_kg,
            $row->total_price,
            $row->notes ?? '-',
        ];
    }
}
