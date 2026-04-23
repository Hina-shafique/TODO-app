<?php

namespace App\Livewire\Todos;

use App\Enum\TodoPriority;
use App\Enum\TodoStatus;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreateTodo extends Component
{
    public $title;
    public $description;
    public $due_date;
    public $priority = 'medium';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'priority' => ['required', new Enum(TodoPriority::class)],
        ];
    }

    public function createTodo()
    {
        $validated = $this->validate();

        Auth::user()->todos()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'status' => TodoStatus::PENDING,
        ]);

        session()->flash('message', 'Todo created successfully.');
        return redirect()->route('todos.index');
    }
    public function render()
    {
        return view('livewire.todos.create-todo', [
            'priorities' => TodoPriority::cases(),
        ]);
    }
}
