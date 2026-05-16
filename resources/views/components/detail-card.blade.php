@props(['label', 'link' => false])

<div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ $label }}</flux:text>
    <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">
        @if($link)
            <a href="{{ $slot }}" target="_blank" rel="noopener noreferrer"
               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 break-all flex items-center gap-1 font-medium">
                {{ $slot }}
                <flux:icon name="arrow-top-right-on-square" class="size-3"/>
            </a>
        @else
            {{ $slot }}
        @endif
    </flux:text>
</div>
