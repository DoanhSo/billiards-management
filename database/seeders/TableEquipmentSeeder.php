<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = \App\Models\BilliardTable::doesntHave('equipments')->get();
        $defaultEquipments = [
            ['name' => 'Cơ Bida', 'quantity' => 4],
            ['name' => 'Bộ Bi', 'quantity' => 1],
            ['name' => 'Lơ Bida', 'quantity' => 4],
            ['name' => 'Lết Bida', 'quantity' => 1],
            ['name' => 'Găng Tay', 'quantity' => 4],
        ];

        foreach ($tables as $table) {
            foreach ($defaultEquipments as $eq) {
                $table->equipments()->create([
                    'name' => $eq['name'],
                    'quantity' => $eq['quantity'],
                    'broken_quantity' => 0,
                    'note' => 'Theo máy',
                ]);
            }
        }
    }
}
