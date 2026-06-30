<?php

namespace Tests\Feature\Teams;

use App\Enum\TeamRole;
use App\Livewire\Teams\CreateTeam;
use App\Livewire\Teams\EditTeam;
use App\Livewire\Teams\IndexTeam;
use App\Livewire\Teams\ShowTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    // --- Index ---

    public function test_index_lists_teams_user_belongs_to(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($user->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        $otherTeam = Team::factory()->create();

        Livewire::actingAs($user)
            ->test(IndexTeam::class)
            ->assertSee($team->name)
            ->assertDontSee($otherTeam->name);
    }

    public function test_index_requires_auth(): void
    {
        $this->get(route('teams.index'))->assertRedirect(route('login'));
    }

    // --- Create ---

    public function test_create_team_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateTeam::class)
            ->set('name', 'Alpha Squad')
            ->set('description', 'Our team description')
            ->call('createTeam')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('teams', ['name' => 'Alpha Squad']);

        $team = Team::where('name', 'Alpha Squad')->first();
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => TeamRole::ADMIN->value,
        ]);
    }

    public function test_create_team_validates_required_name(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateTeam::class)
            ->set('name', '')
            ->call('createTeam')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_create_team_generates_unique_slug(): void
    {
        $user = User::factory()->create();
        Team::factory()->create(['name' => 'Alpha Squad', 'slug' => 'alpha-squad']);

        Livewire::actingAs($user)
            ->test(CreateTeam::class)
            ->set('name', 'Alpha Squad')
            ->call('createTeam');

        $this->assertDatabaseHas('teams', ['slug' => 'alpha-squad-1']);
    }

    // --- Show ---

    public function test_show_renders_team_for_member(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($user->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($user)
            ->test(ShowTeam::class, ['team' => $team])
            ->assertSee($team->name)
            ->assertSee($user->name);
    }

    public function test_show_returns_403_for_non_member(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        Livewire::actingAs($user)
            ->test(ShowTeam::class, ['team' => $team])
            ->assertStatus(403);
    }

    // --- Invite ---

    public function test_admin_can_invite_member_by_email(): void
    {
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $admin->id]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ShowTeam::class, ['team' => $team])
            ->set('inviteEmail', $invitee->email)
            ->call('inviteMember')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $invitee->id,
            'role' => TeamRole::MEMBER->value,
        ]);
    }

    public function test_invite_fails_for_non_existent_email(): void
    {
        $admin = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $admin->id]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ShowTeam::class, ['team' => $team])
            ->set('inviteEmail', 'nobody@example.com')
            ->call('inviteMember')
            ->assertHasErrors(['inviteEmail']);
    }

    public function test_invite_fails_if_user_already_a_member(): void
    {
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $admin->id]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ShowTeam::class, ['team' => $team])
            ->set('inviteEmail', $member->email)
            ->call('inviteMember')
            ->assertHasErrors(['inviteEmail']);
    }

    public function test_member_cannot_invite(): void
    {
        $member = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($member)
            ->test(ShowTeam::class, ['team' => $team])
            ->set('inviteEmail', 'someone@example.com')
            ->call('inviteMember')
            ->assertStatus(403);
    }

    // --- Remove member ---

    public function test_admin_can_remove_member(): void
    {
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $admin->id]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('removeMember', $member->id);

        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id,
        ]);
    }

    public function test_member_cannot_remove_other_members(): void
    {
        $member = User::factory()->create();
        $other = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);
        $team->members()->attach($other->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($member)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('removeMember', $other->id)
            ->assertStatus(403);
    }

    public function test_owner_cannot_be_removed(): void
    {
        $admin = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $admin->id]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('removeMember', $admin->id);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $admin->id,
        ]);
    }

    // --- Change role ---

    public function test_owner_can_promote_member_to_admin(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($owner)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('changeRole', $member->id, TeamRole::ADMIN->value);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id,
            'role' => TeamRole::ADMIN->value,
        ]);
    }

    public function test_non_owner_cannot_change_roles(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('changeRole', $member->id, TeamRole::ADMIN->value)
            ->assertStatus(403);
    }

    // --- Leave team ---

    public function test_member_can_leave_team(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($member)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('leaveTeam')
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id,
        ]);
    }

    public function test_owner_cannot_leave_their_own_team(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($owner)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('leaveTeam')
            ->assertStatus(403);
    }

    // --- Edit ---

    public function test_admin_can_update_team(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($owner)
            ->test(EditTeam::class, ['team' => $team])
            ->set('name', 'Updated Name')
            ->set('description', 'Updated description')
            ->call('updateTeam')
            ->assertHasNoErrors()
            ->assertRedirect(route('teams.show', $team));

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Updated Name']);
    }

    public function test_member_cannot_edit_team(): void
    {
        $member = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($member)
            ->test(EditTeam::class, ['team' => $team])
            ->assertStatus(403);
    }

    // --- Delete ---

    public function test_owner_can_delete_team(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($owner)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('deleteTeam')
            ->assertRedirect(route('teams.index'));

        $this->assertSoftDeleted('teams', ['id' => $team->id]);
    }

    public function test_non_owner_cannot_delete_team(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($admin->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('deleteTeam')
            ->assertStatus(403);
    }

    // --- Change role edge cases ---

    public function test_change_role_with_invalid_role_is_ignored(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);
        $team->members()->attach($member->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);

        Livewire::actingAs($owner)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('changeRole', $member->id, 'invalid_role');

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id,
            'role' => TeamRole::MEMBER->value,
        ]);
    }

    public function test_cannot_change_owner_role(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($owner)
            ->test(ShowTeam::class, ['team' => $team])
            ->call('changeRole', $owner->id, TeamRole::MEMBER->value);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $owner->id,
            'role' => TeamRole::ADMIN->value,
        ]);
    }

    // --- User can be in multiple teams ---

    public function test_user_can_belong_to_multiple_teams(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        $teamA->members()->attach($user->id, ['role' => TeamRole::MEMBER->value, 'joined_at' => now()]);
        $teamB->members()->attach($user->id, ['role' => TeamRole::ADMIN->value, 'joined_at' => now()]);

        Livewire::actingAs($user)
            ->test(IndexTeam::class)
            ->assertSee($teamA->name)
            ->assertSee($teamB->name);
    }
}
