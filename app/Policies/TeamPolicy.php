<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $team->hasUser($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Team $team): bool
    {
        return $team->isAdmin($user);
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->isOwner($user);
    }

    public function invite(User $user, Team $team): bool
    {
        return $team->isAdmin($user);
    }

    public function removeMember(User $user, Team $team): bool
    {
        return $team->isAdmin($user);
    }

    public function changeRole(User $user, Team $team): bool
    {
        return $team->isOwner($user);
    }

    public function leave(User $user, Team $team): bool
    {
        return $team->hasUser($user) && ! $team->isOwner($user);
    }
}
