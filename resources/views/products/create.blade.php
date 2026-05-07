<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">Tambah Produk</h1></x-slot>
    <form method="POST" action="{{ route('products.store') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @include('products._form')
    </form>
</x-app-layout>
