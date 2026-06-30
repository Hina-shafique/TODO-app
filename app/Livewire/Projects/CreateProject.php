<?php

namespace App\Livewire\Projects;

use App\Enum\ProjectStatus;
use App\Models\Project;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Create Project')]
class CreateProject extends Component
{
    public Team $team;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:2000')]
    public string $description = '';

    #[Validate('nullable|date|after_or_equal:today')]
    public string $dueDate = '';

    public function mount(Team $team): void
    {
        $this->authorize('create', [Project::class, $team]);
        $this->team = $team;
    }

    public function createProject(): void
    {
        $this->authorize('create', [Project::class, $this->team]);

        $this->validate();

        $project = $this->team->projects()->create([
            'name' => $this->name,
            'description' => $this->description ?: null,
            'status' => ProjectStatus::ACTIVE,
            'due_date' => $this->dueDate ?: null,
        ]);

        session()->flash('message', 'Project created successfully.');

        $this->redirectRoute('projects.show', [$this->team, $project]);
    }

    public function render()
    {
        return view('livewire.projects.create-project');
    }
}
