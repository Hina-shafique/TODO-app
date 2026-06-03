<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Todo;
use Livewire\Livewire;
use App\Livewire\Users\UserBookmarks;

class UserBookmarksPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_bookmarks_page_shows_only_that_users_bookmarks(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $todoA1 = Todo::factory()->create();
        $todoA2 = Todo::factory()->create();
        $todoB1 = Todo::factory()->create();

        $userA->bookmarks()->attach([$todoA1->id, $todoA2->id]);
        $userB->bookmarks()->attach($todoB1->id);

        // Test as visitor viewing userA bookmarks
        Livewire::test(UserBookmarks::class, ['user' => $userA])
            ->assertSee($todoA1->title)
            ->assertSee($todoA2->title)
            ->assertDontSee($todoB1->title);
    }
}
