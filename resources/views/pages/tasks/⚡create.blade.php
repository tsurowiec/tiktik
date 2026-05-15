<?php

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Task')] class extends Component {
    public string $title = '';
    public ?string $due_date = null;
    public ?string $link = null;
    public ?string $description = null;

    public function save()
    {
        $this->validate([
            'title' => ['required', 'string', 'min:5'],
            'due_date' => ['nullable', 'date'],
            'link' => ['nullable', 'url'],
            'description' => ['nullable', 'string'],
        ]);

        Auth::user()->tasks()->create([
            'title' => $this->title,
            'due_date' => $this->due_date ?: null,
            'original_due_date' => $this->due_date ?: null,
            'link' => $this->link ?: null,
            'description' => $this->description ?: null,
        ]);

        return redirect()->route('tasks');
    }
}; ?>
<x-page-container>
    <x-page-header>
        <flux:heading size="xl">{{ __('Create Task') }}</flux:heading>
    </x-page-header>

    <x-page-content>
        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('Task title...') }}" required />

            <x-date-picker model="due_date" :label="__('Due Date')" />

            <flux:input wire:model="link" :label="__('Link')" placeholder="https://..." type="url" />

            <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Add some notes...') }}" rows="4" />

            <div class="flex items-center gap-4 pt-4">
                <flux:button variant="primary" type="submit">{{ __('Save Task') }}</flux:button>
                <flux:button variant="subtle" :href="route('tasks')" wire:navigate>{{ __('Cancel') }}</flux:button>
            </div>
        </form>
    </x-page-content>
</x-page-container>
