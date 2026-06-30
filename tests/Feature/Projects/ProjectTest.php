<?php

namespace Tests\Feature\Projects;

use App\Enum\ProjectStatus;
use App\Enum\TeamRole;
use App\Livewire\Projects\CreateProject;
use App\Livewire\Projects\EditProject;
use App\Livewire\Projects\IndexProject;
use App\Livewire\Projects\ShowProject;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private function makeTeamWithAdmin(): array
    {
        $admin = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $admin->id]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        return [$admin, $team];
    }

    private function addMember(Team $team): User
    {
        $member = User::factory()->create();
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        return $member;
    }

    // --- Index ---

    public function test_index_lists_projects_for_team_member(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $member = $this->addMember($team);
        $project = Project::factory()->create(['team_id' => $team->id]);
        $otherProject = Project::factory()->create();

        Livewire::actingAs($member)
            ->test(IndexProject::class, ['team' => $team])
            ->assertSee($project->name)
            ->assertDontSee($otherProject->name);
    }

    public function test_index_returns_403_for_non_member(): void
    {
        [, $team] = $this->makeTeamWithAdmin();
        $outsider = User::factory()->create();

        Livewire::actingAs($outsider)
            ->test(IndexProject::class, ['team' => $team])
            ->assertStatus(403);
    }

    public function test_index_requires_auth(): void
    {
        $team = Team::factory()->create();
        $this->get(route('projects.index', $team))->assertRedirect(route('login'));
    }

    // --- Create ---

    public function test_admin_can_create_project(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();

        Livewire::actingAs($admin)
            ->test(CreateProject::class, ['team' => $team])
            ->set('name', 'Website Redesign')
            ->set('description', 'A complete overhaul')
            ->call('createProject')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'team_id' => $team->id,
            'name' => 'Website Redesign',
            'status' => ProjectStatus::ACTIVE->value,
        ]);
    }

    public function test_member_cannot_create_project(): void
    {
        [, $team] = $this->makeTeamWithAdmin();
        $member = $this->addMember($team);

        Livewire::actingAs($member)
            ->test(CreateProject::class, ['team' => $team])
            ->assertStatus(403);
    }

    public function test_create_validates_required_name(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();

        Livewire::actingAs($admin)
            ->test(CreateProject::class, ['team' => $team])
            ->set('name', '')
            ->call('createProject')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_create_validates_name_max_length(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();

        Livewire::actingAs($admin)
            ->test(CreateProject::class, ['team' => $team])
            ->set('name', str_repeat('a', 256))
            ->call('createProject')
            ->assertHasErrors(['name' => 'max']);
    }

    public function test_create_sets_active_status_by_default(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();

        Livewire::actingAs($admin)
            ->test(CreateProject::class, ['team' => $team])
            ->set('name', 'New Project')
            ->call('createProject');

        $this->assertDatabaseHas('projects', [
            'name' => 'New Project',
            'status' => ProjectStatus::ACTIVE->value,
        ]);
    }

    public function test_create_with_due_date(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();

        Livewire::actingAs($admin)
            ->test(CreateProject::class, ['team' => $team])
            ->set('name', 'Sprint 1')
            ->set('dueDate', '2099-12-31')
            ->call('createProject')
            ->assertHasNoErrors();

        $project = Project::where('name', 'Sprint 1')->firstOrFail();
        $this->assertEquals('2099-12-31', $project->due_date->format('Y-m-d'));
    }

    // --- Show ---

    public function test_show_renders_project_for_member(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $member = $this->addMember($team);
        $project = Project::factory()->create(['team_id' => $team->id, 'name' => 'Design Sprint']);

        Livewire::actingAs($member)
            ->test(ShowProject::class, ['project' => $project])
            ->assertSee('Design Sprint')
            ->assertSee($project->status->label());
    }

    public function test_show_returns_403_for_non_member(): void
    {
        [, $team] = $this->makeTeamWithAdmin();
        $outsider = User::factory()->create();
        $project = Project::factory()->create(['team_id' => $team->id]);

        Livewire::actingAs($outsider)
            ->test(ShowProject::class, ['project' => $project])
            ->assertStatus(403);
    }

    public function test_show_displays_overdue_warning(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'due_date' => now()->subDay(),
            'status' => ProjectStatus::ACTIVE,
        ]);

        Livewire::actingAs($admin)
            ->test(ShowProject::class, ['project' => $project])
            ->assertSee('Overdue');
    }

    // --- Edit ---

    public function test_admin_can_edit_project(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id]);

        Livewire::actingAs($admin)
            ->test(EditProject::class, ['project' => $project])
            ->set('name', 'Updated Name')
            ->set('status', ProjectStatus::COMPLETED->value)
            ->call('updateProject')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Name',
            'status' => ProjectStatus::COMPLETED->value,
        ]);
    }

    public function test_member_cannot_edit_project(): void
    {
        [, $team] = $this->makeTeamWithAdmin();
        $member = $this->addMember($team);
        $project = Project::factory()->create(['team_id' => $team->id]);

        Livewire::actingAs($member)
            ->test(EditProject::class, ['project' => $project])
            ->assertStatus(403);
    }

    public function test_edit_validates_required_name(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id]);

        Livewire::actingAs($admin)
            ->test(EditProject::class, ['project' => $project])
            ->set('name', '')
            ->call('updateProject')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_edit_validates_invalid_status(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id]);

        Livewire::actingAs($admin)
            ->test(EditProject::class, ['project' => $project])
            ->set('status', 'invalid_status')
            ->call('updateProject')
            ->assertHasErrors(['status']);
    }

    // --- Status update from ShowProject ---

    public function test_admin_can_update_project_status(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id, 'status' => ProjectStatus::ACTIVE]);

        Livewire::actingAs($admin)
            ->test(ShowProject::class, ['project' => $project])
            ->call('updateStatus', ProjectStatus::COMPLETED->value);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => ProjectStatus::COMPLETED->value,
        ]);
    }

    public function test_member_cannot_update_project_status(): void
    {
        [, $team] = $this->makeTeamWithAdmin();
        $member = $this->addMember($team);
        $project = Project::factory()->create(['team_id' => $team->id, 'status' => ProjectStatus::ACTIVE]);

        Livewire::actingAs($member)
            ->test(ShowProject::class, ['project' => $project])
            ->call('updateStatus', ProjectStatus::COMPLETED->value)
            ->assertStatus(403);
    }

    // --- Delete ---

    public function test_admin_can_delete_project(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id]);

        Livewire::actingAs($admin)
            ->test(ShowProject::class, ['project' => $project])
            ->call('deleteProject')
            ->assertRedirect();

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_member_cannot_delete_project(): void
    {
        [, $team] = $this->makeTeamWithAdmin();
        $member = $this->addMember($team);
        $project = Project::factory()->create(['team_id' => $team->id]);

        Livewire::actingAs($member)
            ->test(ShowProject::class, ['project' => $project])
            ->call('deleteProject')
            ->assertStatus(403);
    }

    // --- Policy ---

    public function test_project_policy_create_without_team_context_allows_all(): void
    {
        $user = User::factory()->create();
        $this->assertTrue($user->can('create', Project::class));
    }

    // --- Status update edge cases ---

    public function test_update_status_with_invalid_value_is_ignored(): void
    {
        [$admin, $team] = $this->makeTeamWithAdmin();
        $project = Project::factory()->create(['team_id' => $team->id, 'status' => ProjectStatus::ACTIVE]);

        Livewire::actingAs($admin)
            ->test(ShowProject::class, ['project' => $project])
            ->call('updateStatus', 'invalid_status');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => ProjectStatus::ACTIVE->value,
        ]);
    }

    // --- Model ---

    public function test_project_belongs_to_team(): void
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf(Team::class, $project->team);
    }

    public function test_project_is_overdue_when_past_due_date_and_not_completed(): void
    {
        $project = Project::factory()->create([
            'due_date' => now()->subDay(),
            'status' => ProjectStatus::ACTIVE,
        ]);

        $this->assertTrue($project->isOverdue());
    }

    public function test_project_is_not_overdue_when_completed(): void
    {
        $project = Project::factory()->create([
            'due_date' => now()->subDay(),
            'status' => ProjectStatus::COMPLETED,
        ]);

        $this->assertFalse($project->isOverdue());
    }

    public function test_project_is_not_overdue_without_due_date(): void
    {
        $project = Project::factory()->create(['due_date' => null]);

        $this->assertFalse($project->isOverdue());
    }

    public function test_team_has_many_projects(): void
    {
        $team = Team::factory()->create();
        Project::factory()->count(3)->create(['team_id' => $team->id]);

        $this->assertCount(3, $team->projects);
    }
}
