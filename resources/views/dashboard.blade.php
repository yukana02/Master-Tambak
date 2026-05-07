<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Dashboard</h1>
        <p class="text-sm text-slate-500">Ringkasan operasional tambak, keuangan, dan penjualan.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Kolam</p>
                <p class="mt-2 text-3xl font-semibold">{{ $pondCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Produk</p>
                <p class="mt-2 text-3xl font-semibold">{{ $productCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Stok Rendah</p>
                <p class="mt-2 text-3xl font-semibold text-amber-600">{{ $lowStockCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Transaksi POS</p>
                <p class="mt-2 text-3xl font-semibold">{{ $saleCount }}</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Pemasukan</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">Rp {{ number_format($income, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Pengeluaran</p>
                <p class="mt-2 text-2xl font-semibold text-red-700">Rp {{ number_format($expense, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Saldo</p>
                <p class="mt-2 text-2xl font-semibold {{ $balance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Grafik Bulanan</h2>
            </div>
            <canvas id="monthlyChart" height="90"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new window.Chart(document.getElementById('monthlyChart'), {
                type: 'line',
                data: {
                    labels: @json($monthlyChart['labels']),
                    datasets: [
                        { label: 'Pemasukan', data: @json($monthlyChart['income']), borderColor: '#047857', tension: .35 },
                        { label: 'Pengeluaran', data: @json($monthlyChart['expense']), borderColor: '#b91c1c', tension: .35 },
                    ],
                },
            });
        });
    </script>
</x-app-layout>
