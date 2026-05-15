<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedRequest;
use App\Models\Feed;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(): View
    {
        return view('feeds.index', [
            'feeds' => Feed::query()
                ->with(['pondFeedings.pond'])
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('feeds.create', [
            'feed' => new Feed(['is_active' => true, 'sack_weight_kg' => 30, 'fcr' => 1.5]),
        ]);
    }

    public function store(FeedRequest $request): RedirectResponse
    {
        Feed::create($request->safe()->merge([
            'is_active' => $request->boolean('is_active'),
        ])->all());

        return redirect()->route('feeds.index')->with('success', 'Pakan berhasil dibuat.');
    }

    public function show(Feed $feed): View
    {
        return view('feeds.show', compact('feed'));
    }

    public function edit(Feed $feed): View
    {
        return view('feeds.edit', compact('feed'));
    }

    public function update(FeedRequest $request, Feed $feed): RedirectResponse
    {
        $feed->update($request->safe()->merge([
            'is_active' => $request->boolean('is_active'),
        ])->all());

        return redirect()->route('feeds.index')->with('success', 'Pakan berhasil diperbarui.');
    }

    public function destroy(Feed $feed): RedirectResponse
    {
        $feed->delete();

        return redirect()->route('feeds.index')->with('success', 'Pakan berhasil dihapus.');
    }
}
