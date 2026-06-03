<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Todo;
use App\Models\User;
use App\Enum\TodoStatus;
use App\Enum\TodoPriority;

class TodoModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_casts_and_relations(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'status' => TodoStatus::IN_PROGRESS,
            'priority' => TodoPriority::HIGH,
        ]);

        $fresh = Todo::find($todo->id);

        $this->assertInstanceOf(TodoStatus::class, $fresh->status);
        $this->assertEquals(TodoStatus::IN_PROGRESS, $fresh->status);

        $this->assertInstanceOf(TodoPriority::class, $fresh->priority);
        $this->assertEquals(TodoPriority::HIGH, $fresh->priority);

        $this->assertEquals($user->id, $fresh->user->id);
    }

    public function test_scopes_pending_completed_overdue(): void
    {
        // create one of each state
        $pending = Todo::factory()->pending()->create();
        $completed = Todo::factory()->completed()->create();
        $overdue = Todo::factory()->overdue()->create();

        $this->assertTrue(Todo::pending()->where('id', $pending->id)->exists());
        $this->assertTrue(Todo::completed()->where('id', $completed->id)->exists());
        $this->assertTrue(Todo::overdue()->where('id', $overdue->id)->exists());
    }

    public function test_bookmarkedBy_returns_users_who_bookmarked(): void
    {
        $todo = Todo::factory()->create();

        $users = User::factory()->count(2)->create();

        // attach both users as bookmarks
        $todo->bookmarkedBy()->attach($users->pluck('id')->toArray());

        $fresh = $todo->fresh();

        $this->assertCount(2, $fresh->bookmarkedBy);
        foreach ($users as $user) {
            $this->assertTrue($fresh->bookmarkedBy->contains('id', $user->id));
        }
    }
}
