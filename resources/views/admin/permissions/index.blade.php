<x-layouts.app>
    <x-slot:title>Permissions</x-slot:title>
    <div class="p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Permissions</h1>
                <p class="text-gray-600 dark:text-gray-400">Define granular access to features and actions</p>
            </div>
            <a href="{{ route('admin.permissions.create') }}"
                class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </a>
        </div>

        <!-- Controls: Per Page, Search -->
        <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <x-index.per-page :per-page="$perPage ?? 10" :q="$q ?? ''" :per-page-options="[10, 25, 50, 100]" label="Entries Per Page" />

            <div class="flex items-center gap-2">
                <x-index.search :per-page="$perPage ?? 10" :q="$q ?? ''" placeholder="Search permissions..." />
            </div>
        </div>

        <x-table.container>
            <x-table.table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th class="w-16">No</x-table.th>
                        <x-table.th>Name</x-table.th>
                        <x-table.th>Slug</x-table.th>
                        <x-table.th class="text-right w-40">Action</x-table.th>
                    </x-table.tr>
                </x-table.thead>
                <x-table.tbody>
                    @forelse($permissions as $permission)
                        <x-table.tr>
                            <x-table.td>{{ $permissions->firstItem() + $loop->index }}</x-table.td>
                            <x-table.td
                                class="font-medium text-gray-900 dark:text-gray-100">{{ $permission->name }}</x-table.td>
                            <x-table.td>{{ $permission->slug }}</x-table.td>
                            <x-table.td class="text-right space-x-2 whitespace-nowrap w-40">
                                <a href="{{ route('admin.permissions.edit', $permission) }}"
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16.862 4.487l1.651 1.651m-2.475-1.177l-9.193 9.193a2.25 2.25 0 00-.53.86l-1.007 3.021a.75.75 0 00.95.95l3.02-1.007c.322-.108.62-.29.861-.53l9.193-9.193m-3.245-3.245a1.875 1.875 0 112.652 2.652L12.15 15.56a4.5 4.5 0 01-1.723 1.06l-2.574.858.858-2.574a4.5 4.5 0 011.06-1.723l8.486-8.486z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Delete permission?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 text-red-700 dark:text-red-400 hover:bg-red-500/20">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-2a1 1 0 00-1-1h-2a1 1 0 00-1 1v2" />
                                        </svg>
                                    </button>
                                </form>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No
                                permissions found.</x-table.td>
                        </x-table.tr>
                    @endforelse
                </x-table.tbody>
            </x-table.table>
            <x-slot:footer>
                {{ $permissions->appends(request()->except('page'))->links() }}
            </x-slot:footer>
        </x-table.container>
    </div>
</x-layouts.app>
