<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Manajemen Kolam</h1>
        <p class="text-sm text-slate-500">Atur peta kolam dengan drag, resize, pan, zoom, lalu simpan layout.</p>
    </x-slot>

    <style>
        /* Cegah font boosting di iOS/Android pada area peta */
        #pondMapViewport,
        #pondMapViewport *,
        #pondMapCanvas,
        #pondMapCanvas *,
        .grid-stack-item,
        .grid-stack-item * {
            -webkit-text-size-adjust: none !important;
            text-size-adjust: none !important;
        }

        #pondMapViewport {
            -webkit-overflow-scrolling: touch;
            touch-action: pan-x pan-y;
        }

        #pondMapCanvas {
            transform-origin: 0 0;
            will-change: transform;
            --pond-zoom: 1;
            --pond-font-scale: 1;
            --pond-pad-scale: 1;
        }

        .grid-stack-item-content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            word-break: break-word;
            padding: calc(12px * var(--pond-pad-scale, 1)) !important;
            border-width: calc(2px * var(--pond-pad-scale, 1)) !important;
            border-radius: calc(8px * var(--pond-pad-scale, 1)) !important;
        }

        .pond-box-title {
            font-size: calc(13px * var(--pond-font-scale, 1)) !important;
            line-height: 1.15 !important;
        }
        .pond-box-badge {
            font-size: calc(9px * var(--pond-font-scale, 1)) !important;
            padding: calc(2px * var(--pond-pad-scale, 1)) calc(4px * var(--pond-pad-scale, 1)) !important;
            border-radius: calc(4px * var(--pond-pad-scale, 1)) !important;
            line-height: 1 !important;
        }
        .pond-box-body {
            margin-top: calc(8px * var(--pond-pad-scale, 1)) !important;
        }
        .pond-box-text {
            font-size: calc(11px * var(--pond-font-scale, 1)) !important;
            line-height: 1.25 !important;
        }
        .pond-box-footer {
            margin-top: calc(12px * var(--pond-pad-scale, 1)) !important;
            gap: calc(6px * var(--pond-pad-scale, 1)) !important;
        }
        .pond-box-btn {
            font-size: calc(10px * var(--pond-font-scale, 1)) !important;
            padding: calc(3px * var(--pond-pad-scale, 1)) calc(6px * var(--pond-pad-scale, 1)) !important;
            border-radius: calc(4px * var(--pond-pad-scale, 1)) !important;
            line-height: 1.2 !important;
        }

        @media (max-width: 639px) {
            #pondMapViewport {
                height: 56vh;
                min-height: 320px;
            }
            #pondMapCanvas {
                padding: 0.75rem !important;
            }
            .grid-stack-item-content {
                padding: 0.5rem !important;
            }
            .pond-box-title { font-size: 11px !important; }
            .pond-box-text { font-size: 9px !important; }
            .pond-box-badge { font-size: 8px !important; }
            .pond-box-btn { font-size: 8px !important; padding: 2px 5px !important; }
        }
    </style>

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

            <div id="pondMapViewport" class="h-[62vh] min-h-[380px] overflow-auto rounded-lg border border-slate-200 bg-slate-100 md:h-[68vh] lg:h-[620px] relative">
                <div id="canvasScaleWrapper" class="origin-top-left" style="width: 100%; height: 100%; transition: width 0.1s ease, height 0.1s ease;">
                    <div id="pondMapCanvas" class="min-h-[760px] min-w-[1280px] origin-top-left p-4 sm:min-h-[900px] sm:min-w-[1680px] sm:p-6 lg:min-h-[1080px] lg:min-w-[2200px] lg:p-8" style="background-color: #eef6f0; background-image: linear-gradient(rgba(15, 23, 42, .08) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, .08) 1px, transparent 1px); background-size: 90px 90px; transform: scale(1); transform-origin: 0 0; transition: transform 0.1s ease; -webkit-text-size-adjust: 100%; text-size-adjust: 100%;">


                    <div class="grid-stack w-[1200px] sm:w-[1560px] lg:w-[2040px]">
                        @foreach ($ponds as $pond)
                            @php
                                $statusClass = ['active' => 'bg-emerald-100/90 text-emerald-950 border-emerald-400', 'ready' => 'bg-sky-100/90 text-sky-950 border-sky-400', 'soon' => 'bg-amber-100/90 text-amber-950 border-amber-400', 'overdue' => 'bg-red-100/90 text-red-950 border-red-400'][$pond->status];
                                $statusLabel = ['active' => 'Aktif', 'ready' => 'Target tercapai', 'soon' => 'Mendekati panen', 'overdue' => 'Terlambat panen'][$pond->status];
                            @endphp
                            <div class="grid-stack-item" gs-id="{{ $pond->id }}" gs-x="{{ $pond->x }}" gs-y="{{ $pond->y }}" gs-w="{{ $pond->width }}" gs-h="{{ $pond->height }}">
                                <div class="grid-stack-item-content rounded border-2 p-3 shadow-sm {{ $statusClass }}">
                                    <div class="flex items-start justify-between gap-1">
                                        <h2 class="font-semibold pond-box-title truncate" title="{{ $pond->name }}">{{ $pond->name }}</h2>
                                        <span class="rounded bg-white/70 font-semibold pond-box-badge shrink-0 whitespace-nowrap">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="mt-2 space-y-0.5 pond-box-body">
                                        <p class="pond-box-text text-slate-700 truncate" title="Panen: {{ $pond->predicted_harvest_date?->format('d M Y') ?? 'belum cukup data' }}">
                                            <span class="text-slate-500 font-medium">Panen:</span> 
                                            {{ $pond->predicted_harvest_date?->format('d M Y') ?? 'belum cukup data' }}
                                        </p>
                                        <p class="pond-box-text text-slate-700 truncate">
                                            <span class="text-slate-500 font-medium">Progress:</span> 
                                            {{ $pond->harvest_progress_percent ? number_format($pond->harvest_progress_percent, 1, ',', '.') . '%' : '-' }}
                                        </p>
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-1.5 pond-box-footer">
                                        <a href="{{ route('ponds.show', $pond) }}#feedings-section" class="inline-flex items-center justify-center rounded bg-white font-bold shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 pond-box-btn">Input Pakan</a>
                                        <a href="{{ route('ponds.edit', $pond) }}" class="inline-flex items-center justify-center rounded bg-white font-bold shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 pond-box-btn">Edit</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
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

            <div class="max-h-[320px] lg:max-h-[520px] overflow-y-auto overflow-x-auto relative">
                <div x-show="isLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-[1px]">
                    <svg class="h-8 w-8 animate-spin text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Desktop Table View --}}
                <table class="hidden min-w-full divide-y divide-slate-200 text-sm lg:table">
                    <thead class="bg-slate-50 text-left text-slate-500 sticky top-0 z-10">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Nama</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Status</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Jenis Ikan</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Jumlah</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Pakan</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Target</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Aktual Konversi</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Tebar</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50">Prediksi Panen</th>
                            <th class="whitespace-nowrap px-4 py-3 bg-slate-50"></th>
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

            <div id="paginationContainer" class="hidden"></div>
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
            const wrapper = document.getElementById('canvasScaleWrapper');
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
            let hasUnsavedLayout = false;

            const markLayoutDirty = () => {
                hasUnsavedLayout = true;
            };

            const clearLayoutDirty = () => {
                hasUnsavedLayout = false;
            };

            const applyZoom = (nextZoom) => {
                zoom = Math.min(1.5, Math.max(0.18, Number(nextZoom.toFixed(2))));
                canvas.style.transform = `scale(${zoom})`;
                canvas.style.transformOrigin = '0 0';

                const fontScale = window.innerWidth < 640 ? Math.max(0.78, zoom) : zoom;
                const padScale = window.innerWidth < 640 ? Math.max(0.72, zoom) : zoom;

                canvas.style.setProperty('--pond-zoom', zoom);
                canvas.style.setProperty('--pond-font-scale', fontScale.toFixed(2));
                canvas.style.setProperty('--pond-pad-scale', padScale.toFixed(2));

                if (wrapper && canvas) {
                    const baseWidth = canvas.scrollWidth || canvas.offsetWidth || 1680;
                    const baseHeight = canvas.scrollHeight || canvas.offsetHeight || 900;
                    wrapper.style.width = `${Math.ceil(baseWidth * zoom)}px`;
                    wrapper.style.height = `${Math.ceil(baseHeight * zoom)}px`;
                }

                zoomLabel.textContent = `${Math.round(zoom * 100)}%`;
            };

            let resizeTimer = null;
            const resizeObserver = new ResizeObserver(() => {
                window.clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(() => applyZoom(zoom), 60);
            });
            if (canvas) {
                resizeObserver.observe(canvas);
            }

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

            grid.on('change', () => {
                if (isEditMode) markLayoutDirty();
            });

            grid.on('dragstop', () => {
                if (isEditMode) markLayoutDirty();
            });

            grid.on('resizestop', () => {
                if (isEditMode) markLayoutDirty();
            });

            // Responsive Map
            const initResponsiveZoom = () => {
                if (window.innerWidth < 640) applyZoom(0.42);
                else if (window.innerWidth < 1024) applyZoom(0.65);
                else applyZoom(1);
            };
            initResponsiveZoom();

            document.getElementById('zoomOut')?.addEventListener('click', () => applyZoom(zoom - 0.08));
            document.getElementById('zoomIn')?.addEventListener('click', () => applyZoom(zoom + 0.08));
            document.getElementById('zoomReset')?.addEventListener('click', () => applyZoom(window.innerWidth < 640 ? 0.42 : 1));

            window.addEventListener('resize', () => {
                window.clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(initResponsiveZoom, 120);
            });

            // Pan logic: Only active when NOT in edit mode OR when target is not a grid item
            // Restrict custom drag-pan to mouse inputs to allow smooth native touch scrolling on mobile
            viewport?.addEventListener('pointerdown', (event) => {
                if (event.pointerType !== 'mouse') return;
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
                if (e.target.closest('a') || e.target.closest('button')) {
                    e.stopPropagation();
                }
            }, true);
            canvas?.addEventListener('mousedown', (e) => {
                if (e.target.closest('a') || e.target.closest('button')) {
                    e.stopPropagation();
                }
            }, true);
            canvas?.addEventListener('click', (e) => {
                if (e.target.closest('a') || e.target.closest('button')) {
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
                        clearLayoutDirty();
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

            window.addEventListener('beforeunload', (e) => {
                if (hasUnsavedLayout) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>
</x-app-layout>
