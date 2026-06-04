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

        return BilliardTable::create($data);
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
        $table->update(['status' => $status]);

        return $table->fresh();
    }

    /**
     * Xóa bàn (chỉ cho phép xóa khi bàn đang AVAILABLE).
     */
    public function deleteTable(int $id): bool
    {
        $table = $this->getTableById($id);

        abort_unless(
            $table->status === 'AVAILABLE',
            422,
            'Không thể xóa bàn đang được sử dụng hoặc đặt trước.'
        );

        return (bool) $table->delete();
    }

    /**
     * Lấy danh sách bàn trống.
     */
    public function getAvailableTables(): Collection
    {
        return BilliardTable::available()->orderBy('table_number')->get();
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
