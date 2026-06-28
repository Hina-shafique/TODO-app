<?php

namespace App\Livewire\Teams;

use App\Enum\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Team')]
class ShowTeam extends Component
{
    public Team $team;

    public bool $showInviteForm = false;

    #[Validate('required|email|exists:users,email')]
    public string $inviteEmail = '';

    public function mount(Team $team): void
    {
        $this->authorize('view', $team);
        $this->team = $team;
    }

    public function inviteMember(): void
    {
        $this->authorize('invite', $this->team);

        $this->validate();

        $user = User::where('email', $this->inviteEmail)->first();

        if ($this->team->hasUser($user)) {
            $this->addError('inviteEmail', 'This user is already a member of the team.');

            return;
        }

        $this->team->members()->attach($user->id, [
            'role' => TeamRole::MEMBER->value,
            'joined_at' => now(),
        ]);

        $this->inviteEmail = '';
        $this->showInviteForm = false;
        session()->flash('message', "{$user->name} has been added to the team.");
    }

    public function removeMember(int $userId): void
    {
        $this->authorize('removeMember', $this->team);

        $member = User::findOrFail($userId);

        if ($this->team->isOwner($member)) {
            session()->flash('error', 'The team owner cannot be removed.');

            return;
        }

        $this->team->members()->detach($userId);
        session()->flash('message', "{$member->name} has been removed from the team.");
    }

    public function changeRole(int $userId, string $role): void
    {
        $this->authorize('changeRole', $this->team);

        if (! in_array($role, [TeamRole::ADMIN->value, TeamRole::MEMBER->value])) {
            return;
        }

        $member = User::findOrFail($userId);

        if ($this->team->isOwner($member)) {
            session()->flash('error', "The owner's role cannot be changed.");

            return;
        }

        $this->team->members()->updateExistingPivot($userId, ['role' => $role]);
        session()->flash('message', "{$member->name}'s role updated to {$role}.");
    }

    public function leaveTeam(): void
    {
        $this->authorize('leave', $this->team);

        $this->team->members()->detach(Auth::id());

        session()->flash('message', 'You have left the team.');

        $this->redirectRoute('teams.index');
    }

    public function deleteTeam(): void
    {
        $this->authorize('delete', $this->team);

        $this->team->delete();

        session()->flash('message', 'Team deleted.');

        $this->redirectRoute('teams.index');
    }

    public function render()
    {
        return view('livewire.teams.show-team', [
            'members' => $this->team->members()->withPivot('role', 'joined_at')->get(),
            'currentUserIsAdmin' => $this->team->isAdmin(Auth::user()),
            'currentUserIsOwner' => $this->team->isOwner(Auth::user()),
        ]);
    }
}
