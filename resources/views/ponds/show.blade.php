<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">{{ $pond->name }}</h1></x-slot>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr.setDefaults({ dateFormat: "d/m/Y", locale: "id", allowInput: true });
            flatpickr('.datepicker');
        });
    </script>
    @php
        $feedTotalsByCategory = $pond->feedings
            ->groupBy('unit')
            ->map(fn ($items) => [
                'unit' => $items->first()->unit,
                'total_input' => $items->sum('quantity'),
                'total_kg' => $items->sum('feed_weight_kg'),
                'feeds' => $items
                    ->groupBy(fn ($feeding) => $feeding->feed?->name ?? 'Pakan dihapus')
                    ->map(fn ($feedItems) => [
                        'total_input' => $feedItems->sum('quantity'),
                        'total_kg' => $feedItems->sum('feed_weight_kg'),
                        'unit' => $feedItems->first()->unit,
                    ])
                    ->sortKeys(),
            ])
            ->sortKeys();

        $feedTotalsByFeed = $pond->feedings
            ->groupBy(fn ($feeding) => $feeding->feed?->name ?? 'Pakan dihapus')
            ->map(fn ($items) => [
                'total_kg' => $items->sum('feed_weight_kg'),
                'total_input' => $items->sum('quantity'),
                'unit' => $items->first()->unit,
            ])
            ->sortKeys();
    @endphp
    <div class="space-y-6">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <dl class="grid gap-4 md:grid-cols-2">
                <div><dt class="text-sm text-slate-500">Jenis Ikan</dt><dd class="font-semibold">{{ $pond->fish_type }}</dd></div>
                <div><dt class="text-sm text-slate-500">Jumlah Ikan</dt><dd class="font-semibold">{{ number_format($pond->fish_count) }} ekor</dd></div>
                <div><dt class="text-sm text-slate-500">Sumber Bibit</dt><dd class="font-semibold">{{ $pond->seed_source ?: '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Ikan Mati</dt><dd class="font-semibold">{{ number_format($pond->dead_fish_count) }} ekor</dd></div>
                <div><dt class="text-sm text-slate-500">Estimasi Hidup</dt><dd class="font-semibold">{{ number_format($pond->estimated_live_fish_count) }} ekor</dd></div>
                <div><dt class="text-sm text-slate-500">Tanggal Tebar</dt><dd class="font-semibold">{{ $pond->stocking_date?->format('d/m/Y') ?? '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Prediksi Tanggal Panen</dt><dd class="font-semibold">{{ $pond->predicted_harvest_date?->format('d/m/Y') ?? 'Belum cukup data' }}</dd></div>
                <div class="md:col-span-2"><dt class="text-sm text-slate-500">Catatan Ukuran Kolam</dt><dd class="font-semibold">{{ $pond->pond_size_notes ?: 'Tidak ada catatan ukuran.' }}</dd></div>
                <div class="md:col-span-2"><dt class="text-sm text-slate-500">Catatan</dt><dd class="font-semibold">{{ $pond->notes ?: 'Tidak ada catatan.' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-4">
                <h2 class="font-semibold text-slate-900">Rangkuman Pakan</h2>
            </div>
            <dl class="grid gap-4 md:grid-cols-3">
                <div><dt class="text-sm text-slate-500">Target Daging</dt><dd class="font-semibold">{{ $pond->target_harvest_weight_kg ? number_format($pond->target_harvest_weight_kg, 2, ',', '.') . ' kg' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Total Pakan Aktual</dt><dd class="font-semibold">{{ $pond->actual_feed_weight_kg > 0 ? number_format($pond->actual_feed_weight_kg, 2, ',', '.') . ' kg' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Total Konversi Aktual</dt><dd class="font-semibold">{{ $pond->actual_estimated_meat_kg > 0 ? number_format($pond->actual_estimated_meat_kg, 2, ',', '.') . ' kg' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Rata-rata Harian</dt><dd class="font-semibold">{{ $pond->daily_estimated_meat_kg ? number_format($pond->daily_estimated_meat_kg, 2, ',', '.') . ' kg/hari' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Progress Target</dt><dd class="font-semibold">{{ $pond->harvest_progress_percent ? number_format($pond->harvest_progress_percent, 1, ',', '.') . '%' : '-' }}</dd></div>
            </dl>
        </div>

        <section 
            x-data="{ 
                search: '{{ request('search_ponds', '') }}',
                isLoading: false,
                async init() {
                    this.$watch('search', value => this.fetchPonds());
                },
                async fetchPonds() {
                    this.isLoading = true;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search_ponds', this.search);
                    url.searchParams.set('ponds_page', 1);
                    await this.loadUrl(url.toString());
                },
                async goToPage(url) {
                    if (!url || this.isLoading) return;
                    await this.loadUrl(url);
                },
                async loadUrl(url) {
                    this.isLoading = true;
                    try {
                        const response = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        document.getElementById('pondListTableContainer').innerHTML = doc.getElementById('pondListTableContainer').innerHTML;
                        document.getElementById('pondListCardContainer').innerHTML = doc.getElementById('pondListCardContainer').innerHTML;
                        document.getElementById('pondListPaginationContainer').innerHTML = doc.getElementById('pondListPaginationContainer').innerHTML;
                        
                        window.history.pushState({}, '', url);
                    } catch (error) {
                        console.error('Pond list pagination error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }"
            @click="if ($event.target.closest('#pondListPaginationContainer a')) { $event.preventDefault(); goToPage($event.target.closest('#pondListPaginationContainer a').href); }"
            class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200"
        >
            <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Daftar Kolam Ringkas</h2>
                    <p class="text-sm text-slate-500">Ringkasan cepat status kolam lainnya.</p>
                </div>
                <input
                    x-model.debounce.300ms="search"
                    type="search"
                    placeholder="Cari kolam..."
                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:w-64"
                >
            </div>
            
            <div class="overflow-x-auto relative">
                <div x-show="isLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-[1px]">
                    <svg class="h-8 w-8 animate-spin text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Desktop Table View --}}
                <table class="hidden min-w-full divide-y divide-slate-200 text-sm md:table">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Nama Kolam</th>
                            <th class="whitespace-nowrap px-4 py-3">Pakan Terakhir</th>
                            <th class="whitespace-nowrap px-4 py-3">Tanggal Pakan Terakhir</th>
                            <th class="whitespace-nowrap px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="pondListTableContainer" class="divide-y divide-slate-100">
                        @forelse ($allPonds as $otherPond)
                            @php
                                $lastFeeding = $otherPond->feedings->first();
                            @endphp
                            <tr class="{{ $otherPond->id === $pond->id ? 'bg-slate-50 font-medium' : '' }}">
                                <td class="whitespace-nowrap px-4 py-3">
                                    <a href="{{ route('ponds.show', $otherPond) }}" class="text-slate-900 hover:underline">
                                        {{ $otherPond->name }}
                                        @if($otherPond->id === $pond->id)
                                            <span class="ml-1 text-xs text-slate-400 font-normal">(Aktif Sekarang)</span>
                                        @endif
                                    </a>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $lastFeeding?->feed?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $lastFeeding?->fed_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('ponds.show', $otherPond) }}#feedings-section" class="font-semibold text-slate-700 underline hover:text-slate-900">Input Pakan</a>
                                        <a href="{{ route('ponds.edit', $otherPond) }}" class="font-semibold text-slate-700 underline hover:text-slate-900">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Kolam tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Mobile Card View --}}
                <div id="pondListCardContainer" class="grid grid-cols-1 divide-y divide-slate-100 md:hidden">
                    @forelse ($allPonds as $otherPond)
                        @php
                            $lastFeeding = $otherPond->feedings->first();
                        @endphp
                        <div class="p-4 space-y-2 text-sm {{ $otherPond->id === $pond->id ? 'bg-slate-50 font-medium' : '' }}">
                            <div class="flex justify-between items-start">
                                <a href="{{ route('ponds.show', $otherPond) }}" class="font-bold text-slate-900 hover:underline">
                                    {{ $otherPond->name }}
                                    @if($otherPond->id === $pond->id)
                                        <span class="ml-1 text-xs text-slate-400 font-normal">(Aktif)</span>
                                    @endif
                                </a>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-slate-600 py-1">
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Pakan Terakhir</div>
                                    <div class="font-medium">{{ $lastFeeding?->feed?->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Tanggal Pakan</div>
                                    <div class="font-medium">{{ $lastFeeding?->fed_at?->format('d/m/Y') ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-1">
                                <a href="{{ route('ponds.show', $otherPond) }}#feedings-section" class="flex-1 rounded bg-slate-100 px-2 py-1.5 text-center text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">Input Pakan</a>
                                <a href="{{ route('ponds.edit', $otherPond) }}" class="flex-1 rounded bg-white px-2 py-1.5 text-center text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">Edit</a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-500">Kolam tidak ditemukan.</div>
                    @endforelse
                </div>
            </div>
            
            <div id="pondListPaginationContainer" class="border-t border-slate-200 px-4 py-4">
                {{ $allPonds->links() }}
            </div>
        </section>

        <section id="feedings-section" class="grid gap-6 xl:grid-cols-3">
            <div x-data="{ feeds: @json($feeds->mapWithKeys(fn ($f) => [$f->id => $f->category?->name ? strtolower($f->category->name) : 'kg'])->all()), selectedFeedId: {{ old('feed_id', $pond->feed_id) ?: 'null' }}, selectedUnit: '{{ old('unit', $pond->feed?->category?->name ? strtolower($pond->feed->category->name) : 'kg') }}' }" x-init="$watch('selectedFeedId', val => { if (this.feeds[val]) this.selectedUnit = this.feeds[val]; })">
                <form method="POST" action="{{ route('ponds.feedings.store', $pond) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    @csrf
                    <h2 class="mb-4 font-semibold text-slate-900">Catat Pemberian Pakan</h2>
                    <div class="space-y-4">
                        <label class="block text-sm font-medium">Tanggal
                            <input type="text" name="fed_at" value="{{ old('fed_at', now()->format('d/m/Y')) }}" class="datepicker mt-1 w-full rounded-md border-slate-300" required>
                        </label>
                        <label class="block text-sm font-medium">Pakan
                            <select name="feed_id" x-model="selectedFeedId" class="mt-1 w-full rounded-md border-slate-300" required>
                                @foreach ($feeds as $feed)
                                    <option value="{{ $feed->id }}" @selected(old('feed_id', $pond->feed_id) == $feed->id)>
                                        {{ $feed->name }} - {{ number_format($feed->sack_weight_kg, 2, ',', '.') }}kg pakan - FCR {{ number_format($feed->fcr, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block text-sm font-medium">Jumlah
                                <input type="number" name="quantity" value="{{ old('quantity') }}" class="mt-1 w-full rounded-md border-slate-300" min="0.01" step="0.01" required>
                            </label>
                            <label class="block text-sm font-medium">Satuan
                                <select name="unit" x-model="selectedUnit" class="mt-1 w-full rounded-md border-slate-300" required>
                                    <option value="kg">kg</option>
                                    @foreach ($feedCategories as $category)
                                        <option value="{{ strtolower($category->name) }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <label class="block text-sm font-medium">Catatan
                            <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-slate-300">{{ old('notes') }}</textarea>
                        </label>
                    </div>
                    <button class="mt-5 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Catatan</button>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                <div class="border-b border-slate-200 px-4 py-4">
                    <h2 class="font-semibold text-slate-900">Riwayat Pemberian Pakan</h2>
                    <p class="text-sm text-slate-500">Total konversi aktual dari catatan ini dipakai untuk progress panen.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3">Tanggal</th>
                                <th class="whitespace-nowrap px-4 py-3">Pakan</th>
                                <th class="whitespace-nowrap px-4 py-3">Input</th>
                                <th class="whitespace-nowrap px-4 py-3">Kg Pakan</th>
                                <th class="whitespace-nowrap px-4 py-3">Konversi Daging</th>
                                <th class="whitespace-nowrap px-4 py-3">Catatan</th>
                                <th class="whitespace-nowrap px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($pond->feedings->sortByDesc('fed_at') as $feeding)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $feeding->fed_at->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $feeding->feed->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ number_format($feeding->quantity, 2, ',', '.') }} {{ $feeding->unit }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ number_format($feeding->feed_weight_kg, 2, ',', '.') }} kg</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ number_format($feeding->estimated_meat_kg, 2, ',', '.') }} kg</td>
                                    <td class="min-w-48 px-4 py-3">{{ $feeding->notes ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('ponds.feedings.destroy', [$pond, $feeding]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pemberian pakan ini?');">
                                            @csrf @method('DELETE')
                                            <button class="font-semibold text-red-700 underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Belum ada catatan pemberian pakan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section x-data="{ view: 'category' }" class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4">
                <div>
                    <h2 class="font-semibold text-slate-900">Total Pakan Aktif</h2>
                    <p class="text-sm text-slate-500">Ringkasan total pakan yang sudah diberikan ke kolam ini.</p>
                </div>
                <div class="flex gap-1 rounded-lg bg-slate-100 p-1 text-xs font-semibold">
                    <button @click="view = 'category'" :class="view === 'category' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-700'" class="rounded-md px-3 py-1.5 transition">Berdasarkan Kategori</button>
                    <button @click="view = 'feed'" :class="view === 'feed' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-700'" class="rounded-md px-3 py-1.5 transition">Berdasarkan Pakan</button>
                </div>
            </div>
            <div class="px-4 py-4">
                <template x-if="view === 'category'">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($feedTotalsByCategory as $categoryName => $summary)
                            <div class="rounded-md bg-slate-50 p-3 text-sm ring-1 ring-slate-200">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-semibold text-slate-900">{{ ucfirst($categoryName) }}</div>
                                    <div class="shrink-0 font-bold text-slate-900">{{ number_format($summary['total_input'], 2, ',', '.') }} {{ $summary['unit'] }}</div>
                                </div>
                                <div class="mt-1 text-xs text-slate-500">{{ number_format($summary['total_kg'], 2, ',', '.') }} kg</div>
                                <div class="mt-2 space-y-1 text-xs text-slate-600">
                                    @foreach ($summary['feeds'] as $feedName => $feedSummary)
                                        <div class="flex justify-between gap-3">
                                            <span class="break-words">{{ $feedName }}</span>
                                            <span class="shrink-0">{{ number_format($feedSummary['total_input'], 2, ',', '.') }} {{ $feedSummary['unit'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center text-sm text-slate-500">Belum ada catatan pakan aktif.</div>
                        @endforelse
                    </div>
                </template>
                <template x-if="view === 'feed'">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($feedTotalsByFeed as $feedName => $summary)
                            <div class="rounded-md bg-slate-50 p-3 text-sm ring-1 ring-slate-200">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 truncate">{{ $feedName }}</div>
                                        <div class="text-xs text-slate-500">Satuan input: {{ $summary['unit'] }}</div>
                                    </div>
                                    <div class="shrink-0 font-bold text-slate-900">{{ number_format($summary['total_kg'], 2, ',', '.') }} kg</div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center text-sm text-slate-500">Belum ada catatan pakan aktif.</div>
                        @endforelse
                    </div>
                </template>
            </div>
        </section>

        <section id="harvest-section" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-4">
                <h2 class="font-semibold text-slate-900">Konfirmasi Panen</h2>
                <p class="text-sm text-slate-500">Menyimpan ringkasan panen ke riwayat, lalu mengosongkan catatan pakan aktif agar siklus kolam berikutnya dimulai dari awal.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-md bg-slate-50 p-3">
                    <div class="text-sm text-slate-500">Total Pakan</div>
                    <div class="font-semibold">{{ number_format($pond->actual_feed_weight_kg, 2, ',', '.') }} kg</div>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <div class="text-sm text-slate-500">Konversi Daging</div>
                    <div class="font-semibold">{{ number_format($pond->actual_estimated_meat_kg, 2, ',', '.') }} kg</div>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <div class="text-sm text-slate-500">Catatan Pakan</div>
                    <div class="font-semibold">{{ $pond->feedings->count() }}</div>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <div class="text-sm text-slate-500">Target</div>
                    <div class="font-semibold">{{ $pond->target_harvest_weight_kg ? number_format($pond->target_harvest_weight_kg, 2, ',', '.') . ' kg' : '-' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('ponds.harvests.store', $pond) }}" class="mt-5 grid gap-4 md:grid-cols-2" onsubmit="return confirm('Konfirmasi panen kolam ini? Catatan pakan aktif akan direset dan siklus baru dimulai.');">
                @csrf
                <label class="block text-sm font-medium">Tanggal Panen
                    <input type="text" name="harvested_at" value="{{ old('harvested_at', now()->format('d/m/Y')) }}" class="datepicker mt-1 w-full rounded-md border-slate-300" required>
                </label>
                <label class="block text-sm font-medium">Catatan Panen
                    <textarea name="notes" rows="2" class="mt-1 w-full rounded-md border-slate-300">{{ old('notes') }}</textarea>
                </label>
                <div class="md:col-span-2">
                    <button class="rounded-md bg-sky-700 px-4 py-2 text-sm font-semibold text-white">Konfirmasi Panen</button>
                </div>
            </form>
        </section>

        <section 
            x-data="{ 
                search: '',
                isLoading: false,
                async goToPage(url) {
                    if (!url || this.isLoading) return;
                    this.isLoading = true;
                    try {
                        const response = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Update table body and pagination
                        document.getElementById('harvestTableContainer').innerHTML = doc.getElementById('harvestTableContainer').innerHTML;
                        document.getElementById('harvestCardContainer').innerHTML = doc.getElementById('harvestCardContainer').innerHTML;
                        document.getElementById('harvestPaginationContainer').innerHTML = doc.getElementById('harvestPaginationContainer').innerHTML;
                        
                        // Update URL without reload
                        window.history.pushState({}, '', url);
                    } catch (error) {
                        console.error('Pagination error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }" 
            class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200"
            @click="if ($event.target.closest('#harvestPaginationContainer a')) { $event.preventDefault(); goToPage($event.target.closest('#harvestPaginationContainer a').href); }"
        >
            <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Riwayat Panen</h2>
                    <p class="text-sm text-slate-500">Ringkasan siklus panen yang sudah dikonfirmasi.</p>
                </div>
                <input
                    x-model.debounce.200ms="search"
                    type="search"
                    placeholder="Cari riwayat panen..."
                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:w-64"
                >
            </div>
            
            <div class="overflow-x-auto relative">
                <div x-show="isLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-[1px]">
                    <svg class="h-8 w-8 animate-spin text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Desktop View --}}
                <table class="hidden min-w-full divide-y divide-slate-200 text-sm md:table">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Tanggal</th>
                            <th class="whitespace-nowrap px-4 py-3">Jenis Ikan</th>
                            <th class="whitespace-nowrap px-4 py-3">Target</th>
                            <th class="whitespace-nowrap px-4 py-3">Total Pakan</th>
                            <th class="whitespace-nowrap px-4 py-3">Konversi</th>
                            <th class="whitespace-nowrap px-4 py-3">Catatan Pakan</th>
                            <th class="whitespace-nowrap px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="harvestTableContainer" class="divide-y divide-slate-100">
                        @forelse ($harvests as $harvest)
                            @php
                                $searchText = Str::lower($harvest->harvested_at->format('d/m/Y').' '.$harvest->fish_type.' '.$harvest->notes);
                            @endphp
                            <tr x-show="!search || {{ Js::from($searchText) }}.includes(search.toLowerCase())">
                                <td class="whitespace-nowrap px-4 py-3">{{ $harvest->harvested_at->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $harvest->fish_type }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $harvest->target_harvest_weight_kg ? number_format($harvest->target_harvest_weight_kg, 2, ',', '.') . ' kg' : '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ number_format($harvest->total_feed_weight_kg, 2, ',', '.') }} kg</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ number_format($harvest->total_estimated_meat_kg, 2, ',', '.') }} kg</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $harvest->feeding_count }}</td>
                                <td class="min-w-48 px-4 py-3">{{ $harvest->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Belum ada riwayat panen.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Mobile Card View --}}
                <div id="harvestCardContainer" class="grid grid-cols-1 divide-y divide-slate-100 md:hidden">
                    @forelse ($harvests as $harvest)
                        @php
                            $searchText = Str::lower($harvest->harvested_at->format('d/m/Y').' '.$harvest->fish_type.' '.$harvest->notes);
                        @endphp
                        <div x-show="!search || {{ Js::from($searchText) }}.includes(search.toLowerCase())" class="p-4 space-y-2 text-sm">
                            <div class="flex justify-between items-start">
                                <div class="font-bold text-slate-900">{{ $harvest->harvested_at->format('d/m/Y') }}</div>
                                <span class="rounded bg-sky-100 px-2 py-0.5 text-xs font-semibold text-sky-800">{{ $harvest->fish_type }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-slate-600 py-1">
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Target</div>
                                    <div class="font-medium">{{ $harvest->target_harvest_weight_kg ? number_format($harvest->target_harvest_weight_kg, 2, ',', '.') . ' kg' : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Total Pakan</div>
                                    <div class="font-medium">{{ number_format($harvest->total_feed_weight_kg, 2, ',', '.') }} kg</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Konversi Daging</div>
                                    <div class="font-medium">{{ number_format($harvest->total_estimated_meat_kg, 2, ',', '.') }} kg</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Catatan Pakan</div>
                                    <div class="font-medium">{{ $harvest->feeding_count }}</div>
                                </div>
                            </div>
                            @if($harvest->notes)
                                <div class="text-xs bg-slate-50 p-2 rounded text-slate-600">
                                    <strong class="text-slate-700">Catatan:</strong> {{ $harvest->notes }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-500">Belum ada riwayat panen.</div>
                    @endforelse
                </div>
            </div>
            
            <div id="harvestPaginationContainer" class="border-t border-slate-200 px-4 py-4">
                {{ $harvests->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
