@props([
    'title' => '',
    'description' => '',
    // items: array of [ 'label' => string, 'url' => string|null ]
    'items' => [],
])

<div>
    @if(!empty($items))
        <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
            <ol class="flex items-center gap-1">
                @foreach($items as $index => $item)
                    <li class="flex items-center gap-1">
                        @if(!empty($item['url']))
                            <a href="{{ $item['url'] }}" class="hover:text-gray-700 dark:hover:text-gray-200">{{ $item['label'] }}</a>
                        @else
                            <span class="text-gray-700 dark:text-gray-200">{{ $item['label'] }}</span>
                        @endif
                    </li>
                    @if($index < count($items) - 1)
                        <li aria-hidden="true" class="text-gray-400 mx-1">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif

    @if($title)
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $title }}</h1>
    @endif
    @if($description)
        <p class="text-gray-600 dark:text-gray-400">{{ $description }}</p>
    @endif
</div>
