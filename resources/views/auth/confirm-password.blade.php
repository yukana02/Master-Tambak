<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-950">Konfirmasi password</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Area ini membutuhkan verifikasi ulang sebelum melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                Konfirmasi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
