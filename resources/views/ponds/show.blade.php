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
                <h2 class="font-semibold text-slate-900">Perencanaan Pakan</h2>
            </div>
            <dl class="grid gap-4 md:grid-cols-3">
                <div><dt class="text-sm text-slate-500">Target Daging</dt><dd class="font-semibold">{{ $pond->target_harvest_weight_kg ? number_format($pond->target_harvest_weight_kg, 2, ',', '.') . ' kg' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Total Pakan Aktual</dt><dd class="font-semibold">{{ $pond->actual_feed_weight_kg > 0 ? number_format($pond->actual_feed_weight_kg, 2, ',', '.') . ' kg' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Total Konversi Aktual</dt><dd class="font-semibold">{{ $pond->actual_estimated_meat_kg > 0 ? number_format($pond->actual_estimated_meat_kg, 2, ',', '.') . ' kg' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Rata-rata Harian</dt><dd class="font-semibold">{{ $pond->daily_estimated_meat_kg ? number_format($pond->daily_estimated_meat_kg, 2, ',', '.') . ' kg/hari' : '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500">Progress Target</dt><dd class="font-semibold">{{ $pond->harvest_progress_percent ? number_format($pond->harvest_progress_percent, 1, ',', '.') . '%' : '-' }}</dd></div>
            </dl>
        </div>

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
                                        <form method="POST" action="{{ route('ponds.feedings.destroy', [$pond, $feeding]) }}">
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

        <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 px-4 py-4">
                <h2 class="font-semibold text-slate-900">Riwayat Panen</h2>
                <p class="text-sm text-slate-500">Ringkasan siklus panen yang sudah dikonfirmasi.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
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
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pond->harvests->sortByDesc('harvested_at') as $harvest)
                            <tr>
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
            </div>
        </section>
    </div>
</x-app-layout>
