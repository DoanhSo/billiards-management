<?php

namespace App\Services\Session;

use App\Models\BilliardTable;
use App\Models\TableSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * Lớp TableSessionService
 * 
 * Quản lý nghiệp vụ phiên chơi (Table Session).
 * Xử lý việc bắt đầu chơi (tính giờ), kết thúc chơi, và tính toán số tiền
 * tương ứng với số giờ chơi của khách hàng.
 */
class TableSessionService
{
    /**
     * Lấy danh sách tất cả phiên chơi (phân trang, lọc trạng thái).
     *
     * @param string $status Trạng thái phiên chơi ('PLAYING' hoặc 'FINISHED')
     * @param int $perPage Số bản ghi trên mỗi trang
     * @return LengthAwarePaginator
     */
    public function getAllSessions(string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        return TableSession::with(['billiardTable', 'customer'])
            ->when($status, fn(Builder $query): Builder => $query->where('status', $status))
            ->latest() // Phiên mới nhất lên đầu
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin chi tiết của một phiên chơi theo ID.
     * 
     * Load sẵn các quan hệ: bàn (billiardTable), khách hàng (customer), và hóa đơn (invoice) kèm chi tiết sản phẩm.
     *
     * @param int $id ID của phiên chơi
     * @return TableSession
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getSessionById(int $id): TableSession
    {
        return TableSession::with(['billiardTable', 'customer', 'invoice.invoiceDetails.product'])
            ->findOrFail($id);
    }

    /**
     * Bắt đầu một phiên chơi mới cho một bàn.
     * 
     * Nghiệp vụ:
     * 1. Kiểm tra trạng thái bàn (chỉ cho phép bàn trống hoặc đã được đặt trước).
     * 2. Đảm bảo không có phiên chơi nào khác đang diễn ra (PLAYING) trên bàn này.
     * 3. Khởi tạo phiên mới và đổi trạng thái bàn thành PLAYING.
     *
     * @param int $tableId ID của bàn bida
     * @param int|null $customerId ID khách hàng (nếu có khách hàng thành viên)
     * @return TableSession Trả về phiên chơi vừa tạo
     * @throws ValidationException
     */
    public function startSession(int $tableId, ?int $customerId = null): TableSession
    {
        $table = BilliardTable::findOrFail($tableId);

        // Bàn phải ở trạng thái Sẵn sàng hoặc Đã đặt chỗ thì mới được phép mở
        if (! in_array($table->status, ['AVAILABLE', 'RESERVED'])) {
            throw ValidationException::withMessages([
                'billiard_table_id' => ["Bàn {$table->table_number} hiện không thể bắt đầu phiên chơi (trạng thái: {$table->status})."],
            ]);
        }

        // Kiểm tra an toàn: Đảm bảo không có phiên nào đang PLAYING trên bàn này
        $activeSessions = TableSession::where('billiard_table_id', $tableId)
            ->where('status', 'PLAYING')
            ->exists();

        if ($activeSessions) {
            throw ValidationException::withMessages([
                'billiard_table_id' => ["Bàn {$table->table_number} đang có phiên chơi chưa kết thúc."],
            ]);
        }

        // Tạo phiên chơi mới (giờ bắt đầu là hiện tại)
        $session = TableSession::create([
            'billiard_table_id' => $tableId,
            'customer_id'       => $customerId,
            'start_time'        => now(),
            'status'            => 'PLAYING',
            'total_hours'       => 0, // Sẽ tính khi kết thúc
            'table_price'       => 0, // Sẽ tính khi kết thúc
        ]);

        // Cập nhật trạng thái bàn sang đang chơi
        $table->update(['status' => 'PLAYING']);

        return $session->load(['billiardTable', 'customer']);
    }

    /**
     * Kết thúc phiên chơi.
     * 
     * Nghiệp vụ:
     * 1. Ghi nhận thời gian kết thúc.
     * 2. Tính toán tổng số giờ đã chơi (từ start_time đến end_time).
     * 3. Tính tiền bàn dựa trên đơn giá của bàn và tổng giờ chơi.
     * 4. Giải phóng bàn (đổi trạng thái về AVAILABLE).
     *
     * @param int $sessionId ID của phiên chơi cần đóng
     * @return TableSession
     * @throws ValidationException Nếu phiên đã đóng từ trước
     */
    public function endSession(int $sessionId): TableSession
    {
        $session = $this->getSessionById($sessionId);

        // Ngăn chặn việc đóng 2 lần cho cùng một phiên
        if ($session->status !== 'PLAYING') {
            throw ValidationException::withMessages([
                'status' => ['Phiên chơi này đã kết thúc.'],
            ]);
        }

        $endTime    = now();
        $totalHours = $this->calculateTotalHours($session, $endTime);
        $tablePrice = $this->calculateTablePrice($session, $totalHours);

        // Cập nhật số liệu vào Database
        $session->update([
            'end_time'    => $endTime,
            'total_hours' => $totalHours,
            'table_price' => $tablePrice,
            'status'      => 'FINISHED',
        ]);

        // Trả bàn về trạng thái trống (AVAILABLE) để đón khách khác
        $session->billiardTable->update(['status' => 'AVAILABLE']);

        return $session->fresh(['billiardTable', 'customer', 'invoice']);
    }

    /**
     * Tính tổng số giờ chơi.
     * 
     * Tính toán khoảng cách (phút) giữa thời gian bắt đầu và kết thúc,
     * sau đó quy đổi ra giờ và làm tròn đến 2 chữ số thập phân.
     *
     * @param TableSession $session Phiên chơi
     * @param Carbon|null $endTime Thời gian kết thúc (mặc định lấy hiện tại nếu chưa đóng)
     * @return float Tổng số giờ (ví dụ: 1.5 giờ = 1 tiếng rưỡi)
     */
    public function calculateTotalHours(TableSession $session, ?Carbon $endTime = null): float
    {
        $end     = $endTime ?? ($session->end_time ?? now());
        $minutes = $session->start_time->diffInMinutes($end);

        // Chia 60 để ra giờ, làm tròn 2 chữ số (VD: 90 phút = 1.5 giờ)
        return round($minutes / 60, 2);
    }

    /**
     * Tính tiền bàn dựa trên số giờ chơi và đơn giá của bàn đó.
     *
     * @param TableSession $session Phiên chơi
     * @param float $totalHours Tổng số giờ đã chơi
     * @return float Tổng tiền bàn (làm tròn 2 chữ số thập phân)
     */
    public function calculateTablePrice(TableSession $session, float $totalHours): float
    {
        // Ép kiểu float để đảm bảo tính toán chính xác
        $pricePerHour = (float) $session->billiardTable->price_per_hour;

        // Thành tiền = Đơn giá/Giờ * Số giờ
        return round($pricePerHour * $totalHours, 2);
    }

    /**
     * Lấy phiên chơi đang diễn ra (PLAYING) của một bàn cụ thể.
     * 
     * Dùng để kiểm tra nhanh xem bàn X hiện đang do ai chơi, đã chơi từ bao giờ.
     *
     * @param int $tableId ID của bàn bida
     * @return TableSession|null Trả về đối tượng TableSession nếu có, null nếu bàn đang trống
     */
    public function getActiveSessionByTable(int $tableId): ?TableSession
    {
        return TableSession::with(['billiardTable', 'customer'])
            ->where('billiard_table_id', $tableId)
            ->where('status', 'PLAYING')
            ->first();
    }
}
