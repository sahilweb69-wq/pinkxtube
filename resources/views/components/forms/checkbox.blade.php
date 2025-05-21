@props(['label', 'name'])
<label for="{{ $name }}"
    {{ $attributes->merge(['class' => 'ml-2 block text-sm text-gray-700 dark:text-gray-300']) }}>
    <input type="checkbox" id="{{ $name }}" name="{{ $name }}"
        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
    {{ $label }}
</label>
