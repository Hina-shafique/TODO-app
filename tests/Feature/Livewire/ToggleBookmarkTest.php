<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Todo;
use Livewire\Livewire;
use App\Livewire\Todos\IndexTodo;

class ToggleBookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_bookmark_attaches_and_detaches(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create();

        $this->actingAs($user);

        Livewire::test(IndexTodo::class)
            ->call('toggleBookmark', $todo)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'todo_id' => $todo->id,
        ]);

        // call again to detach
        Livewire::test(IndexTodo::class)
            ->call('toggleBookmark', $todo)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'todo_id' => $todo->id,
        ]);
    }
}

