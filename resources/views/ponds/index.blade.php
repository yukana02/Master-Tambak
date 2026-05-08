<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Manajemen Kolam</h1>
        <p class="text-sm text-slate-500">Atur peta kolam dengan drag, resize, pan, zoom, lalu simpan layout.</p>
    </x-slot>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
        <div class="flex flex-wrap gap-2 text-sm">
            <span class="rounded bg-emerald-100 px-2 py-1 text-emerald-800">Aktif</span>
            <span class="rounded bg-amber-100 px-2 py-1 text-amber-800">Mendekati panen</span>
            <span class="rounded bg-red-100 px-2 py-1 text-red-800">Terlambat panen</span>
        </div>
        <a href="{{ route('ponds.create') }}" class="inline-flex justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Kolam</a>
    </div>

    <form method="POST" action="{{ route('ponds.layout') }}" id="layoutForm" class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200 sm:p-4">
        @csrf
        <input type="hidden" name="items" id="layoutItems">
        @if ($ponds->isEmpty())
            <div class="py-16 text-center text-slate-500">Belum ada kolam.</div>
        @else
            <div class="mb-4 flex flex-col gap-3 border-b border-slate-200 pb-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <h2 class="font-semibold text-slate-900">Peta Tambak</h2>
                    <p class="text-sm text-slate-500">Geser area kosong untuk pan, gunakan tombol zoom untuk melihat area lebih luas.</p>
                </div>
                <div class="flex w-full items-center gap-2 sm:w-auto">
                    <button type="button" id="zoomOut" class="min-h-10 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-none">-</button>
                    <span id="zoomLabel" class="w-14 text-center text-sm font-semibold text-slate-700">100%</span>
                    <button type="button" id="zoomIn" class="min-h-10 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-none">+</button>
                    <button type="button" id="zoomReset" class="min-h-10 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-none">Reset</button>
                </div>
            </div>

            <div id="pondMapViewport" class="h-[62vh] min-h-[380px] overflow-auto rounded-lg border border-slate-200 bg-slate-100 md:h-[68vh] lg:h-[620px]">
                <div id="pondMapCanvas" class="min-h-[760px] min-w-[1280px] origin-top-left p-4 sm:min-h-[900px] sm:min-w-[1680px] sm:p-6 lg:min-h-[1080px] lg:min-w-[2200px] lg:p-8" style="background-color: #eef6f0; background-image: linear-gradient(rgba(15, 23, 42, .08) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, .08) 1px, transparent 1px); background-size: 90px 90px;">
                    <div class="mb-4 flex items-center gap-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span>Utara</span>
                        <span class="h-px w-12 bg-slate-400"></span>
                        <span>Area Tambak Rasyid</span>
                    </div>

                    <div class="grid-stack w-[1200px] sm:w-[1560px] lg:w-[2040px]">
                        @foreach ($pondTable as $pond)
                            @php
                                $statusClass = ['active' => 'bg-emerald-100/90 text-emerald-950 border-emerald-400', 'soon' => 'bg-amber-100/90 text-amber-950 border-amber-400', 'overdue' => 'bg-red-100/90 text-red-950 border-red-400'][$pond->status];
                            @endphp
                            <div class="grid-stack-item" gs-id="{{ $pond->id }}" gs-x="{{ $pond->x }}" gs-y="{{ $pond->y }}" gs-w="{{ $pond->width }}" gs-h="{{ $pond->height }}">
                                <div class="grid-stack-item-content rounded border-2 p-4 shadow-sm {{ $statusClass }}">
                                    <div class="flex justify-between gap-2">
                                        <div>
                                            <h2 class="font-semibold">{{ $pond->name }}</h2>
                                            <p class="text-sm">{{ $pond->fish_type }} &middot; {{ number_format($pond->fish_count) }} ekor</p>
                                        </div>
                                        <a href="{{ route('ponds.edit', $pond) }}" class="text-sm font-semibold underline">Edit</a>
                                    </div>
                                    <p class="mt-3 text-sm">Panen: {{ $pond->harvest_date?->format('d M Y') ?? '-' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">Posisi dan ukuran kolam disimpan dalam koordinat grid peta.</p>
                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Layout</button>
            </div>
        @endif
    </form>

    @if ($ponds->isNotEmpty())
        <section x-data="{ search: '' }" class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Daftar Kolam</h2>
                    <p class="text-sm text-slate-500">List data kolam berdasarkan peta dan jadwal panen.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded bg-emerald-100 px-2 py-1 text-emerald-800">Aktif: {{ $ponds->where('status', 'active')->count() }}</span>
                        <span class="rounded bg-amber-100 px-2 py-1 text-amber-800">Mendekati: {{ $ponds->where('status', 'soon')->count() }}</span>
                        <span class="rounded bg-red-100 px-2 py-1 text-red-800">Terlambat: {{ $ponds->where('status', 'overdue')->count() }}</span>
                    </div>
                    <input
                        x-model.debounce.200ms="search"
                        type="search"
                        placeholder="Cari kolam..."
                        class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:w-64"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Nama</th>
                            <th class="whitespace-nowrap px-4 py-3">Status</th>
                            <th class="whitespace-nowrap px-4 py-3">Jenis Ikan</th>
                            <th class="whitespace-nowrap px-4 py-3">Jumlah</th>
                            <th class="whitespace-nowrap px-4 py-3">Tebar</th>
                            <th class="whitespace-nowrap px-4 py-3">Panen</th>
                            <th class="whitespace-nowrap px-4 py-3">Posisi</th>
                            <th class="whitespace-nowrap px-4 py-3">Ukuran</th>
                            <th class="whitespace-nowrap px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($ponds as $pond)
                            @php
                                $statusLabel = ['active' => 'Aktif', 'soon' => 'Mendekati panen', 'overdue' => 'Terlambat panen'][$pond->status];
                                $statusBadge = ['active' => 'bg-emerald-100 text-emerald-800', 'soon' => 'bg-amber-100 text-amber-800', 'overdue' => 'bg-red-100 text-red-800'][$pond->status];
                                $searchText = Str::lower($pond->name.' '.$pond->fish_type.' '.$statusLabel.' '.$pond->notes);
                            @endphp
                            <tr x-show="{{ Js::from($searchText) }}.includes(search.toLowerCase())">
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">{{ $pond->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="rounded px-2 py-1 text-xs font-semibold {{ $statusBadge }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->fish_type }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ number_format($pond->fish_count) }} ekor</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->stocking_date?->format('d M Y') ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->harvest_date?->format('d M Y') ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">X{{ $pond->x }}, Y{{ $pond->y }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->width }} x {{ $pond->height }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('ponds.show', $pond) }}" class="font-semibold text-slate-700 underline">Detail</a>
                                        <a href="{{ route('ponds.edit', $pond) }}" class="font-semibold text-slate-700 underline">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-4">
                {{ $pondTable->links() }}
            </div>
        </section>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const grid = window.GridStack.init({
                cellHeight: 90,
                column: 24,
                float: true,
                margin: 10,
                minRow: 12,
            });
            const viewport = document.getElementById('pondMapViewport');
            const canvas = document.getElementById('pondMapCanvas');
            const zoomLabel = document.getElementById('zoomLabel');
            let zoom = 1;
            let isPanning = false;
            let startX = 0;
            let startY = 0;
            let scrollLeft = 0;
            let scrollTop = 0;

            const applyZoom = (nextZoom) => {
                zoom = Math.min(1.5, Math.max(0.1, Number(nextZoom.toFixed(2))));
                canvas.style.zoom = zoom;
                zoomLabel.textContent = `${Math.round(zoom * 100)}%`;
            };

            document.getElementById('zoomOut')?.addEventListener('click', () => applyZoom(zoom - 0.1));
            document.getElementById('zoomIn')?.addEventListener('click', () => applyZoom(zoom + 0.1));
            document.getElementById('zoomReset')?.addEventListener('click', () => applyZoom(1));

            viewport?.addEventListener('wheel', (event) => {
                if (!event.ctrlKey) {
                    return;
                }

                event.preventDefault();
                applyZoom(zoom + (event.deltaY > 0 ? -0.1 : 0.1));
            }, { passive: false });

            viewport?.addEventListener('pointerdown', (event) => {
                if (event.target.closest('.grid-stack-item')) {
                    return;
                }

                isPanning = true;
                startX = event.clientX;
                startY = event.clientY;
                scrollLeft = viewport.scrollLeft;
                scrollTop = viewport.scrollTop;
                viewport.setPointerCapture(event.pointerId);
                viewport.classList.add('cursor-grabbing');
            });

            viewport?.addEventListener('pointermove', (event) => {
                if (!isPanning) {
                    return;
                }

                viewport.scrollLeft = scrollLeft - (event.clientX - startX);
                viewport.scrollTop = scrollTop - (event.clientY - startY);
            });

            viewport?.addEventListener('pointerup', (event) => {
                isPanning = false;
                viewport.releasePointerCapture(event.pointerId);
                viewport.classList.remove('cursor-grabbing');
            });

            document.getElementById('layoutForm')?.addEventListener('submit', () => {
                const items = grid.save(false).map(item => ({ id: item.id, x: item.x, y: item.y, w: item.w, h: item.h }));
                document.getElementById('layoutItems').name = 'items_json';
                document.getElementById('layoutItems').value = JSON.stringify(items);
            });
        });
    </script>
</x-app-layout>
