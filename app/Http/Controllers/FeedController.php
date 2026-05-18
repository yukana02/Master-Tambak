<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedRequest;
use App\Models\Feed;
use App\Models\FeedCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(): View
    {
        return view('feeds.index', [
            'feeds' => Feed::query()
                ->with(['category', 'pondFeedings.pond'])
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function categoriesIndex(): View
    {
        return view('feed-categories.index', [
            'categories' => FeedCategory::withCount('feeds')->orderBy('name')->paginate(15),
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
        Feed::create($this->feedData($request));

        return redirect()->route('feeds.index')->with('success', 'Pakan berhasil dibuat.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:feed_categories,name'],
        ]);

        FeedCategory::create($validated);

        return back()->with('success', 'Kategori pakan berhasil dibuat.');
    }

    public function updateCategory(Request $request, FeedCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:feed_categories,name,' . $category->id],
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategori pakan berhasil diperbarui.');
    }

    public function destroyCategory(Request $request, FeedCategory $category): RedirectResponse|JsonResponse
    {
        if ($category->feeds()->exists()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Kategori tidak bisa dihapus karena masih dipakai pakan.'], 422);
            }
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai pakan.');
        }

        $category->delete();

        return back()->with('success', 'Kategori pakan berhasil dihapus.');
    }

    public function show(Feed $feed): View
    {
        return view('feeds.show', [
            'feed' => $feed->load('category'),
        ]);
    }

    public function edit(Feed $feed): View
    {
        return view('feeds.edit', [
            'feed' => $feed,
        ]);
    }

    public function update(FeedRequest $request, Feed $feed): RedirectResponse
    {
        $feed->update($this->feedData($request));

        return redirect()->route('feeds.index')->with('success', 'Pakan berhasil diperbarui.');
    }

    public function destroy(Feed $feed): RedirectResponse
    {
        $feed->delete();

        return redirect()->route('feeds.index')->with('success', 'Pakan berhasil dihapus.');
    }

    private function feedData(FeedRequest $request): array
    {
        return array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active'),
        ]);
    }
}
