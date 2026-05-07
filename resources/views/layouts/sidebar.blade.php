@php
    $navLinks = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'roles' => null],
        ['label' => 'Kolam', 'route' => 'ponds.index', 'active' => 'ponds.*', 'roles' => 'Super Admin|Admin'],
//        ['label' => 'Keuangan', 'route' => 'finance.index', 'active' => 'finance.*', 'roles' => 'Super Admin|Admin'],
//        ['label' => 'Produk', 'route' => 'products.index', 'active' => 'products.*', 'roles' => 'Super Admin'],
//        ['label' => 'Penjualan', 'route' => 'sales.index', 'active' => 'sales.*', 'roles' => 'Super Admin'],
//        ['label' => 'Role', 'route' => 'roles.index', 'active' => 'roles.*', 'roles' => 'Super Admin'],
//        ['label' => 'POS', 'route' => 'pos.index', 'active' => 'pos.*', 'roles' => 'Super Admin|Kasir'],
    ];
@endphp

<aside class="hidden h-screen w-64 shrink-0 flex-col border-r border-slate-200 bg-white lg:sticky lg:top-0 lg:flex">
    <div class="flex h-16 items-center border-b border-slate-200 px-6">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-slate-950">Tambak Rasyid</a>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto p-4 text-sm font-medium">
        @foreach ($navLinks as $link)
            @if ($link['roles'] === null || Auth::user()->hasAnyRole(explode('|', $link['roles'])))
                <a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['active']) ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2">{{ $link['label'] }}</a>
            @endif
        @endforeach
    </nav>

    <div class="border-t border-slate-200 bg-white p-4">
        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2 text-sm font-medium">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="mt-1 w-full rounded-md px-3 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-100">Log out</button>
        </form>
    </div>
</aside>

<div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" aria-modal="true" role="dialog">
    <button type="button" @click="sidebarOpen = false" class="absolute inset-0 bg-slate-950/40" aria-label="Tutup menu"></button>

    <aside
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="relative flex h-full w-72 max-w-[85vw] flex-col bg-white shadow-xl"
    >
        <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4">
            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="font-semibold text-slate-950">Tambak Rasyid</a>
            <button type="button" @click="sidebarOpen = false" class="inline-flex h-10 w-10 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100" aria-label="Tutup menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto p-4 text-sm font-medium">
            @foreach ($navLinks as $link)
                @if ($link['roles'] === null || Auth::user()->hasAnyRole(explode('|', $link['roles'])))
                    <a href="{{ route($link['route']) }}" @click="sidebarOpen = false" class="{{ request()->routeIs($link['active']) ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2">{{ $link['label'] }}</a>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-slate-200 bg-white p-4">
            <div class="mb-3 px-3 text-sm">
                <div class="font-medium text-slate-900">{{ Auth::user()->name }}</div>
                <div class="text-slate-500">{{ Auth::user()->getRoleNames()->join(', ') }}</div>
            </div>
            <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('profile.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2 text-sm font-medium">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="mt-1 w-full rounded-md px-3 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-100">Log out</button>
            </form>
        </div>
    </aside>
</div>
