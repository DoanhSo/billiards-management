<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\BilliardTable;
use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::create(['name' => 'Admin', 'description' => 'Quản trị viên hệ thống']);
        $staffRole = Role::create(['name' => 'Staff', 'description' => 'Nhân viên quản lý quán']);
        $customerRole = Role::create(['name' => 'Customer', 'description' => 'Khách hàng thành viên']);

        // 2. Create Users
        $admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Nguyễn Admin',
            'email' => 'admin@example.com',
            'phone' => '0987654321',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        $staff = User::create([
            'role_id' => $staffRole->id,
            'name' => 'Trần Nhân Viên',
            'email' => 'staff@example.com',
            'phone' => '0912345678',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        $customer1 = User::create([
            'role_id' => $customerRole->id,
            'name' => 'Phạm Khách Hàng 1',
            'email' => 'customer1@example.com',
            'phone' => '0905111222',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        $customer2 = User::create([
            'role_id' => $customerRole->id,
            'name' => 'Lê Khách Hàng 2',
            'email' => 'customer2@example.com',
            'phone' => '0905333444',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        // 3. Create Billiard Tables
        $table1 = BilliardTable::create([
            'table_number' => '01',
            'table_type' => 'POOL',
            'price_per_hour' => 80000,
            'status' => 'AVAILABLE',
            'description' => 'Bàn bida lỗ gần quầy bar, thích hợp nhóm nhỏ.',
        ]);

        $table2 = BilliardTable::create([
            'table_number' => '02',
            'table_type' => 'POOL',
            'price_per_hour' => 80000,
            'status' => 'PLAYING',
            'description' => 'Bàn bida lỗ trung tâm quán, hướng nhìn rộng.',
        ]);

        $table3 = BilliardTable::create([
            'table_number' => '03',
            'table_type' => 'SNOOKER',
            'price_per_hour' => 120000,
            'status' => 'RESERVED',
            'description' => 'Bàn Snooker cao cấp tiêu chuẩn thi đấu.',
        ]);

        $table4 = BilliardTable::create([
            'table_number' => '04',
            'table_type' => 'CAROM',
            'price_per_hour' => 90000,
            'status' => 'MAINTENANCE',
            'description' => 'Bàn bida phăng (3 băng), đang thay mặt vải.',
        ]);

        $table5 = BilliardTable::create([
            'table_number' => '05',
            'table_type' => 'POOL',
            'price_per_hour' => 80000,
            'status' => 'AVAILABLE',
            'description' => 'Bàn bida lỗ trong khu vực phòng VIP riêng tư.',
        ]);

        // 4. Create Bookings
        Booking::create([
            'user_id' => $customer1->id,
            'billiard_table_id' => $table3->id,
            'booking_date' => Carbon::today(),
            'start_time' => Carbon::today()->setTime(18, 0, 0),
            'end_time' => Carbon::today()->setTime(20, 0, 0),
            'status' => 'CONFIRMED',
            'note' => 'Khách yêu cầu gậy gộc chuẩn bị trước.',
        ]);

        Booking::create([
            'user_id' => $customer2->id,
            'billiard_table_id' => $table1->id,
            'booking_date' => Carbon::tomorrow(),
            'start_time' => Carbon::tomorrow()->setTime(14, 0, 0),
            'end_time' => Carbon::tomorrow()->setTime(16, 0, 0),
            'status' => 'PENDING',
            'note' => 'Khách đặt trước 2 tiếng chơi.',
        ]);
        
        Booking::create([
            'user_id' => $customer1->id,
            'billiard_table_id' => $table5->id,
            'booking_date' => Carbon::yesterday(),
            'start_time' => Carbon::yesterday()->setTime(19, 0, 0),
            'end_time' => Carbon::yesterday()->setTime(21, 0, 0),
            'status' => 'COMPLETED',
            'note' => 'Khách đã thanh toán đầy đủ.',
        ]);
    }
}
