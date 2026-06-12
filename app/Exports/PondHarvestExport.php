<?php

namespace App\Exports;

use App\Models\PondHarvestInput;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PondHarvestExport implements FromQuery, WithHeadings, WithMapping, WithEvents
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
        return ['Tanggal', 'Nama Bakul', 'Kg', 'Harga/Kg', 'Metode Bayar', 'Cash', 'TF', 'Total', 'Catatan'];
    }

    public function map($row): array
    {
        return [
            $row->harvested_at?->format('d/m/Y'),
            $row->bucket_name,
            $row->kg,
            $row->price_per_kg,
            strtoupper($row->payment_method ?? 'cash'),
            $row->cash_amount,
            $row->tf_amount,
            $row->total_price,
            $row->notes ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Sheet $sheet */
                $sheet = $event->getSheet();
                $worksheet = $sheet->getDelegate();
                $lastDataRow = $worksheet->getHighestRow();

                $kgCol = 'C';
                $cashCol = 'F';
                $tfCol = 'G';
                $totalCol = 'H';

                $sumKg = 0;
                $sumCash = 0;
                $sumTf = 0;
                $sumTotal = 0;

                for ($row = 2; $row <= $lastDataRow; $row++) {
                    $sumKg += (float) $worksheet->getCell($kgCol . $row)->getValue();
                    $sumCash += (float) $worksheet->getCell($cashCol . $row)->getValue();
                    $sumTf += (float) $worksheet->getCell($tfCol . $row)->getValue();
                    $sumTotal += (float) $worksheet->getCell($totalCol . $row)->getValue();
                }

                $summaryRow = $lastDataRow + 1;

                $worksheet->setCellValue('A' . $summaryRow, 'Jumlah');
                $worksheet->setCellValue($kgCol . $summaryRow, $sumKg);
                $worksheet->setCellValue($cashCol . $summaryRow, $sumCash);
                $worksheet->setCellValue($tfCol . $summaryRow, $sumTf);
                $worksheet->setCellValue($totalCol . $summaryRow, $sumTotal);

                $styleRange = 'A' . $summaryRow . ':' . $totalCol . $summaryRow;
                $worksheet->getStyle($styleRange)->getFont()->setBold(true);
                $worksheet->getStyle($styleRange)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
