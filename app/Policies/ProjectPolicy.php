<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user, ?Team $team = null): bool
    {
        if ($team === null) {
            return true;
        }

        return $team->hasUser($user);
    }

    public function view(User $user, Project $project): bool
    {
        return $project->team->hasUser($user);
    }

    public function create(User $user, ?Team $team = null): bool
    {
        if ($team === null) {
            return true;
        }

        return $team->isAdmin($user);
    }

    public function update(User $user, Project $project): bool
    {
        return $project->team->isAdmin($user);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->team->isAdmin($user);
    }
}
