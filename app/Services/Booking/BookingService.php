<?php

namespace App\Services\Booking;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\TableSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * Lớp BookingService
 * 
 * Quản lý nghiệp vụ đặt bàn trước (Booking).
 * Xử lý luồng trạng thái đặt bàn: PENDING -> CONFIRMED -> COMPLETED (hoặc CANCELLED).
 * Đảm bảo logic kiểm tra chống trùng lấp giờ đặt (overlap) và chuyển đổi trạng thái bàn tương ứng.
 */
class BookingService
{
    /**
     * Lấy danh sách tất cả đặt bàn (phân trang, tìm kiếm, lọc trạng thái).
     * 
     * Hỗ trợ tìm kiếm theo tên/số điện thoại khách hàng hoặc số bàn.
     * Có thể lọc theo user_id nếu là khách hàng tự xem lịch sử của mình.
     *
     * @param string $search Từ khóa tìm kiếm
     * @param string $status Trạng thái đặt bàn
     * @param int $perPage Số bản ghi trên mỗi trang
     * @param int|null $userId Lọc theo ID người dùng (nếu có)
     * @return LengthAwarePaginator
     */
    public function getAllBookings(string $search = '', string $status = '', int $perPage = 15, ?int $userId = null): LengthAwarePaginator
    {
        return Booking::with(['user', 'billiardTable'])
            ->when($userId, fn(Builder $query) => $query->where('user_id', $userId))
            ->when($search, function (Builder $query) use ($search): void {
                $query->where(function (Builder $q) use ($search) {
                    // Tìm theo quan hệ User hoặc BilliardTable
                    $q->whereHas('user', fn(Builder $userQuery): Builder => $userQuery->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                      ->orWhereHas('billiardTable', fn(Builder $tableQuery): Builder => $tableQuery->where('table_number', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn(Builder $query): Builder => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin chi tiết một lượt đặt bàn theo ID.
     *
     * @param int $id ID đặt bàn
     * @return Booking
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getBookingById(int $id): Booking
    {
        return Booking::with(['user', 'billiardTable'])->findOrFail($id);
    }

    /**
     * Tạo đơn đặt bàn mới.
     * 
     * Nghiệp vụ:
     * 1. Không cho phép đặt giờ ở quá khứ.
     * 2. Phải kiểm tra bàn đó có trống trong khung giờ được chọn không (chống trùng lấp).
     * 3. Trạng thái khởi tạo luôn là 'PENDING'.
     *
     * @param array<string, mixed> $data Dữ liệu form đặt bàn
     * @return Booking
     * @throws ValidationException
     */
    public function createBooking(array $data): Booking
    {
        // Kiểm tra xem giờ bắt đầu có ở trong quá khứ không
        if (now()->gt($data['start_time'])) {
            throw ValidationException::withMessages([
                'start_time' => ['Giờ bắt đầu đặt bàn không thể ở trong quá khứ.'],
            ]);
        }

        // Kiểm tra bàn có bị trùng lấp lịch đặt trong khung giờ đó không
        $isAvailable = $this->checkTableAvailability(
            (int) $data['billiard_table_id'],
            $data['start_time'],
            $data['end_time']
        );

        if (! $isAvailable) {
            throw ValidationException::withMessages([
                'billiard_table_id' => ['Bàn này đã được đặt trong khung giờ bạn chọn. Vui lòng chọn giờ khác.'],
            ]);
        }

        // Chỉ tạo booking PENDING — KHÔNG đổi trạng thái bàn ngay.
        // Bàn chỉ chuyển thành RESERVED khi nhân viên xác nhận (confirmBooking).
        $data['status'] = 'PENDING';

        $booking = Booking::create($data);

        return $booking->load(['user', 'billiardTable']);
    }

    /**
     * Xác nhận đặt bàn (Chỉ dành cho Nhân viên/Admin).
     * 
     * Chuyển trạng thái booking từ PENDING sang CONFIRMED.
     * Đồng thời, nếu bàn đang trống (AVAILABLE), đổi trạng thái bàn sang RESERVED (đã giữ chỗ).
     *
     * @param int $id ID đặt bàn
     * @return Booking
     * @throws ValidationException
     */
    public function confirmBooking(int $id): Booking
    {
        $booking = $this->getBookingById($id);

        if ($booking->status !== 'PENDING') {
            throw ValidationException::withMessages([
                'status' => ['Chỉ có thể xác nhận đặt bàn ở trạng thái PENDING.'],
            ]);
        }

        $booking->update(['status' => 'CONFIRMED']);

        // Khi staff xác nhận → bàn chuyển RESERVED để báo hiệu đã được giữ chỗ.
        // Chỉ cập nhật nếu bàn đang AVAILABLE (tránh ghi đè lên trạng thái PLAYING hoặc MAINTENANCE).
        if ($booking->billiardTable->status === 'AVAILABLE') {
            $booking->billiardTable->update(['status' => 'RESERVED']);
        }

        return $booking->fresh(['user', 'billiardTable']);
    }

    /**
     * Hủy đơn đặt bàn.
     * 
     * Chuyển trạng thái booking thành CANCELLED.
     * Trả lại trạng thái bàn về AVAILABLE nếu bàn đang bị giữ (RESERVED).
     *
     * @param int $id ID đặt bàn
     * @return Booking
     * @throws ValidationException
     */
    public function cancelBooking(int $id): Booking
    {
        $booking = $this->getBookingById($id);

        // Không cho phép hủy những đơn đã hoàn tất hoặc đã bị hủy trước đó
        if (in_array($booking->status, ['COMPLETED', 'CANCELLED'])) {
            throw ValidationException::withMessages([
                'status' => ['Không thể hủy đặt bàn đã hoàn thành hoặc đã hủy.'],
            ]);
        }

        $booking->update(['status' => 'CANCELLED']);

        // Trả bàn về AVAILABLE nếu đang ở trạng thái RESERVED
        if ($booking->billiardTable->status === 'RESERVED') {
            $booking->billiardTable->update(['status' => 'AVAILABLE']);
        }

        return $booking->fresh(['user', 'billiardTable']);
    }

    /**
     * Hoàn tất quá trình đặt bàn khi khách đến chơi.
     * 
     * Chuyển booking sang trạng thái COMPLETED.
     * Tự động tạo một phiên chơi mới (TableSession) và đổi trạng thái bàn thành PLAYING.
     *
     * @param int $id ID đặt bàn
     * @return Booking
     * @throws ValidationException
     */
    public function completeBooking(int $id): Booking
    {
        $booking = $this->getBookingById($id);

        if ($booking->status !== 'CONFIRMED') {
            throw ValidationException::withMessages([
                'status' => ['Chỉ có thể hoàn tất đặt bàn ở trạng thái CONFIRMED.'],
            ]);
        }

        $booking->update(['status' => 'COMPLETED']);

        // Khi khách đến bắt đầu chơi:
        // 1. Đổi trạng thái bàn → PLAYING
        if ($booking->billiardTable->status === 'RESERVED') {
            $booking->billiardTable->update(['status' => 'PLAYING']);
        }

        // 2. Tự động mở một phiên chơi (TableSession) mới bằng thông tin lấy từ booking
        TableSession::create([
            'billiard_table_id' => $booking->billiard_table_id,
            'customer_id'       => $booking->user_id,
            'start_time'        => now(),
            'status'            => 'PLAYING',
            'total_hours'       => 0,
            'table_price'       => 0,
        ]);

        return $booking->fresh(['user', 'billiardTable']);
    }

    /**
     * Lấy lịch sử đặt bàn (chỉ các trạng thái đã đóng: COMPLETED, CANCELLED).
     *
     * @param int $perPage
     * @param int|null $userId
     * @return LengthAwarePaginator
     */
    public function getBookingHistory(int $perPage = 15, ?int $userId = null): LengthAwarePaginator
    {
        return Booking::with(['user', 'billiardTable'])
            ->whereIn('status', ['COMPLETED', 'CANCELLED'])
            ->when($userId, fn(Builder $query) => $query->where('user_id', $userId))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Kiểm tra khả năng cung cấp của bàn trong một khung giờ cụ thể.
     * 
     * Trả về true nếu bàn TRỐNG (không bị trùng giờ với bất kỳ booking hợp lệ nào).
     * Logic kiểm tra chồng lấp (Overlap): (start1 < end2) AND (end1 > start2)
     *
     * @param int $tableId ID bàn
     * @param string $startTime Giờ bắt đầu
     * @param string $endTime Giờ kết thúc
     * @param int|null $excludeBookingId ID của booking cần loại trừ (dùng khi cập nhật booking)
     * @return bool
     */
    public function checkTableAvailability(
        int $tableId,
        string $startTime,
        string $endTime,
        ?int $excludeBookingId = null
    ): bool {
        return ! Booking::where('billiard_table_id', $tableId)
            ->whereNotIn('status', ['CANCELLED']) // Bỏ qua các đơn đã hủy
            ->when($excludeBookingId, fn(Builder $q): Builder => $q->where('id', '!=', $excludeBookingId))
            ->where(function (Builder $query) use ($startTime, $endTime): void {
                // Kiểm tra overlap: booking đã có trong DB chồng lấp thời gian với booking mới
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    /**
     * Thống kê số lượng đặt bàn theo từng trạng thái.
     *
     * @param int|null $userId
     * @return array<string, int> Mảng với key là trạng thái, value là số lượng
     */
    public function getBookingStatusSummary(?int $userId = null): array
    {
        return Booking::selectRaw('status, COUNT(*) as total')
            ->when($userId, fn(Builder $query) => $query->where('user_id', $userId))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Lấy danh sách events (lịch đặt bàn) định dạng cho thư viện FullCalendar ở frontend.
     * 
     * Có tính năng bảo vệ quyền riêng tư: Nếu người xem là khách hàng (Customer) 
     * và không phải là chủ sở hữu của đơn đặt bàn, thông tin cá nhân (tên, số điện thoại) sẽ bị ẩn (ẩn thành 'Khách hàng', '***').
     *
     * @param string $start Ngày bắt đầu xem lịch
     * @param string $end Ngày kết thúc xem lịch
     * @param int|null $currentUserId ID của người dùng đang đăng nhập
     * @param bool $isCustomer Cờ báo người xem có phải là khách hàng không
     * @return array Danh sách sự kiện đã format cho FullCalendar
     */
    public function getEventsForCalendar(string $start, string $end, ?int $currentUserId = null, bool $isCustomer = false): array
    {
        $bookings = Booking::with(['user', 'billiardTable'])
            ->whereNotIn('status', ['CANCELLED'])
            ->where(function (Builder $query) use ($start, $end): void {
                $query->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end]);
            })
            ->get();

        $events = [];
        foreach ($bookings as $booking) {
            // Định nghĩa màu sắc hiển thị trên lịch tùy theo trạng thái
            $color = match ($booking->status) {
                'PENDING'   => '#f59e0b', // Màu vàng (Cảnh báo/Chờ xác nhận)
                'CONFIRMED' => '#10b981', // Màu xanh lá (Đã xác nhận)
                'COMPLETED' => '#3b82f6', // Màu xanh dương (Hoàn tất)
                default     => '#6b7280', // Màu xám
            };

            $isOwner = $currentUserId === $booking->user_id;
            
            // Xử lý bảo mật thông tin cá nhân
            if ($isCustomer && !$isOwner) {
                $title = 'Bàn ' . $booking->billiardTable->table_number . ' - Đã đặt';
                $customerName = 'Khách hàng';
                $phone = '***';
                $note = '';
            } else {
                $title = $booking->user->name . ' - ' . $booking->billiardTable->table_number;
                $customerName = $booking->user->name;
                $phone = $booking->user->phone;
                $note = $booking->note;
            }

            $events[] = [
                'id' => $booking->id,
                'resourceId' => $booking->billiard_table_id, // Dành cho timeline view (nhóm theo bàn)
                'title' => $title,
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'color' => $color,
                'extendedProps' => [
                    'status' => $booking->status,
                    'note' => $note,
                    'customer' => $customerName,
                    'phone' => $phone,
                    'table_number' => $booking->billiardTable->table_number,
                ],
            ];
        }

        return $events;
    }
}
