<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    public function run()
    {
        // Create SuperAdmin User
        User::create([
            'username' => 'superadmin',
            'password' => Hash::make('superadmin123'),
            'role' => 'superadmin',
            'status' => 'active'
        ]);

        // Create Admin User
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        // Create Secretary User
        User::create([
            'username' => 'secretary',
            'password' => Hash::make('secretary123'),
            'role' => 'secretary',
            'status' => 'active'
        ]);

        // Create Treasurer User
        User::create([
            'username' => 'treasurer',
            'password' => Hash::make('treasurer123'),
            'role' => 'treasurer',
            'status' => 'active'
        ]);


        // Console Output
        $this->command->info('✅ Default users created successfully!');
        $this->command->info('🔹 SuperAdmin credentials: superadmin / superadmin123');
        $this->command->info('🔹 Admin credentials: admin / admin123');
        $this->command->info('🔹 Secretary credentials: secretary / secretary123');
        $this->command->info('🔹 Treasurer credentials: treasurer / treasurer123');
    }
}
