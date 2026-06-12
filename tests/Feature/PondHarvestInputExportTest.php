<?php

namespace Tests\Feature;

use App\Exports\PondHarvestInputExport;
use App\Models\Pond;
use App\Models\PondHarvestInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PondHarvestInputExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_only_includes_active_draft_inputs_and_uses_pond_name_column(): void
    {
        $pond = Pond::create([
            'name' => 'kolan 12',
            'fish_type' => 'Lele',
            'fish_count' => 1000,
            'dead_fish_count' => 0,
            'target_harvest_weight_kg' => 500,
            'planned_feed_sacks' => 0,
            'stocking_date' => '2026-06-01',
            'harvest_date' => '2026-06-30',
            'x' => 0,
            'y' => 0,
            'width' => 3,
            'height' => 2,
        ]);

        $draftInput = PondHarvestInput::create([
            'pond_id' => $pond->id,
            'harvested_at' => '2026-06-12',
            'bucket_name' => 'rudi',
            'kg' => 150,
            'price_per_kg' => 21000,
            'total_price' => 3150000,
            'notes' => 'cash',
            'status' => 'draft',
        ]);

        PondHarvestInput::create([
            'pond_id' => $pond->id,
            'harvested_at' => '2026-06-10',
            'bucket_name' => 'arsip',
            'kg' => 100,
            'price_per_kg' => 20000,
            'total_price' => 2000000,
            'notes' => 'final',
            'status' => 'final',
        ]);

        $export = new PondHarvestInputExport($pond->id);

        $this->assertSame(
            ['Tanggal', 'Nama Bakul', 'Kg', 'Harga/Kg', 'Total', 'Nama Kolam', 'Catatan'],
            $export->headings()
        );

        $this->assertSame(
            ['12/06/2026', 'rudi', '150.00', '21000.00', '3150000.00', 'kolan 12', 'cash'],
            $export->map($draftInput->load('pond'))
        );
    }
}
