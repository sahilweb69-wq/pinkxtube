<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
    {{ $slot }}
    @isset($footer)
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $footer }}
        </div>
    @endisset
</div>
