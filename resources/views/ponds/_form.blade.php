@csrf
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr.setDefaults({ dateFormat: "d/m/Y", locale: "id", allowInput: true });
        flatpickr('.datepicker');
    });
</script>
<div class="grid gap-4 md:grid-cols-2">
    <label class="block text-sm font-medium">Nama
        <input name="name" value="{{ old('name', $pond->name) }}" class="mt-1 w-full rounded-md border-slate-300" required>
    </label>
    <label class="block text-sm font-medium">Jenis Ikan
        <input name="fish_type" value="{{ old('fish_type', $pond->fish_type) }}" class="mt-1 w-full rounded-md border-slate-300" required>
    </label>
    <label class="block text-sm font-medium">Jumlah Ikan
        <input type="number" name="fish_count" value="{{ old('fish_count', $pond->fish_count ?? 0) }}" class="mt-1 w-full rounded-md border-slate-300" min="0" required>
    </label>
    <label class="block text-sm font-medium">Tanggal Tebar
        <input type="text" name="stocking_date" value="{{ old('stocking_date', optional($pond->stocking_date)->format('d/m/Y')) }}" class="datepicker mt-1 w-full rounded-md border-slate-300">
    </label>
    <label class="block text-sm font-medium text-slate-500">Tanggal Panen (Estimasi Otomatis)
        <input type="text" value="{{ $pond->predicted_harvest_date?->format('d/m/Y') ?? 'Belum cukup data' }}" class="mt-1 w-full rounded-md border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed" disabled>
        <p class="mt-1 text-xs text-slate-400">Tanggal panen dihitung otomatis berdasarkan konversi pakan harian.</p>
    </label>
</div>

<section id="target-panen-section" class="mt-6 rounded-lg border border-slate-200 bg-slate-50 p-4">
    <div class="mb-4">
        <h2 class="font-semibold text-slate-900">Target Panen</h2>
        <p class="text-sm text-slate-500">Target panen dipakai untuk membaca progress aktual dari catatan pemberian pakan harian.</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <label class="block text-sm font-medium">Target Daging Panen (kg)
            <input type="number" name="target_harvest_weight_kg" value="{{ old('target_harvest_weight_kg', $pond->target_harvest_weight_kg) }}" class="mt-1 w-full rounded-md border-slate-300" min="0.01" step="0.01" placeholder="Contoh: 100">
        </label>
        <div class="rounded-md bg-white p-3 text-sm ring-1 ring-slate-200">
            <div class="text-slate-500">Progress aktual dari catatan</div>
            <div class="font-semibold text-slate-900">{{ $pond->harvest_progress_percent ? number_format($pond->harvest_progress_percent, 1, ',', '.') . '%' : '-' }}</div>
            <div class="mt-1 text-xs text-slate-500">
                {{ $pond->actual_estimated_meat_kg > 0 ? number_format($pond->actual_estimated_meat_kg, 2, ',', '.') . ' kg dari catatan pakan' : 'Belum ada catatan pakan aktual.' }}
            </div>
        </div>
    </div>
</section>

<div class="mt-4 grid gap-4 md:grid-cols-2">
    <label class="block text-sm font-medium">Catatan
        <textarea name="notes" class="mt-1 w-full rounded-md border-slate-300" rows="3">{{ old('notes', $pond->notes) }}</textarea>
    </label>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
    <a href="{{ route('ponds.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Batal</a>
</div>
