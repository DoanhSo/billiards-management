<?php

namespace App\Services\Table;

use App\Models\BilliardTable;
use App\Models\TableEquipment;
use Illuminate\Database\Eloquent\Collection;

/**
 * Lớp TableEquipmentService
 * 
 * Quản lý phụ kiện đi kèm theo từng bàn bida (ví dụ: Cơ, Bi, Lơ, Lết, Găng tay).
 * Cung cấp chức năng thêm mới, cập nhật, báo mất/hỏng và xóa phụ kiện.
 */
class TableEquipmentService
{
    /**
     * Lấy danh sách toàn bộ phụ kiện đang được trang bị cho một bàn.
     *
     * @param int $tableId ID của bàn bida
     * @return Collection Danh sách phụ kiện
     */
    public function getEquipmentsByTable(int $tableId): Collection
    {
        return TableEquipment::where('billiard_table_id', $tableId)->get();
    }

    /**
     * Thêm phụ kiện mới vào bàn.
     * 
     * Logic nghiệp vụ:
     * - Nếu phụ kiện (cùng tên) đã tồn tại trên bàn này -> Cộng dồn số lượng.
     * - Nếu chưa tồn tại -> Tạo mới bản ghi phụ kiện.
     *
     * @param int $tableId ID của bàn bida
     * @param array<string, mixed> $data Dữ liệu phụ kiện (name, quantity, note)
     * @return TableEquipment Phụ kiện vừa được tạo hoặc cập nhật
     */
    public function addEquipment(int $tableId, array $data): TableEquipment
    {
        // Kiểm tra xem bàn có phụ kiện tên giống nhau chưa
        $existing = TableEquipment::where('billiard_table_id', $tableId)
            ->where('name', $data['name'])
            ->first();

        // Nếu có thì cộng dồn số lượng thay vì tạo dòng mới (tránh trùng lặp dữ liệu)
        if ($existing) {
            $existing->quantity += $data['quantity'];
            $existing->save();
            return $existing;
        }

        // Nếu chưa có thì tạo mới
        return TableEquipment::create([
            'billiard_table_id' => $tableId,
            'name'              => $data['name'],
            'quantity'          => $data['quantity'],
            'note'              => $data['note'] ?? null,
        ]);
    }

    /**
     * Cập nhật thông tin phụ kiện (Sửa tên, số lượng khả dụng, ghi chú).
     *
     * @param int $id ID của phụ kiện cần sửa
     * @param array<string, mixed> $data Dữ liệu cập nhật
     * @return TableEquipment
     */
    public function updateEquipment(int $id, array $data): TableEquipment
    {
        $equipment = TableEquipment::findOrFail($id);
        $equipment->update([
            'name'     => $data['name'],
            'quantity' => $data['quantity'],
            'note'     => $data['note'] ?? null,
        ]);

        return $equipment;
    }

    /**
     * Báo hỏng / báo mất phụ kiện.
     * 
     * Nghiệp vụ: Chuyển một phần (hoặc toàn bộ) số lượng phụ kiện từ
     * trạng thái dùng được (quantity) sang trạng thái hỏng/mất (broken_quantity).
     *
     * @param int $id ID của phụ kiện
     * @param int $brokenAmount Số lượng bị hỏng/mất
     * @param string|null $note Ghi chú lý do hỏng (nếu có)
     * @return TableEquipment
     */
    public function reportBroken(int $id, int $brokenAmount, string $note = null): TableEquipment
    {
        $equipment = TableEquipment::findOrFail($id);

        // Ngăn chặn việc báo hỏng nhiều hơn số lượng đang có
        if ($brokenAmount > $equipment->quantity) {
            $brokenAmount = $equipment->quantity;
        }

        // Trừ đi số lượng dùng được, cộng vào số lượng hỏng
        $equipment->quantity -= $brokenAmount;
        $equipment->broken_quantity += $brokenAmount;
        
        if ($note) {
            $equipment->note = $note;
        }

        $equipment->save();

        return $equipment;
    }

    /**
     * Xóa hoàn toàn phụ kiện khỏi bàn.
     *
     * @param int $id ID của phụ kiện
     * @return bool True nếu xóa thành công
     */
    public function deleteEquipment(int $id): bool
    {
        $equipment = TableEquipment::findOrFail($id);
        return (bool) $equipment->delete();
    }
}
