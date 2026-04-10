<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Tests\TestCase;
use App\Enum\TodoStatus;
use App\Enum\TodoPriority;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_pending_todo_returned_by_scope(): void
    {
        $user = User::factory()->create();
        $pendingTodo = Todo::factory()->pending()->create(['user_id' => $user->id]);
        $completedTodo = Todo::factory()->completed()->create(['user_id' => $user->id]);

        $this->assertTrue(Todo::pending()->get()->contains($pendingTodo));
        $this->assertFalse(Todo::pending()->get()->contains($completedTodo));
    }

    public function test_completed_todo_returned_by_scope(): void
    {
        $user = User::factory()->create();
        $pendingTodo = Todo::factory()->pending()->create(['user_id' => $user->id]);
        $completedTodo = Todo::factory()->completed()->create(['user_id' => $user->id]);

        $this->assertTrue(Todo::completed()->get()->contains($completedTodo));
        $this->assertFalse(Todo::completed()->get()->contains($pendingTodo));
    }

    public function test_overdue_todo_returned_by_scope(): void
    {
        $user = User::factory()->create();
        $overdueTodo = Todo::factory()->overdue()->create(['user_id' => $user->id]);
        $pendingTodo = Todo::factory()->pending()->create(['user_id' => $user->id]);
        $completedTodo = Todo::factory()->completed()->create(['user_id' => $user->id]);

        $this->assertTrue(Todo::overdue()->get()->contains($overdueTodo));
        $this->assertFalse(Todo::overdue()->get()->contains($pendingTodo));
        $this->assertFalse(Todo::overdue()->get()->contains($completedTodo));
    }

    public function test_todo_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $todo->user);
        $this->assertEquals($user->id, $todo->user->id);
    }
}
