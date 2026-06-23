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
        // Kiểm tra xem giờ bắt đầu có ở trong quá khứ không
        if (now()->gt($data['start_time'])) {
            throw ValidationException::withMessages([
                'start_time' => ['Giờ bắt đầu đặt bàn không thể ở trong quá khứ.'],
            ]);
        }

        // Kiểm tra bàn có trống trong khung giờ đó không
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
        // Bàn chỉ chuyển RESERVED khi staff xác nhận (confirmBooking).
        $data['status'] = 'PENDING';

        $booking = Booking::create($data);

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

        // Khi staff xác nhận → bàn chuyển RESERVED để báo hiệu đã được giữ chỗ.
        // Chỉ cập nhật nếu bàn đang AVAILABLE (tránh ghi đè PLAYING/MAINTENANCE).
        if ($booking->billiardTable->status === 'AVAILABLE') {
            $booking->billiardTable->update(['status' => 'RESERVED']);
        }

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

    public function getBookingStatusSummary(): array
    {
        return Booking::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Lấy danh sách events cho FullCalendar
     */
    public function getEventsForCalendar(string $start, string $end): array
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
            $color = match ($booking->status) {
                'PENDING'   => '#f59e0b', // warning
                'CONFIRMED' => '#10b981', // success
                'COMPLETED' => '#3b82f6', // primary
                default     => '#6b7280', // gray
            };

            $events[] = [
                'id' => $booking->id,
                'resourceId' => $booking->billiard_table_id, // For resource-based timeline view
                'title' => $booking->user->name . ' - ' . $booking->billiardTable->table_number,
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'color' => $color,
                'extendedProps' => [
                    'status' => $booking->status,
                    'note' => $booking->note,
                    'customer' => $booking->user->name,
                    'phone' => $booking->user->phone,
                    'table_number' => $booking->billiardTable->table_number,
                ],
            ];
        }

        return $events;
    }
}
