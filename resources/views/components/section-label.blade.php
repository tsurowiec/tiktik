@props(['variant' => 'default'])

@php
$classes = match($variant) {
    'danger' => 'text-red-400 dark:text-red-500 mt-2 mb-2 px-2',
    default   => 'text-zinc-400 dark:text-zinc-500 mt-4 mb-2 px-2',
};
@endphp

<flux:text size="sm" class="{{ $classes }}">{{ $slot }}</flux:text>
