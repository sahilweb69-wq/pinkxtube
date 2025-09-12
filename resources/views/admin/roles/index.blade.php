<x-layouts.app>
    <x-slot:title>Roles</x-slot:title>
    <div class="p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Roles</h1>
                <p class="text-gray-600 dark:text-gray-400">Create and manage user roles and their permissions</p>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Role
            </a>
        </div>

        <!-- Controls: Per Page, Actions, Search -->
        <div class="mt-2 mb-4 grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
            <form method="GET" class="flex items-center gap-3 order-2 md:order-1">
                <label class="text-sm text-gray-600 dark:text-gray-300">Entries Per Page</label>
                <select name="per_page" class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm"
                        onchange="this.form.submit()">
                    @foreach([10,25,50,100] as $n)
                        <option value="{{ $n }}" @selected(($perPage ?? 10) == $n)>{{ $n }}</option>
                    @endforeach
                </select>
                @if(!empty($q))
                    <input type="hidden" name="q" value="{{ $q }}" />
                @endif
            </form>

            <!-- Action buttons (center column) -->
            <div class="flex items-center gap-2 order-3 md:order-2 md:justify-center">
                <!-- Placeholder export button -->
                <button type="button" class="inline-flex items-center px-3 py-2 rounded-md bg-emerald-500 text-white hover:bg-emerald-600" title="Export CSV">
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5m0 0l5-5m-5 5V4" /></svg>
                    Export
                </button>

                <!-- Search (right column on md+) -->
                <form method="GET" class="flex items-center order-1 md:order-3 md:col-start-3 md:justify-end">
                    <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}" />
                    <div class="relative">
                        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search..."
                               class="pl-9 pr-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm w-64" />
                        <span class="absolute left-2 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.5a7.5 7.5 0 006.15-3.85z"/></svg>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300 w-16">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Slug</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300 w-24">Users</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Permissions</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300 w-40">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900">
                        @forelse($roles as $role)
                            <tr class="border-t border-gray-200 dark:border-gray-700 align-top">
                                <td class="px-4 py-3">{{ $roles->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $role->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $role->slug }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap w-24">{{ $role->users_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5 max-w-full">
                                        @forelse($role->permissions as $perm)
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">{{ $perm->name }}</span>
                                        @empty
                                            <span class="text-sm text-gray-500">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap w-40">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20">Edit</a>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Delete role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 text-red-700 dark:text-red-400 hover:bg-red-500/20">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $roles->links() }}</div>
        </div>
    </div>
</x-layouts.app>
