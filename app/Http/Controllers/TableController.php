<?php

namespace App\Http\Controllers;

use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;
use App\Services\Table\TableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function __construct(
        private readonly TableService $tableService
    ) {}

    /**
     * Danh sách bàn (có tìm kiếm + lọc trạng thái).
     */
    public function index(Request $request): View
    {
        $search  = $request->string('search')->toString();
        $status  = $request->string('status')->toString();
        $tables  = $this->tableService->getAllTables($search, $status);
        $summary = $this->tableService->getTableStatusSummary();

        return view('tables.index', compact('tables', 'search', 'status', 'summary'));
    }

    /**
     * Form thêm bàn mới.
     */
    public function create(): View
    {
        return view('tables.create');
    }

    /**
     * Lưu bàn mới.
     */
    public function store(StoreTableRequest $request): RedirectResponse
    {
        $this->tableService->createTable($request->validated());

        return redirect()->route('tables.index')
            ->with('success', 'Thêm bàn mới thành công.');
    }

    /**
     * Form chỉnh sửa thông tin bàn.
     */
    public function edit(int $id): View
    {
        $table = $this->tableService->getTableById($id);

        return view('tables.edit', compact('table'));
    }

    /**
     * Cập nhật thông tin bàn.
     */
    public function update(UpdateTableRequest $request, int $id): RedirectResponse
    {
        $this->tableService->updateTable($id, $request->validated());

        return redirect()->route('tables.index')
            ->with('success', 'Cập nhật thông tin bàn thành công.');
    }

    /**
     * Cập nhật trạng thái bàn (AVAILABLE | RESERVED | PLAYING | MAINTENANCE).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $status = $request->string('status')->toString();
        $table  = $this->tableService->updateTableStatus($id, $status);

        $labels = [
            'AVAILABLE'   => 'Sẵn sàng',
            'RESERVED'    => 'Đã đặt trước',
            'PLAYING'     => 'Đang chơi',
            'MAINTENANCE' => 'Bảo trì',
        ];

        return redirect()->back()
            ->with('success', "Bàn {$table->table_number} đã chuyển sang trạng thái: " . ($labels[$status] ?? $status));
    }

    /**
     * Xóa bàn.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->tableService->deleteTable($id);

        return redirect()->route('tables.index')
            ->with('success', 'Xóa bàn thành công.');
    }
}
