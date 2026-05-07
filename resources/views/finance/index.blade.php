<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Keuangan</h1>
        <p class="text-sm text-slate-500">Catat pemasukan, pengeluaran, kategori, dan pantau saldo.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Total Pemasukan</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">Rp {{ number_format($income, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Total Pengeluaran</p>
                <p class="mt-2 text-2xl font-semibold text-red-700">Rp {{ number_format($expense, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Saldo</p>
                <p class="mt-2 text-2xl font-semibold {{ $balance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <form method="POST" action="{{ route('finance.store') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                @csrf
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold">Tambah Transaksi</h2>
                    <a href="{{ route('exports.finance') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold">Export Excel</a>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block text-sm font-medium">Tipe
                        <select name="type" class="mt-1 w-full rounded-md border-slate-300" required>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Kategori
                        <select name="category_id" class="mt-1 w-full rounded-md border-slate-300" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type }})</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Nominal
                        <input type="number" name="amount" class="mt-1 w-full rounded-md border-slate-300" min="1" step="0.01" required>
                    </label>
                    <label class="block text-sm font-medium">Tanggal
                        <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" class="mt-1 w-full rounded-md border-slate-300" required>
                    </label>
                    <label class="block text-sm font-medium md:col-span-2">Deskripsi
                        <input name="description" class="mt-1 w-full rounded-md border-slate-300" required>
                    </label>
                </div>
                <button class="mt-4 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Transaksi</button>
            </form>

            <form method="POST" action="{{ route('finance.categories.store') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                @csrf
                <h2 class="mb-4 font-semibold">Kategori Baru</h2>
                <label class="block text-sm font-medium">Nama
                    <input name="name" class="mt-1 w-full rounded-md border-slate-300" required>
                </label>
                <label class="mt-4 block text-sm font-medium">Tipe
                    <select name="type" class="mt-1 w-full rounded-md border-slate-300" required>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </label>
                <label class="mt-4 block text-sm font-medium">Deskripsi
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-slate-300"></textarea>
                </label>
                <button class="mt-4 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Kategori</button>
            </form>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="mb-4 font-semibold">Line Chart Bulanan</h2>
                <canvas id="financeLineChart" height="120"></canvas>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="mb-4 font-semibold">Pie Chart Kategori</h2>
                <canvas id="financePieChart" height="120"></canvas>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
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
                            <td class="px-4 py-3">{{ $transaction->transaction_date->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $transaction->category->name }}</td>
                            <td class="px-4 py-3">{{ $transaction->description }}</td>
                            <td class="px-4 py-3 font-semibold {{ $transaction->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('finance.destroy', $transaction) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">Belum ada transaksi keuangan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $transactions->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new window.Chart(document.getElementById('financeLineChart'), {
                type: 'line',
                data: {
                    labels: @json($monthlyChart['labels']),
                    datasets: [
                        { label: 'Pemasukan', data: @json($monthlyChart['income']), borderColor: '#047857', tension: .35 },
                        { label: 'Pengeluaran', data: @json($monthlyChart['expense']), borderColor: '#b91c1c', tension: .35 },
                    ],
                },
            });

            new window.Chart(document.getElementById('financePieChart'), {
                type: 'pie',
                data: {
                    labels: @json($categoryChart['labels']),
                    datasets: [{ data: @json($categoryChart['data']), backgroundColor: ['#0f766e', '#b91c1c', '#ca8a04', '#2563eb', '#7c3aed'] }],
                },
            });
        });
    </script>
</x-app-layout>
