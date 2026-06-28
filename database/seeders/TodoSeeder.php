<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::all()->each(fn (User $user) => Todo::factory(5)->create(['user_id' => $user->id]));
    }
}
