<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Role & Permission</h1>
        <p class="text-sm text-slate-500">Kelola akses Super Admin, Admin, dan Kasir.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="mb-4 font-semibold">Assign Role User</h2>
            <form method="POST" action="{{ route('roles.assign') }}" class="grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                @csrf
                <label class="block text-sm font-medium">User
                    <select name="user_id" class="mt-1 w-full rounded-md border-slate-300" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} · {{ $user->email }} · {{ $user->getRoleNames()->join(', ') ?: 'Tanpa role' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium">Role
                    <select name="role" class="mt-1 w-full rounded-md border-slate-300" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex items-end">
                    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
                </div>
            </form>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            @foreach ($roles as $role)
                <form method="POST" action="{{ route('roles.permissions', $role) }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    @csrf
                    @method('PUT')
                    <h2 class="font-semibold">{{ $role->name }}</h2>
                    <p class="mb-4 text-sm text-slate-500">
                        @if ($role->name === 'Super Admin')
                            Akses semua modul.
                        @elseif ($role->name === 'Admin')
                            Kelola kolam dan keuangan.
                        @else
                            Akses POS.
                        @endif
                    </p>
                    <div class="space-y-2">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="rounded border-slate-300" @checked($role->hasPermissionTo($permission->name))>
                                {{ $permission->name }}
                            </label>
                        @endforeach
                    </div>
                    <button class="mt-4 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update Permission</button>
                </form>
            @endforeach
        </div>
    </div>
</x-app-layout>
