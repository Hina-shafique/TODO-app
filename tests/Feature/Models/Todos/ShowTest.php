<?php

namespace Tests\Feature\Models\Todos;

use App\Enum\TodoStatus;
use App\Livewire\Todos\ShowTodo;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_todo_detail(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => 'My important task',
            'description' => 'Some details here',
        ]);

        Livewire::actingAs($user)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->assertSee('My important task')
            ->assertSee('Some details here')
            ->assertSet('isBookmarked', false);
    }

    public function test_returns_403_for_another_users_todo(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($other)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->assertStatus(403);
    }

    public function test_shows_overdue_badge_when_past_due_and_not_completed(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->overdue()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->assertSee('Overdue');
    }

    public function test_does_not_show_overdue_badge_when_completed(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->completed()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->assertDontSee('Overdue');
    }

    public function test_shows_completed_at_when_status_is_completed(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->completed()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->assertSee('Completed');
    }

    public function test_toggle_bookmark_adds_bookmark(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->call('toggleBookmark')
            ->assertSet('isBookmarked', true);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'todo_id' => $todo->id,
        ]);
    }

    public function test_toggle_bookmark_removes_existing_bookmark(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $user->bookmarks()->attach($todo->id);

        Livewire::actingAs($user)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->assertSet('isBookmarked', true)
            ->call('toggleBookmark')
            ->assertSet('isBookmarked', false);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'todo_id' => $todo->id,
        ]);
    }

    public function test_delete_removes_todo_and_redirects(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->call('delete')
            ->assertRedirect(route('todos.index'));

        $this->assertSoftDeleted('todos', ['id' => $todo->id]);
    }

    public function test_delete_returns_403_for_another_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($other)
            ->test(ShowTodo::class, ['todo' => $todo])
            ->assertStatus(403);

        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
    }

    public function test_detail_page_is_accessible_via_route(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('todos.show', $todo))
            ->assertOk()
            ->assertSeeLivewire(ShowTodo::class);
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $todo = Todo::factory()->create();

        $this->get(route('todos.show', $todo))
            ->assertRedirect(route('login'));
    }
}
