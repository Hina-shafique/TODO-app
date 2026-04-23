<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;
use App\Models\Todo;

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

        $this->assertTrue($admin->canAccessPanel(new \Filament\Panel()));
        $this->assertFalse($member->canAccessPanel(new \Filament\Panel()));
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