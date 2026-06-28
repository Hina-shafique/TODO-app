<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Edit Team')]
class EditTeam extends Component
{
    public Team $team;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    public function mount(Team $team): void
    {
        $this->authorize('update', $team);

        $this->team = $team;
        $this->name = $team->name;
        $this->description = $team->description ?? '';
    }

    public function updateTeam(): void
    {
        $this->authorize('update', $this->team);

        $this->validate();

        $this->team->update([
            'name' => $this->name,
            'description' => $this->description ?: null,
        ]);

        session()->flash('message', 'Team updated successfully.');

        $this->redirectRoute('teams.show', $this->team);
    }

    public function render()
    {
        return view('livewire.teams.edit-team');
    }
}
