<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100">
            <style>[x-cloak] { display: none !important; }</style>
            <div class="flex min-h-screen">
                @include('layouts.sidebar')

                <div class="flex min-w-0 flex-1 flex-col">
                    <header class="border-b border-slate-200 bg-white">
                        <div class="flex min-h-16 items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
                            <div class="flex min-w-0 items-center gap-3">
                                <button type="button" @click="sidebarOpen = true" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md border border-slate-300 text-slate-700 hover:bg-slate-50 lg:hidden" aria-label="Buka menu">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div class="min-w-0">
                                    @isset($header)
                                        {{ $header }}
                                    @else
                                        <h1 class="text-xl font-semibold text-slate-900">AnasyrahFarm</h1>
                                    @endisset
                                </div>
                            </div>
                            <div class="shrink-0 text-right text-sm">
                                <div class="font-medium text-slate-900">{{ Auth::user()->name }}</div>
                                <div class="hidden text-slate-500 sm:block">{{ Auth::user()->getRoleNames()->join(', ') }}</div>
                            </div>
                        </div>
                    </header>

                    @if (session('success'))
                        <div class="mx-4 mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 sm:mx-6 lg:mx-8">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mx-6 lg:mx-8">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
