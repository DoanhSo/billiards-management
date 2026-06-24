<?php

namespace App\Services\Table;

use App\Models\BilliardTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Lớp TableService
 * 
 * Quản lý nghiệp vụ về bàn Bida.
 * Xử lý thêm, sửa, xóa bàn, quản lý trạng thái (Trống, Đang chơi, Đã đặt, Bảo trì)
 * và tự động khởi tạo các thiết bị/phụ kiện đi kèm khi tạo bàn mới.
 */
class TableService
{
    /**
     * Lấy danh sách tất cả bàn bida (có phân trang, tìm kiếm, lọc theo trạng thái).
     *
     * @param string $search Tìm kiếm theo tên/số bàn hoặc loại bàn
     * @param string $status Lọc theo trạng thái ('AVAILABLE', 'PLAYING', 'RESERVED', 'MAINTENANCE')
     * @param int $perPage Số bàn trên mỗi trang
     * @return LengthAwarePaginator
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
            ->latest() // Đưa bàn mới thêm lên đầu
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin chi tiết một bàn bida theo ID.
     *
     * @param int $id ID của bàn
     * @return BilliardTable
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getTableById(int $id): BilliardTable
    {
        return BilliardTable::findOrFail($id);
    }

    /**
     * Tạo một bàn bida mới trong hệ thống.
     * 
     * Nghiệp vụ phụ đi kèm: Ngay sau khi tạo bàn thành công, hệ thống sẽ tự động tạo ra
     * danh sách các phụ kiện mặc định (Cơ, Bi, Lơ, Lết, Găng tay) gắn liền với bàn này.
     *
     * @param array<string, mixed> $data Dữ liệu form tạo bàn
     * @return BilliardTable Bàn vừa được tạo
     */
    public function createTable(array $data): BilliardTable
    {
        // Mặc định bàn mới tạo ra luôn ở trạng thái sẵn sàng đón khách
        $data['status'] = $data['status'] ?? 'AVAILABLE';

        $table = BilliardTable::create($data);

        // Danh sách các phụ kiện/thiết bị mặc định cần có cho mỗi bàn bida
        $defaultEquipments = [
            ['name' => 'Cơ Bida', 'quantity' => 4],
            ['name' => 'Bộ Bi', 'quantity' => 1],
            ['name' => 'Lơ Bida', 'quantity' => 4],
            ['name' => 'Lết Bida', 'quantity' => 1],
            ['name' => 'Găng Tay', 'quantity' => 4],
        ];

        // Tự động seed dữ liệu vào bảng table_equipments
        foreach ($defaultEquipments as $eq) {
            $table->equipments()->create([
                'name' => $eq['name'],
                'quantity' => $eq['quantity'],
                'broken_quantity' => 0, // Mới tạo thì chưa có đồ hỏng
                'note' => 'Theo máy',
            ]);
        }

        return $table;
    }

    /**
     * Cập nhật thông tin cơ bản của bàn (số bàn, loại bàn, đơn giá).
     * Không áp dụng cho việc đổi status (đổi status dùng updateTableStatus).
     *
     * @param int $id ID bàn
     * @param array<string, mixed> $data Dữ liệu cập nhật
     * @return BilliardTable
     */
    public function updateTable(int $id, array $data): BilliardTable
    {
        $table = $this->getTableById($id);
        $table->update($data);

        return $table->fresh();
    }

    /**
     * Chuyển đổi trạng thái hoạt động của bàn.
     * Trạng thái hợp lệ: AVAILABLE | RESERVED | PLAYING | MAINTENANCE.
     * 
     * Nghiệp vụ an toàn: Không cho phép đổi trạng thái của một bàn đang PLAYING (đang có khách chơi)
     * sang trạng thái khác (phải chờ khách thanh toán kết thúc phiên chơi trước).
     *
     * @param int $id ID bàn
     * @param string $status Trạng thái mới
     * @return BilliardTable
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 422 nếu vi phạm logic
     */
    public function updateTableStatus(int $id, string $status): BilliardTable
    {
        $allowed = ['AVAILABLE', 'RESERVED', 'PLAYING', 'MAINTENANCE'];

        // Kiểm tra tính hợp lệ của status đầu vào
        abort_unless(in_array($status, $allowed), 422, 'Trạng thái không hợp lệ.');

        $table = $this->getTableById($id);

        // Bảo vệ dữ liệu: Bàn đang có khách chơi (PLAYING) không thể đột ngột bị báo hỏng (MAINTENANCE) hay báo trống
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
     * Xóa bàn bida khỏi hệ thống.
     * 
     * Các ràng buộc an toàn (chỉ xóa được khi):
     * 1. Bàn đang ở trạng thái trống (AVAILABLE).
     * 2. Bàn không có phiên chơi nào đang diễn ra.
     * 3. Bàn không có lịch khách đặt trước (chưa hoàn thành).
     *
     * @param int $id ID bàn
     * @return bool True nếu xóa thành công
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 422 nếu vi phạm 1 trong 3 ràng buộc trên
     */
    public function deleteTable(int $id): bool
    {
        $table = $this->getTableById($id);

        // Ràng buộc 1
        abort_unless(
            $table->status === 'AVAILABLE',
            422,
            'Không thể xóa bàn đang được sử dụng hoặc bảo trì.'
        );

        // Ràng buộc 2
        $hasActiveSession = $table->tableSessions()->where('status', 'PLAYING')->exists();
        abort_if(
            $hasActiveSession,
            422,
            'Không thể xóa bàn vì đang có phiên chơi đang hoạt động.'
        );

        // Ràng buộc 3
        $hasFutureBookings = $table->bookings()->whereIn('status', ['PENDING', 'CONFIRMED'])->exists();
        abort_if(
            $hasFutureBookings,
            422,
            'Không thể xóa bàn vì đang có lịch đặt trước chưa hoàn thành.'
        );

        return (bool) $table->delete();
    }

    /**
     * Lấy danh sách toàn bộ các bàn đang trống (AVAILABLE).
     * Thường dùng để đổ dữ liệu vào ô chọn bàn khi bắt đầu phiên chơi nhanh.
     *
     * @return Collection Danh sách bàn trống
     */
    public function getAvailableTables(): Collection
    {
        return BilliardTable::available()->orderBy('table_number')->get();
    }

    /**
     * Lấy danh sách các bàn có khả năng cho phép đặt lịch trước (Booking).
     * 
     * Bao gồm: Bàn đang trống (AVAILABLE) hoặc bàn đã bị đặt trước đó (RESERVED).
     * Lưu ý: Bàn đang RESERVED vẫn có thể được đặt trước cho một ngày/khung giờ khác ở tương lai,
     * hệ thống sẽ dùng BookingService::checkTableAvailability để kiểm tra overlap giờ.
     * Bàn đang PLAYING (đang chơi chưa biết khi nào nghỉ) và MAINTENANCE (đang sửa) sẽ bị loại trừ.
     *
     * @return Collection Danh sách bàn có thể booking
     */
    public function getBookableTables(): Collection
    {
        return BilliardTable::whereIn('status', ['AVAILABLE', 'RESERVED'])
            ->orderBy('table_number')
            ->get();
    }

    /**
     * Thống kê tổng số lượng bàn chia theo từng trạng thái.
     * Phục vụ hiển thị trên màn hình Dashboard tổng quan.
     *
     * @return array<string, int> Mảng key là trạng thái, value là số lượng
     */
    public function getTableStatusSummary(): array
    {
        return BilliardTable::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}
