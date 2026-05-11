<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Task')] class extends Component {
    public Task $task;

    public function mount(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);
    }

    public function completeTask(): void
    {
        $this->task->update([
            'completed' => true,
            'completed_date' => now()->toDateString(),
        ]);
    }

    public function revertTask(): void
    {
        $this->task->update([
            'completed' => false,
            'completed_date' => null,
        ]);
    }
}; ?>

<div class="w-full max-w-2xl mx-auto p-4 md:p-8">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <flux:checkbox
                :checked="$task->completed"
                wire:click="{{ $task->completed ? 'revertTask' : 'completeTask' }}"
            />
            <flux:heading size="xl" class="{{ $task->completed ? 'line-through opacity-50' : '' }}">{{ $task->title }}</flux:heading>
        </div>
        <flux:button variant="subtle" size="sm" icon="pencil" :href="route('tasks.edit', $task)" wire:navigate />
    </div>

    <div class="space-y-4">
        @if ($task->due_date)
            <div>
                <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Due date') }}</flux:text>
                <flux:text class="text-zinc-900">{{ $task->due_date->format('j F Y') }}</flux:text>
            </div>
        @endif

        @if ($task->link)
            <div>
                <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Link') }}</flux:text>
                <a href="{{ $task->link }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 break-all">{{ $task->link }}</a>
            </div>
        @endif

        @if ($task->description)
            <div>
                <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Description') }}</flux:text>
                <flux:text class="whitespace-pre-wrap text-zinc-900">{{ $task->description }}</flux:text>
            </div>
        @endif

        @if ($task->completed && $task->completed_date)
            <div>
                <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Completed on') }}</flux:text>
                <flux:text class="text-zinc-900">{{ $task->completed_date->format('j F Y') }}</flux:text>
            </div>
        @endif
    </div>

    <div class="mt-8">
        <flux:button variant="filled" :href="route('tasks')" wire:navigate>{{ __('Back') }}</flux:button>
    </div>
</div>
