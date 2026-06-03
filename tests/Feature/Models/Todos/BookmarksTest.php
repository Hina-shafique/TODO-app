<?php

namespace Tests\Feature\models\todos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Todo;
use Tests\TestCase;

class BookmarksTest extends TestCase
{
    use RefreshDatabase, WithFaker;
        public function test_bookmarked_by_user(): void
    {
            $user = User::factory()->create();
            $todo = Todo::factory()->create();

            $user->bookmarks()->attach($todo->id);

            $this->assertDatabaseHas('bookmarks', [
                'user_id' => $user->id,
                'todo_id' => $todo->id,
            ]);
    }

    public function test_bookmark_by_many_users_of_same_todo(): void
    {
            $users = User::factory()->count(3)->create();
            $todo = Todo::factory()->create();

            foreach ($users as $user) {
                $user->bookmarks()->attach($todo->id);
            }

            foreach ($users as $user) {
                $this->assertDatabaseHas('bookmarks', [
                    'user_id' => $user->id,
                    'todo_id' => $todo->id,
                ]);
            }
    }
}
