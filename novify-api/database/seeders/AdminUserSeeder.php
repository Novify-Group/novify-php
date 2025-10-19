<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the first super admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@novify.com',
            'password' => Hash::make('admin123'),
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Super admin user created successfully!');
        $this->command->info('Email: admin@novify.com');
        $this->command->info('Password: admin123');
    }
}

