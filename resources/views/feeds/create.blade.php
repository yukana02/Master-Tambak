<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">Tambah Pakan</h1></x-slot>
    <form method="POST" action="{{ route('feeds.store') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @include('feeds._form')
    </form>
</x-app-layout>
