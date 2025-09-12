<x-layouts.app>
    <x-slot:title>{{ $permission->exists ? 'Edit Permission' : 'Create Permission' }}</x-slot:title>
    <div class="p-4">
        <div class="bg-white dark:bg-gray-900 shadow rounded p-4 max-w-2xl">
            <form method="POST" action="{{ $permission->exists ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}" class="space-y-4">
                @csrf
                @if($permission->exists)
                    @method('PUT')
                @endif

                <x-forms.input label="Name" name="name" type="text" :value="$permission->name" />
                <x-forms.input label="Slug" name="slug" type="text" :value="$permission->slug" />
                <x-forms.textarea label="Description" name="description" :value="$permission->description" rows="3" />

                <div class="flex items-center gap-2">
                    <x-button type="primary">{{ $permission->exists ? 'Update' : 'Save' }}</x-button>
                    <a class="btn" href="{{ route('admin.permissions.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
