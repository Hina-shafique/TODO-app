<?php

namespace App\Livewire\Todos;

use Auth;
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
        ]);
    }
}
