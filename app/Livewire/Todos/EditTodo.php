<?php

namespace App\Livewire\Todos;

use App\Enum\TodoPriority;
use App\Enum\TodoStatus;
use App\Livewire\Forms\TodoForm;
use App\Models\Todo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Edit Todo')]
class EditTodo extends Component
{
    public Todo $todo;

    public TodoForm $form;

    public function mount(Todo $todo): void
    {
        $this->authorize('update', $todo);

        $this->todo = $todo;
        $this->form->title = $todo->title;
        $this->form->description = $todo->description ?? '';
        $this->form->priority = $todo->priority->value;
        $this->form->status = $todo->status->value;
        $this->form->due_date = $todo->due_date?->format('Y-m-d') ?? '';
    }

    public function editTodo(): void
    {
        $validated = $this->form->validate();

        $wasCompleted = $this->todo->status === TodoStatus::COMPLETED;
        $nowCompleted = $validated['status'] === TodoStatus::COMPLETED->value;

        $this->todo->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?: null,
            'completed_at' => $nowCompleted
                ? ($wasCompleted ? $this->todo->completed_at : now())
                : null,
        ]);

        session()->flash('message', 'Todo updated successfully.');

        $this->redirectRoute('todos.index');
    }

    public function render()
    {
        return view('livewire.todos.edit-todo', [
            'priorities' => TodoPriority::cases(),
            'statuses' => TodoStatus::cases(),
        ]);
    }
}
