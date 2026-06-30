<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('My Bookmarks')]
class UserBookmarks extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user): void
    {
        abort_unless(Auth::id() === $user->id, 403);
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.users.user-bookmarks', [
            'bookmarked' => $this->user->bookmarks()
                ->orderBy('bookmarks.created_at', 'desc')
                ->paginate(10),
        ]);
    }
}
