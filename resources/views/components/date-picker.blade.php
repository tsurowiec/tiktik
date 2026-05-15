@props(['model', 'label', 'placeholder' => 'yyyy-mm-dd'])

<div wire:ignore x-data="{
    init() {
        flatpickr(this.$el.querySelector('input'), {
            dateFormat: 'Y-m-d',
            defaultDate: $wire.{{ $model }} || '',
            disableMobile: true,
            onChange: (dates, str) => { $wire.{{ $model }} = str }
        });
    }
}">
    <flux:input wire:model="{{ $model }}" type="text" :label="$label" :placeholder="$placeholder" />
</div>
