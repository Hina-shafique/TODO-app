<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserBookmarks extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        $bookmarked = $this->user->bookmarks()->orderBy('bookmarks.created_at', 'desc')->paginate(10);

        return view('livewire.users.user-bookmarks', [
            'bookmarked' => $bookmarked,
        ]);
    }
}
