<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Task;

new #[Title('Tasks')]
class extends Component {
    public function completeTask(int $taskId): void
    {
        $task = Auth::user()->tasks()->findOrFail($taskId);
        $task->complete();
    }

    public function revertTask(int $taskId): void
    {
        $task = Auth::user()->tasks()->findOrFail($taskId);
        $task->revert();
    }

    public function with(): array
    {
        $user = Auth::user();
        $today = now()->startOfDay();
        $in10Days = now()->addDays(9)->endOfDay();

        $next10DaysItems = $user->tasks()
            ->where('completed', false)
            ->where('due_date', '>=', $today)
            ->where('due_date', '<=', $in10Days)
            ->orderBy('due_date')
            ->get();

        $next10DaysGrouped = $next10DaysItems->groupBy(function ($task) use ($today) {
            if ($task->due_date->isSameDay($today)) return 'Today';
            if ($task->due_date->isSameDay($today->copy()->addDay())) return 'Tomorrow';
            return $task->due_date->format('j M');
        });

        return [
            'overdueTasks' => $user->tasks()
                ->where('completed', false)
                ->where('due_date', '<', $today)
                ->orderBy('due_date')
                ->get(),
            'next10DaysTasks' => $next10DaysItems,
            'next10DaysGrouped' => $next10DaysGrouped,
            'laterTasks' => $user->tasks()
                ->where('completed', false)
                ->where('due_date', '>', $in10Days)
                ->orderBy('due_date')
                ->get(),
            'somedayTasks' => $user->tasks()
                ->where('completed', false)
                ->whereNull('due_date')
                ->orderBy('id')
                ->get(),
            'completedTasks' => $user->tasks()
                ->where('completed', true)
                ->whereDate('completed_date', $today)
                ->orderBy('due_date')
                ->get(),
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
    <div class="flex justify-between mb-8">
        <flux:button :href="route('tasks.create')" variant="primary" icon="plus" class="px-16 py-6 text-lg"
                     wire:navigate>
            {{ __('Add task') }}
        </flux:button>
        <flux:button :href="route('countdowns.create')" variant="primary" icon="plus" class="px-16 py-6 text-lg"
                     wire:navigate>
            {{ __('Add countdown') }}
        </flux:button>
    </div>

    <x-tabs active="next10Days" class="flex-1 min-h-0" :tabs="[
        'next10Days' => ['label' => __('Next 10 Days'), 'icon' => 'calendar-days', 'badge' => $next10DaysTasks->count() + $overdueTasks->count()],
        'later' => ['label' => __('Later'), 'icon' => 'arrow-right-circle', 'badge' => $laterTasks->count()],
        'someday' => ['label' => __('Someday'), 'icon' => 'sparkles', 'badge' => $somedayTasks->count()],
        'countdowns' => ['label' => __('Countdowns'), 'icon' => 'flag', 'badge' => $countdowns->count()],
    ]">
        <x-slot:next10Days>
            <div class="flex flex-col">
                @if ($overdueTasks->isNotEmpty())
                    <flux:text size="sm" class="text-red-400 dark:text-red-500 mt-2 mb-2 px-2">{{ __('Overdue') }}</flux:text>
                    @foreach ($overdueTasks as $task)
                        <x-task-row :task="$task"/>
                    @endforeach
                @endif

                @foreach ($next10DaysGrouped as $dateLabel => $tasks)
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mt-4 mb-2 px-2">{{ $dateLabel }}</flux:text>
                    @foreach ($tasks as $task)
                        <x-task-row :task="$task"/>
                    @endforeach
                @endforeach

                @if ($overdueTasks->isEmpty() && $next10DaysTasks->isEmpty())
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky"
                                   class="mb-2">{{ __('No tasks in coming 10 days.') }}</flux:text>
                        <flux:button variant="subtle" size="sm" :href="route('tasks.create')"
                                     wire:navigate>{{ __('Create one now') }}</flux:button>
                    </div>
                @endif

                @if ($completedTasks->isNotEmpty())
                    <div class="mt-6">
                        <flux:text size="sm"
                                   class="text-zinc-400 dark:text-zinc-500 mb-2 px-2">{{ __('Completed') }}</flux:text>
                        @foreach ($completedTasks as $task)
                            <x-task-row :task="$task"/>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:next10Days>

        <x-slot:later>
            <div class="flex flex-col">
                @if ($laterTasks->isNotEmpty())
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mt-2 mb-2 px-2">{{ __('Later') }}</flux:text>
                @endif
                @forelse ($laterTasks as $task)
                    <x-task-row :task="$task"/>
                @empty
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky" class="mb-2">{{ __('No tasks scheduled for later.') }}</flux:text>
                        <flux:button variant="subtle" size="sm" :href="route('tasks.create')"
                                     wire:navigate>{{ __('Create one now') }}</flux:button>
                    </div>
                @endforelse
            </div>
        </x-slot:later>

        <x-slot:someday>
            <div class="flex flex-col">
                @if ($somedayTasks->isNotEmpty())
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mt-2 mb-2 px-2">{{ __('Someday') }}</flux:text>
                @endif
                @forelse ($somedayTasks as $task)
                    <x-task-row :task="$task"/>
                @empty
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky" class="mb-2">{{ __('No tasks in your Someday list.') }}</flux:text>
                        <flux:button variant="subtle" size="sm" :href="route('tasks.create')"
                                     wire:navigate>{{ __('Create one now') }}</flux:button>
                    </div>
                @endforelse
            </div>
        </x-slot:someday>

        <x-slot:countdowns>
            <div class="flex flex-col">
                @if ($countdowns->isNotEmpty())
                    <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mt-2 mb-2 px-2">{{ __('Countdowns') }}</flux:text>
                @endif
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
        </x-slot:countdowns>
    </x-tabs>
</div>
