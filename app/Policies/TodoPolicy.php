<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class TodoPolicy
{
    public function view(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function update(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function delete(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function toggleStatus(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function bookmark(User $user, Todo $todo): bool
    {
        return true;
    }
}
