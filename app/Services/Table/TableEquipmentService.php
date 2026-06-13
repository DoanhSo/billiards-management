<?php

namespace App\Services\Table;

use App\Models\BilliardTable;
use App\Models\TableEquipment;
use Illuminate\Database\Eloquent\Collection;

class TableEquipmentService
{
    /**
     * Lấy danh sách phụ kiện của một bàn.
     */
    public function getEquipmentsByTable(int $tableId): Collection
    {
        return TableEquipment::where('billiard_table_id', $tableId)->get();
    }

    /**
     * Thêm phụ kiện mới vào bàn.
     */
    public function addEquipment(int $tableId, array $data): TableEquipment
    {
        // Kiem tra xem ban co phu kien ten giong nhau chua, neu co thi cong don
        $existing = TableEquipment::where('billiard_table_id', $tableId)
            ->where('name', $data['name'])
            ->first();

        if ($existing) {
            $existing->quantity += $data['quantity'];
            $existing->save();
            return $existing;
        }

        return TableEquipment::create([
            'billiard_table_id' => $tableId,
            'name' => $data['name'],
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
        ]);
    }

    /**
     * Cập nhật thông tin phụ kiện.
     */
    public function updateEquipment(int $id, array $data): TableEquipment
    {
        $equipment = TableEquipment::findOrFail($id);
        $equipment->update([
            'name' => $data['name'],
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
        ]);

        return $equipment;
    }

    /**
     * Báo hỏng / báo mất phụ kiện (chuyển từ quantity sang broken_quantity).
     */
    public function reportBroken(int $id, int $brokenAmount, string $note = null): TableEquipment
    {
        $equipment = TableEquipment::findOrFail($id);

        if ($brokenAmount > $equipment->quantity) {
            $brokenAmount = $equipment->quantity;
        }

        $equipment->quantity -= $brokenAmount;
        $equipment->broken_quantity += $brokenAmount;
        
        if ($note) {
            $equipment->note = $note;
        }

        $equipment->save();

        return $equipment;
    }

    /**
     * Xóa phụ kiện khỏi bàn.
     */
    public function deleteEquipment(int $id): bool
    {
        $equipment = TableEquipment::findOrFail($id);
        return $equipment->delete();
    }
}
