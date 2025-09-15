@props([
    'perPage' => 10,
    'q' => '',
    'perPageOptions' => [10, 25, 50, 100],
    'queryParam' => 'q',
    'perPageParam' => 'per_page',
    'label' => 'Entries Per Page',
    'method' => 'GET',
])

@php
    $formMethod = strtoupper($method);
    $spoofMethod = !in_array($formMethod, ['GET', 'POST']);
    $htmlMethod = $formMethod === 'GET' ? 'GET' : 'POST';
@endphp

<form method="{{ strtolower($htmlMethod) }}" class="flex items-center gap-3">
    @if($formMethod !== 'GET')
        @csrf
    @endif
    @if($spoofMethod)
        @method($formMethod)
    @endif
    <label class="text-sm text-gray-600 dark:text-gray-300">{{ $label }}</label>
    <select name="{{ $perPageParam }}"
            class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm"
            onchange="this.form.submit()">
        @foreach($perPageOptions as $n)
            <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }}</option>
        @endforeach
    </select>

    @if(!empty($q))
        <input type="hidden" name="{{ $queryParam }}" value="{{ $q }}" />
    @endif

    @foreach(request()->except([$perPageParam, $queryParam]) as $key => $value)
        @if(is_array($value))
            @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
</form>
