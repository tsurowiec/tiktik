<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Task')] class extends Component {
    public Task $task;
    public string $title = '';
    public ?string $due_date = null;
    public ?string $original_due_date = null;
    public ?string $link = null;
    public ?string $description = null;

    public function mount(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);
        $this->title = $task->title;
        $this->due_date = $task->due_date?->format('Y-m-d');
        $this->original_due_date = $task->original_due_date?->format('Y-m-d');
        $this->link = $task->link;
        $this->description = $task->description;
    }

    public function save()
    {
        $this->validate([
            'title' => ['required', 'string', 'min:5'],
            'due_date' => ['nullable', 'date'],
            'link' => ['nullable', 'url'],
            'description' => ['nullable', 'string'],
        ]);

        $this->task->update([
            'title' => $this->title,
            'due_date' => $this->due_date ?: null,
            'original_due_date' => $this->original_due_date ?: $this->due_date ?: null,
            'link' => $this->link ?: null,
            'description' => $this->description ?: null,
        ]);

        return redirect()->route('tasks.show', $this->task);
    }
}; ?>
<div class="w-full max-w-2xl mx-auto pt-6 flex flex-col flex-1 min-h-0 overflow-hidden">
    <div class="flex-shrink-0 px-4 md:px-8">
        <flux:heading size="xl" class="mb-6">{{ __('Edit Task') }}</flux:heading>
    </div>

    <div class="flex-1 overflow-y-auto min-h-0 px-4 md:px-8 pb-8">
        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('Task title...') }}" required />

            <div wire:ignore x-data="{
                init() {
                    flatpickr(this.$el.querySelector('input'), {
                        dateFormat: 'Y-m-d',
                        defaultDate: $wire.due_date || '',
                        disableMobile: true,
                        onChange: (dates, str) => { $wire.due_date = str }
                    });
                }
            }">
                <flux:input wire:model="due_date" type="text" :label="__('Due Date')" placeholder="yyyy-mm-dd" />
            </div>

            <flux:input wire:model="link" :label="__('Link')" placeholder="https://..." type="url" />

            <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Add some notes...') }}" rows="4" />

            <div class="flex items-center gap-4 pt-4">
                <flux:button variant="primary" type="submit">{{ __('Save Task') }}</flux:button>
                <flux:button variant="subtle" :href="route('tasks.show', $task)" wire:navigate>{{ __('Cancel') }}</flux:button>
            </div>
        </form>
    </div>
</div>
