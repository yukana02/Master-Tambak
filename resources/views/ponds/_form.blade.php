@csrf
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
        <input type="date" name="stocking_date" value="{{ old('stocking_date', optional($pond->stocking_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-slate-300">
    </label>
    <label class="block text-sm font-medium">Tanggal Panen
        <input type="date" name="harvest_date" value="{{ old('harvest_date', optional($pond->harvest_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-slate-300">
    </label>
    <label class="block text-sm font-medium">Catatan
        <textarea name="notes" class="mt-1 w-full rounded-md border-slate-300" rows="3">{{ old('notes', $pond->notes) }}</textarea>
    </label>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
    <a href="{{ route('ponds.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Batal</a>
</div>
