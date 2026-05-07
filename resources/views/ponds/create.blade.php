<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">Tambah Kolam</h1></x-slot>
    <form method="POST" action="{{ route('ponds.store') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @include('ponds._form')
    </form>
</x-app-layout>
