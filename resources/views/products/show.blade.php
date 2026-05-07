<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">{{ $product->name }}</h1></x-slot>
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <dl class="grid gap-4 md:grid-cols-2">
            <div><dt class="text-sm text-slate-500">SKU</dt><dd class="font-semibold">{{ $product->sku }}</dd></div>
            <div><dt class="text-sm text-slate-500">Kategori</dt><dd class="font-semibold">{{ $product->category->name }}</dd></div>
            <div><dt class="text-sm text-slate-500">Harga</dt><dd class="font-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</dd></div>
            <div><dt class="text-sm text-slate-500">Stok</dt><dd class="font-semibold">{{ $product->stock }} {{ $product->unit }}</dd></div>
        </dl>
    </div>
</x-app-layout>
