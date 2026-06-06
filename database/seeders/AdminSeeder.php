<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        User::firstOrCreate(
            ['email' => 'admin@billiards.com'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Super Admin',
                'phone'    => '0900000000',
                'password' => Hash::make('Admin@123456'),
                'status'   => true,
            ]
        );

        $this->command->info('✅ Admin created: admin@billiards.com / Admin@123456');
    }
}
