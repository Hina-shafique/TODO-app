<?php

namespace Tests\Feature\Models\Todos;

use App\Enum\TodoPriority;
use App\Livewire\Todos\CreateTodo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_todo(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateTodo::class)
            ->set('form.title', 'test todo')
            ->set('form.description', 'A description.')
            ->set('form.due_date', '2026-12-31')
            ->set('form.priority', TodoPriority::MEDIUM->value)
            ->call('createTodo')
            ->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => 'test todo',
            'priority' => 'medium',
        ]);
    }
}
