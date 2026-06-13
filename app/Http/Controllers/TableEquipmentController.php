<?php

namespace App\Http\Controllers;

use App\Services\Table\TableEquipmentService;
use App\Services\Table\TableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TableEquipmentController extends Controller
{
    public function __construct(
        private readonly TableEquipmentService $equipmentService,
        private readonly TableService $tableService
    ) {}

    /**
     * Thêm phụ kiện mới vào bàn
     */
    public function store(Request $request, int $tableId): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $this->equipmentService->addEquipment($tableId, $data);

        return redirect()->route('tables.show', $tableId)
            ->with('success', 'Thêm phụ kiện thành công.');
    }

    /**
     * Cập nhật thông tin phụ kiện
     */
    public function update(Request $request, int $tableId, int $id): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        $this->equipmentService->updateEquipment($id, $data);

        return redirect()->route('tables.show', $tableId)
            ->with('success', 'Cập nhật phụ kiện thành công.');
    }

    /**
     * Báo hỏng/mất phụ kiện
     */
    public function report(Request $request, int $tableId, int $id): RedirectResponse
    {
        $request->validate([
            'broken_amount' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $this->equipmentService->reportBroken($id, $request->broken_amount, $request->note);

        return redirect()->route('tables.show', $tableId)
            ->with('warning', 'Đã báo hỏng/mất phụ kiện thành công.');
    }

    /**
     * Xóa phụ kiện khỏi bàn
     */
    public function destroy(int $tableId, int $id): RedirectResponse
    {
        $this->equipmentService->deleteEquipment($id);

        return redirect()->route('tables.show', $tableId)
            ->with('success', 'Đã xóa phụ kiện khỏi bàn.');
    }
}
