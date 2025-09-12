<x-layouts.app>
    <x-slot:title>Permissions</x-slot:title>
    <div class="p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Permissions</h1>
                <p class="text-gray-600 dark:text-gray-400">Define granular access to features and actions</p>
            </div>
            <a href="{{ route('admin.permissions.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Permission
            </a>
        </div>

        <!-- Controls: Per Page, Actions, Search -->
        <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <form method="GET" class="flex items-center gap-3">
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

            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center">
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
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300 w-40">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900">
                        @forelse($permissions as $permission)
                            <tr class="border-t border-gray-200 dark:border-gray-700 align-top">
                                <td class="px-4 py-3">{{ $permissions->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $permission->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $permission->slug }}</td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap w-40">
                                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20">Edit</a>
                                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline" onsubmit="return confirm('Delete permission?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 text-red-700 dark:text-red-400 hover:bg-red-500/20">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $permissions->links() }}</div>
        </div>
    </div>
</x-layouts.app>
