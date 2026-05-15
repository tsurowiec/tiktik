@props(['label'])

<div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ $label }}</flux:text>
    {{ $slot }}
</div>
