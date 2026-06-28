<?php

namespace App\Livewire\Teams;

use App\Enum\TeamRole;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Create Team')]
class CreateTeam extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    public function createTeam(): void
    {
        $this->authorize('create', Team::class);

        $this->validate();

        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;

        while (Team::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        $team = Team::create([
            'owner_id' => Auth::id(),
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description ?: null,
        ]);

        $team->members()->attach(Auth::id(), [
            'role' => TeamRole::ADMIN->value,
            'joined_at' => now(),
        ]);

        session()->flash('message', 'Team created successfully.');

        $this->redirectRoute('teams.show', $team);
    }

    public function render()
    {
        return view('livewire.teams.create-team');
    }
}
