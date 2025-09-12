<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $perPage = (int) request('per_page', 10);
        $q = trim((string) request('q', ''));

        $query = Role::query()
            ->withCount('users')
            ->with('permissions');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        $roles = $query->orderBy('name')
            ->paginate($perPage > 0 ? $perPage : 10)
            ->appends(request()->query());

        return view('admin.roles.index', compact('roles', 'q', 'perPage'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get();
        $role = new Role();
        return view('admin.roles.upsert', compact('permissions', 'role'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create(collect($data)->except('permissions')->toArray());
        $role->permissions()->sync($data['permissions'] ?? []);

        return to_route('admin.roles.index')->with('status', 'Role created');
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get();
        return view('admin.roles.upsert', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug,' . $role->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update(collect($data)->except('permissions')->toArray());
        $role->permissions()->sync($data['permissions'] ?? []);

        return to_route('admin.roles.index')->with('status', 'Role updated');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();
        return back()->with('status', 'Role deleted');
    }
}
