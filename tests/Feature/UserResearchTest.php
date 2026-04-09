<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\UserResource\Pages\ListUsers;
use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Filament\Admin\Resources\UserResource;
use Illuminate\Foundation\Testing\WithFaker;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use App\Models\User;
use Tests\TestCase;

class UserResearchTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'web');
        $this->withoutMiddleware([
            \Filament\Http\Middleware\Authenticate::class,
        ]);
    }

    public function test_it_can_access_list_page()
    {
        $this->get(UserResource::getUrl('index'))->assertSuccessful();

        Livewire::test(ListUsers::class)->assertCanSeeTableRecords(User::limit(10)->get());
    }

    public function test_can_render_create_page()
    {
        $this->get(UserResource::getUrl('create'))->assertSuccessful();
    }

    public function test_can_create_new_user()
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'new user',
                'email' => 'new@example.com',
                'password' => 'password',
                'role' => 'member',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function test_can_render_edit_page()
    {
        $user = User::factory()->create();

        $this->get(UserResource::getUrl('edit', ['record' => $user]))->assertSuccessful();
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create(['name' => 'old name']);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'name' => 'update name',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123'
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('update name', $user->fresh()->name);
    }

    public function test_it_can_toggle_user_status_action(): void
    {
        // This hits the custom Action in UserResource.php
        $user = User::factory()->create(['is_active' => true]);

        Livewire::test(ListUsers::class)
            ->callTableAction('toggle_status', $user);

        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_it_can_soft_delete_a_user(): void
    {
        $user = User::factory()->create();

        Livewire::test(ListUsers::class)
            ->callTableAction('delete', $user);

        $this->assertSoftDeleted($user);
    }
}
