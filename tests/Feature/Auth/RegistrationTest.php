<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'Password@123')
            ->set('password_confirmation', 'Password@123');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_already_registered_email()
    {
        User::factory()->create([
            'email' => 'doe@example.com',
        ]);

        $component = Volt::test('pages.auth.register')
            ->set('name', 'John Doe')
            ->set('email', 'doe@example.com')
            ->set('password', 'Password@123')
            ->set('password_confirmation', 'Password@123');

        $component->call('register');

        $component->assertHasErrors(['email']);

        $this->assertCount(1, User::where('email', 'doe@example.com')->get());
    }

    public function test_invalid_name()
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'john@2')
            ->set('email', 'test@example.com')
            ->set('password', 'Password@123')
            ->set('password_confirmation', 'Password@123');

        $component->call('register');

        $component->assertHasErrors(['name']);

    }

    public function test_weak_password()
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'John Doe')
            ->set('email', 'test@example.com')
            ->set('password', 'weakpass')
            ->set('password_confirmation', 'weakpass');

        $component->call('register');

        $component->assertHasErrors(['password']);
    }

    public function test_mismatch_password_confirmation()
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'John Doe')
            ->set('email', 'test@example.com')
            ->set('password', 'Password@123')
            ->set('password_confirmation', 'Password@124');

        $component->call('register');

        $component->assertHasErrors(['password']);
    }
}
