<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AnasyrahFarm</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-900 antialiased">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="text-base font-semibold text-slate-950 sm:text-lg">AnasyrahFarm</a>
                <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                    <a href="#profil" class="hover:text-slate-950">Profil</a>
                    <a href="#layanan" class="hover:text-slate-950">Layanan</a>
                    <a href="#lokasi" class="hover:text-slate-950">Lokasi</a>
                </nav>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Login</a>
                    @endauth
                @endif
            </div>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:px-8 lg:py-16">
                <div class="flex flex-col justify-center">
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Budidaya dan pengelolaan kolam ikan</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-bold leading-tight text-slate-950 sm:text-5xl lg:text-6xl">AnasyrahFarm</h1>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                        Area kolam ikan di kawasan Jombang, Jawa Timur, dengan fokus pada pengelolaan kolam, stok ikan, pencatatan transaksi, dan operasional penjualan yang tertata.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="https://www.google.com/maps/place/Kolam+Ikan+An'nasrah/@-7.4806063,112.2751623,17z/data=!3m1!4b1!4m6!3m5!1s0x2e781577d0bd626f:0x96c2e786d546818!8m2!3d-7.4806063!4d112.2777426!16s%2Fg%2F11fn23b1ft" target="_blank" rel="noopener" class="inline-flex justify-center rounded-md bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800">Buka Google Maps</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex justify-center rounded-md border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-white">Masuk Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex justify-center rounded-md border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-white">Login Pengelola</a>
                        @endauth
                    </div>
                </div>

                <div class="relative min-h-[420px] overflow-hidden rounded-lg border border-slate-200 bg-emerald-950 shadow-sm">
                    <iframe
                        title="Lokasi AnasyrahFarm"
                        src="https://www.google.com/maps?q=-7.4806063,112.2777426&z=16&output=embed"
                        class="h-full min-h-[420px] w-full"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    <div class="absolute bottom-4 left-4 right-4 rounded bg-white/95 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-slate-950">Lokasi dari Google Maps</p>
                        <p class="mt-1 text-sm text-slate-600">Koordinat: -7.4806063, 112.2777426</p>
                    </div>
                </div>
            </section>

            <section id="profil" class="border-y border-slate-200 bg-white">
                <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-3 lg:px-8">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Profil</p>
                        <h2 class="mt-3 text-2xl font-bold text-slate-950">Pengelolaan kolam yang mudah dipantau.</h2>
                    </div>
                    <div class="space-y-4 text-sm leading-7 text-slate-600 lg:col-span-2">
                        <p>
                            AnasyrahFarm dikelola sebagai area budidaya ikan dengan kebutuhan operasional yang lengkap: pendataan kolam, jadwal tebar dan panen, stok produk, transaksi POS, serta laporan keuangan.
                        </p>
                        <p>
                            Website ini juga terhubung dengan sistem internal Tambak Rasyid untuk membantu pengelola melihat kondisi kolam, mencatat pemasukan-pengeluaran, dan memproses penjualan secara lebih rapi.
                        </p>
                    </div>
                </div>
            </section>

            <section id="layanan" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Layanan</p>
                        <h2 class="mt-3 text-2xl font-bold text-slate-950">Fokus operasional kolam</h2>
                    </div>
                    <p class="max-w-xl text-sm leading-6 text-slate-600"></p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-semibold text-slate-950">Budidaya Ikan</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Pendataan jenis ikan, jumlah tebar, dan jadwal panen setiap kolam.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-semibold text-slate-950">Manajemen Kolam</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Peta kolam interaktif dengan posisi, ukuran, status, dan catatan operasional.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-semibold text-slate-950">Produk dan Stok</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Produk ikan, pakan, pupuk, dan alat dapat dikelola dari dashboard.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-semibold text-slate-950">POS dan Laporan</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Transaksi penjualan, struk, laporan keuangan, dan export Excel.</p>
                    </div>
                </div>
            </section>

            <section id="lokasi" class="bg-white">
                <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[.8fr_1.2fr] lg:px-8">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Lokasi</p>
                        <h2 class="mt-3 text-2xl font-bold text-slate-950">AnasyrahFarm</h2>
                        <dl class="mt-6 space-y-4 text-sm">
                            <div>
                                <dt class="font-semibold text-slate-950">Sumber data</dt>
                                <dd class="mt-1 text-slate-600">Google Maps share link</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-950">Koordinat</dt>
                                <dd class="mt-1 text-slate-600">-7.4806063, 112.2777426</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-950">Detail tambahan</dt>
                                <dd class="mt-1 text-slate-600"></dd>
                            </div>
                        </dl>
                    </div>

                    <iframe
                        title="Peta AnasyrahFarm"
                        src="https://www.google.com/maps?q=-7.4806063,112.2777426&z=17&output=embed"
                        class="h-[360px] w-full rounded-lg border border-slate-200"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-slate-950 text-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-6 text-sm sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p class="font-semibold">AnasyrahFarm</p>
                <p class="text-slate-300">Company profile dan sistem operasional Tambak Rasyid.</p>
            </div>
        </footer>
    </body>
</html>
