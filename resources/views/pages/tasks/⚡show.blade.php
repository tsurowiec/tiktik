<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Task')]
class extends Component {
    public Task $task;

    public function mount(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);
    }

    public function completeTask(): void
    {
        $this->task->complete();
    }

    public function revertTask(): void
    {
        $this->task->revert();
    }

    public function delete()
    {
        $this->task->delete();

        return redirect()->route('tasks');
    }
}; ?>

<div class="w-full max-w-2xl mx-auto pt-6 flex flex-col flex-1 min-h-0 overflow-hidden">
    <div class="flex-shrink-0 px-4 md:px-8 flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ $task->shortTitle() }}</flux:heading>
        <div class="flex items-center gap-2">
            <flux:button variant="subtle" size="sm" icon="pencil" :href="route('tasks.edit', $task)" wire:navigate/>
            <flux:modal.trigger name="delete-task">
                <flux:button variant="subtle" size="sm" icon="trash" />
            </flux:modal.trigger>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto min-h-0 px-4 md:px-8 pb-8">
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 flex items-center gap-6">
                <flux:checkbox
                    :checked="$task->completed"
                    wire:click="{{ $task->completed ? 'revertTask' : 'completeTask' }}"
                />
                <div class="flex-1">
                    <div class="text-xl font-bold {{ $task->completed ? 'line-through opacity-50 text-zinc-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                        {{ $task->shortTitle() }}
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @if ($task->due_date)
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Due date') }}</flux:text>
                    <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->due_date->format('j F Y') }}</flux:text>
                </div>
            @endif

            @if ($task->recurring())
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Repeat') }}</flux:text>
                    <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->repeatPhrase() }}</flux:text>
                </div>
            @endif

            @if ($task->link)
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Link') }}</flux:text>
                    <a href="{{ $task->link }}" target="_blank" rel="noopener noreferrer"
                       class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 break-all flex items-center gap-1 font-medium">
                        {{ $task->link }}
                        <flux:icon name="arrow-top-right-on-square" class="size-3" />
                    </a>
                </div>
            @endif

            @if ($task->description)
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Description') }}</flux:text>
                    <flux:text class="whitespace-pre-wrap text-zinc-900 dark:text-zinc-100">{{ $task->description }}</flux:text>
                </div>
            @endif

            @if ($task->completed && $task->completed_date)
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-1">{{ __('Completed on') }}</flux:text>
                    <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->completed_date->format('j F Y') }}</flux:text>
                </div>
            @endif
        </div>

        <div class="mt-8">
            <flux:button variant="filled" :href="route('tasks')" wire:navigate>{{ __('Back') }}</flux:button>
        </div>
    </div>

    <flux:modal name="delete-task" class="min-w-[22rem]">
        <form wire:submit="delete" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete task?') }}</flux:heading>
                <flux:subheading>
                    {{ __('Are you sure you want to delete this task? This action cannot be undone.') }}
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Delete task') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
