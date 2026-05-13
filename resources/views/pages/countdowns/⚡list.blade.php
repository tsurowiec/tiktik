<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Task;

new #[Title('Countdowns')]
class extends Component {
    public function with(): array
    {
        $user = Auth::user();
        $today = now()->startOfDay();

        return [
            'countdowns' => $user->tasks()
                ->where('countdown', true)
                ->where('due_date', '>=', $today)
                ->orderBy('due_date')
                ->take(30)
                ->get(),
        ];
    }
}; ?>

<div class="w-full max-w-2xl mx-auto px-4 md:px-8 pt-6 flex flex-col flex-1 min-h-0 overflow-hidden">
    <div class="flex justify-start mb-8 flex-shrink-0">
        <flux:button :href="route('countdowns.create')" variant="primary" icon="plus" class="px-8 py-6 text-lg"
                     wire:navigate>
            {{ __('Add a countdown') }}
        </flux:button>
    </div>

    <div class="flex-1 overflow-y-auto min-h-0 pb-8">
        <div class="flex flex-col">
            @foreach ($countdowns as $task)
                <x-task-row :task="$task"/>
            @endforeach

            @if ($countdowns->isEmpty())
                <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                    <flux:text color="sky" class="mb-2">{{ __('No countdowns') }}</flux:text>
                    <flux:button variant="subtle" size="sm" :href="route('countdowns.create')"
                                 wire:navigate>{{ __('Create one now') }}</flux:button>
                </div>
            @endif
        </div>
    </div>
</div>
