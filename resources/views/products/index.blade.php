<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Produk</h1>
        <p class="text-sm text-slate-500">Kelola produk, kategori, harga, dan stok POS.</p>
    </x-slot>

    <div x-data="productManagement()" class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <form method="POST" action="{{ route('product-categories.store') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                @csrf
                <h2 class="mb-4 font-semibold text-slate-900">Kategori Produk</h2>
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700">Nama
                        <input name="name" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" placeholder="Ikan, Pakan, Alat, Pupuk" required>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">Deskripsi
                        <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500"></textarea>
                    </label>
                </div>
                <button class="mt-5 w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 sm:w-auto">TAMBAH KATEGORI</button>
            </form>

            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-semibold text-slate-900">Daftar Kategori</h2>
                    <a href="{{ route('products.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800">TAMBAH PRODUK</a>
                </div>
                <div class="flex flex-wrap gap-2">
                    @forelse ($categories as $category)
                        <button 
                            type="button" 
                            @click="selectedCategory = selectedCategory === '{{ $category->name }}' ? '' : '{{ $category->name }}'"
                            :class="selectedCategory === '{{ $category->name }}' ? 'bg-slate-900 text-white ring-slate-900' : 'bg-slate-50 text-slate-700 ring-slate-200'"
                            class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium ring-1 ring-inset transition-colors"
                        >
                            {{ $category->name }}
                        </button>
                    @empty
                        <p class="py-4 text-sm text-slate-500">Belum ada kategori produk.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 sm:max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    x-model="search" 
                    type="text" 
                    placeholder="Cari nama produk atau SKU..." 
                    class="block w-full rounded-md border-slate-300 pl-10 text-sm focus:ring-slate-500"
                >
            </div>
            <div class="text-xs font-medium text-slate-500">
                Menampilkan <span class="font-bold text-slate-900" x-text="filteredProducts.length"></span> dari {{ $products->count() }} produk
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            {{-- Desktop View --}}
            <table class="hidden min-w-full divide-y divide-slate-200 text-sm md:table">
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
                    <template x-for="product in filteredProducts" :key="product.id">
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900" x-text="product.name"></div>
                                <div class="text-xs text-slate-500" x-text="product.sku"></div>
                            </td>
                            <td class="px-4 py-3" x-text="product.category"></td>
                            <td class="px-4 py-3 font-medium text-slate-900" x-text="money(product.price)"></td>
                            <td class="px-4 py-3" :class="product.stock <= 10 ? 'font-bold text-amber-700' : 'text-slate-700'" x-text="product.stock + ' ' + product.unit"></td>
                            <td class="px-4 py-3">
                                <span 
                                    class="rounded px-2 py-0.5 text-xs font-semibold"
                                    :class="product.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800'"
                                    x-text="product.is_active ? 'Aktif' : 'Nonaktif'"
                                ></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a :href="product.edit_url" class="font-bold text-slate-700 underline">Edit</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Mobile View --}}
            <div class="grid grid-cols-1 divide-y divide-slate-100 md:hidden">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-bold text-slate-900" x-text="product.name"></div>
                                <div class="text-xs text-slate-500" x-text="product.sku + ' · ' + product.category"></div>
                            </div>
                            <span 
                                class="rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                :class="product.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800'"
                                x-text="product.is_active ? 'Aktif' : 'Nonaktif'"
                            ></span>
                        </div>
                        
                        <div class="flex items-center justify-between border-y border-slate-50 py-2">
                            <div>
                                <div class="text-[10px] uppercase text-slate-400 font-bold">Harga</div>
                                <div class="text-sm font-bold text-slate-900" x-text="money(product.price)"></div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] uppercase text-slate-400 font-bold">Stok</div>
                                <div class="text-sm font-bold" :class="product.stock <= 10 ? 'text-amber-700' : 'text-slate-900'" x-text="product.stock + ' ' + product.unit"></div>
                            </div>
                        </div>

                        <div class="pt-1">
                            <a :href="product.edit_url" class="flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm ring-1 ring-slate-200">EDIT PRODUK</a>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="filteredProducts.length === 0" class="p-12 text-center text-sm text-slate-500" x-cloak>
                Tidak ada produk yang cocok dengan pencarian Anda.
            </div>
        </div>
    </div>

    @php
        $productsData = $products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'price' => (float) $p->price,
            'stock' => $p->stock,
            'unit' => $p->unit,
            'is_active' => (bool) $p->is_active,
            'category' => $p->category->name,
            'edit_url' => route('products.edit', $p),
            'search_blob' => Str::lower($p->name.' '.$p->sku.' '.$p->category->name)
        ]);
    @endphp

    <script>
        function productManagement() {
            return {
                allProducts: @js($productsData),
                search: '',
                selectedCategory: '',
                
                get filteredProducts() {
                    const q = this.search.toLowerCase();
                    return this.allProducts.filter(p => {
                        const matchSearch = !q || p.search_blob.includes(q);
                        const matchCategory = !this.selectedCategory || p.category === this.selectedCategory;
                        return matchSearch && matchCategory;
                    });
                },

                money(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
                }
            }
        }
    </script>
</x-app-layout>
