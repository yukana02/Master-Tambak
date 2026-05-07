<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Struk Penjualan</h1>
        <p class="text-sm text-slate-500">{{ $sale->invoice_number }}</p>
    </x-slot>

    <div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 pb-4 text-center">
            <h2 class="text-lg font-semibold">Tambak Rasyid</h2>
            <p class="text-sm text-slate-500">{{ $sale->sold_at->format('d M Y H:i') }}</p>
            <p class="text-sm text-slate-500">{{ $sale->invoice_number }}</p>
        </div>

        <div class="divide-y divide-slate-100 py-4">
            @foreach ($sale->items as $item)
                <div class="flex justify-between gap-4 py-3 text-sm">
                    <div>
                        <div class="font-semibold">{{ $item->product_name }}</div>
                        <div class="text-slate-500">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    </div>
                    <div class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>

        <div class="space-y-2 border-t border-slate-200 pt-4 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><strong>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</strong></div>
            <div class="flex justify-between"><span>Diskon</span><strong>Rp {{ number_format($sale->discount, 0, ',', '.') }}</strong></div>
            <div class="flex justify-between text-base"><span>Total</span><strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></div>
            <div class="flex justify-between"><span>Cash</span><strong>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</strong></div>
            <div class="flex justify-between"><span>Kembalian</span><strong>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</strong></div>
        </div>

        <div class="mt-6 flex gap-3 print:hidden">
            <button onclick="window.print()" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Print</button>
            <a href="{{ route('pos.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Transaksi Baru</a>
        </div>
    </div>
</x-app-layout>
