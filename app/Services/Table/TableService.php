<?php

namespace App\Services\Table;

use App\Models\BilliardTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TableService
{
    /**
     * Lấy danh sách tất cả bàn (có phân trang, tìm kiếm, lọc trạng thái).
     */
    public function getAllTables(string $search = '', string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        return BilliardTable::when($search, function (Builder $query) use ($search): void {
                $query->where(function (Builder $q) use ($search): void {
                    $q->where('table_number', 'like', "%{$search}%")
                      ->orWhere('table_type', 'like', "%{$search}%");
                });
            })
            ->when($status, fn(Builder $query): Builder => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin bàn theo ID.
     */
    public function getTableById(int $id): BilliardTable
    {
        return BilliardTable::findOrFail($id);
    }

    /**
     * Tạo bàn mới.
     *
     * @param array<string, mixed> $data
     */
    public function createTable(array $data): BilliardTable
    {
        $data['status'] = $data['status'] ?? 'AVAILABLE';

        $table = BilliardTable::create($data);

        // Thêm phụ kiện mặc định
        $defaultEquipments = [
            ['name' => 'Cơ Bida', 'quantity' => 4],
            ['name' => 'Bộ Bi', 'quantity' => 1],
            ['name' => 'Lơ Bida', 'quantity' => 4],
            ['name' => 'Lết Bida', 'quantity' => 1],
            ['name' => 'Găng Tay', 'quantity' => 4],
        ];

        foreach ($defaultEquipments as $eq) {
            $table->equipments()->create([
                'name' => $eq['name'],
                'quantity' => $eq['quantity'],
                'broken_quantity' => 0,
                'note' => 'Theo máy',
            ]);
        }

        return $table;
    }

    /**
     * Cập nhật thông tin bàn.
     *
     * @param array<string, mixed> $data
     */
    public function updateTable(int $id, array $data): BilliardTable
    {
        $table = $this->getTableById($id);
        $table->update($data);

        return $table->fresh();
    }

    /**
     * Cập nhật trạng thái bàn.
     * Trạng thái: AVAILABLE | RESERVED | PLAYING | MAINTENANCE
     */
    public function updateTableStatus(int $id, string $status): BilliardTable
    {
        $allowed = ['AVAILABLE', 'RESERVED', 'PLAYING', 'MAINTENANCE'];

        abort_unless(in_array($status, $allowed), 422, 'Trạng thái không hợp lệ.');

        $table = $this->getTableById($id);

        if ($table->status === 'PLAYING' && $status !== 'PLAYING') {
            $hasActiveSession = $table->tableSessions()->where('status', 'PLAYING')->exists();
            abort_if(
                $hasActiveSession,
                422,
                'Bàn đang có phiên chơi đang hoạt động, không thể chuyển trạng thái.'
            );
        }

        $table->update(['status' => $status]);

        return $table->fresh();
    }

    /**
     * Xóa bàn (chỉ cho phép xóa khi bàn đang AVAILABLE và không có lịch đặt trước).
     */
    public function deleteTable(int $id): bool
    {
        $table = $this->getTableById($id);

        abort_unless(
            $table->status === 'AVAILABLE',
            422,
            'Không thể xóa bàn đang được sử dụng hoặc bảo trì.'
        );

        $hasActiveSession = $table->tableSessions()->where('status', 'PLAYING')->exists();
        abort_if(
            $hasActiveSession,
            422,
            'Không thể xóa bàn vì đang có phiên chơi đang hoạt động.'
        );

        $hasFutureBookings = $table->bookings()->whereIn('status', ['PENDING', 'CONFIRMED'])->exists();
        abort_if(
            $hasFutureBookings,
            422,
            'Không thể xóa bàn vì đang có lịch đặt trước chưa hoàn thành.'
        );

        return (bool) $table->delete();
    }

    /**
     * Lấy danh sách bàn trống (status = AVAILABLE).
     */
    public function getAvailableTables(): Collection
    {
        return BilliardTable::available()->orderBy('table_number')->get();
    }

    /**
     * Lấy danh sách bàn có thể đặt lịch trước (AVAILABLE hoặc RESERVED).
     * Bàn RESERVED vẫn cho phép đặt khung giờ khác — hệ thống sẽ kiểm tra overlap.
     * Bàn PLAYING và MAINTENANCE không cho đặt.
     */
    public function getBookableTables(): Collection
    {
        return BilliardTable::whereIn('status', ['AVAILABLE', 'RESERVED'])
            ->orderBy('table_number')
            ->get();
    }

    /**
     * Thống kê số lượng bàn theo từng trạng thái.
     *
     * @return array<string, int>
     */
    public function getTableStatusSummary(): array
    {
        return BilliardTable::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}
