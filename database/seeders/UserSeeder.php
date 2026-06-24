<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $staffRole    = Role::where('name', 'staff')->firstOrFail();
        $customerRole = Role::where('name', 'customer')->firstOrFail();

        // ── Nhân viên ──────────────────────────────────────────────
        $staffs = [
            [
                'email'  => 'staff@billiards.com',
                'name'   => 'Trần Nhân Viên',
                'phone'  => '0912345678',
            ],
            [
                'email'  => 'staff2@billiards.com',
                'name'   => 'Nguyễn Thị Lan',
                'phone'  => '0912345699',
            ],
        ];

        foreach ($staffs as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'role_id'  => $staffRole->id,
                    'name'     => $data['name'],
                    'phone'    => $data['phone'],
                    'password' => Hash::make('password'),
                    'status'   => true,
                ]
            );
        }

        // ── Khách hàng ─────────────────────────────────────────────
        $customers = [
            ['email' => 'customer1@billiards.com', 'name' => 'Phạm Văn An',   'phone' => '0905111222'],
            ['email' => 'customer2@billiards.com', 'name' => 'Lê Thị Bình',   'phone' => '0905333444'],
            ['email' => 'customer3@billiards.com', 'name' => 'Hoàng Minh Tú', 'phone' => '0905555666'],
            ['email' => 'customer4@billiards.com', 'name' => 'Đặng Thị Cúc',  'phone' => '0905777888'],
            ['email' => 'customer5@billiards.com', 'name' => 'Vũ Quốc Huy',   'phone' => '0905999000'],
        ];

        foreach ($customers as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'role_id'  => $customerRole->id,
                    'name'     => $data['name'],
                    'phone'    => $data['phone'],
                    'password' => Hash::make('password'),
                    'status'   => true,
                ]
            );
        }

        $this->command->info('✅ Users seeded: 2 staff + 5 customers (password: password)');
    }
}
