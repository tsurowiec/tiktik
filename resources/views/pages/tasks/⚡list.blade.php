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
            if ($task->due_date->isSameDay($today->copy()->addDays(2))) return $task->due_date->format('l');
            if ($task->due_date->isSameDay($today->copy()->addDays(3))) return $task->due_date->format('l');
            if ($task->due_date->isSameDay($today->copy()->addDays(4))) return $task->due_date->format('l');
            if ($task->due_date->isSameDay($today->copy()->addDays(5))) return $task->due_date->format('l');
            if ($task->due_date->isSameDay($today->copy()->addDays(6))) return $task->due_date->format('l');
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

<x-page-container class="px-4 md:px-8">
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
                    <x-section-label variant="danger">{{ __('Overdue') }}</x-section-label>
                    @foreach ($overdueTasks as $task)
                        <x-task-row :task="$task"/>
                    @endforeach
                @endif

                @foreach ($next10DaysGrouped as $dateLabel => $tasks)
                    <x-section-label>{{ $dateLabel }}</x-section-label>
                    @foreach ($tasks as $task)
                        <x-task-row :task="$task"/>
                    @endforeach
                @endforeach

                @if ($overdueTasks->isEmpty() && $next10DaysTasks->isEmpty())
                    <x-empty-state :create-route="route('tasks.create')">
                        {{ __('No tasks in coming 10 days.') }}
                    </x-empty-state>
                @endif

                @if ($completedTasks->isNotEmpty())
                    <div class="mt-6">
                        <x-section-label>{{ __('Completed') }}</x-section-label>
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
                    <x-section-label>{{ __('Later') }}</x-section-label>
                @endif
                @forelse ($laterTasks as $task)
                    <x-task-row :task="$task"/>
                @empty
                    <x-empty-state :create-route="route('tasks.create')">
                        {{ __('No tasks scheduled for later.') }}
                    </x-empty-state>
                @endforelse
            </div>
        </x-slot:later>

        <x-slot:someday>
            <div class="flex flex-col">
                @if ($somedayTasks->isNotEmpty())
                    <x-section-label>{{ __('Someday') }}</x-section-label>
                @endif
                @forelse ($somedayTasks as $task)
                    <x-task-row :task="$task"/>
                @empty
                    <x-empty-state :create-route="route('tasks.create')">
                        {{ __('No tasks in your Someday list.') }}
                    </x-empty-state>
                @endforelse
            </div>
        </x-slot:someday>

        <x-slot:countdowns>
            <div class="flex flex-col">
                @if ($countdowns->isNotEmpty())
                    <x-section-label>{{ __('Countdowns') }}</x-section-label>
                @endif
                @foreach ($countdowns as $task)
                    <x-task-row :task="$task"/>
                @endforeach

                @if ($countdowns->isEmpty())
                    <x-empty-state :create-route="route('countdowns.create')">
                        {{ __('No countdowns') }}
                    </x-empty-state>
                @endif
            </div>
        </x-slot:countdowns>
    </x-tabs>
</x-page-container>
