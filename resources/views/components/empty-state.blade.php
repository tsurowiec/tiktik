@props(['createRoute', 'createLabel' => 'Create one now'])

<div class="p-8 text-center border border-dashed rounded-xl border-zinc-400 dark:border-zinc-500">
    <flux:text color="sky" class="mb-2">{{ $slot }}</flux:text>
    <flux:button variant="subtle" size="sm" :href="$createRoute" wire:navigate>{{ $createLabel }}</flux:button>
</div>
