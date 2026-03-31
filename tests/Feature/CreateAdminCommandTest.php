<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;
    public function test_create_admin_user_successfully(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Enter admin name', 'jeff')
            ->expectsQuestion('Enter admin email', 'jeff@example.com')
            ->expectsQuestion('Enter admin password', 'Password@123')
            ->expectsOutput('Admin user created successfully!')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'jeff',
            'email' => 'jeff@example.com',
        ]);
    }

    public function test_fails_validation_for_admin_user_creation(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Enter admin name', 'jeff')
            ->expectsQuestion('Enter admin email', '')
            ->expectsQuestion('Enter admin password', 'short')
            ->expectsOutput('Admin not created')
            ->expectsOutput('The email field is required.')
            ->expectsOutput('The password field must be at least 8 characters.')
            ->assertExitCode(1);
    }

}
