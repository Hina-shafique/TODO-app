<?php

namespace Tests\Feature\Models\Todos;

use App\Livewire\Todos\IndexTodo;
use App\Models\Todo;
use App\Models\User;
use livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_todos(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Todo::factory()->create([
            'user_id' => $userA->id,
            'title' => 'User A Task',
        ]);

        Todo::factory()->create([
            'user_id' => $userB->id,
            'title' => 'User B Task',
        ]);

        Livewire::actingAs($userA)
            ->test(IndexTodo::class)
            ->assertSee('User A Task')
            ->assertDontSee('User B Task');
    }

    public function test_delete_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(IndexTodo::class)
            ->call('delete', $todo->id);

        $this->assertSoftDeleted('todos', ['id' => $todo->id]);
    }

    public function test_delete_todo_unauthorized(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $todo = Todo::factory()->create(['user_id' => $userA->id]);

        Livewire::actingAs($userB)
            ->test(IndexTodo::class)
            ->call('delete', $todo->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
    }

    public function test_edit_todo_redirects(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(IndexTodo::class)
            ->call('edit', $todo->id)
            ->assertRedirect(route('todos.edit', $todo->id));
    }

}
