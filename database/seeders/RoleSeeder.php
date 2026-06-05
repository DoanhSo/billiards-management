<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',    'description' => 'Quản trị viên hệ thống'],
            ['name' => 'staff',    'description' => 'Nhân viên quán'],
            ['name' => 'customer', 'description' => 'Khách hàng'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        $this->command->info('✅ Roles seeded: admin, staff, customer');
    }
}
