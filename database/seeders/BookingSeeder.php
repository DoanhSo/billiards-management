<?php

namespace Database\Seeders;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customer1 = User::where('email', 'customer1@billiards.com')->first();
        $customer2 = User::where('email', 'customer2@billiards.com')->first();
        $customer3 = User::where('email', 'customer3@billiards.com')->first();
        $customer4 = User::where('email', 'customer4@billiards.com')->first();

        $tableS01 = BilliardTable::where('table_number', 'S01')->first();
        $tableB01 = BilliardTable::where('table_number', 'B01')->first();
        $tableB02 = BilliardTable::where('table_number', 'B02')->first();
        $tableC01 = BilliardTable::where('table_number', 'C01')->first();

        if (! $customer1 || ! $tableS01) {
            $this->command->warn('⚠️  BookingSeeder: Cần chạy UserSeeder và BilliardTableSeeder trước.');
            return;
        }

        $bookings = [
            // Đặt bàn hôm nay - CONFIRMED (khách sắp đến)
            [
                'user_id'           => $customer1->id,
                'billiard_table_id' => $tableS01->id,
                'booking_date'      => Carbon::today()->toDateString(),
                'start_time'        => Carbon::today()->setTime(18, 0, 0),
                'end_time'          => Carbon::today()->setTime(20, 0, 0),
                'status'            => 'CONFIRMED',
                'note'              => 'Khách yêu cầu chuẩn bị gậy trước.',
            ],
            // Đặt bàn ngày mai - PENDING (chờ xác nhận)
            [
                'user_id'           => $customer2->id,
                'billiard_table_id' => $tableB01->id,
                'booking_date'      => Carbon::tomorrow()->toDateString(),
                'start_time'        => Carbon::tomorrow()->setTime(14, 0, 0),
                'end_time'          => Carbon::tomorrow()->setTime(16, 0, 0),
                'status'            => 'PENDING',
                'note'              => 'Nhóm 4 người, cần thêm ghế.',
            ],
            // Đặt bàn ngày kia - PENDING
            [
                'user_id'           => $customer3->id,
                'billiard_table_id' => $tableC01->id,
                'booking_date'      => Carbon::now()->addDays(2)->toDateString(),
                'start_time'        => Carbon::now()->addDays(2)->setTime(10, 0, 0),
                'end_time'          => Carbon::now()->addDays(2)->setTime(12, 0, 0),
                'status'            => 'PENDING',
                'note'              => null,
            ],
            // Lịch sử - COMPLETED (hôm qua)
            [
                'user_id'           => $customer1->id,
                'billiard_table_id' => $tableB02->id,
                'booking_date'      => Carbon::yesterday()->toDateString(),
                'start_time'        => Carbon::yesterday()->setTime(19, 0, 0),
                'end_time'          => Carbon::yesterday()->setTime(21, 0, 0),
                'status'            => 'COMPLETED',
                'note'              => 'Khách đã đến và chơi đủ.',
            ],
            // Lịch sử - CANCELLED
            [
                'user_id'           => $customer4->id,
                'billiard_table_id' => $tableS01->id,
                'booking_date'      => Carbon::now()->subDays(3)->toDateString(),
                'start_time'        => Carbon::now()->subDays(3)->setTime(15, 0, 0),
                'end_time'          => Carbon::now()->subDays(3)->setTime(17, 0, 0),
                'status'            => 'CANCELLED',
                'note'              => 'Khách bận đột xuất, hủy trước 2 tiếng.',
            ],
        ];

        foreach ($bookings as $data) {
            Booking::firstOrCreate(
                [
                    'user_id'           => $data['user_id'],
                    'billiard_table_id' => $data['billiard_table_id'],
                    'start_time'        => $data['start_time'],
                ],
                $data
            );
        }

        $this->command->info('✅ Bookings seeded: 2 PENDING + 1 CONFIRMED + 1 COMPLETED + 1 CANCELLED');
    }
}
