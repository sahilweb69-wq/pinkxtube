@props([
    'label' => null,
    'name',
    'value' => 1,
    'checked' => false,
    'id' => null,
])
@php($id = $id ?: $name.'-'.md5($name.$value))
<label for="{{ $id }}" class="flex items-center text-sm text-gray-700 dark:text-gray-300">
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
        @checked(old(str_replace('[]','',$name)) ? in_array($value, (array) old(str_replace('[]','',$name))) : $checked)
        {{ $attributes->merge(['class' => 'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-2']) }}>
    @if($label)
        <span>{{ $label }}</span>
    @endif
</label>
