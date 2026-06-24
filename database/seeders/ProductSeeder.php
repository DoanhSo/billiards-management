<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Danh mục ────────────────────────────────────────────────
        $drinks  = Category::firstOrCreate(['name' => 'Đồ uống'],  ['description' => 'Các loại nước giải khát']);
        $food    = Category::firstOrCreate(['name' => 'Đồ ăn'],    ['description' => 'Các món ăn nhẹ']);
        $snacks  = Category::firstOrCreate(['name' => 'Snack'],    ['description' => 'Bánh kẹo, đồ ăn vặt']);
        $tobacco = Category::firstOrCreate(['name' => 'Thuốc lá'], ['description' => 'Thuốc lá các loại']);

        // ── Sản phẩm ────────────────────────────────────────────────
        $products = [
            // Đồ uống
            ['category_id' => $drinks->id,  'name' => 'Coca-Cola lon',     'price' => 15000, 'quantity' => 120, 'status' => true,  'description' => 'Coca-Cola 330ml lon'],
            ['category_id' => $drinks->id,  'name' => 'Pepsi lon',         'price' => 15000, 'quantity' => 100, 'status' => true,  'description' => 'Pepsi 330ml lon'],
            ['category_id' => $drinks->id,  'name' => 'Sting Dâu',         'price' => 18000, 'quantity' => 80,  'status' => true,  'description' => 'Sting Dâu 330ml lon'],
            ['category_id' => $drinks->id,  'name' => 'Nước suối',         'price' => 8000,  'quantity' => 200, 'status' => true,  'description' => 'Nước suối LaVie 500ml'],
            ['category_id' => $drinks->id,  'name' => 'Trà đá',            'price' => 5000,  'quantity' => 999, 'status' => true,  'description' => 'Trà đá ly lớn'],
            ['category_id' => $drinks->id,  'name' => 'Bò húc',            'price' => 20000, 'quantity' => 60,  'status' => true,  'description' => 'Red Bull 250ml'],
            ['category_id' => $drinks->id,  'name' => 'Bia Heineken',      'price' => 35000, 'quantity' => 48,  'status' => true,  'description' => 'Bia Heineken 330ml lon'],
            ['category_id' => $drinks->id,  'name' => 'Bia Tiger',         'price' => 28000, 'quantity' => 48,  'status' => true,  'description' => 'Bia Tiger 330ml lon'],

            // Đồ ăn
            ['category_id' => $food->id,    'name' => 'Mì xào trứng',      'price' => 30000, 'quantity' => 20,  'status' => true,  'description' => 'Mì xào trứng tươi'],
            ['category_id' => $food->id,    'name' => 'Bánh tráng trộn',   'price' => 20000, 'quantity' => 30,  'status' => true,  'description' => 'Bánh tráng trộn đặc biệt'],
            ['category_id' => $food->id,    'name' => 'Hủ tiếu khô',       'price' => 35000, 'quantity' => 15,  'status' => true,  'description' => 'Hủ tiếu khô đặc biệt'],
            ['category_id' => $food->id,    'name' => 'Cơm chiên dương châu','price' => 40000,'quantity' => 10, 'status' => true,  'description' => 'Cơm chiên dương châu'],

            // Snack
            ['category_id' => $snacks->id,  'name' => 'Khoai tây chiên',   'price' => 10000, 'quantity' => 50,  'status' => true,  'description' => 'Lay\'s khoai tây chiên 35g'],
            ['category_id' => $snacks->id,  'name' => 'Bắp rang bơ',       'price' => 15000, 'quantity' => 40,  'status' => true,  'description' => 'Bắp rang bơ 100g'],
            ['category_id' => $snacks->id,  'name' => 'Hạt hướng dương',   'price' => 12000, 'quantity' => 30,  'status' => true,  'description' => 'Hạt hướng dương rang muối'],

            // Thuốc lá
            ['category_id' => $tobacco->id, 'name' => 'Thuốc lá Marlboro', 'price' => 28000, 'quantity' => 50,  'status' => true,  'description' => 'Marlboro Red gói 20 điếu'],
            ['category_id' => $tobacco->id, 'name' => 'Thuốc lá Thăng Long','price' => 15000,'quantity' => 40,  'status' => true,  'description' => 'Thăng Long gói 20 điếu'],
            ['category_id' => $tobacco->id, 'name' => 'Thuốc lá 555',      'price' => 32000, 'quantity' => 0,   'status' => false, 'description' => 'Thuốc lá 555 (tạm hết hàng)'],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['name' => $data['name'], 'category_id' => $data['category_id']],
                $data
            );
        }

        $this->command->info('✅ Products seeded: 4 categories + 18 products');
    }
}
