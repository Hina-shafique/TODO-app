<?php

namespace App\Livewire\Todos;

use App\Enum\TodoPriority;
use App\Enum\TodoStatus;
use App\Livewire\Forms\TodoForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Create Todo')]
class CreateTodo extends Component
{
    public TodoForm $form;

    public function mount(): void
    {
        $this->form->status = TodoStatus::PENDING->value;
    }

    public function createTodo(): void
    {
        $validated = $this->form->validate();

        Auth::user()->todos()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'due_date' => $validated['due_date'] ?: null,
            'priority' => $validated['priority'],
            'status' => TodoStatus::PENDING,
        ]);

        session()->flash('message', 'Todo created successfully.');

        $this->redirectRoute('todos.index');
    }

    public function render()
    {
        return view('livewire.todos.create-todo', [
            'priorities' => TodoPriority::cases(),
            'form' => $this->form,
        ]);
    }
}
