<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Keuangan</h1>
        <p class="text-sm text-slate-500">Catat pemasukan, pengeluaran, kategori, dan pantau saldo.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg bg-white p-4 sm:p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">Total Pemasukan</p>
                <p class="mt-2 text-xl sm:text-2xl font-extrabold text-emerald-700">Rp {{ number_format($income, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 sm:p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">Total Pengeluaran</p>
                <p class="mt-2 text-xl sm:text-2xl font-extrabold text-red-700">Rp {{ number_format($expense, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 sm:p-5 shadow-sm ring-1 ring-slate-200 sm:col-span-2 lg:col-span-1">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">Saldo Saat Ini</p>
                <p class="mt-2 text-xl sm:text-2xl font-extrabold {{ $balance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <form method="POST" action="{{ route('finance.store') }}" class="rounded-lg bg-white p-4 sm:p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                @csrf
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="font-semibold text-slate-900">Tambah Transaksi</h2>
                    <a href="{{ route('exports.finance') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 uppercase tracking-wider">EXPORT EXCEL</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium text-slate-700">Tipe
                        <select name="type" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" required>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">Kategori
                        <select name="category_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type }})</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">Nominal
                        <input type="number" name="amount" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" min="1" step="0.01" required>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">Tanggal
                        <input type="text" name="transaction_date" value="{{ now()->format('d/m/Y') }}" class="datepicker mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" required>
                    </label>
                    <label class="block text-sm font-medium text-slate-700 sm:col-span-2">Deskripsi
                        <input name="description" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" required>
                    </label>
                </div>
                <button class="mt-5 w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 sm:w-auto">SIMPAN TRANSAKSI</button>
            </form>

            <form method="POST" action="{{ route('finance.categories.store') }}" class="rounded-lg bg-white p-4 sm:p-5 shadow-sm ring-1 ring-slate-200">
                @csrf
                <h2 class="mb-4 font-semibold text-slate-900">Kategori Baru</h2>
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700">Nama
                        <input name="name" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" required>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">Tipe
                        <select name="type" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500" required>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">Deskripsi
                        <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:ring-slate-500"></textarea>
                    </label>
                </div>
                <button class="mt-5 w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800">TAMBAH KATEGORI</button>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <h2 class="mb-4 font-semibold text-slate-900">Tren Bulanan</h2>
                <div class="relative h-[200px] md:h-[300px] w-full">
                    <canvas id="financeLineChart"></canvas>
                </div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <h2 class="mb-4 font-semibold text-slate-900">Proporsi Kategori</h2>
                <div class="relative h-[200px] md:h-[300px] w-full">
                    <canvas id="financePieChart"></canvas>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            {{-- Desktop View --}}
            <table class="hidden min-w-full divide-y divide-slate-200 text-sm md:table">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">Nominal</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $transaction->category->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $transaction->description }}</td>
                            <td class="px-4 py-3 whitespace-nowrap font-bold {{ $transaction->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('finance.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 font-bold underline text-xs uppercase tracking-wider">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">Belum ada transaksi keuangan.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Mobile View --}}
            <div class="grid grid-cols-1 divide-y divide-slate-100 md:hidden">
                @forelse ($transactions as $transaction)
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $transaction->category->name }}</div>
                                <div class="font-bold text-slate-950 text-base mt-0.5">{{ $transaction->description }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-extrabold {{ $transaction->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </div>
                                <div class="text-xs font-medium text-slate-500">{{ $transaction->transaction_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        <div class="pt-1">
                            <form method="POST" action="{{ route('finance.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button class="flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-xs font-bold text-red-600 shadow-sm ring-1 ring-slate-200 uppercase tracking-wider">Hapus Transaksi</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-sm text-slate-500">Belum ada transaksi keuangan.</div>
                @endforelse
            </div>
        </div>
        <div class="mt-2">
            {{ $transactions->links() }}
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            flatpickr.setDefaults({ dateFormat: "d/m/Y", locale: "id", allowInput: true });
            flatpickr('.datepicker');

            const lineCtx = document.getElementById('financeLineChart').getContext('2d');
            new window.Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: @json($monthlyChart['labels']),
                    datasets: [
                        { label: 'Pemasukan', data: @json($monthlyChart['income']), borderColor: '#047857', backgroundColor: '#04785722', fill: true, tension: .4 },
                        { label: 'Pengeluaran', data: @json($monthlyChart['expense']), borderColor: '#b91c1c', backgroundColor: '#b91c1c22', fill: true, tension: .4 },
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

            const pieCtx = document.getElementById('financePieChart').getContext('2d');
            new window.Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: @json($categoryChart['labels']),
                    datasets: [{ 
                        data: @json($categoryChart['data']), 
                        backgroundColor: ['#0f766e', '#b91c1c', '#ca8a04', '#2563eb', '#7c3aed', '#db2777', '#4b5563'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                padding: 10,
                                font: { size: window.innerWidth < 640 ? 10 : 12 }
                            }
                        } 
                    }
                }
            });
        });
    </script>
</x-app-layout>
