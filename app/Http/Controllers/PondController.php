<?php

namespace App\Http\Controllers;

use App\Http\Requests\PondRequest;
use App\Models\Feed;
use App\Models\Pond;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PondController extends Controller
{
    public function index(): View
    {
        return view('ponds.index', [
            'ponds' => Pond::with(['feed', 'feedings'])->orderBy('y')->orderBy('x')->get(),
            'pondTable' => Pond::query()
                ->with(['feed', 'feedings'])
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('ponds.create', [
            'pond' => new Pond,
        ]);
    }

    public function store(PondRequest $request): RedirectResponse
    {
        Pond::create($request->validated());

        return redirect()->route('ponds.index')->with('success', 'Kolam berhasil dibuat.');
    }

    public function show(Pond $pond): View
    {
        $pond->load(['feed', 'feedings.feed', 'harvests']);

        return view('ponds.show', [
            'pond' => $pond,
            'feeds' => Feed::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function edit(Pond $pond): View
    {
        return view('ponds.edit', [
            'pond' => $pond->load('feedings'),
        ]);
    }

    public function update(PondRequest $request, Pond $pond): RedirectResponse
    {
        $pond->update($request->validated());

        return redirect()->route('ponds.index')->with('success', 'Kolam berhasil diperbarui.');
    }

    public function destroy(Pond $pond): RedirectResponse
    {
        $pond->delete();

        return redirect()->route('ponds.index')->with('success', 'Kolam berhasil dihapus.');
    }

    public function layout(Request $request): RedirectResponse
    {
        if ($request->filled('items_json')) {
            $request->merge(['items' => json_decode($request->string('items_json')->toString(), true)]);
        }

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:ponds,id'],
            'items.*.x' => ['required', 'integer', 'min:0'],
            'items.*.y' => ['required', 'integer', 'min:0'],
            'items.*.w' => ['required', 'integer', 'min:1', 'max:12'],
            'items.*.h' => ['required', 'integer', 'min:1', 'max:8'],
        ]);

        foreach ($validated['items'] as $item) {
            Pond::whereKey($item['id'])->update([
                'x' => $item['x'],
                'y' => $item['y'],
                'width' => $item['w'],
                'height' => $item['h'],
            ]);
        }

        return back()->with('success', 'Layout kolam berhasil disimpan.');
    }
}
