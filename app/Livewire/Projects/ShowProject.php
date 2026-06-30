<?php

namespace App\Livewire\Projects;

use App\Enum\ProjectStatus;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Project')]
class ShowProject extends Component
{
    public Project $project;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
    }

    public function updateStatus(string $status): void
    {
        $this->authorize('update', $this->project);

        if (! ProjectStatus::tryFrom($status)) {
            return;
        }

        $this->project->update(['status' => $status]);

        session()->flash('message', 'Project status updated.');
    }

    public function deleteProject(): void
    {
        $this->authorize('delete', $this->project);

        $team = $this->project->team;

        $this->project->delete();

        session()->flash('message', 'Project deleted.');

        $this->redirectRoute('projects.index', $team);
    }

    public function render()
    {
        return view('livewire.projects.show-project', [
            'team' => $this->project->team,
            'currentUserIsAdmin' => $this->project->team->isAdmin(auth()->user()),
        ]);
    }
}
