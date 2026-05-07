<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Point of Sale</h1>
        <p class="text-sm text-slate-500">Tambah produk ke cart, validasi stok, dan simpan transaksi cash.</p>
    </x-slot>

    <div x-data="posPage()" class="grid gap-6 xl:grid-cols-[1fr_420px]">
        <section class="space-y-4">
            <form method="GET" class="flex flex-wrap gap-3 rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <input name="search" value="{{ request('search') }}" placeholder="Cari produk atau SKU" class="min-w-64 flex-1 rounded-md border-slate-300">
                <select name="category" class="rounded-md border-slate-300">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
            </form>

            <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                @forelse ($products as $product)
                    <button type="button" x-on:click="addProduct(@js([
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'stock' => $product->stock,
                        'unit' => $product->unit,
                        'category' => $product->category->name,
                    ]))" class="rounded-lg bg-white p-4 text-left shadow-sm ring-1 ring-slate-200 transition hover:ring-slate-400">
                        <div class="flex justify-between gap-3">
                            <div>
                                <div class="font-semibold text-slate-950">{{ $product->name }}</div>
                                <div class="text-sm text-slate-500">{{ $product->category->name }} · {{ $product->sku }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                <div class="text-sm {{ $product->stock <= 10 ? 'text-amber-700' : 'text-slate-500' }}">Stok {{ $product->stock }} {{ $product->unit }}</div>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="rounded-lg bg-white p-12 text-center text-slate-500 shadow-sm ring-1 ring-slate-200 md:col-span-2 2xl:col-span-3">
                        Tidak ada produk aktif untuk filter ini.
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="font-semibold">Cart</h2>
            <form method="POST" action="{{ route('pos.store') }}" class="mt-4 space-y-4">
                @csrf
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="rounded-md border border-slate-200 p-3">
                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${index}][qty]`" :value="item.qty">
                        <div class="flex justify-between gap-3">
                            <div>
                                <div class="font-semibold" x-text="item.name"></div>
                                <div class="text-sm text-slate-500" x-text="money(item.price) + ' · stok ' + item.stock + ' ' + item.unit"></div>
                            </div>
                            <button type="button" x-on:click="removeItem(item.id)" class="text-sm font-semibold text-red-700">Hapus</button>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <button type="button" x-on:click="decrease(item.id)" class="h-8 w-8 rounded-md border border-slate-300">-</button>
                            <input type="number" x-model.number="item.qty" min="1" :max="item.stock" class="h-8 w-20 rounded-md border-slate-300 text-center">
                            <button type="button" x-on:click="increase(item.id)" class="h-8 w-8 rounded-md border border-slate-300">+</button>
                            <div class="ml-auto font-semibold" x-text="money(item.qty * item.price)"></div>
                        </div>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="rounded-md border border-dashed border-slate-300 py-10 text-center text-sm text-slate-500">
                    Cart masih kosong.
                </div>

                <label class="block text-sm font-medium">Diskon
                    <input type="number" name="discount" x-model.number="discount" min="0" step="0.01" class="mt-1 w-full rounded-md border-slate-300">
                </label>
                <label class="block text-sm font-medium">Cash
                    <input type="number" name="paid_amount" x-model.number="paidAmount" min="0" step="0.01" class="mt-1 w-full rounded-md border-slate-300" required>
                </label>
                <input type="hidden" name="payment_method" value="cash">

                <div class="space-y-2 border-t border-slate-200 pt-4 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><strong x-text="money(subtotal())"></strong></div>
                    <div class="flex justify-between"><span>Diskon</span><strong x-text="money(discount || 0)"></strong></div>
                    <div class="flex justify-between text-base"><span>Total</span><strong x-text="money(total())"></strong></div>
                    <div class="flex justify-between"><span>Kembalian</span><strong x-text="money(change())"></strong></div>
                </div>

                <button :disabled="cart.length === 0 || paidAmount < total()" class="w-full rounded-md bg-slate-900 px-4 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:bg-slate-400">
                    Simpan Transaksi
                </button>
            </form>
        </aside>
    </div>

    <script>
        function posPage() {
            return {
                cart: [],
                discount: 0,
                paidAmount: 0,
                addProduct(product) {
                    const existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        this.increase(product.id);
                        return;
                    }
                    if (product.stock > 0) this.cart.push({ ...product, qty: 1 });
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
                total() {
                    return Math.max(this.subtotal() - (Number(this.discount) || 0), 0);
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
