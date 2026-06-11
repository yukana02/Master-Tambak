<?php

namespace App\Http\Controllers;

use App\Http\Requests\PondHarvestRequest;
use App\Models\Pond;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PondHarvestController extends Controller
{
    public function store(PondHarvestRequest $request, Pond $pond): RedirectResponse
    {
        $validated = $request->validated();
        $pond->load('feedings');

        DB::transaction(function () use ($pond, $validated): void {
            // Get all draft harvest inputs
            $draftInputs = $pond->harvestInputs()->where('status', 'draft')->get();

            // Create the pond_harvest record
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

            // Archive all draft inputs to this harvest
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
}
