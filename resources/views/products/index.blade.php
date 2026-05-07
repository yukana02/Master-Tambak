<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Produk</h1>
        <p class="text-sm text-slate-500">Kelola produk, kategori, harga, dan stok POS.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-3">
            <form method="POST" action="{{ route('product-categories.store') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                @csrf
                <h2 class="mb-4 font-semibold">Kategori Produk</h2>
                <label class="block text-sm font-medium">Nama
                    <input name="name" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ikan, Pakan, Alat, Pupuk" required>
                </label>
                <label class="mt-4 block text-sm font-medium">Deskripsi
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-slate-300"></textarea>
                </label>
                <button class="mt-4 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Kategori</button>
            </form>

            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold">Daftar Kategori</h2>
                    <a href="{{ route('products.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Produk</a>
                </div>
                <div class="flex flex-wrap gap-2">
                    @forelse ($categories as $category)
                        <span class="rounded bg-slate-100 px-3 py-2 text-sm">{{ $category->name }}</span>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada kategori produk.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $product->sku }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $product->category->name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 {{ $product->stock <= 10 ? 'font-semibold text-amber-700' : '' }}">{{ $product->stock }} {{ $product->unit }}</td>
                            <td class="px-4 py-3">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('products.edit', $product) }}" class="font-semibold underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>
</x-app-layout>
