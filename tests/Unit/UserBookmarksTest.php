<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Todo;

class UserBookmarksTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_bookmarked_reflects_pivot_state(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create();

        $this->assertFalse($user->hasBookmarked($todo));

        $user->bookmarks()->attach($todo->id);
        $this->assertTrue($user->hasBookmarked($todo));

        $user->bookmarks()->detach($todo->id);
        $this->assertFalse($user->hasBookmarked($todo));
    }
}
