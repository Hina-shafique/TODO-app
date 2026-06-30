<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Teams\Pages\CreateTeam;
use App\Filament\Admin\Resources\Teams\Pages\EditTeam;
use App\Filament\Admin\Resources\Teams\Pages\ListTeams;
use App\Filament\Admin\Resources\Teams\TeamResource;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin, 'web');
        $this->withoutMiddleware([
            Authenticate::class,
        ]);
    }

    public function test_can_render_the_team_list_page(): void
    {
        $this->get(TeamResource::getUrl('index'))->assertSuccessful();
    }

    public function test_can_see_teams_in_the_table(): void
    {
        $teams = Team::factory()->count(3)->create();

        Livewire::test(ListTeams::class)
            ->assertCanSeeTableRecords($teams);
    }

    public function test_can_render_the_create_team_page(): void
    {
        $this->get(TeamResource::getUrl('create'))->assertSuccessful();
    }

    public function test_can_create_a_team(): void
    {
        $owner = User::factory()->create();

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'New Team',
                'slug' => 'new-team',
                'description' => 'A test team',
                'owner_id' => $owner->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('teams', ['slug' => 'new-team']);
    }

    public function test_validates_required_fields_on_create(): void
    {
        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => null,
                'slug' => null,
                'owner_id' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required', 'slug' => 'required', 'owner_id' => 'required']);
    }

    public function test_can_render_the_edit_team_page(): void
    {
        $team = Team::factory()->create();

        $this->get(TeamResource::getUrl('edit', ['record' => $team]))->assertSuccessful();
    }

    public function test_can_update_a_team(): void
    {
        $team = Team::factory()->create(['name' => 'Old Name']);
        $owner = User::factory()->create();

        Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
                'slug' => $team->slug,
                'owner_id' => $owner->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Updated Name', $team->fresh()->name);
    }

    public function test_can_soft_delete_a_team_from_the_table(): void
    {
        $admin = User::first();
        $team = Team::factory()->create(['owner_id' => $admin->id]);

        Livewire::test(ListTeams::class)
            ->callTableAction('delete', $team);

        $this->assertSoftDeleted($team);
    }

    public function test_can_delete_returns_true_for_any_record(): void
    {
        $team = Team::factory()->create();
        $this->assertTrue(TeamResource::canDelete($team));
    }

    public function test_enforces_unique_slug_on_create(): void
    {
        $existing = Team::factory()->create(['slug' => 'taken-slug']);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'Another Team',
                'slug' => 'taken-slug',
                'owner_id' => $existing->owner_id,
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }
}
