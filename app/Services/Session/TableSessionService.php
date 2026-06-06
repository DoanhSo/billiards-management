<?php

namespace App\Services\Session;

use App\Models\BilliardTable;
use App\Models\TableSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class TableSessionService
{
    /**
     * Lấy danh sách tất cả phiên chơi (phân trang, lọc trạng thái).
     */
    public function getAllSessions(string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        return TableSession::with(['billiardTable', 'customer'])
            ->when($status, fn(Builder $query): Builder => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin phiên chơi theo ID.
     */
    public function getSessionById(int $id): TableSession
    {
        return TableSession::with(['billiardTable', 'customer', 'invoice.invoiceDetails.product'])
            ->findOrFail($id);
    }

    /**
     * Bắt đầu phiên chơi cho một bàn.
     * Cập nhật trạng thái bàn sang PLAYING.
     *
     * @throws ValidationException
     */
    public function startSession(int $tableId, ?int $customerId = null): TableSession
    {
        $table = BilliardTable::findOrFail($tableId);

        if (! in_array($table->status, ['AVAILABLE', 'RESERVED'])) {
            throw ValidationException::withMessages([
                'billiard_table_id' => ["Bàn {$table->table_number} hiện không thể bắt đầu phiên chơi (trạng thái: {$table->status})."],
            ]);
        }

        // Kiểm tra không có phiên đang PLAYING nào trên bàn này
        $activeSessions = TableSession::where('billiard_table_id', $tableId)
            ->where('status', 'PLAYING')
            ->exists();

        if ($activeSessions) {
            throw ValidationException::withMessages([
                'billiard_table_id' => ["Bàn {$table->table_number} đang có phiên chơi chưa kết thúc."],
            ]);
        }

        $session = TableSession::create([
            'billiard_table_id' => $tableId,
            'customer_id'       => $customerId,
            'start_time'        => now(),
            'status'            => 'PLAYING',
            'total_hours'       => 0,
            'table_price'       => 0,
        ]);

        $table->update(['status' => 'PLAYING']);

        return $session->load(['billiardTable', 'customer']);
    }

    /**
     * Kết thúc phiên chơi.
     * Tính toán tổng giờ và tiền bàn.
     * Cập nhật trạng thái bàn về AVAILABLE.
     *
     * @throws ValidationException
     */
    public function endSession(int $sessionId): TableSession
    {
        $session = $this->getSessionById($sessionId);

        if ($session->status !== 'PLAYING') {
            throw ValidationException::withMessages([
                'status' => ['Phiên chơi này đã kết thúc.'],
            ]);
        }

        $endTime    = now();
        $totalHours = $this->calculateTotalHours($session, $endTime);
        $tablePrice = $this->calculateTablePrice($session, $totalHours);

        $session->update([
            'end_time'    => $endTime,
            'total_hours' => $totalHours,
            'table_price' => $tablePrice,
            'status'      => 'FINISHED',
        ]);

        // Trả bàn về AVAILABLE
        $session->billiardTable->update(['status' => 'AVAILABLE']);

        return $session->fresh(['billiardTable', 'customer', 'invoice']);
    }

    /**
     * Tính tổng số giờ chơi (làm tròn 2 chữ số thập phân).
     */
    public function calculateTotalHours(TableSession $session, ?Carbon $endTime = null): float
    {
        $end     = $endTime ?? ($session->end_time ?? now());
        $minutes = $session->start_time->diffInMinutes($end);

        return round($minutes / 60, 2);
    }

    /**
     * Tính tiền bàn dựa trên giờ chơi và giá bàn/giờ.
     */
    public function calculateTablePrice(TableSession $session, float $totalHours): float
    {
        $pricePerHour = (float) $session->billiardTable->price_per_hour;

        return round($pricePerHour * $totalHours, 2);
    }

    /**
     * Lấy phiên đang PLAYING của một bàn.
     */
    public function getActiveSessionByTable(int $tableId): ?TableSession
    {
        return TableSession::with(['billiardTable', 'customer'])
            ->where('billiard_table_id', $tableId)
            ->where('status', 'PLAYING')
            ->first();
    }
}
