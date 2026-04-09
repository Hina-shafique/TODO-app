<?php 

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $timezone;

    public function mount(): void
    {
        $this->timezone = Auth::user()->timezone;
    }

    public function timezoneUpdate()
    {
        $this->validate([
            'timezone' => ['required', 'string', 'timezone'],
        ]);

        Auth::user()->update([
            'timezone' => $this->timezone,
        ]);

        $this->dispatch('timezone-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Timezone') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Select your preferred timezone to ensure that all timestamps are displayed correctly.') }}
        </p>
    </header>

    <form wire:submit.prevent="timezoneUpdate" class="mt-6 space-y-6">
        <div>
            <label for="timezone" class="block text-sm font-medium text-gray-700">{{ __('Timezone') }}</label>
            <select id="timezone" wire:model="timezone"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach (timezone_identifiers_list() as $tz)
                    <option value="{{ $tz }}">{{ $tz }}</option>
                @endforeach
            </select>
            @error('timezone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="timezone-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>