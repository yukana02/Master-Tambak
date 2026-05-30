<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Manajemen Kolam</h1>
        <p class="text-sm text-slate-500">Atur peta kolam dengan drag, resize, pan, zoom, lalu simpan layout.</p>
    </x-slot>

    <form id="layoutForm" class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200 sm:p-4">
        @csrf
        @if ($ponds->isEmpty())
            <div class="py-16 text-center text-slate-500">Belum ada kolam.</div>
        @else
            <div class="mb-4 flex flex-col gap-4 border-b border-slate-200 pb-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="font-semibold text-slate-900">Peta Tambak <span id="modeLabel" class="ml-2 rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">PREVIEW</span></h2>
                        <p class="text-sm text-slate-500" id="modeDescription">Geser untuk pan peta. Gunakan Mode Edit untuk mengatur posisi kolam.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="toggleEditMode" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Mode Edit</button>
                        <button type="submit" form="layoutForm" id="saveLayoutBtn" class="inline-flex justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Simpan Layout</button>
                        <a href="{{ route('ponds.create') }}" class="inline-flex justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Kolam</a>
                    </div>
                </div>
                <div class="flex w-full items-center gap-2 sm:w-auto sm:justify-end">
                    <button type="button" id="zoomOut" class="min-h-10 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-none">-</button>
                    <span id="zoomLabel" class="w-14 text-center text-sm font-semibold text-slate-700">100%</span>
                    <button type="button" id="zoomIn" class="min-h-10 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-none">+</button>
                    <button type="button" id="zoomReset" class="min-h-10 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-none">Reset</button>
                </div>
            </div>

            <div id="pondMapViewport" class="h-[62vh] min-h-[380px] overflow-auto rounded-lg border border-slate-200 bg-slate-100 md:h-[68vh] lg:h-[620px]">
                <div id="pondMapCanvas" class="min-h-[760px] min-w-[1280px] origin-top-left p-4 sm:min-h-[900px] sm:min-w-[1680px] sm:p-6 lg:min-h-[1080px] lg:min-w-[2200px] lg:p-8" style="background-color: #eef6f0; background-image: linear-gradient(rgba(15, 23, 42, .08) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, .08) 1px, transparent 1px); background-size: 90px 90px;">


                    <div class="grid-stack w-[1200px] sm:w-[1560px] lg:w-[2040px]">
                        @foreach ($ponds as $pond)
                            @php
                                $statusClass = ['active' => 'bg-emerald-100/90 text-emerald-950 border-emerald-400', 'ready' => 'bg-sky-100/90 text-sky-950 border-sky-400', 'soon' => 'bg-amber-100/90 text-amber-950 border-amber-400', 'overdue' => 'bg-red-100/90 text-red-950 border-red-400'][$pond->status];
                                $statusLabel = ['active' => 'Aktif', 'ready' => 'Target tercapai', 'soon' => 'Mendekati panen', 'overdue' => 'Terlambat panen'][$pond->status];
                            @endphp
                            <div class="grid-stack-item" gs-id="{{ $pond->id }}" gs-x="{{ $pond->x }}" gs-y="{{ $pond->y }}" gs-w="{{ $pond->width }}" gs-h="{{ $pond->height }}">
                                <div class="grid-stack-item-content rounded border-2 p-4 shadow-sm {{ $statusClass }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <h2 class="font-semibold">{{ $pond->name }}</h2>
                                        <span class="rounded bg-white/70 px-2 py-1 text-xs font-semibold">{{ $statusLabel }}</span>
                                    </div>
                                    <p class="mt-3 text-sm">Prediksi panen: {{ $pond->predicted_harvest_date?->format('d M Y') ?? 'belum cukup data' }}</p>
                                    <p class="mt-1 text-sm">Progress: {{ $pond->harvest_progress_percent ? number_format($pond->harvest_progress_percent, 1, ',', '.') . '%' : '-' }}</p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <a href="{{ route('ponds.show', $pond) }}#feedings-section" class="inline-flex items-center justify-center rounded bg-white px-2 py-1 text-xs font-bold shadow-sm ring-1 ring-slate-200 hover:bg-slate-50">Input Pakan</a>
                                        <a href="{{ route('ponds.edit', $pond) }}" class="inline-flex items-center justify-center rounded bg-white px-2 py-1 text-xs font-bold shadow-sm ring-1 ring-slate-200 hover:bg-slate-50">Edit</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">Posisi dan ukuran kolam disimpan dalam koordinat grid peta.</p>
            </div>
        @endif
    </form>

    @if ($ponds->isNotEmpty())
        <section 
            x-data="{ 
                search: '{{ request('search', '') }}',
                isLoading: false,
                async init() {
                    this.$watch('search', value => this.fetchPonds());
                },
                async fetchPonds() {
                    this.isLoading = true;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', this.search);
                    url.searchParams.set('page', 1);
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
                        
                        document.getElementById('pondTableContainer').innerHTML = doc.getElementById('pondTableContainer').innerHTML;
                        document.getElementById('pondCardContainer').innerHTML = doc.getElementById('pondCardContainer').innerHTML;
                        document.getElementById('paginationContainer').innerHTML = doc.getElementById('paginationContainer').innerHTML;
                        
                        window.history.pushState({}, '', url);
                    } catch (error) {
                        console.error('Pagination error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }" 
            class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200"
            @click="if ($event.target.closest('#paginationContainer a')) { $event.preventDefault(); goToPage($event.target.closest('#paginationContainer a').href); }"
        >
            <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Daftar Kolam</h2>
                    <p class="text-sm text-slate-500">List data kolam berdasarkan peta dan jadwal panen.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded bg-emerald-100 px-2 py-1 text-emerald-800">Aktif: {{ $ponds->where('status', 'active')->count() }}</span>
                        <span class="rounded bg-sky-100 px-2 py-1 text-sky-800">Target: {{ $ponds->where('status', 'ready')->count() }}</span>
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

            <div class="overflow-x-auto relative">
                <div x-show="isLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-[1px]">
                    <svg class="h-8 w-8 animate-spin text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Desktop Table View --}}
                <table class="hidden min-w-full divide-y divide-slate-200 text-sm lg:table">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Nama</th>
                            <th class="whitespace-nowrap px-4 py-3">Status</th>
                            <th class="whitespace-nowrap px-4 py-3">Jenis Ikan</th>
                            <th class="whitespace-nowrap px-4 py-3">Jumlah</th>
                            <th class="whitespace-nowrap px-4 py-3">Pakan</th>
                            <th class="whitespace-nowrap px-4 py-3">Target</th>
                            <th class="whitespace-nowrap px-4 py-3">Aktual Konversi</th>
                            <th class="whitespace-nowrap px-4 py-3">Tebar</th>
                            <th class="whitespace-nowrap px-4 py-3">Prediksi Panen</th>
                            <th class="whitespace-nowrap px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="pondTableContainer" class="divide-y divide-slate-100">
                        @foreach ($pondTable as $pond)
                            @php
                                $statusLabel = ['active' => 'Aktif', 'ready' => 'Target tercapai', 'soon' => 'Mendekati panen', 'overdue' => 'Terlambat panen'][$pond->status];
                                $statusBadge = ['active' => 'bg-emerald-100 text-emerald-800', 'ready' => 'bg-sky-100 text-sky-800', 'soon' => 'bg-amber-100 text-amber-800', 'overdue' => 'bg-red-100 text-red-800'][$pond->status];
                                $searchText = Str::lower($pond->name.' '.$pond->fish_type.' '.$statusLabel.' '.$pond->notes.' '.$pond->feed?->name);
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">{{ $pond->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="rounded px-2 py-1 text-xs font-semibold {{ $statusBadge }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->fish_type }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ number_format($pond->fish_count) }} ekor</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->feed?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->target_harvest_weight_kg ? number_format($pond->target_harvest_weight_kg, 1, ',', '.') . ' kg' : '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->actual_estimated_meat_kg > 0 ? number_format($pond->actual_estimated_meat_kg, 1, ',', '.') . ' kg' : '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->stocking_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pond->predicted_harvest_date?->format('d/m/Y') ?? 'Belum cukup data' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('ponds.show', $pond) }}#feedings-section" class="font-semibold text-slate-700 underline">Input Pakan</a>
                                        <a href="{{ route('ponds.edit', $pond) }}" class="font-semibold text-slate-700 underline">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Mobile Card View --}}
                <div id="pondCardContainer" class="grid grid-cols-1 divide-y divide-slate-100 lg:hidden">
                    @foreach ($pondTable as $pond)
                        @php
                            $statusLabel = ['active' => 'Aktif', 'ready' => 'Target tercapai', 'soon' => 'Mendekati panen', 'overdue' => 'Terlambat panen'][$pond->status];
                            $statusBadge = ['active' => 'bg-emerald-100 text-emerald-800', 'ready' => 'bg-sky-100 text-sky-800', 'soon' => 'bg-amber-100 text-amber-800', 'overdue' => 'bg-red-100 text-red-800'][$pond->status];
                            $searchText = Str::lower($pond->name.' '.$pond->fish_type.' '.$statusLabel.' '.$pond->notes.' '.$pond->feed?->name);
                        @endphp
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-bold text-slate-900">{{ $pond->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $pond->fish_type }} · {{ number_format($pond->fish_count) }} ekor</div>
                                </div>
                                <span class="rounded px-2 py-1 text-[10px] font-bold uppercase tracking-wider {{ $statusBadge }}">{{ $statusLabel }}</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm border-y border-slate-50 py-2">
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Pakan Terakhir</div>
                                    <div class="font-medium text-slate-700">{{ $pond->feed?->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Prediksi Panen</div>
                                    <div class="font-medium text-slate-700">{{ $pond->predicted_harvest_date?->format('d/m/Y') ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Target / Aktual</div>
                                    <div class="font-medium text-slate-700">
                                        {{ $pond->target_harvest_weight_kg ? number_format($pond->target_harvest_weight_kg, 1, ',', '.') : '0' }} / 
                                        <span class="text-emerald-700">{{ number_format($pond->actual_estimated_meat_kg, 1, ',', '.') }} kg</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Tanggal Tebar</div>
                                    <div class="font-medium text-slate-700">{{ $pond->stocking_date?->format('d/m/Y') ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="flex gap-2 pt-1">
                                <a href="{{ route('ponds.show', $pond) }}#feedings-section" class="flex-1 rounded-md bg-slate-100 px-3 py-2 text-center text-xs font-bold text-slate-700 shadow-sm ring-1 ring-slate-200">Input Pakan</a>
                                <a href="{{ route('ponds.edit', $pond) }}" class="flex-1 rounded-md bg-white px-3 py-2 text-center text-xs font-bold text-slate-700 shadow-sm ring-1 ring-slate-200">Edit</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="paginationContainer" class="border-t border-slate-200 px-4 py-4">
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
                disableDrag: true, // Start disabled
                disableResize: true,
            });

            const viewport = document.getElementById('pondMapViewport');
            const canvas = document.getElementById('pondMapCanvas');
            const zoomLabel = document.getElementById('zoomLabel');
            const toggleEditBtn = document.getElementById('toggleEditMode');
            const modeLabel = document.getElementById('modeLabel');
            const modeDescription = document.getElementById('modeDescription');
            const saveLayoutBtn = document.getElementById('saveLayoutBtn');
            
            let zoom = 1;
            let isPanning = false;
            let startX = 0;
            let startY = 0;
            let scrollLeft = 0;
            let scrollTop = 0;
            let isEditMode = false;

            const applyZoom = (nextZoom) => {
                zoom = Math.min(1.5, Math.max(0.1, Number(nextZoom.toFixed(2))));
                canvas.style.zoom = zoom;
                zoomLabel.textContent = `${Math.round(zoom * 100)}%`;
            };

            // Toggle Mode
            toggleEditBtn.addEventListener('click', () => {
                isEditMode = !isEditMode;
                grid.enableMove(isEditMode);
                grid.enableResize(isEditMode);
                
                // Button shows the NEXT action
                toggleEditBtn.textContent = isEditMode ? 'Mode Preview' : 'Mode Edit';
                
                // Visual feedback
                toggleEditBtn.classList.toggle('bg-emerald-50', isEditMode);
                toggleEditBtn.classList.toggle('ring-emerald-500', isEditMode);
                modeLabel.textContent = isEditMode ? 'EDIT' : 'PREVIEW';
                modeLabel.classList.toggle('bg-emerald-100', isEditMode);
                modeLabel.classList.toggle('text-emerald-700', isEditMode);
                modeDescription.textContent = isEditMode ? 'Mode Edit aktif: Anda bisa menggeser dan mengatur ukuran kolam.' : 'Mode Preview aktif: Geser untuk pan peta.';
            });

            // Responsive Map
            if (window.innerWidth < 640) applyZoom(0.5);
            else if (window.innerWidth < 1024) applyZoom(0.7);
            else applyZoom(1);

            document.getElementById('zoomOut')?.addEventListener('click', () => applyZoom(zoom - 0.1));
            document.getElementById('zoomIn')?.addEventListener('click', () => applyZoom(zoom + 0.1));
            document.getElementById('zoomReset')?.addEventListener('click', () => applyZoom(1));

            // Pan logic: Only active when NOT in edit mode OR when target is not a grid item
            viewport?.addEventListener('pointerdown', (event) => {
                if (isEditMode && event.target.closest('.grid-stack-item')) return;

                isPanning = true;
                startX = event.clientX;
                startY = event.clientY;
                scrollLeft = viewport.scrollLeft;
                scrollTop = viewport.scrollTop;
                viewport.setPointerCapture(event.pointerId);
                viewport.classList.add('cursor-grabbing');
            });

            viewport?.addEventListener('pointermove', (event) => {
                if (!isPanning) return;
                viewport.scrollLeft = scrollLeft - (event.clientX - startX);
                viewport.scrollTop = scrollTop - (event.clientY - startY);
            });

            viewport?.addEventListener('pointerup', (event) => {
                isPanning = false;
                viewport.releasePointerCapture(event.pointerId);
                viewport.classList.remove('cursor-grabbing');
            });

            // Prevent drag/pan interception when clicking action links
            canvas?.addEventListener('pointerdown', (e) => {
                if (e.target.closest('a')) {
                    e.stopPropagation();
                }
            }, true);
            canvas?.addEventListener('mousedown', (e) => {
                if (e.target.closest('a')) {
                    e.stopPropagation();
                }
            }, true);
            canvas?.addEventListener('click', (e) => {
                if (e.target.closest('a')) {
                    e.stopPropagation();
                }
            }, true);

            // AJAX Save Layout
            document.getElementById('layoutForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const items = grid.save(false).map(item => ({ id: item.id, x: item.x, y: item.y, w: item.w, h: item.h }));
                
                try {
                    const response = await fetch('/ponds-layout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                        },
                        body: JSON.stringify({ items_json: JSON.stringify(items) })
                    });
                    
                    if (response.ok) {
                        alert('Layout berhasil disimpan!');
                    } else {
                        const errData = await response.json().catch(() => ({}));
                        const errMsg = errData.message || 'Gagal menyimpan.';
                        throw new Error(errMsg);
                    }
                } catch (error) {
                    alert(error.message);
                }
            });
        });
    </script>
</x-app-layout>
