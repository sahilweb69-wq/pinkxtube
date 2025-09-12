<x-layouts.app>
    <x-slot:title>{{ $role->exists ? 'Edit Role' : 'Create Role' }}</x-slot:title>
    <div class="p-6 space-y-6 max-w-4xl">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $role->exists ? 'Edit Role' : 'Create Role' }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Define role details and assign permissions</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Role Details</h2>
            </div>

            <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="p-6 space-y-6">
                @csrf
                @if($role->exists)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-forms.input label="Name" name="name" type="text" :value="$role->name" />
                    <x-forms.input label="Slug" name="slug" type="text" :value="$role->slug" />
                </div>
                <x-forms.textarea label="Description" name="description" :value="$role->description" rows="3" />

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium">Permissions</label>
                        <span class="text-xs text-gray-500">Select the permissions this role should grant</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-72 overflow-auto p-3 border border-gray-200 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900">
                        @php $selected = old('permissions', $role->exists ? $role->permissions->pluck('id')->all() : []); @endphp
                        @foreach($permissions as $permission)
                            <x-forms.checkbox-item name="permissions[]" :checked="in_array($permission->id, $selected)" :value="$permission->id" :label="$permission->name" />
                        @endforeach
                    </div>
                    @error('permissions')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <x-button type="primary">{{ $role->exists ? 'Update Role' : 'Create Role' }}</x-button>
                    <a class="btn" href="{{ route('admin.roles.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
