<?php

namespace App\Livewire\Forms;

use App\Enum\TodoPriority;
use App\Enum\TodoStatus;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TodoForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string')]
    public string $description = '';

    #[Validate(['required', new Enum(TodoPriority::class)])]
    public string $priority = 'medium';

    #[Validate('nullable|date|after_or_equal:today')]
    public string $due_date = '';

    #[Validate(['required', new Enum(TodoStatus::class)])]
    public string $status = '';
}
