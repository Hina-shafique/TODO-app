<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Projects')]
class IndexProject extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->authorize('viewAny', [Project::class, $team]);
        $this->team = $team;
    }

    public function render()
    {
        return view('livewire.projects.index-project', [
            'projects' => $this->team->projects()->latest()->get(),
        ]);
    }
}
