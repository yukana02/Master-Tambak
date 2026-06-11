<?php

namespace App\Http\Controllers;

use App\Exports\PondHarvestInputExport;
use App\Http\Requests\PondHarvestInputRequest;
use App\Models\Pond;
use App\Models\PondHarvestInput;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PondHarvestInputController extends Controller
{
    public function store(PondHarvestInputRequest $request, Pond $pond): RedirectResponse
    {
        $validated = $request->validated();
        $validated['total_price'] = $validated['kg'] * $validated['price_per_kg'];
        $validated['pond_id'] = $pond->id;
        $validated['status'] = 'draft';

        PondHarvestInput::create($validated);

        return redirect()->route('ponds.show', $pond)
            ->with('success', 'Catatan panen berhasil disimpan.');
    }

    public function update(PondHarvestInputRequest $request, Pond $pond, PondHarvestInput $input): RedirectResponse
    {
        if ($input->status !== 'draft') {
            return redirect()->route('ponds.show', $pond)
                ->with('error', 'Hanya catatan draft yang bisa diedit.');
        }

        $validated = $request->validated();
        $validated['total_price'] = $validated['kg'] * $validated['price_per_kg'];

        $input->update($validated);

        return redirect()->route('ponds.show', $pond)
            ->with('success', 'Catatan panen berhasil diperbarui.');
    }

    public function destroy(Pond $pond, PondHarvestInput $input): RedirectResponse
    {
        if ($input->status !== 'draft') {
            return redirect()->route('ponds.show', $pond)
                ->with('error', 'Hanya catatan draft yang bisa dihapus.');
        }

        $input->delete();

        return redirect()->route('ponds.show', $pond)
            ->with('success', 'Catatan panen berhasil dihapus.');
    }

    public function export(Pond $pond): BinaryFileResponse
    {
        $filename = 'panen_' . $pond->id . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new PondHarvestInputExport($pond->id), $filename);
    }
}
