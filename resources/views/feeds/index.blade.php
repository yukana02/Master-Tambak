<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Pakan</h1>
        <p class="text-sm text-slate-500">Kelola FCR, berat sak, dan komposisi pakan untuk perhitungan target panen kolam.</p>
    </x-slot>

    <div class="mb-4 flex justify-end">
        <a href="{{ route('feeds.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Pakan</a>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Komposisi</th>
                    <th class="px-4 py-3">Berat Sak</th>
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
                        <td class="whitespace-nowrap px-4 py-3">{{ number_format($feed->sack_weight_kg, 2, ',', '.') }} kg/sak</td>
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
    </div>

    <div class="mt-4">
        {{ $feeds->links() }}
    </div>
</x-app-layout>
