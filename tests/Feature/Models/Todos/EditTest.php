<?php

namespace Tests\Feature\Models\Todos;

use App\Enum\TodoPriority;
use App\Livewire\Todos\EditTodo;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_edit_todo(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => 'test todo',
            'priority' => TodoPriority::MEDIUM,
        ]);

        Livewire::actingAs($user)
            ->test(EditTodo::class, ['todo' => $todo])
            ->set('form.title', 'updated todo')
            ->set('form.description', 'An updated description.')
            ->set('form.due_date', '2027-01-31')
            ->set('form.priority', TodoPriority::HIGH->value)
            ->call('editTodo')
            ->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'updated todo',
            'description' => 'An updated description.',
            'due_date' => '2027-01-31 00:00:00',
            'priority' => 'high',
        ]);
    }

    public function test_edit_todo_unauthorized(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => 'private task',
        ]);

        Livewire::actingAs($otherUser)
            ->test(EditTodo::class, ['todo' => $todo])
            ->assertStatus(403);
    }
}
