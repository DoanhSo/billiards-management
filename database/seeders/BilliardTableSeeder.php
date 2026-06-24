<?php

namespace Database\Seeders;

use App\Models\BilliardTable;
use Illuminate\Database\Seeder;

class BilliardTableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            // POOL tables
            [
                'table_number'   => 'B01',
                'table_type'     => 'POOL',
                'price_per_hour' => 80000,
                'status'         => 'AVAILABLE',
                'description'    => 'Bàn bida lỗ gần quầy bar, thích hợp nhóm nhỏ.',
            ],
            [
                'table_number'   => 'B02',
                'table_type'     => 'POOL',
                'price_per_hour' => 80000,
                'status'         => 'AVAILABLE',
                'description'    => 'Bàn bida lỗ trung tâm quán, hướng nhìn rộng.',
            ],
            [
                'table_number'   => 'B03',
                'table_type'     => 'POOL',
                'price_per_hour' => 80000,
                'status'         => 'AVAILABLE',
                'description'    => 'Bàn bida lỗ khu vực phòng VIP riêng tư.',
            ],
            // SNOOKER tables
            [
                'table_number'   => 'S01',
                'table_type'     => 'SNOOKER',
                'price_per_hour' => 120000,
                'status'         => 'AVAILABLE',
                'description'    => 'Bàn Snooker cao cấp tiêu chuẩn thi đấu quốc tế.',
            ],
            [
                'table_number'   => 'S02',
                'table_type'     => 'SNOOKER',
                'price_per_hour' => 120000,
                'status'         => 'MAINTENANCE',
                'description'    => 'Bàn Snooker đang thay mặt vải, sẽ sẵn sàng trong 2 ngày.',
            ],
            // CAROM tables
            [
                'table_number'   => 'C01',
                'table_type'     => 'CAROM',
                'price_per_hour' => 100000,
                'status'         => 'AVAILABLE',
                'description'    => 'Bàn bida phăng (3 băng), tiêu chuẩn thi đấu.',
            ],
            [
                'table_number'   => 'C02',
                'table_type'     => 'CAROM',
                'price_per_hour' => 90000,
                'status'         => 'AVAILABLE',
                'description'    => 'Bàn bida phăng (1 băng) dành cho người mới học.',
            ],
        ];

        foreach ($tables as $data) {
            BilliardTable::firstOrCreate(
                ['table_number' => $data['table_number']],
                $data
            );
        }

        $this->command->info('✅ Billiard tables seeded: 3 POOL + 2 SNOOKER + 2 CAROM');
    }
}
