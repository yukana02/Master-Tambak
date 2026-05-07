<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-950">Verifikasi email</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Klik link verifikasi yang sudah dikirim ke email kamu. Jika belum menerima, kirim ulang dari tombol di bawah.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            Link verifikasi baru sudah dikirim.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim Ulang Email
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Log out
            </button>
        </form>
    </div>
</x-guest-layout>
