<div class="flex items-center gap-x-3 bg-gray-50 px-4 py-2 dark:bg-gray-800">
    <x-heroicon-o-calendar class="h-5 w-5 text-gray-400" />

    <h3 class="flex-1 truncate font-medium text-gray-900 dark:text-white">
        {{ $getTitle() }}
    </h3>

    <span class="text-xs text-gray-500">
        {{ $getState() }} записей
    </span>
</div>