<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">Edit Pakan</h1></x-slot>
    <form method="POST" action="{{ route('feeds.update', $feed) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @method('PUT')
        @include('feeds._form')
    </form>
    <form method="POST" action="{{ route('feeds.destroy', $feed) }}" class="mt-4">
        @csrf @method('DELETE')
        <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white">Hapus Pakan</button>
    </form>
</x-app-layout>
