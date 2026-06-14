<?php

namespace App\Http\Controllers;

use App\Exports\PondHarvestExport;
use App\Http\Requests\PondHarvestRequest;
use App\Models\Pond;
use App\Models\PondHarvest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PondHarvestController extends Controller
{
    public function store(PondHarvestRequest $request, Pond $pond): RedirectResponse
    {
        $validated = $request->validated();
        $pond->load('feedings');

        DB::transaction(function () use ($pond, $validated): void {
            $draftInputs = $pond->harvestInputs()->where('status', 'draft')->get();

            $harvest = $pond->harvests()->create([
                'harvested_at' => $validated['harvested_at'],
                'fish_type' => $pond->fish_type,
                'fish_count' => $pond->fish_count,
                'target_harvest_weight_kg' => $pond->target_harvest_weight_kg,
                'total_feed_weight_kg' => $pond->actual_feed_weight_kg,
                'total_estimated_meat_kg' => $pond->actual_estimated_meat_kg,
                'feeding_started_at' => $pond->feedings->min('fed_at'),
                'feeding_ended_at' => $pond->feedings->max('fed_at'),
                'feeding_count' => $pond->feedings->count(),
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($draftInputs->isNotEmpty()) {
                $pond->harvestInputs()->where('status', 'draft')->update([
                    'pond_harvest_id' => $harvest->id,
                    'status' => 'final',
                ]);
            }

            $pond->feedings()->delete();
            $pond->update([
                'feed_id' => null,
                'planned_feed_sacks' => null,
                'stocking_date' => $validated['harvested_at'],
                'harvest_date' => null,
            ]);
        });

        return redirect()->route('ponds.show', $pond)->with('success', 'Panen berhasil dikonfirmasi. Catatan pakan aktif sudah direset.');
    }

    public function destroy(Pond $pond, PondHarvest $harvest): RedirectResponse
    {
        abort_unless($harvest->pond_id === $pond->id, 404);

        $harvest->delete(); // cascade otomatis ke harvestInputs via foreign key

        return redirect()->route('ponds.show', $pond)
            ->with('success', 'Riwayat panen beserta data input panennya berhasil dihapus.');
    }

    public function export(Pond $pond, PondHarvest $harvest): BinaryFileResponse
    {
        abort_unless($harvest->pond_id === $pond->id, 404);

        $filename = 'riwayat-panen-'.$pond->id.'-'.$harvest->id.'-'.now()->format('Ymd').'.xlsx';

        return Excel::download(new PondHarvestExport($harvest->id), $filename);
    }
}
