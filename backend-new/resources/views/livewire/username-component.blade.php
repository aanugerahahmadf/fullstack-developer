<form wire:submit="save" class="fi-sc-form">
    {{ $this->form }}

    <div class="fi-ac fi-align-end">
        <x-filament::button type="submit">
            {{ __('filament-edit-profile::default.save') }}
        </x-filament::button>
    </div>
</form>

<x-filament-actions::modals />