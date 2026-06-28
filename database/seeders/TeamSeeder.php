<?php

namespace Database\Seeders;

use App\Enum\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = User::all();

        Team::factory(3)->create()->each(function (Team $team) use ($users): void {
            // Owner is already set by factory; add them as admin
            $team->members()->attach($team->owner_id, [
                'role' => TeamRole::ADMIN->value,
                'joined_at' => now(),
            ]);

            // Attach 3 random other members
            $users->whereNotIn('id', [$team->owner_id])
                ->random(3)
                ->each(fn (User $user) => $team->members()->attach($user->id, [
                    'role' => TeamRole::MEMBER->value,
                    'joined_at' => now(),
                ]));
        });
    }
}
