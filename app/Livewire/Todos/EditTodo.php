<?php

namespace App\Livewire\Todos;

use App\Enum\TodoPriority;
use App\Enum\TodoStatus;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use App\Models\Todo;
use Livewire\Component;

#[Layout('layouts.app')]
class EditTodo extends Component
{
    public Todo $todo;
    public $title;
    public $description;
    public $priority;
    public $status;
    public $due_date;
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', new Enum(TodoPriority::class)],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', new Enum(TodoStatus::class)],
        ];
    }

    public function mount(Todo $todo)
    {
        if ($todo->user_id !== Auth::user()->id) {
            abort(403);
        }
        $this->todo = $todo;
        $this->title = $todo->title;
        $this->description = $todo->description;
        $this->priority = $todo->priority->value;
        $this->status = $todo->status->value;
        $this->due_date = $todo->due_date;

    }

    public function editTodo()
    {
        $validated = $this->validate();

        $this->todo->update($validated);
        session()->flash('message', 'Todo updated successfully.');
        return redirect()->route('todos.index');

    }
    public function render()
    {
        return view('livewire.todos.edit-todo', [
            'priorities' => TodoPriority::cases(),
            'statuses' => TodoStatus::cases(),
        ]);
    }
}
