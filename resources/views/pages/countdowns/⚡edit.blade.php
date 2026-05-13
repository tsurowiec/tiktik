<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Countdown')] class extends Component {
    public Task $task;
    public string $title = '';
    public ?string $base_date = null;
    public ?string $countdown_date = null;
    public ?string $link = null;
    public ?string $description = null;
    public string $icon = 'cake';

    public function mount(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);
        abort_if(!$task->countdown, 404);

        $this->task = $task;
        $this->title = $task->title;
        $this->countdown_date = $task->due_date?->format('Y-m-d');
        $this->base_date = $task->original_due_date?->format('Y-m-d');
        $this->link = $task->link;
        $this->description = $task->description;
        $this->icon = $task->icon ?: 'cake';
    }

    public function save()
    {
        $this->validate([
            'title' => ['required', 'string', 'min:5'],
            'countdown_date' => ['date', 'after_or_equal:today'],
            'base_date' => ['nullable', 'date'],
            'link' => ['nullable', 'url'],
            'description' => ['nullable', 'string'],
            'icon' => ['required', 'string'],
        ]);

        $this->task->update([
            'title' => $this->title,
            'due_date' => $this->countdown_date ?: null,
            'original_due_date' => $this->base_date ?: null,
            'link' => $this->link ?: null,
            'description' => $this->description ?: null,
            'icon' => $this->icon,
        ]);

        return redirect()->route('countdowns.show', $this->task);
    }
}; ?>

<div class="w-full max-w-2xl mx-auto pt-6 flex flex-col flex-1 min-h-0 overflow-hidden">
    <div class="flex-shrink-0 px-4 md:px-8">
        <flux:heading size="xl" class="mb-6">{{ __('Edit Countdown') }}</flux:heading>
    </div>

    <div class="flex-1 overflow-y-auto min-h-0 px-4 md:px-8 pb-8">
        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('Countdown title...') }}" required />

            <div wire:ignore x-data="{
                init() {
                    flatpickr(this.$el.querySelector('input'), {
                        dateFormat: 'Y-m-d',
                        defaultDate: $wire.countdown_date || '',
                        disableMobile: true,
                        onChange: (dates, str) => { $wire.countdown_date = str }
                    });
                }
            }">
                <flux:input wire:model="countdown_date" type="text" :label="__('Countdown Until')" placeholder="yyyy-mm-dd" />
            </div>

            <div wire:ignore x-data="{
                init() {
                    flatpickr(this.$el.querySelector('input'), {
                        dateFormat: 'Y-m-d',
                        defaultDate: $wire.base_date || '',
                        disableMobile: true,
                        onChange: (dates, str) => { $wire.base_date = str }
                    });
                }
            }">
                <flux:input wire:model="base_date" type="text" :label="__('Base Date')" placeholder="yyyy-mm-dd" />
            </div>

            <flux:select wire:model="icon" :label="__('Icon')">
                @foreach (Task::icons() as $icon)
                    <flux:select.option :value="$icon" :icon="$icon">{{ str($icon)->replace('-', ' ')->title() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="link" :label="__('Link')" placeholder="https://..." type="url" />

            <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Add some notes...') }}" rows="4" />

            <div class="flex items-center gap-4 pt-4">
                <flux:button variant="primary" type="submit">{{ __('Save Changes') }}</flux:button>
                <flux:button variant="subtle" :href="route('countdowns.show', $task)" wire:navigate>{{ __('Cancel') }}</flux:button>
            </div>
        </form>
    </div>
</div>
