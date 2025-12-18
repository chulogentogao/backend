<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=admin@inventory.com} {--password=admin123} {--name=Admin User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Check if admin already exists
        $existingAdmin = User::where('email', $email)->first();
        
        if ($existingAdmin) {
            if ($this->confirm("User with email {$email} already exists. Do you want to update it?")) {
                $existingAdmin->update([
                    'name' => $name,
                    'password' => Hash::make($password),
                    'is_restricted' => false,
                ]);
                
                if (!$existingAdmin->hasRole('admin')) {
                    $existingAdmin->assignRole('admin');
                }
                
                $this->info("Admin user updated successfully!");
                $this->info("Email: {$email}");
                $this->info("Password: {$password}");
                return Command::SUCCESS;
            } else {
                $this->info("Operation cancelled.");
                return Command::SUCCESS;
            }
        }

        // Create new admin user
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_restricted' => false,
        ]);

        $admin->assignRole('admin');

        $this->info("Admin user created successfully!");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");

        return Command::SUCCESS;
    }
}

