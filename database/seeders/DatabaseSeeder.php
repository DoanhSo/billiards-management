<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Product;
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
        // 1. Run RoleSeeder and AdminSeeder
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
        ]);

        $staffRole = Role::where('name', 'staff')->firstOrFail();
        $customerRole = Role::where('name', 'customer')->firstOrFail();

        // 2. Create Staff and Customer Users
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'role_id' => $staffRole->id,
                'name' => 'Trần Nhân Viên',
                'phone' => '0912345678',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        // also seed test@example.com so we don't break login instructions we gave earlier
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'role_id' => $staffRole->id,
                'name' => 'Test User',
                'phone' => '0912345679',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        $customer1 = User::firstOrCreate(
            ['email' => 'customer1@example.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'Phạm Khách Hàng 1',
                'phone' => '0905111222',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@example.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'Lê Khách Hàng 2',
                'phone' => '0905333444',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        // 3. Create Billiard Tables
        $table1 = BilliardTable::firstOrCreate(
            ['table_number' => '01'],
            [
                'table_type' => 'POOL',
                'price_per_hour' => 80000,
                'status' => 'AVAILABLE',
                'description' => 'Bàn bida lỗ gần quầy bar, thích hợp nhóm nhỏ.',
            ]
        );

        $table2 = BilliardTable::firstOrCreate(
            ['table_number' => '02'],
            [
                'table_type' => 'POOL',
                'price_per_hour' => 80000,
                'status' => 'PLAYING',
                'description' => 'Bàn bida lỗ trung tâm quán, hướng nhìn rộng.',
            ]
        );

        $table3 = BilliardTable::firstOrCreate(
            ['table_number' => '03'],
            [
                'table_type' => 'SNOOKER',
                'price_per_hour' => 120000,
                'status' => 'RESERVED',
                'description' => 'Bàn Snooker cao cấp tiêu chuẩn thi đấu.',
            ]
        );

        $table4 = BilliardTable::firstOrCreate(
            ['table_number' => '04'],
            [
                'table_type' => 'CAROM',
                'price_per_hour' => 90000,
                'status' => 'MAINTENANCE',
                'description' => 'Bàn bida phăng (3 băng), đang thay mặt vải.',
            ]
        );

        $table5 = BilliardTable::firstOrCreate(
            ['table_number' => '05'],
            [
                'table_type' => 'POOL',
                'price_per_hour' => 80000,
                'status' => 'AVAILABLE',
                'description' => 'Bàn bida lỗ trong khu vực phòng VIP riêng tư.',
            ]
        );

        // 4. Create Bookings
        Booking::firstOrCreate(
            [
                'user_id' => $customer1->id,
                'billiard_table_id' => $table3->id,
                'start_time' => Carbon::today()->setTime(18, 0, 0)->toDateTimeString(),
            ],
            [
                'booking_date' => Carbon::today()->toDateString(),
                'end_time' => Carbon::today()->setTime(20, 0, 0)->toDateTimeString(),
                'status' => 'CONFIRMED',
                'note' => 'Khách yêu cầu gậy gộc chuẩn bị trước.',
            ]
        );

        Booking::firstOrCreate(
            [
                'user_id' => $customer2->id,
                'billiard_table_id' => $table1->id,
                'start_time' => Carbon::tomorrow()->setTime(14, 0, 0)->toDateTimeString(),
            ],
            [
                'booking_date' => Carbon::tomorrow()->toDateString(),
                'end_time' => Carbon::tomorrow()->setTime(16, 0, 0)->toDateTimeString(),
                'status' => 'PENDING',
                'note' => 'Khách đặt trước 2 tiếng chơi.',
            ]
        );
        
        Booking::firstOrCreate(
            [
                'user_id' => $customer1->id,
                'billiard_table_id' => $table5->id,
                'start_time' => Carbon::yesterday()->setTime(19, 0, 0)->toDateTimeString(),
            ],
            [
                'booking_date' => Carbon::yesterday()->toDateString(),
                'end_time' => Carbon::yesterday()->setTime(21, 0, 0)->toDateTimeString(),
                'status' => 'COMPLETED',
                'note' => 'Khách đã thanh toán đầy đủ.',
            ]
        );

        // 5. Seed Categories & Products (From Hieu's local stashed changes)
        $drinkCategory = Category::firstOrCreate(['name' => 'Đồ uống']);
        $foodCategory  = Category::firstOrCreate(['name' => 'Đồ ăn']);

        Product::firstOrCreate(
            ['name' => 'Coca-Cola'],
            [
                'category_id' => $drinkCategory->id,
                'price'       => 15000,
                'quantity'    => 100,
                'status'      => true,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Sting Dâu'],
            [
                'category_id' => $drinkCategory->id,
                'price'       => 18000,
                'quantity'    => 50,
                'status'      => true,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Mì xào trứng'],
            [
                'category_id' => $foodCategory->id,
                'price'       => 30000,
                'quantity'    => 15,
                'status'      => true,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Bánh tráng trộn'],
            [
                'category_id' => $foodCategory->id,
                'price'       => 20000,
                'quantity'    => 20,
                'status'      => true,
            ]
        );
    }
}
