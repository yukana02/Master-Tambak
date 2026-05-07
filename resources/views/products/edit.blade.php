<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">Edit Produk</h1></x-slot>
    <form method="POST" action="{{ route('products.update', $product) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @method('PUT')
        @include('products._form')
    </form>
    <form method="POST" action="{{ route('products.destroy', $product) }}" class="mt-4">
        @csrf @method('DELETE')
        <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white">Hapus Produk</button>
    </form>
</x-app-layout>
