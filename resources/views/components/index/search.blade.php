@props([
    'perPage' => 10,
    'q' => '',
    'queryParam' => 'q',
    'perPageParam' => 'per_page',
    'placeholder' => 'Search...',
    'widthClass' => 'w-64',
    'method' => 'GET',
])

@php
    $formMethod = strtoupper($method);
    $spoofMethod = !in_array($formMethod, ['GET', 'POST']);
    $htmlMethod = $formMethod === 'GET' ? 'GET' : 'POST';
@endphp

<form method="{{ strtolower($htmlMethod) }}" class="flex items-center">
    @if($formMethod !== 'GET')
        @csrf
    @endif
    @if($spoofMethod)
        @method($formMethod)
    @endif
    <input type="hidden" name="{{ $perPageParam }}" value="{{ $perPage }}" />

    @foreach(request()->except([$perPageParam, $queryParam]) as $key => $value)
        @if(is_array($value))
            @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    <div class="relative">
        <input type="text" name="{{ $queryParam }}" value="{{ $q }}" placeholder="{{ $placeholder }}"
               class="pl-9 pr-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm {{ $widthClass }}" />
        <span class="absolute left-2 top-2.5 text-gray-400">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.5a7.5 7.5 0 006.15-3.85z"/></svg>
        </span>
    </div>
</form>
