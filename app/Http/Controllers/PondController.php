<?php

namespace App\Http\Controllers;

use App\Http\Requests\PondRequest;
use App\Models\Feed;
use App\Models\FeedCategory;
use App\Models\Pond;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PondController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $pondTableQuery = Pond::query()
            ->with(['feed', 'feedings'])
            ->orderBy('name');

        if (!empty($search)) {
            $pondTableQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('fish_type', 'like', "%{$search}%")
                  ->orWhereHas('feed', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return view('ponds.index', [
            'ponds' => Pond::with(['feed', 'feedings'])->orderBy('y')->orderBy('x')->get(),
            'pondTable' => $pondTableQuery->get(),
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
        $validated = $request->validated();
        $nextY = (int) Pond::query()->selectRaw('COALESCE(MAX(y + height), 0) as max_bottom')->value('max_bottom');

        $validated['x'] = 0;
        $validated['y'] = $nextY + 1;
        $validated['width'] ??= 3;
        $validated['height'] ??= 2;

        Pond::create($validated);

        return redirect()->route('ponds.index')->with('success', 'Kolam berhasil dibuat.');
    }

    public function show(Pond $pond, Request $request): View
    {
        $pond->load(['feed.category', 'feedings.feed.category']);

        $harvests = $pond->harvests()
            ->with('harvestInputs')
            ->orderByDesc('harvested_at')
            ->paginate(5, ['*'], 'harvests_page')
            ->withQueryString();

        $search = $request->input('search_ponds');
        $allPondsQuery = Pond::with(['feedings' => function($q) {
                $q->orderBy('fed_at', 'desc')->with('feed');
            }])
            ->orderBy('name');

        if (!empty($search)) {
            $allPondsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('fish_type', 'like', "%{$search}%")
                  ->orWhereHas('feedings.feed', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $allPonds = $allPondsQuery->get();

        return view('ponds.show', [
            'pond' => $pond,
            'harvests' => $harvests,
            'allPonds' => $allPonds,
            'feeds' => Feed::with('category')->where('is_active', true)->orderBy('name')->get(),
            'feedCategories' => FeedCategory::orderBy('name')->get(),
            'inputs' => $pond->harvestInputs()->orderByDesc('harvested_at')->get(),
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

    public function layout(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        if ($request->filled('items_json')) {
            $request->merge(['items' => json_decode($request->string('items_json')->toString(), true)]);
        }

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:ponds,id'],
            'items.*.x' => ['required', 'integer', 'min:0'],
            'items.*.y' => ['required', 'integer', 'min:0'],
            'items.*.w' => ['required', 'integer', 'min:1', 'max:24'],
            'items.*.h' => ['required', 'integer', 'min:1', 'max:24'],
        ]);

        foreach ($validated['items'] as $item) {
            Pond::whereKey($item['id'])->update([
                'x' => $item['x'],
                'y' => $item['y'],
                'width' => $item['w'],
                'height' => $item['h'],
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Layout kolam berhasil disimpan.']);
        }

        return back()->with('success', 'Layout kolam berhasil disimpan.');
    }
}
