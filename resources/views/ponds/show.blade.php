<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">{{ $pond->name }}</h1></x-slot>
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <p class="text-slate-500">{{ $pond->notes ?: 'Tidak ada catatan.' }}</p>
    </div>
</x-app-layout>
