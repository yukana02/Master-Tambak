<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">{{ $feed->name }}</h1></x-slot>
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <dl class="grid gap-4 md:grid-cols-2">
            <div><dt class="text-sm text-slate-500">Kategori</dt><dd class="font-semibold">{{ $feed->category?->name ?? '-' }}</dd></div>
            <div><dt class="text-sm text-slate-500">Berat per Sak</dt><dd class="font-semibold">{{ number_format($feed->sack_weight_kg, 2, ',', '.') }} kg</dd></div>
            <div><dt class="text-sm text-slate-500">FCR</dt><dd class="font-semibold">{{ number_format($feed->fcr, 2, ',', '.') }}</dd></div>
            <div><dt class="text-sm text-slate-500">Status</dt><dd class="font-semibold">{{ $feed->is_active ? 'Aktif' : 'Nonaktif' }}</dd></div>
            <div><dt class="text-sm text-slate-500">Kolam Terkait</dt><dd class="font-semibold">{{ $feed->ponds()->count() }}</dd></div>
            <div class="md:col-span-2"><dt class="text-sm text-slate-500">Komposisi</dt><dd class="font-semibold">{{ $feed->composition ?: '-' }}</dd></div>
        </dl>
    </div>
</x-app-layout>
