<?php 

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class EnsureUserIsActiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_is_logged_out_and_redirected(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSessionHas('last_activity_check')
            ->assertRedirect('/login')
            ->assertSessionHasErrors();
    }

    public function test_active_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }
}