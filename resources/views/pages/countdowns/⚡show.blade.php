<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Countdown')]
class extends Component {
    public Task $task;

    public function mount(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);
        abort_if(!$task->countdown, 404);
    }

    public function delete()
    {
        $this->task->delete();

        return redirect()->route('tasks');
    }
}; ?>

<x-page-container>
    <x-page-header>
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                <flux:icon :name="$task->icon ?: 'cake'" class="size-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <flux:heading size="xl">{{ $task->title }}</flux:heading>
        </div>
        <x-slot:actions>
            <flux:button variant="subtle" size="sm" icon="pencil" :href="route('countdowns.edit', $task)" wire:navigate/>
            <flux:modal.trigger name="delete-countdown">
                <flux:button variant="subtle" size="sm" icon="trash" />
            </flux:modal.trigger>
        </x-slot:actions>
    </x-page-header>

    <x-page-content>
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 text-center">
                <flux:text size="lg" class="text-zinc-500 mb-2">{{ __('Remaining Time') }}</flux:text>
                <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">
                    {{ $task->countdownPhrase() }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if ($task->due_date)
                    <x-detail-card :label="__('Countdown Until')">
                        <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->due_date->format('j F Y') }}</flux:text>
                    </x-detail-card>
                @endif

                @if ($task->original_due_date)
                    <x-detail-card :label="__('Base Date')">
                        <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->original_due_date->format('j F Y') }}</flux:text>
                    </x-detail-card>
                @endif
            </div>

            @if ($task->link)
                <x-detail-card :label="__('Link')">
                    <a href="{{ $task->link }}" target="_blank" rel="noopener noreferrer"
                       class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 break-all flex items-center gap-1">
                        {{ $task->link }}
                        <flux:icon name="arrow-top-right-on-square" class="size-3" />
                    </a>
                </x-detail-card>
            @endif

            @if ($task->recurring())
                <x-detail-card :label="__('Repeats')">
                    <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $task->repeatPhrase() }}</flux:text>
                </x-detail-card>
            @endif

            @if ($task->description)
                <x-detail-card :label="__('Description')">
                    <flux:text class="whitespace-pre-wrap text-zinc-900 dark:text-zinc-100">{{ $task->description }}</flux:text>
                </x-detail-card>
            @endif
        </div>

        <div class="mt-8 flex items-center gap-4">
            <flux:button variant="filled" :href="route('tasks')" wire:navigate>{{ __('Back to Tasks') }}</flux:button>
        </div>
    </x-page-content>

    <flux:modal name="delete-countdown" class="min-w-[22rem]">
        <form wire:submit="delete" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete countdown?') }}</flux:heading>
                <flux:subheading>
                    {{ __('Are you sure you want to delete this countdown? This action cannot be undone.') }}
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Delete countdown') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</x-page-container>
