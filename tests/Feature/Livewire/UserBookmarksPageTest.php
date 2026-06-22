<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Users\UserBookmarks;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

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

        Livewire::actingAs($userA)
            ->test(UserBookmarks::class, ['user' => $userA])
            ->assertSee($todoA1->title)
            ->assertSee($todoA2->title)
            ->assertDontSee($todoB1->title);
    }
}
