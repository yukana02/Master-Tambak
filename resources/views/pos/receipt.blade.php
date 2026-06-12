<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Struk Penjualan</h1>
        <p class="text-sm text-slate-500">{{ $sale->invoice_number }}</p>
    </x-slot>

    <div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 pb-4 text-center">
            <h2 class="text-lg font-semibold">AnasyrahFarm</h2>
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
            <button onclick="sendToWhatsApp()" class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                <svg class="inline-block h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Kirim ke WA
            </button>
            <a href="{{ route('pos.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Transaksi Baru</a>
        </div>
    </div>

    <script>
    const saleData = {
        date: '{{ $sale->sold_at->format("d M Y H:i") }}',
        invoice: '{{ $sale->invoice_number }}',
        subtotal: {{ $sale->subtotal }},
        discount: {{ $sale->discount }},
        total: {{ $sale->total }},
        paid: {{ $sale->paid_amount }},
        change: {{ $sale->change_amount }},
        items: [
            @foreach ($sale->items as $item)
                {
                    'name': '{{ $item->product_name }}',
                    'qty': {{ $item->qty }},
                    'price': {{ $item->price }},
                    'subtotal': {{ $item->subtotal }}
                },
            @endforeach
        ]
    };

    function sendToWhatsApp() {
        let message = `*AnasyrahFarm*\n`;
        message += `${saleData.date}\n`;
        message += `No: ${saleData.invoice}\n\n`;

        saleData.items.forEach(item => {
            message += `${item.name}\n`;
            message += `${item.qty} x Rp ${Number(item.price).toLocaleString('id-ID')}\n`;
            message += `Rp ${Number(item.subtotal).toLocaleString('id-ID')}\n\n`;
        });

        message += `──────────────\n`;
        message += `Subtotal: Rp ${Number(saleData.subtotal).toLocaleString('id-ID')}\n`;
        message += `Diskon: Rp ${Number(saleData.discount).toLocaleString('id-ID')}\n`;
        message += `*Total: Rp ${Number(saleData.total).toLocaleString('id-ID')}*\n`;
        message += `Cash: Rp ${Number(saleData.paid).toLocaleString('id-ID')}\n`;
        message += `Kembalian: Rp ${Number(saleData.change).toLocaleString('id-ID')}\n\n`;
        message += `Terima kasih!`;

        const encoded = encodeURIComponent(message);
        window.open(`https://wa.me/?text=${encoded}`, '_blank');
    }
    </script>
</x-app-layout>
