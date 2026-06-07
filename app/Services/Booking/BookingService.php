<?php

namespace App\Services\Booking;

use App\Models\BilliardTable;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Lấy danh sách tất cả đặt bàn (phân trang, tìm kiếm, lọc trạng thái).
     */
    public function getAllBookings(string $search = '', string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        return Booking::with(['user', 'billiardTable'])
            ->when($search, function (Builder $query) use ($search): void {
                $query->whereHas('user', fn(Builder $q): Builder => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('billiardTable', fn(Builder $q): Builder => $q->where('table_number', 'like', "%{$search}%"));
            })
            ->when($status, fn(Builder $query): Builder => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin đặt bàn theo ID.
     */
    public function getBookingById(int $id): Booking
    {
        return Booking::with(['user', 'billiardTable'])->findOrFail($id);
    }

    /**
     * Tạo đặt bàn mới.
     * Kiểm tra bàn có trống vào khung giờ được chọn không.
     *
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function createBooking(array $data): Booking
    {
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

        $data['status'] = 'PENDING';

        $booking = Booking::create($data);

        // Cập nhật trạng thái bàn sang RESERVED
        BilliardTable::findOrFail($data['billiard_table_id'])
            ->update(['status' => 'RESERVED']);

        return $booking->load(['user', 'billiardTable']);
    }

    /**
     * Xác nhận đặt bàn.
     *
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

        return $booking->fresh(['user', 'billiardTable']);
    }

    /**
     * Hủy đặt bàn.
     *
     * @throws ValidationException
     */
    public function cancelBooking(int $id): Booking
    {
        $booking = $this->getBookingById($id);

        if (in_array($booking->status, ['COMPLETED', 'CANCELLED'])) {
            throw ValidationException::withMessages([
                'status' => ['Không thể hủy đặt bàn đã hoàn thành hoặc đã hủy.'],
            ]);
        }

        $booking->update(['status' => 'CANCELLED']);

        // Trả bàn về AVAILABLE nếu đang RESERVED
        if ($booking->billiardTable->status === 'RESERVED') {
            $booking->billiardTable->update(['status' => 'AVAILABLE']);
        }

        return $booking->fresh(['user', 'billiardTable']);
    }

    /**
     * Hoàn tất đặt bàn.
     *
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

        // Trả bàn về AVAILABLE nếu đang RESERVED
        if ($booking->billiardTable->status === 'RESERVED') {
            $booking->billiardTable->update(['status' => 'AVAILABLE']);
        }

        return $booking->fresh(['user', 'billiardTable']);
    }

    /**
     * Lấy lịch sử đặt bàn (đã hoàn thành hoặc đã hủy).
     */
    public function getBookingHistory(int $perPage = 15): LengthAwarePaginator
    {
        return Booking::with(['user', 'billiardTable'])
            ->whereIn('status', ['COMPLETED', 'CANCELLED'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Kiểm tra bàn có trống trong khoảng thời gian không.
     * Trả về true nếu bàn trống (có thể đặt).
     */
    public function checkTableAvailability(
        int $tableId,
        string $startTime,
        string $endTime,
        ?int $excludeBookingId = null
    ): bool {
        return ! Booking::where('billiard_table_id', $tableId)
            ->whereNotIn('status', ['CANCELLED'])
            ->when($excludeBookingId, fn(Builder $q): Builder => $q->where('id', '!=', $excludeBookingId))
            ->where(function (Builder $query) use ($startTime, $endTime): void {
                // Kiểm tra overlap: booking hiện tại chồng lấp với booking mới
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->exists();
    }
}
