@csrf
<div class="grid gap-4 md:grid-cols-2">
    <label class="block text-sm font-medium">Nama Pakan
        <input name="name" value="{{ old('name', $feed->name) }}" class="mt-1 w-full rounded-md border-slate-300" required>
    </label>
    <label class="block text-sm font-medium">Berat per Sak (kg)
        <input type="number" name="sack_weight_kg" value="{{ old('sack_weight_kg', $feed->sack_weight_kg) }}" class="mt-1 w-full rounded-md border-slate-300" min="0.01" step="0.01" required>
    </label>
    <label class="block text-sm font-medium">FCR
        <input type="number" name="fcr" value="{{ old('fcr', $feed->fcr) }}" class="mt-1 w-full rounded-md border-slate-300" min="0.01" step="0.01" required>
        <span class="mt-1 block text-xs font-normal text-slate-500">Contoh: FCR 1.50 berarti 1.5 kg pakan menghasilkan 1 kg daging.</span>
    </label>
    <label class="flex items-center gap-2 text-sm font-medium md:pt-7">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $feed->is_active ?? true))>
        Aktif untuk kolam
    </label>
    <label class="block text-sm font-medium md:col-span-2">Komposisi
        <textarea name="composition" class="mt-1 w-full rounded-md border-slate-300" rows="4" placeholder="Protein, lemak, serat, vitamin, atau catatan komposisi lain">{{ old('composition', $feed->composition) }}</textarea>
    </label>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
    <a href="{{ route('feeds.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Batal</a>
</div>
