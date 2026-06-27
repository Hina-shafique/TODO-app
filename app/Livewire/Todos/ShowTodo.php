<?php

namespace App\Livewire\Todos;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Todo Detail')]
class ShowTodo extends Component
{
    public Todo $todo;

    public bool $isBookmarked = false;

    public function mount(Todo $todo): void
    {
        $this->authorize('view', $todo);

        $this->todo = $todo;
        $this->isBookmarked = Auth::user()->bookmarks()->wherePivot('todo_id', $todo->id)->exists();
    }

    public function toggleBookmark(): void
    {
        $this->authorize('bookmark', $this->todo);

        $user = Auth::user();

        if ($this->isBookmarked) {
            $user->bookmarks()->detach($this->todo->id);
            $this->isBookmarked = false;
            session()->flash('message', 'Bookmark removed.');
        } else {
            $user->bookmarks()->attach($this->todo->id);
            $this->isBookmarked = true;
            session()->flash('message', 'Bookmarked successfully.');
        }
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->todo);

        $this->todo->delete();

        session()->flash('message', 'Todo deleted successfully.');

        $this->redirectRoute('todos.index');
    }

    public function render()
    {
        return view('livewire.todos.show-todo');
    }
}
