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
        $in2Weeks = now()->addDays(13)->endOfDay();

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
            'fortnightTasks' => $user->tasks()
                ->where('completed', false)
                ->where('due_date', '>=', $today)
                ->where('due_date', '<=', $in2Weeks)
                ->orderBy('due_date')
                ->get(),
            'laterTasks' => $user->tasks()
                ->where('completed', false)
                ->where('due_date', '>', $in2Weeks)
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
            'completedFortnightTasks' => $user->tasks()
                ->where('completed', true)
                ->where('completed_date', '>=', $today)
                ->where('completed_date', '<=', $in2Weeks)
                ->orderBy('due_date')
                ->get(),
            'completedLaterTasks' => $user->tasks()
                ->where('completed', true)
                ->where('completed_date', '>', $in2Weeks)
                ->orderBy('due_date')
                ->get(),
        ];
    }
}; ?>

<div class="w-full max-w-2xl mx-auto px-4 md:px-8 pt-6 flex flex-col flex-1 min-h-0 overflow-hidden">
    <div class="flex justify-start mb-8 flex-shrink-0">
        <flux:button :href="route('tasks.create')" variant="primary" icon="plus" class="px-8 py-6 text-lg"
                     wire:navigate>
            {{ __('Add a task') }}
        </flux:button>
    </div>

    <x-tabs active="fortnight" class="flex-1 min-h-0" :tabs="[
        'today' => ['label' => __('Today'), 'icon' => 'sun', 'badge' => $todayTasks->count() + $overdueTasks->count(), 'overdue' => (bool) $overdueTasks->count()],
        'fortnight' => ['label' => __('Fortnight'), 'icon' => 'calendar-days', 'badge' => $fortnightTasks->count() + $overdueTasks->count()],
        'later' => ['label' => __('Later'), 'icon' => 'clock', 'badge' => $laterTasks->count()],
        'someday' => ['label' => __('Someday'), 'icon' => 'sparkles', 'badge' => $somedayTasks->count()],
    ]">
        <x-slot:today>
            <div class="flex flex-col">
                @foreach ($overdueTasks as $task)
                    <x-task-row :task="$task"/>
                @endforeach

                @foreach ($todayTasks as $task)
                    <x-task-row :task="$task"/>
                @endforeach

                @if ($overdueTasks->isEmpty() && $todayTasks->isEmpty())
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky" class="mb-2">{{ __('No tasks for today!') }}</flux:text>
                        <flux:button variant="subtle" size="sm" :href="route('tasks.create')"
                                     wire:navigate>{{ __('Create one now') }}</flux:button>
                    </div>
                @endif

                @if ($completedTodayTasks->isNotEmpty())
                    <div class="mt-6">
                        <flux:text size="sm"
                                   class="text-zinc-400 dark:text-zinc-500 py-4 px-2">{{ __('Completed') }}</flux:text>
                        @foreach ($completedTodayTasks as $task)
                            <x-task-row :task="$task"/>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:today>

        <x-slot:fortnight>
            <div class="flex flex-col">
                @foreach ($overdueTasks as $task)
                    <x-task-row :task="$task"/>
                @endforeach

                @foreach ($fortnightTasks as $task)
                    <x-task-row :task="$task"/>
                @endforeach

                @if ($overdueTasks->isEmpty() && $fortnightTasks->isEmpty())
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky"
                                   class="mb-2">{{ __('Nothing scheduled for the next 14 days.') }}</flux:text>
                        <flux:button variant="subtle" size="sm" :href="route('tasks.create')"
                                     wire:navigate>{{ __('Create one now') }}</flux:button>
                    </div>
                @endif

                @if ($completedFortnightTasks->isNotEmpty())
                    <div class="mt-6">
                        <flux:text size="sm"
                                   class="text-zinc-400 dark:text-zinc-500 mb-2 px-2">{{ __('Completed') }}</flux:text>
                        @foreach ($completedFortnightTasks as $task)
                            <x-task-row :task="$task"/>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:fortnight>

        <x-slot:later>
            <div class="flex flex-col">
                @forelse ($laterTasks as $task)
                    <x-task-row :task="$task"/>
                @empty
                    <div class="p-8 text-center border border-dashed rounded-xl border-zinc-200 dark:border-zinc-700">
                        <flux:text color="sky" class="mb-2">{{ __('No tasks scheduled for later.') }}</flux:text>
                        <flux:button variant="subtle" size="sm" :href="route('tasks.create')"
                                     wire:navigate>{{ __('Create one now') }}</flux:button>
                    </div>
                @endforelse

                @if ($completedLaterTasks->isNotEmpty())
                    <div class="mt-6">
                        <flux:text size="sm"
                                   class="text-zinc-400 dark:text-zinc-500 m-2 px-2">{{ __('Completed') }}</flux:text>
                        @foreach ($completedLaterTasks as $task)
                            <x-task-row :task="$task"/>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:later>

        <x-slot:someday>
            <div class="flex flex-col">
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
    </x-tabs>
</div>
