<?php

namespace Tests\Feature\Models;

use App\Models\Team;
use App\Models\Todo;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_is_admin(): void
    {
        $user = User::factory()->admin()->create();
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isMember());
    }

    public function test_user_role_is_member(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isMember());
    }

    public function test_can_access_admin_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $this->assertTrue($admin->canAccessPanel(new Panel()));
        $this->assertFalse($member->canAccessPanel(new Panel()));
    }

    public function test_user_has_owned_teams(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $user->id]);

        $this->assertTrue($user->ownedTeams->contains($team));
    }

    public function test_user_has_many_todos(): void
    {
        $user = User::factory()->create();
        $todo1 = Todo::factory()->create(['user_id' => $user->id]);
        $todo2 = Todo::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->todos->contains($todo1));
        $this->assertTrue($user->todos->contains($todo2));
    }
}
