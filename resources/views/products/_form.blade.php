@csrf
<div class="grid gap-4 md:grid-cols-2">
    <label class="block text-sm font-medium">Kategori
        <select name="product_category_id" class="mt-1 w-full rounded-md border-slate-300" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('product_category_id', $product->product_category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm font-medium">Nama Produk
        <input name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full rounded-md border-slate-300" required>
    </label>
    <label class="block text-sm font-medium">SKU
        <input name="sku" value="{{ old('sku', $product->sku) }}" class="mt-1 w-full rounded-md border-slate-300" required>
    </label>
    <label class="block text-sm font-medium">Harga
        <input type="number" name="price" value="{{ old('price', $product->price) }}" class="mt-1 w-full rounded-md border-slate-300" min="0" step="0.01" required>
    </label>
    <label class="block text-sm font-medium">Stok
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="mt-1 w-full rounded-md border-slate-300" min="0" required>
    </label>
    <label class="block text-sm font-medium">Satuan
        <input name="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}" class="mt-1 w-full rounded-md border-slate-300" required>
    </label>
    <label class="flex items-center gap-2 text-sm font-medium md:col-span-2">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $product->is_active ?? true))>
        Aktif di POS
    </label>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
    <a href="{{ route('products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Batal</a>
</div>
