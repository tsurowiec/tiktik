@props(['tabs', 'active' => null, 'overdue' => false])

@php
    $active = $active ?? array_key_first($tabs);
@endphp

<div x-data="{ activeTab: '{{ $active }}' }" {{ $attributes->class('w-full flex flex-col') }}>
    <div class="shrink-0 flex border-b border-zinc-200 dark:border-zinc-700 mb-6 overflow-x-auto no-scrollbar">
        <div class="flex w-full justify-between">
            @foreach ($tabs as $name => $tab)
                @php
                    $label = is_array($tab) ? $tab['label'] : $tab;
                    $icon = is_array($tab) ? ($tab['icon'] ?? null) : null;
                    $badge = is_array($tab) ? ($tab['badge'] ?? null) : null;
                    $tabOverdue = is_array($tab) && (($tab['overdue'] ?? false));
                @endphp
                <button
                    @click="activeTab = '{{ $name }}'"
                    type="button"
                    :class="activeTab === '{{ $name }}'
                        ? 'border-zinc-800 dark:border-zinc-100 text-zinc-800 dark:text-zinc-100'
                        : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-200'"
                    class="group flex items-center gap-1 px-2 py-2 border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap"
                >
                    @if ($icon)
                        <flux:icon :name="$icon" class="size-5" />
                    @else
                        <span>{{ $label }}</span>
                    @endif

                    @if ($badge !== null)
                        <span
                            @if($tabOverdue)
                                class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-[10px] font-bold rounded-full transition-colors duration-200 bg-red-700 text-white"
                            @else
                                :class="activeTab === '{{ $name }}'
                                    ? 'bg-indigo-900 text-white dark:bg-zinc-100 dark:text-zinc-800'
                                    : 'bg-indigo-200 text-zinc-500 group-hover:bg-indigo-300 group-hover:text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:group-hover:bg-zinc-700 dark:group-hover:text-zinc-200'"
                                class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-[10px] font-bold rounded-full transition-colors duration-200"
                            @endif
                        >
                            {{ $badge }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <div class="flex-1 overflow-y-auto min-h-0">
        @foreach ($tabs as $name => $tab)
            @php $label = is_array($tab) ? $tab['label'] : $tab; @endphp
            <div
                x-show="activeTab === '{{ $name }}'"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="focus:outline-none"
            >
                <flux:heading size="lg" class="mb-4">{{ $label }}</flux:heading>
                {{ ${$name} }}
            </div>
        @endforeach
    </div>
</div>
