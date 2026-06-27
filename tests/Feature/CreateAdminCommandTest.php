<?php

namespace Tests\Feature;

use App\Enum\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_promotes_existing_member_to_admin(): void
    {
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $this->artisan('app:create-admin')
            ->expectsTable(['ID', 'Name', 'Email'], [[$member->id, $member->name, $member->email]])
            ->expectsQuestion('Enter the email of the member to promote to admin', $member->email)
            ->expectsOutput("'{$member->name}' has been promoted to admin.")
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'role' => UserRole::ADMIN->value,
        ]);
    }

    public function test_fails_when_no_members_exist(): void
    {
        $this->artisan('app:create-admin')
            ->expectsOutput('No members found to promote.')
            ->assertExitCode(1);
    }

    public function test_fails_when_email_does_not_match_any_member(): void
    {
        User::factory()->create(['role' => UserRole::MEMBER]);

        $this->artisan('app:create-admin')
            ->expectsQuestion('Enter the email of the member to promote to admin', 'nobody@example.com')
            ->expectsOutput('No member found with that email.')
            ->assertExitCode(1);
    }

    public function test_fails_when_email_belongs_to_existing_admin(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        User::factory()->create(['role' => UserRole::MEMBER]);

        $this->artisan('app:create-admin')
            ->expectsQuestion('Enter the email of the member to promote to admin', $admin->email)
            ->expectsOutput('No member found with that email.')
            ->assertExitCode(1);
    }
}
