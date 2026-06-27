<?php

namespace App\Console\Commands;

use App\Enum\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Promote an existing member to admin';

    public function handle(): int
    {
        $members = User::where('role', UserRole::MEMBER)->get(['id', 'name', 'email']);

        if ($members->isEmpty()) {
            $this->error('No members found to promote.');

            return 1;
        }

        $this->table(['ID', 'Name', 'Email'], $members->toArray());

        $email = $this->ask('Enter the email of the member to promote to admin');

        $user = User::where('email', $email)->where('role', UserRole::MEMBER)->first();

        if (! $user) {
            $this->error('No member found with that email.');

            return 1;
        }

        $user->update(['role' => UserRole::ADMIN]);

        $this->info("'{$user->name}' has been promoted to admin.");

        return 0;
    }
}
