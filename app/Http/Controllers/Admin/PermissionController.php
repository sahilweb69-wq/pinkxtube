<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $perPage = (int) request('per_page', 10);
        $q = trim((string) request('q', ''));

        $query = Permission::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $permissions = $query->orderBy('name')
            ->paginate($perPage > 0 ? $perPage : 10)
            ->appends(request()->query());

        return view('admin.permissions.index', compact('permissions', 'q', 'perPage'));
    }

    public function create(): View
    {
        $permission = new Permission();
        return view('admin.permissions.upsert', compact('permission'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Permission::create($data);
        return to_route('admin.permissions.index')->with('status', 'Permission created');
    }

    public function edit(Permission $permission): View
    {
        return view('admin.permissions.upsert', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions,slug,' . $permission->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $permission->update($data);
        return to_route('admin.permissions.index')->with('status', 'Permission updated');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();
        return back()->with('status', 'Permission deleted');
    }
}
