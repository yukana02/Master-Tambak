<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Point of Sale</h1>
        <p class="text-sm text-slate-500">Tambah produk ke cart, validasi stok, dan simpan transaksi cash.</p>
    </x-slot>

    <div x-data="posPage()" class="relative">
        {{-- Mobile Floating Cart Button --}}
        <div class="fixed bottom-6 right-6 z-50 lg:hidden">
            <button @click="cartOpen = true" class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg ring-4 ring-white transition-transform active:scale-95">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span x-show="cart.length > 0" x-text="cart.length" class="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold"></span>
            </button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_400px]">
            <section class="space-y-4">
                <div class="flex flex-col gap-3 rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:flex-row">
                    <input 
                        x-model="search" 
                        placeholder="Cari produk atau SKU..." 
                        class="w-full flex-1 rounded-md border-slate-300 sm:min-w-64 focus:ring-slate-500"
                    >
                    <div class="flex gap-2">
                        <select x-model="selectedCategory" class="flex-1 rounded-md border-slate-300 text-sm focus:ring-slate-500">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                    @php
                        $productsData = $products->map(fn($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'sku' => $p->sku,
                            'price' => (float) $p->price,
                            'stock' => $p->stock,
                            'unit' => $p->unit,
                            'category' => $p->category->name,
                            'search_blob' => Str::lower($p->name.' '.$p->sku.' '.$p->category->name)
                        ]);
                    @endphp
                    
                    <template x-for="product in filteredProducts" :key="product.id">
                        <button 
                            type="button" 
                            x-on:click="addProduct(product)" 
                            class="rounded-lg bg-white p-4 text-left shadow-sm ring-1 ring-slate-200 transition hover:ring-slate-400"
                        >
                            <div class="flex justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-slate-950" x-text="product.name"></div>
                                    <div class="text-sm text-slate-500" x-text="product.category + ' · ' + product.sku"></div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold" x-text="money(product.price)"></div>
                                    <div class="text-sm" :class="product.stock <= 10 ? 'text-amber-700' : 'text-slate-500'" x-text="'Stok ' + product.stock + ' ' + product.unit"></div>
                                </div>
                            </div>
                        </button>
                    </template>

                    <div x-show="filteredProducts.length === 0" class="rounded-lg bg-white p-12 text-center text-slate-500 shadow-sm ring-1 ring-slate-200 md:col-span-2 2xl:col-span-3" x-cloak>
                        Tidak ada produk yang cocok dengan pencarian Anda.
                    </div>
                </div>
            </section>

            {{-- Cart Sidebar / Slide-over --}}
            <aside 
                :class="cartOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 right-0 z-[60] flex w-full max-w-[400px] flex-col bg-white p-0 shadow-2xl transition-transform duration-300 lg:static lg:z-auto lg:h-auto lg:translate-x-0 lg:rounded-lg lg:p-5 lg:shadow-sm lg:ring-1 lg:ring-slate-200"
            >
                {{-- Mobile Cart Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 p-5 lg:hidden">
                    <h2 class="text-lg font-bold">Detail Pesanan</h2>
                    <button @click="cartOpen = false" class="rounded-md p-2 text-slate-500 hover:bg-slate-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex flex-1 flex-col overflow-y-auto p-5 lg:p-0 lg:block">
                    <h2 class="hidden font-semibold lg:block">Cart</h2>
                    <form method="POST" action="{{ route('pos.store') }}" class="mt-4 flex flex-1 flex-col lg:block">
                        @csrf
                        <div class="flex-1 space-y-4">
                            <template x-for="(item, index) in cart" :key="item.id">
                                <div class="rounded-md border border-slate-200 p-3">
                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                                    <input type="hidden" :name="`items[${index}][qty]`" :value="item.qty">
                                    <div class="flex justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-slate-900" x-text="item.name"></div>
                                            <div class="text-xs text-slate-500" x-text="money(item.price) + ' · stok ' + item.stock"></div>
                                        </div>
                                        <button type="button" x-on:click="removeItem(item.id)" class="text-xs font-bold text-red-600 uppercase tracking-wider">Hapus</button>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center gap-1">
                                            <button type="button" x-on:click="decrease(item.id)" class="h-9 w-9 rounded-md bg-slate-100 text-lg font-bold">-</button>
                                            <input type="number" x-model.number="item.qty" min="1" :max="item.stock" class="h-9 w-16 rounded-md border-slate-300 p-0 text-center text-sm focus:ring-slate-500">
                                            <button type="button" x-on:click="increase(item.id)" class="h-9 w-9 rounded-md bg-slate-100 text-lg font-bold">+</button>
                                        </div>
                                        <div class="font-bold text-slate-900" x-text="money(item.qty * item.price)"></div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="cart.length === 0" class="rounded-md border border-dashed border-slate-300 py-16 text-center text-sm text-slate-500">
                                Cart masih kosong.
                            </div>
                        </div>

                        <div class="mt-8 space-y-4 border-t border-slate-200 pt-6">
                            <div class="grid grid-cols-2 gap-4">
                                <label class="block text-sm font-medium text-slate-600">Diskon (Rp)
                                    <input type="number" name="discount" x-model.number="discount" min="0" step="1" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" placeholder="0">
                                </label>
                                <label class="block text-sm font-medium text-slate-600">Diskon (%)
                                    <input type="number" x-model.number="discountPercent" min="0" max="100" step="0.1" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" placeholder="0">
                                </label>
                            </div>
                            <label class="block text-sm font-medium text-slate-600">Bayar Cash
                                <input type="number" name="paid_amount" x-model.number="paidAmount" min="0" step="1" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" required>
                            </label>
                            <input type="hidden" name="payment_method" value="cash">

                            <div class="rounded-lg bg-slate-50 p-4 space-y-2 text-sm">
                                <div class="flex justify-between"><span>Subtotal</span><span class="font-medium text-slate-900" x-text="money(subtotal())"></span></div>
                                <div class="flex justify-between text-amber-700">
                                    <span>Total Diskon</span>
                                    <span class="font-medium" x-text="'- ' + money(totalDiscount())"></span>
                                </div>
                                <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold text-slate-900">
                                    <span>Total</span><span x-text="money(total())"></span>
                                </div>
                                <div class="flex justify-between text-emerald-700 font-bold pt-1">
                                    <span>Kembalian</span><span x-text="money(change())"></span>
                                </div>
                            </div>

                            <button :disabled="cart.length === 0 || paidAmount < total()" class="w-full rounded-md bg-slate-900 px-4 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-300">
                                SIMPAN TRANSAKSI
                            </button>
                        </div>
                    </form>
                </div>
            </aside>
            {{-- Mobile Backdrop --}}
            <div x-show="cartOpen" @click="cartOpen = false" x-cloak class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm lg:hidden"></div>
        </div>
    </div>

    <script>
        function posPage() {
            return {
                allProducts: @js($productsData),
                search: '',
                selectedCategory: '',
                cart: [],
                discount: 0,
                discountPercent: 0,
                paidAmount: 0,
                cartOpen: false,
                
                get filteredProducts() {
                    const q = this.search.toLowerCase();
                    return this.allProducts.filter(p => {
                        const matchSearch = !q || p.search_blob.includes(q);
                        const matchCategory = !this.selectedCategory || p.category === this.selectedCategory;
                        return matchSearch && matchCategory;
                    });
                },

                addProduct(product) {
                    const existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        this.increase(product.id);
                        return;
                    }
                    if (product.stock > 0) {
                        this.cart.push({ ...product, qty: 1 });
                        // Optional: auto open cart on first item for mobile
                        if (this.cart.length === 1 && window.innerWidth < 1024) {
                            // this.cartOpen = true; 
                        }
                    }
                },
                increase(id) {
                    const item = this.cart.find(item => item.id === id);
                    if (item && item.qty < item.stock) item.qty++;
                },
                decrease(id) {
                    const item = this.cart.find(item => item.id === id);
                    if (item && item.qty > 1) item.qty--;
                },
                removeItem(id) {
                    this.cart = this.cart.filter(item => item.id !== id);
                },
                subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.qty * item.price), 0);
                },
                totalDiscount() {
                    const fromCash = Number(this.discount) || 0;
                    const fromPercent = (this.subtotal() * (Number(this.discountPercent) || 0)) / 100;
                    return fromCash + fromPercent;
                },
                total() {
                    return Math.max(this.subtotal() - this.totalDiscount(), 0);
                },
                change() {
                    return Math.max((Number(this.paidAmount) || 0) - this.total(), 0);
                },
                money(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
                },
            };
        }
    </script>
</x-app-layout>
