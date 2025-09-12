@props([
    'label' => null,
    'name',
    'value' => '',
    'rows' => 4,
])

@if($label)
    <label class="block text-sm font-medium mb-1" for="{{ $name }}">{{ $label }}</label>
@endif
<textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
    {{ $attributes->merge(['class' => 'w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500']) }}>{{ old($name, $value) }}</textarea>
@error($name)
    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
@enderror
