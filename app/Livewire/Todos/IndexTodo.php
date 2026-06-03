<?php

namespace App\Livewire\Todos;

use Auth;
use Carbon\Carbon;
use App\Enum\TodoStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Todo;

#[Layout('layouts.app')]
class IndexTodo extends Component
{
    use WithPagination;

    public function edit(int $id)
    {
        return redirect()->route('todos.edit', $id);
    }

    public function delete(Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) {
            abort(403);
        }
        $todo->delete();
        session()->flash('message', 'Todo deleted successfully.');
    }
    public function render()
    {
        return view('livewire.todos.index-todo', [
            'todos' => Auth::user()->todos()->latest()->paginate(10),
            'bookmarkedIds' => Auth::user()->bookmarks()->pluck('bookmarks.todo_id')->toArray(),
        ]);
    }

    public function toggleStatus(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }
        
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

    public function toggleBookmark(Todo $todo)
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
}
