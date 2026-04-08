<?php 

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

new class extends Component {

    use WithFileUploads;

    public $avatar = null;

    public function save(): void
    {
        $user = Auth::user();
        $this->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ]);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');

            $user->update([
                'avatar' => $path,
            ]);

            $this->reset('avatar');
            $this->dispatch('avatar-updated');
        }
    }

}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Avatar') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Upload a new avatar to update your profile picture.') }}
        </p>
    </header>

    <form wire:submit.prevent="save" class="mt-6 space-y-6">
        <div>
            @if (auth()->user()->avatar)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}'s avatar"
                        class="w-40 h-40 rounded-full object-cover">
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />
            <x-text-input wire:model="avatar" id="avatar" type="file" class="block mt-1 w-full" />
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Save') }}
            </x-primary-button>

            <x-action-message class="me-3" on="Avatar-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>

</section>