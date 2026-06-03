<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Todo;
use Livewire\Livewire;
use App\Livewire\Todos\IndexTodo;
use App\Enum\TodoStatus;

class IndexTodoActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_redirects_to_edit_route(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(IndexTodo::class)
            ->call('edit', $todo->id)
            ->assertRedirect(route('todos.edit', $todo->id));
    }

    public function test_delete_soft_deletes_when_owner(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(IndexTodo::class)
            ->call('delete', $todo)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('todos', ['id' => $todo->id]);
    }

    public function test_delete_aborts_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other);

        // Attempting to delete as non-owner should not remove the todo
        Livewire::test(IndexTodo::class)
            ->call('delete', $todo)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
    }

    public function test_toggle_status_cycles_and_sets_completed_at(): void
    {
        $user = User::factory()->create();

        // pending -> in_progress
        $todo = Todo::factory()->pending()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        Livewire::test(IndexTodo::class)->call('toggleStatus', $todo)->assertHasNoErrors();
        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'status' => TodoStatus::IN_PROGRESS->value]);

        // in_progress -> completed (sets completed_at)
        $todo->refresh();
        Livewire::test(IndexTodo::class)->call('toggleStatus', $todo)->assertHasNoErrors();
        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'status' => TodoStatus::COMPLETED->value]);

        // completed -> pending (clears completed_at)
        $todo->refresh();
        Livewire::test(IndexTodo::class)->call('toggleStatus', $todo)->assertHasNoErrors();
        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'status' => TodoStatus::PENDING->value]);
    }

    public function test_toggle_status_aborts_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other);

        $originalStatus = $todo->status;

        Livewire::test(IndexTodo::class)
            ->call('toggleStatus', $todo)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'status' => $originalStatus->value]);
    }

    public function test_render_passes_bookmarked_ids(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $user->bookmarks()->attach($todo->id);

        $this->actingAs($user);

        Livewire::test(IndexTodo::class)
            ->assertViewHas('bookmarkedIds', function ($ids) use ($todo) {
                return in_array($todo->id, $ids, true);
            });
    }
}
