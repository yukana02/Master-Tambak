<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">Edit Kolam</h1></x-slot>
    <form id="pond-form" method="POST" action="{{ route('ponds.update', $pond) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @method('PUT')
        @include('ponds._form')
    </form>
    <script>
        const form = document.getElementById('pond-form');
        let isDirty = false;

        form.addEventListener('input', () => {
            isDirty = true;
        });

        form.addEventListener('submit', () => {
            isDirty = false;
        });

        window.addEventListener('beforeunload', (e) => {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
    <form method="POST" action="{{ route('ponds.destroy', $pond) }}" class="mt-4" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kolam ini? Semua data terkait kolam akan ikut terhapus.');">
        @csrf @method('DELETE')
        <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white">Hapus Kolam</button>
    </form>
</x-app-layout>
