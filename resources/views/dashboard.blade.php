<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Dashboard</h1>
        <p class="text-sm text-slate-500">Ringkasan operasional tambak, keuangan, dan penjualan.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kolam</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $pondCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Produk</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $productCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Stok Rendah</p>
                <p class="mt-2 text-2xl font-extrabold text-amber-600 sm:text-3xl">{{ $lowStockCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaksi POS</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $saleCount }}</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pemasukan</p>
                <p class="mt-1 text-lg font-extrabold text-emerald-700 sm:text-2xl">Rp {{ number_format($income, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengeluaran</p>
                <p class="mt-1 text-lg font-extrabold text-red-700 sm:text-2xl">Rp {{ number_format($expense, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5 sm:col-span-2 lg:col-span-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Saldo</p>
                <p class="mt-1 text-lg font-extrabold sm:text-2xl {{ $balance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
            <div class="mb-4">
                <h2 class="font-bold text-slate-900 uppercase text-xs tracking-widest">Grafik Bulanan</h2>
            </div>
            <div class="relative h-[250px] md:h-[350px] w-full">
                <canvas id="monthlyChart"></canvas>
            </div>
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
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: { size: window.innerWidth < 640 ? 10 : 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
