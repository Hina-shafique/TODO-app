<?php

namespace Tests\Feature\Database;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Todo;

class BookmarkUniqueConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_bookmarks_are_not_created(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create();

        $user->bookmarks()->attach($todo->id);
        // attaching again should not create a duplicate due to unique constraint
        try {
            $user->bookmarks()->attach($todo->id);
        } catch (\Throwable $e) {
            // Some DB drivers will throw for duplicates; swallow and continue
        }

        $count = \DB::table('bookmarks')
            ->where('user_id', $user->id)
            ->where('todo_id', $todo->id)
            ->count();

        $this->assertEquals(1, $count);
    }
}
