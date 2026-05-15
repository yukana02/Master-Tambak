<?php

namespace App\Http\Controllers;

use App\Http\Requests\PondFeedingRequest;
use App\Models\Feed;
use App\Models\Pond;
use App\Models\PondFeeding;
use Illuminate\Http\RedirectResponse;

class PondFeedingController extends Controller
{
    public function store(PondFeedingRequest $request, Pond $pond): RedirectResponse
    {
        $validated = $request->validated();
        $feed = Feed::findOrFail($validated['feed_id']);
        $feedWeightKg = $validated['unit'] === 'sak'
            ? (float) $validated['quantity'] * (float) $feed->sack_weight_kg
            : (float) $validated['quantity'];

        $pond->feedings()->create([
            'feed_id' => $feed->id,
            'fed_at' => $validated['fed_at'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'feed_weight_kg' => $feedWeightKg,
            'estimated_meat_kg' => $feedWeightKg / (float) $feed->fcr,
            'notes' => $validated['notes'] ?? null,
        ]);

        $pond->update(['feed_id' => $feed->id]);

        return back()->with('success', 'Catatan pakan berhasil disimpan.');
    }

    public function destroy(Pond $pond, PondFeeding $feeding): RedirectResponse
    {
        abort_unless($feeding->pond_id === $pond->id, 404);

        $feeding->delete();

        $pond->update([
            'feed_id' => $pond->feedings()->latest('fed_at')->first()?->feed_id,
        ]);

        return back()->with('success', 'Catatan pakan berhasil dihapus.');
    }
}
