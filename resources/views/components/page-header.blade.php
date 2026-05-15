<div @class([
    'flex-shrink-0 px-4 md:px-8 mb-6',
    'flex items-center justify-between' => isset($actions),
])>
    {{ $slot }}
    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
