<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Thứ tự chạy quan trọng (foreign key dependency):
     *   1. RoleSeeder          → tạo roles
     *   2. AdminSeeder         → tạo tài khoản admin
     *   3. UserSeeder          → tạo staff + customers
     *   4. BilliardTableSeeder → tạo bàn billiards
     *   5. TableEquipmentSeeder→ tạo dụng cụ bàn
     *   6. BookingSeeder       → tạo lịch đặt bàn
     *   7. ProductSeeder       → tạo danh mục + sản phẩm
     *   8. TableSessionSeeder  → tạo phiên chơi + hóa đơn mẫu
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            BilliardTableSeeder::class,
            TableEquipmentSeeder::class,
            BookingSeeder::class,
            ProductSeeder::class,
            TableSessionSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🎱 Billiards Management — Dữ liệu mẫu đã được tạo thành công!');
        $this->command->newLine();
        $this->command->table(
            ['Tài khoản', 'Email', 'Mật khẩu'],
            [
                ['Super Admin', 'admin@billiards.com', 'Admin@123456'],
                ['Nhân viên 1', 'staff@billiards.com', 'password'],
                ['Nhân viên 2', 'staff2@billiards.com', 'password'],
                ['Khách hàng 1', 'customer1@billiards.com', 'password'],
                ['Khách hàng 2', 'customer2@billiards.com', 'password'],
            ]
        );
    }
}
