<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Task;

new #[Title('Tasks')] class extends Component {
    public function completeTask(int $taskId): void
    {
        $task = Auth::user()->tasks()->findOrFail($taskId);
        $task->update([
            'completed' => true,
            'completed_date' => now()->toDateString(),
        ]);
    }

    public function revertTask(int $taskId): void
    {
        $task = Auth::user()->tasks()->findOrFail($taskId);
        $task->update([
            'completed' => false,
            'completed_date' => null,
        ]);
    }

    public function with(): array
    {
        $user = Auth::user();
        $today = now()->startOfDay();
        $nextWeek = now()->addDays(7)->endOfDay();

        return [
            'overdueTasks' => $user->tasks()
                ->where('completed', false)
                ->where('due_date', '<', $today)
                ->orderBy('due_date')
                ->get(),
            'todayTasks' => $user->tasks()
                ->where('completed', false)
                ->whereDate('due_date', $today)
                ->orderBy('due_date')
                ->get(),
            'next7DaysTasks' => $user->tasks()
                ->where('completed', false)
                ->where('due_date', '>=', $today)
                ->where('due_date', '<=', $nextWeek)
                ->orderBy('due_date')
                ->get(),
            'laterTasks' => $user->tasks()
                ->where('completed', false)
                ->where('due_date', '>', $nextWeek)
                ->orderBy('due_date')
                ->get(),
            'somedayTasks' => $user->tasks()
                ->where('completed', false)
                ->whereNull('due_date')
                ->orderBy('id')
                ->get(),
            'completedTodayTasks' => $user->tasks()
                ->where('completed', true)
                ->whereDate('completed_date', $today)
                ->orderBy('due_date')
                ->get(),
            'completedNext7DaysTasks' => $user->tasks()
                ->where('completed', true)
                ->where('completed_date', '>=', $today)
                ->where('completed_date', '<=', $nextWeek)
                ->orderBy('due_date')
                ->get(),
            'completedLaterTasks' => $user->tasks()
                ->where('completed', true)
                ->where('completed_date', '>', $nextWeek)
                ->orderBy('due_date')
                ->get(),
        ];
    }
}; ?>

<div class="w-full max-w-2xl mx-auto p-4 md:p-8">
    <div class="flex justify-start mb-8">
        <flux:button :href="route('tasks.create')" variant="primary" icon="plus" class="px-8 py-6 text-lg" wire:navigate>
            {{ __('Add a task') }}
        </flux:button>
    </div>

    <x-tabs active="next_7_days" :tabs="[
        'today' => ['label' => __('Today'), 'icon' => 'sun', 'badge' => $todayTasks->count() + $overdueTasks->count()],
        'next_7_days' => ['label' => __('Next 7 days'), 'icon' => 'calendar-days', 'badge' => $next7DaysTasks->count() + $overdueTasks->count()],
        'later' => ['label' => __('Later'), 'icon' => 'clock', 'badge' => $laterTasks->count()],
        'someday' => ['label' => __('Someday'), 'icon' => 'sparkles', 'badge' => $somedayTasks->count()],
    ]">
        <x-slot:today>
            <div class="flex flex-col">
                @foreach ($overdueTasks as $task)
                    <x-task-row :task="$task" />
                @endforeach

                @foreach ($todayTasks as $task)
                    <x-task-row :task="$task" />
                @endforeach

                @if ($overdueTasks->isEmpty() && $todayTasks->isEmpty())
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky" class="mb-2">{{ __('No tasks for today!') }}</flux:text>
                        <flux:button variant="subtle" size="sm" :href="route('tasks.create')" wire:navigate>{{ __('Create one now') }}</flux:button>
                    </div>
                @endif

                @if ($completedTodayTasks->isNotEmpty())
                    <div class="mt-6">
                        <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 py-4 px-2">{{ __('Completed') }}</flux:text>
                        @foreach ($completedTodayTasks as $task)
                            <x-task-row :task="$task" />
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:today>

        <x-slot:next_7_days>
            <div class="flex flex-col">
                @foreach ($overdueTasks as $task)
                    <x-task-row :task="$task" />
                @endforeach

                @foreach ($next7DaysTasks as $task)
                    <x-task-row :task="$task" />
                @endforeach

                @if ($overdueTasks->isEmpty() && $next7DaysTasks->isEmpty())
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky">{{ __('Nothing scheduled for the next 7 days.') }}</flux:text>
                    </div>
                @endif

                @if ($completedNext7DaysTasks->isNotEmpty())
                    <div class="mt-6">
                        <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 mb-2 px-2">{{ __('Completed') }}</flux:text>
                        @foreach ($completedNext7DaysTasks as $task)
                            <x-task-row :task="$task" />
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:next_7_days>

        <x-slot:later>
            <div class="flex flex-col">
                @forelse ($laterTasks as $task)
                    <x-task-row :task="$task" />
                @empty
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky">{{ __('No tasks scheduled for later.') }}</flux:text>
                    </div>
                @endforelse

                @if ($completedLaterTasks->isNotEmpty())
                    <div class="mt-6">
                        <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500 m-2 px-2">{{ __('Completed') }}</flux:text>
                        @foreach ($completedLaterTasks as $task)
                            <x-task-row :task="$task" />
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:later>

        <x-slot:someday>
            <div class="flex flex-col">
                @forelse ($somedayTasks as $task)
                    <x-task-row :task="$task" />
                @empty
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky">{{ __('No tasks in your Someday list.') }}</flux:text>
                    </div>
                @endforelse
            </div>
        </x-slot:someday>
    </x-tabs>
</div>
