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

<x-page-container>
    <x-page-header>
        <flux:heading size="xl">{{ $task->shortTitle() }}</flux:heading>
        <x-slot:actions>
            <flux:button variant="subtle" size="sm" icon="pencil" :href="route('tasks.edit', $task)" wire:navigate/>
            <flux:modal.trigger name="delete-task">
                <flux:button variant="subtle" size="sm" icon="trash" />
            </flux:modal.trigger>
        </x-slot:actions>
    </x-page-header>

    <x-page-content>
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 flex items-center gap-6">
                <flux:checkbox
                    :checked="$task->completed"
                    wire:click="{{ $task->completed ? 'revertTask' : 'completeTask' }}"
                    x-on:click="{{ $task->completed ? 'playSound(false)' : 'playSound(true)' }}"
                />
                <div class="flex-1">
                    <div class="text-xl font-bold {{ $task->completed ? 'line-through opacity-50 text-zinc-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                        {{ $task->shortTitle() }}
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @if ($task->due_date)
                    <x-detail-card :label="__('Due date')">
                        <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->due_date->format('j F Y') }}</flux:text>
                    </x-detail-card>
                @endif

                @if ($task->recurring())
                    <x-detail-card :label="__('Repeat')">
                        <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->repeatPhrase() }}</flux:text>
                    </x-detail-card>
                @endif

                @if ($task->link)
                    <x-detail-card :label="__('Link')">
                        <a href="{{ $task->link }}" target="_blank" rel="noopener noreferrer"
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 break-all flex items-center gap-1 font-medium">
                            {{ $task->link }}
                            <flux:icon name="arrow-top-right-on-square" class="size-3" />
                        </a>
                    </x-detail-card>
                @endif

                @if ($task->description)
                    <x-detail-card :label="__('Description')">
                        <flux:text class="whitespace-pre-wrap text-zinc-900 dark:text-zinc-100">{{ $task->description }}</flux:text>
                    </x-detail-card>
                @endif

                @if ($task->completed && $task->completed_date)
                    <x-detail-card :label="__('Completed on')">
                        <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->completed_date->format('j F Y') }}</flux:text>
                    </x-detail-card>
                @endif
            </div>

            <div class="mt-8">
                <flux:button variant="filled" :href="route('tasks')" wire:navigate>{{ __('Back') }}</flux:button>
            </div>
        </div>
    </x-page-content>

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
</x-page-container>
