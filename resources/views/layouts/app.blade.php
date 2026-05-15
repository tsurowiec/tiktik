<x-layouts::app.header :title="$title ?? null">
    <flux:main class="flex flex-col min-h-0 overflow-hidden p-0!">
        {{ $slot }}
    </flux:main>
</x-layouts::app.header>
