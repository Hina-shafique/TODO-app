<?php

namespace Tests\Feature;

use App\Enum\ProjectStatus;
use App\Enum\TeamRole;
use App\Filament\Admin\Resources\Projects\Pages\CreateProject;
use App\Filament\Admin\Resources\Projects\Pages\EditProject;
use App\Filament\Admin\Resources\Projects\Pages\ListProjects;
use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($this->admin, 'web');
        $this->withoutMiddleware([Authenticate::class]);
    }

    private function makeTeamWithAdmin(): Team
    {
        $team = Team::factory()->create(['owner_id' => $this->admin->id]);
        $team->members()->attach($this->admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        return $team;
    }

    public function test_can_render_the_project_list_page(): void
    {
        $this->get(ProjectResource::getUrl('index'))->assertSuccessful();
    }

    public function test_can_see_projects_in_the_table(): void
    {
        $projects = Project::factory()->count(3)->create();

        Livewire::test(ListProjects::class)
            ->assertCanSeeTableRecords($projects);
    }

    public function test_can_render_the_create_project_page(): void
    {
        $this->get(ProjectResource::getUrl('create'))->assertSuccessful();
    }

    public function test_can_create_a_project(): void
    {
        $team = $this->makeTeamWithAdmin();

        Livewire::test(CreateProject::class)
            ->fillForm([
                'team_id' => $team->id,
                'name' => 'New Project',
                'status' => ProjectStatus::ACTIVE->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', ['name' => 'New Project', 'team_id' => $team->id]);
    }

    public function test_validates_required_fields_on_create(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'team_id' => null,
                'name' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['team_id' => 'required', 'name' => 'required']);
    }

    public function test_can_render_the_edit_project_page(): void
    {
        $team = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id]);

        $this->get(ProjectResource::getUrl('edit', ['record' => $project]))->assertSuccessful();
    }

    public function test_can_update_a_project(): void
    {
        $team = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id, 'name' => 'Old Name']);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->fillForm([
                'team_id' => $team->id,
                'name' => 'Updated Name',
                'status' => ProjectStatus::COMPLETED->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Updated Name', $project->fresh()->name);
    }
}
