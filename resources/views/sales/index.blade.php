<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Laporan Penjualan</h1>
        <p class="text-sm text-slate-500">Pantau transaksi POS, omzet, diskon, dan akses struk penjualan.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Omzet</p>
                <p class="mt-2 text-xl font-bold text-emerald-700 sm:text-2xl">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Diskon</p>
                <p class="mt-2 text-xl font-bold text-amber-700 sm:text-2xl">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:col-span-2 lg:col-span-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Item Terjual</p>
                <p class="mt-2 text-xl font-bold text-slate-900 sm:text-2xl">{{ number_format($totalItems, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-semibold text-slate-900">Riwayat Transaksi</h2>
                <a href="{{ route('exports.sales') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">EXPORT EXCEL</a>
            </div>

            <div class="overflow-x-auto">
                {{-- Desktop View --}}
                <table class="hidden min-w-full divide-y divide-slate-200 text-sm md:table">
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
                                <td class="px-4 py-3 whitespace-nowrap">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $sale->invoice_number }}</td>
                                <td class="px-4 py-3">{{ number_format($sale->items_sum_qty ?? 0, 0, ',', '.') }} item</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-600">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-amber-700">Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap font-bold text-emerald-700">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-600">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-xs">
                                    <a href="{{ route('pos.receipt', $sale) }}" class="font-bold text-slate-700 underline uppercase tracking-wider">Struk</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">Belum ada transaksi penjualan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Mobile View --}}
                <div class="grid grid-cols-1 divide-y divide-slate-100 md:hidden">
                    @forelse ($sales as $sale)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-bold text-slate-900">{{ $sale->invoice_number }}</div>
                                    <div class="text-xs text-slate-500">{{ $sale->sold_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-emerald-700">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-500 uppercase tracking-wide">{{ number_format($sale->items_sum_qty ?? 0, 0, ',', '.') }} item</div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 border-y border-slate-50 py-2 text-sm">
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-bold">Subtotal</div>
                                    <div class="font-medium text-slate-700">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400 font-bold">Diskon / Cash</div>
                                    <div class="font-medium text-slate-700">Rp {{ number_format($sale->discount, 0, ',', '.') }} / {{ number_format($sale->paid_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="pt-1">
                                <a href="{{ route('pos.receipt', $sale) }}" class="flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm ring-1 ring-slate-200 uppercase tracking-wider">Lihat Struk</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-sm text-slate-500">Belum ada transaksi penjualan.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{ $sales->links() }}
    </div>
</x-app-layout>
