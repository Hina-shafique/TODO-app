<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
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
}