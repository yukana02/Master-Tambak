<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tambak Rasyid') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <main class="min-h-screen bg-slate-100">
            <div class="grid min-h-screen lg:grid-cols-[1.05fr_.95fr]">
                <section class="relative hidden overflow-hidden bg-slate-950 text-white lg:flex lg:flex-col lg:justify-between">
                    <div class="absolute inset-0 opacity-25" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 72px 72px;"></div>
                    <div class="relative p-10">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded bg-emerald-500 text-lg font-bold text-slate-950">TR</span>
                            <span class="text-lg font-semibold">Tambak Rasyid</span>
                        </a>
                    </div>

                    <div class="relative px-10 pb-12">
                        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-300">Kolam Ikan An'nasrah</p>
                        <h1 class="mt-4 max-w-xl text-5xl font-bold leading-tight">Kelola kolam, stok, POS, dan laporan dalam satu dashboard.</h1>
                        <p class="mt-5 max-w-lg text-base leading-8 text-slate-300">Sistem internal untuk membantu operasional tambak lebih tertata dari pencatatan kolam sampai transaksi penjualan.</p>

                        <div class="mt-10 grid max-w-xl gap-3 sm:grid-cols-3">
                            <div class="rounded border border-white/10 bg-white/10 p-4">
                                <p class="text-2xl font-bold">3</p>
                                <p class="mt-1 text-sm text-slate-300">Role akses</p>
                            </div>
                            <div class="rounded border border-white/10 bg-white/10 p-4">
                                <p class="text-2xl font-bold">POS</p>
                                <p class="mt-1 text-sm text-slate-300">Transaksi kasir</p>
                            </div>
                            <div class="rounded border border-white/10 bg-white/10 p-4">
                                <p class="text-2xl font-bold">Excel</p>
                                <p class="mt-1 text-sm text-slate-300">Export laporan</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="flex min-h-screen flex-col justify-center px-4 py-8 sm:px-6 lg:px-12">
                    <div class="mx-auto w-full max-w-md">
                        <div class="mb-8 flex items-center justify-between">
                            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded bg-slate-900 text-sm font-bold text-white">TR</span>
                                <span>
                                    <span class="block font-semibold text-slate-950">Tambak Rasyid</span>
                                    <span class="block text-xs text-slate-500">Kolam Ikan An'nasrah</span>
                                </span>
                            </a>
                            <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-950">Beranda</a>
                        </div>

                        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
