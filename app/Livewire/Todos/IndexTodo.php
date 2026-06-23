<?php

namespace App\Livewire\Todos;

use App\Enum\TodoStatus;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('My Todos')]
class IndexTodo extends Component
{
    use WithPagination;

    public function delete(Todo $todo): void
    {
        $this->authorize('delete', $todo);
        $todo->delete();
        session()->flash('message', 'Todo deleted successfully.');
    }

    public function toggleStatus(Todo $todo): void
    {
        $this->authorize('toggleStatus', $todo);

        $nextStatus = match ($todo->status) {
            TodoStatus::PENDING => TodoStatus::IN_PROGRESS,
            TodoStatus::IN_PROGRESS => TodoStatus::COMPLETED,
            TodoStatus::COMPLETED => TodoStatus::PENDING,
        };

        $todo->update([
            'status' => $nextStatus,
            'completed_at' => ($nextStatus === TodoStatus::COMPLETED) ? Carbon::now() : null,
        ]);
    }

    public function toggleBookmark(Todo $todo): void
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();

        if ($user->bookmarks()->wherePivot('todo_id', $todo->id)->exists()) {
            $user->bookmarks()->detach($todo->id);
            session()->flash('message', 'Bookmark removed.');
        } else {
            $user->bookmarks()->attach($todo->id);
            session()->flash('message', 'Bookmarked successfully.');
        }
    }

    public function render()
    {
        return view('livewire.todos.index-todo', [
            'todos' => Auth::user()
                ->todos()
                ->latest()
                ->paginate(10),
            'bookmarkedIds' => Auth::user()
                ->bookmarks()
                ->pluck('bookmarks.todo_id')
                ->toArray(),
        ]);
    }
}
