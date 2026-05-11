@php use Carbon\CarbonImmutable; @endphp
@props(['task'])

@php
    $overdue = !$task->completed && $task->due_date && $task->due_date->toDateString() < now()->toDateString();

    $formatDate = function (CarbonImmutable $date): string {
        $diffDays = (int) now()->startOfDay()->diffInDays($date, false);
        return match(true) {
            $diffDays === 0  => 'Today',
            $diffDays === 1  => 'Tomorrow',
            $diffDays === -1 => 'Yesterday',
            $diffDays > 1 && $diffDays < 7   => "In {$diffDays} days",
            $diffDays < -1 && $diffDays > -7 => abs($diffDays) . ' days ago',
            default => $date->format('j F') . ($date->year !== now()->year ? ' ' . $date->year : ''),
        };
    };
@endphp

@if ($task->completed)
    <div wire:key="completed-{{ $task->id }}" class="flex items-center gap-3 p-2 rounded-lg opacity-50">
        <flux:checkbox checked wire:click="revertTask({{ $task->id }})"/>
        <div class="flex-1 min-w-0">
            <flux:text class="line-through">
                <a href="{{ route('tasks.show', $task) }}" wire:navigate class="no-underline text-inherit">{{ $task->title }}</a>
            </flux:text>
            @if ($task->completed_date)
                <div class="flex items-center gap-2 w-full">
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500">{{ $formatDate($task->completed_date) }}</flux:text>
                    <span class="ml-auto flex items-center gap-1">
                        @if ($task->link)
                            <flux:icon name="link" class="size-3 text-zinc-400 dark:text-zinc-500" />
                        @endif
                        @if ($task->description)
                            <flux:icon name="bars-3-bottom-left" class="size-3 text-zinc-400 dark:text-zinc-500" />
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </div>
@else
    <div wire:key="pending-{{ $task->id }}"
         class="group flex items-center justify-between gap-3 p-2 rounded-lg transition-colors {{ $overdue ? 'hover:bg-red-50 dark:hover:bg-red-900/20' : 'hover:bg-indigo-100 dark:hover:bg-zinc-800/50' }}">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <flux:checkbox wire:click="completeTask({{ $task->id }})"/>
            <div class="flex-1 min-w-0">
                <flux:text class="{{ $overdue ? 'text-red-600 dark:text-red-400' : 'text-zinc-900' }}">
                    <a href="{{ route('tasks.show', $task) }}" wire:navigate class="no-underline text-inherit">{{ $task->title }}</a>
                </flux:text>
                <div class="flex items-center gap-2 w-full">
                    @if ($task->due_date)
                        <flux:text size="sm" class="{{ $overdue ? 'text-red-400 dark:text-red-500' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $formatDate($task->due_date) }}</flux:text>
                    @endif
                    <span class="ml-auto flex items-center gap-1">
                        @if ($task->link)
                            <flux:icon name="link" class="size-3 text-zinc-400 dark:text-zinc-500" />
                        @endif
                        @if ($task->description)
                            <flux:icon name="bars-3-bottom-left" class="size-3 text-zinc-400 dark:text-zinc-500" />
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif
