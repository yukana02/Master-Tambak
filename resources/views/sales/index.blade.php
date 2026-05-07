<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Laporan Penjualan</h1>
        <p class="text-sm text-slate-500">Pantau transaksi POS, omzet, diskon, dan akses struk penjualan.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Total Omzet</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Total Diskon</p>
                <p class="mt-2 text-2xl font-semibold text-amber-700">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Item Terjual</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($totalItems, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Riwayat Transaksi</h2>
                <a href="{{ route('exports.sales') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Export Excel</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Subtotal</th>
                            <th class="px-4 py-3">Diskon</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Cash</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $sale->sold_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $sale->invoice_number }}</td>
                                <td class="px-4 py-3">{{ number_format($sale->items_sum_qty ?? 0, 0, ',', '.') }} item</td>
                                <td class="px-4 py-3 whitespace-nowrap">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap font-semibold text-emerald-700">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('pos.receipt', $sale) }}" class="font-semibold text-slate-700 underline">Struk</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">Belum ada transaksi penjualan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $sales->links() }}
    </div>
</x-app-layout>
