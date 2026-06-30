<?php

namespace App\Livewire\Projects;

use App\Enum\ProjectStatus;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Edit Project')]
class EditProject extends Component
{
    public Project $project;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:2000')]
    public string $description = '';

    #[Validate('required|string|in:active,completed,archived')]
    public string $status = '';

    #[Validate('nullable|date')]
    public string $dueDate = '';

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);

        $this->project = $project;
        $this->name = $project->name;
        $this->description = $project->description ?? '';
        $this->status = $project->status->value;
        $this->dueDate = $project->due_date?->format('Y-m-d') ?? '';
    }

    public function updateProject(): void
    {
        $this->authorize('update', $this->project);

        $this->validate();

        $this->project->update([
            'name' => $this->name,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'due_date' => $this->dueDate ?: null,
        ]);

        session()->flash('message', 'Project updated successfully.');

        $this->redirectRoute('projects.show', [$this->project->team, $this->project]);
    }

    public function render()
    {
        return view('livewire.projects.edit-project', [
            'statuses' => ProjectStatus::cases(),
        ]);
    }
}
