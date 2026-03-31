<?php

namespace App\Console\Commands;

use Hash;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Enter admin name');
        $email = $this->ask('Enter admin email');
        $password = $this->secret('Enter admin password');

        //validation
        $validator = validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        if ($validator->fails()) {
            $this->info('Admin not created');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Create the admin user
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->info('Admin user created successfully!');
        return 0; // Exit code for success


    }
}
