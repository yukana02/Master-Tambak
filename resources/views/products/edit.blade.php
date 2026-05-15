<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">Edit Produk</h1></x-slot>
    <form method="POST" action="{{ route('products.update', $product) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @method('PUT')
        @include('products._form')
    </form>
    <form method="POST" action="{{ route('products.destroy', $product) }}" class="mt-4" id="deleteForm">
        @csrf @method('DELETE')
        <button type="button" onclick="confirmDelete('{{ $product->name }}')" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Hapus Produk</button>
    </form>

    <script>
        function confirmDelete(productName) {
            if (confirm(`⚠️ PERINGATAN!\n\nAnda akan menghapus produk:\n"${productName}"\n\nTindakan ini TIDAK DAPAT DIBATALKAN.\n- Produk akan dihapus secara permanen\n- Semua data penjualan produk ini juga akan dihapus\n\nApakah Anda yakin?"`)) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</x-app-layout>
