<?php

namespace Database\Seeders;

use App\Models\BilliardTable;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\TableSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TableSessionSeeder extends Seeder
{
    public function run(): void
    {
        $customer1 = User::where('email', 'customer1@billiards.com')->first();
        $customer2 = User::where('email', 'customer2@billiards.com')->first();
        $customer3 = User::where('email', 'customer3@billiards.com')->first();
        $staff     = User::where('email', 'staff@billiards.com')->first();

        $tableB01 = BilliardTable::where('table_number', 'B01')->first();
        $tableB02 = BilliardTable::where('table_number', 'B02')->first();
        $tableS01 = BilliardTable::where('table_number', 'S01')->first();
        $tableC01 = BilliardTable::where('table_number', 'C01')->first();

        if (! $customer1 || ! $tableB01) {
            $this->command->warn('⚠️  TableSessionSeeder: Cần chạy UserSeeder và BilliardTableSeeder trước.');
            return;
        }

        // ── Phiên FINISHED với hóa đơn đầy đủ ──────────────────────

        // Phiên 1: Hôm qua 2 tiếng
        $start1 = Carbon::yesterday()->setTime(14, 0, 0);
        $end1   = Carbon::yesterday()->setTime(16, 0, 0);
        $hours1 = 2.00;
        $price1 = $hours1 * $tableB01->price_per_hour; // 160,000

        $session1 = TableSession::firstOrCreate(
            ['billiard_table_id' => $tableB01->id, 'start_time' => $start1],
            [
                'customer_id'  => $customer1->id,
                'end_time'     => $end1,
                'total_hours'  => $hours1,
                'table_price'  => $price1,
                'status'       => 'FINISHED',
            ]
        );

        // Hóa đơn phiên 1 (đã thanh toán)
        if ($session1->wasRecentlyCreated || ! $session1->invoice) {
            $cola    = Product::where('name', 'Coca-Cola lon')->first();
            $sting   = Product::where('name', 'Sting Dâu')->first();
            $snack   = Product::where('name', 'Khoai tây chiên')->first();

            $productsTotal = ($cola ? 15000 * 2 : 0)
                           + ($sting ? 18000 * 1 : 0)
                           + ($snack ? 10000 * 2 : 0);

            $subtotal = $price1 + $productsTotal;
            $discount = 0;

            $invoice1 = Invoice::firstOrCreate(
                ['table_session_id' => $session1->id],
                [
                    'staff_id'       => $staff?->id,
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'total_amount'   => $subtotal - $discount,
                    'payment_method' => 'CASH',
                    'payment_status' => 'PAID',
                ]
            );

            if ($invoice1->wasRecentlyCreated) {
                if ($cola)  InvoiceDetail::create(['invoice_id' => $invoice1->id, 'product_id' => $cola->id,  'quantity' => 2, 'unit_price' => $cola->price,  'total_price' => $cola->price * 2]);
                if ($sting) InvoiceDetail::create(['invoice_id' => $invoice1->id, 'product_id' => $sting->id, 'quantity' => 1, 'unit_price' => $sting->price, 'total_price' => $sting->price]);
                if ($snack) InvoiceDetail::create(['invoice_id' => $invoice1->id, 'product_id' => $snack->id, 'quantity' => 2, 'unit_price' => $snack->price, 'total_price' => $snack->price * 2]);
            }
        }

        // Phiên 2: Hôm qua, Snooker, 1.5 tiếng
        $start2 = Carbon::yesterday()->setTime(19, 0, 0);
        $end2   = Carbon::yesterday()->setTime(20, 30, 0);
        $hours2 = 1.50;
        $price2 = $hours2 * $tableS01->price_per_hour; // 180,000

        $session2 = TableSession::firstOrCreate(
            ['billiard_table_id' => $tableS01->id, 'start_time' => $start2],
            [
                'customer_id'  => $customer2->id,
                'end_time'     => $end2,
                'total_hours'  => $hours2,
                'table_price'  => $price2,
                'status'       => 'FINISHED',
            ]
        );

        if ($session2->wasRecentlyCreated || ! $session2->invoice) {
            $beer    = Product::where('name', 'Bia Heineken')->first();
            $popcorn = Product::where('name', 'Bắp rang bơ')->first();

            $productsTotal2 = ($beer ? 35000 * 4 : 0)
                            + ($popcorn ? 15000 * 1 : 0);

            $subtotal2 = $price2 + $productsTotal2;
            $discount2 = 20000; // Giảm giá 20k

            $invoice2 = Invoice::firstOrCreate(
                ['table_session_id' => $session2->id],
                [
                    'staff_id'       => $staff?->id,
                    'subtotal'       => $subtotal2,
                    'discount'       => $discount2,
                    'total_amount'   => $subtotal2 - $discount2,
                    'payment_method' => 'BANKING',
                    'payment_status' => 'PAID',
                ]
            );

            if ($invoice2->wasRecentlyCreated) {
                if ($beer)    InvoiceDetail::create(['invoice_id' => $invoice2->id, 'product_id' => $beer->id,    'quantity' => 4, 'unit_price' => $beer->price,    'total_price' => $beer->price * 4]);
                if ($popcorn) InvoiceDetail::create(['invoice_id' => $invoice2->id, 'product_id' => $popcorn->id, 'quantity' => 1, 'unit_price' => $popcorn->price, 'total_price' => $popcorn->price]);
            }
        }

        // Phiên 3: 3 ngày trước, Carom, chưa có khách cụ thể
        $start3 = Carbon::now()->subDays(3)->setTime(10, 0, 0);
        $end3   = Carbon::now()->subDays(3)->setTime(12, 30, 0);
        $hours3 = 2.50;
        $price3 = $hours3 * $tableC01->price_per_hour; // 250,000

        $session3 = TableSession::firstOrCreate(
            ['billiard_table_id' => $tableC01->id, 'start_time' => $start3],
            [
                'customer_id'  => $customer3->id,
                'end_time'     => $end3,
                'total_hours'  => $hours3,
                'table_price'  => $price3,
                'status'       => 'FINISHED',
            ]
        );

        if ($session3->wasRecentlyCreated || ! $session3->invoice) {
            $water = Product::where('name', 'Nước suối')->first();
            $chips = Product::where('name', 'Khoai tây chiên')->first();

            $productsTotal3 = ($water ? 8000 * 3 : 0)
                            + ($chips ? 10000 * 1 : 0);

            $subtotal3 = $price3 + $productsTotal3;

            $invoice3 = Invoice::firstOrCreate(
                ['table_session_id' => $session3->id],
                [
                    'staff_id'       => $staff?->id,
                    'subtotal'       => $subtotal3,
                    'discount'       => 0,
                    'total_amount'   => $subtotal3,
                    'payment_method' => 'CASH',
                    'payment_status' => 'PAID',
                ]
            );

            if ($invoice3->wasRecentlyCreated) {
                if ($water) InvoiceDetail::create(['invoice_id' => $invoice3->id, 'product_id' => $water->id, 'quantity' => 3, 'unit_price' => $water->price, 'total_price' => $water->price * 3]);
                if ($chips) InvoiceDetail::create(['invoice_id' => $invoice3->id, 'product_id' => $chips->id, 'quantity' => 1, 'unit_price' => $chips->price, 'total_price' => $chips->price]);
            }
        }

        // ── Phiên PLAYING (đang chơi) ──────────────────────────────
        // Bàn B02 đang có khách chơi, chưa kết thúc
        $existingPlaying = TableSession::where('billiard_table_id', $tableB02->id)
            ->where('status', 'PLAYING')
            ->exists();

        if (! $existingPlaying) {
            TableSession::create([
                'billiard_table_id' => $tableB02->id,
                'customer_id'       => $customer1->id,
                'start_time'        => Carbon::now()->subMinutes(45),
                'end_time'          => null,
                'total_hours'       => 0,
                'table_price'       => 0,
                'status'            => 'PLAYING',
            ]);

            // Đổi trạng thái bàn B02 thành PLAYING
            $tableB02->update(['status' => 'PLAYING']);
        }

        $this->command->info('✅ TableSessions seeded: 3 FINISHED (với hóa đơn PAID) + 1 PLAYING');
    }
}
