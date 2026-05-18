<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Kategori Pakan</h1>
        <p class="text-sm text-slate-500">Kelola kategori untuk mengelompokkan jenis pakan.</p>
    </x-slot>

    <div class="mb-4 flex items-center justify-between gap-3">
        <a href="{{ route('feeds.index') }}" class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Pakan
        </a>
    </div>

    <div class="mb-6 rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="mb-4 text-base font-semibold text-slate-900">Tambah Kategori Baru</h2>
        <form method="POST" action="{{ route('feed-categories.store') }}" class="flex gap-3">
            @csrf
            <input name="name" class="w-full rounded-md border-slate-300 text-sm" placeholder="Nama kategori (contoh: Pelet, Cacing, dll)" required>
            <button type="submit" class="shrink-0 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Tambah</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama Kategori</th>
                    <th class="px-4 py-3">Jumlah Pakan</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $category->feeds_count }} pakan</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    onclick="document.getElementById('edit-modal-{{ $category->id }}').style.display = 'flex'"
                                    class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800"
                                    title="Edit"
                                >
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('feed-categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded p-1 text-red-500 hover:bg-red-50" title="Hapus">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-12 text-center text-slate-500">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    @foreach ($categories as $category)
        <div id="edit-modal-{{ $category->id }}" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40" onclick="document.getElementById('edit-modal-{{ $category->id }}').style.display = 'none'"></div>
            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Edit Kategori</h3>
                <form method="POST" action="{{ route('feed-categories.update', $category) }}">
                    @csrf @method('PUT')
                    <div class="flex gap-2">
                        <input name="name" value="{{ $category->name }}" class="w-full rounded-md border-slate-300 text-sm" required>
                        <button type="submit" class="shrink-0 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
                        <button type="button" onclick="document.getElementById('edit-modal-{{ $category->id }}').style.display = 'none'" class="shrink-0 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-app-layout>
