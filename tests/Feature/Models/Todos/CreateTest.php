<?php

namespace Tests\Feature\Models\Todos;

use App\Enum\TodoPriority;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Livewire\Todos\CreateTodo;
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
            ->set('title', 'test todo')
            ->set('description', 'A description.')
            ->set('due_date', '2026-12-31')
            ->set('priority', TodoPriority::MEDIUM->value)
            ->call('createTodo')
            ->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => 'test todo',
            'priority' => 'medium',
        ]);
    }
}
