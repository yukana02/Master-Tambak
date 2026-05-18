<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Pakan</h1>
        <p class="text-sm text-slate-500">Kelola FCR, berat pakan, dan komposisi pakan untuk perhitungan target panen kolam.</p>
    </x-slot>

    <div class="mb-4">
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('feeds.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Pakan</a>
            <a href="{{ route('feed-categories.index') }}" class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kelola Kategori
            </a>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
        <table class="hidden min-w-full divide-y divide-slate-200 text-sm md:table">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Komposisi</th>
                    <th class="px-4 py-3">Berat Pakan</th>
                    <th class="px-4 py-3">FCR</th>
                    <th class="px-4 py-3">Dipakai Kolam</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($feeds as $feed)
                    @php
                        $usedPonds = $feed->pondFeedings
                            ->pluck('pond')
                            ->filter()
                            ->unique('id')
                            ->sortBy('name')
                            ->values();
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $feed->name }}</td>
                        <td class="max-w-md px-4 py-3 text-slate-600">{{ Str::limit($feed->composition ?: '-', 90) }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ number_format($feed->sack_weight_kg, 2, ',', '.') }}kg pakan</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ number_format($feed->fcr, 2, ',', '.') }} kg pakan/kg daging</td>
                        <td class="px-4 py-3">
                            @if ($usedPonds->isNotEmpty())
                                <div class="flex max-w-md flex-wrap gap-2">
                                    @foreach ($usedPonds as $pond)
                                        <a href="{{ route('ponds.show', $pond) }}" class="rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 underline">{{ $pond->name }}</a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-500">Belum dipakai</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $feed->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('feeds.edit', $feed) }}" class="font-semibold underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Belum ada data pakan.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="grid grid-cols-1 divide-y divide-slate-100 md:hidden">
            @forelse ($feeds as $feed)
                @php
                    $usedPonds = $feed->pondFeedings
                        ->pluck('pond')
                        ->filter()
                        ->unique('id')
                        ->sortBy('name')
                        ->values();
                @endphp
                <div class="space-y-3 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="break-words font-bold text-slate-900">{{ $feed->name }}</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">{{ Str::limit($feed->composition ?: 'Tidak ada komposisi.', 120) }}</p>
                        </div>
                        <span class="shrink-0 rounded px-2 py-1 text-[10px] font-bold uppercase {{ $feed->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ $feed->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 border-y border-slate-100 py-3 text-sm">
                        <div>
                            <div class="text-[10px] font-semibold uppercase text-slate-400">Berat Pakan</div>
                            <div class="mt-1 font-semibold text-slate-900">{{ number_format($feed->sack_weight_kg, 2, ',', '.') }}kg pakan</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-semibold uppercase text-slate-400">FCR</div>
                            <div class="mt-1 font-semibold text-slate-900">{{ number_format($feed->fcr, 2, ',', '.') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-[10px] font-semibold uppercase text-slate-400">Dipakai Kolam</div>
                        @if ($usedPonds->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($usedPonds as $pond)
                                    <a href="{{ route('ponds.show', $pond) }}" class="rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 underline">{{ $pond->name }}</a>
                                @endforeach
                            </div>
                        @else
                            <span class="text-sm text-slate-500">Belum dipakai</span>
                        @endif
                    </div>

                    <a href="{{ route('feeds.edit', $feed) }}" class="flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm ring-1 ring-slate-200">Edit Pakan</a>
                </div>
            @empty
                <div class="px-4 py-12 text-center text-sm text-slate-500">Belum ada data pakan.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $feeds->links() }}
    </div>
</x-app-layout>
