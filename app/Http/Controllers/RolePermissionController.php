<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePermissionRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        return view('roles.index', [
            'users' => User::with('roles')->orderBy('name')->get(),
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function assign(RolePermissionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::findOrFail($data['user_id']);
        $user->syncRoles([$data['role']]);

        return back()->with('success', 'Role user berhasil diperbarui.');
    }

    public function syncPermissions(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', 'Permission role berhasil diperbarui.');
    }
}
