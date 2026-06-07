<?php

namespace App\Services\Dashboard;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\TableSession;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Lấy tổng quan thống kê cho Dashboard.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        return [
            'revenue'         => $this->getRevenueStatistics(),
            'bookings'        => $this->getBookingStatistics(),
            'popular_tables'  => $this->getMostUsedTables(),
            'top_products'    => $this->getBestSellingProducts(),
            'table_status'    => $this->getTableStatusSummary(),
            'recent_invoices' => $this->getRecentInvoices(),
        ];
    }

    /**
     * Thống kê doanh thu:
     * - Hôm nay
     * - Tuần này
     * - Tháng này
     * - Doanh thu 7 ngày gần nhất (cho biểu đồ)
     *
     * @return array<string, mixed>
     */
    public function getRevenueStatistics(): array
    {
        $today     = Carbon::today();
        $thisWeek  = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $revenueToday = Invoice::whereDate('created_at', $today)
            ->where('payment_status', 'PAID')
            ->sum('total_amount');

        $revenueWeek = Invoice::where('created_at', '>=', $thisWeek)
            ->where('payment_status', 'PAID')
            ->sum('total_amount');

        $revenueMonth = Invoice::where('created_at', '>=', $thisMonth)
            ->where('payment_status', 'PAID')
            ->sum('total_amount');

        // Doanh thu 7 ngày gần nhất cho biểu đồ
        $last7Days = collect(range(6, 0))->map(function (int $daysAgo): array {
            $date    = Carbon::today()->subDays($daysAgo);
            $revenue = Invoice::whereDate('created_at', $date)
                ->where('payment_status', 'PAID')
                ->sum('total_amount');

            return [
                'date'    => $date->format('d/m'),
                'revenue' => (float) $revenue,
            ];
        });

        return [
            'today' => (float) $revenueToday,
            'week'  => (float) $revenueWeek,
            'month' => (float) $revenueMonth,
            'chart' => $last7Days->values()->toArray(),
        ];
    }

    /**
     * Thống kê đặt bàn:
     * - Tổng đặt bàn hôm nay
     * - Theo từng trạng thái
     *
     * @return array<string, mixed>
     */
    public function getBookingStatistics(): array
    {
        $today = Carbon::today();

        $totalToday = Booking::whereDate('created_at', $today)->count();

        $byStatus = Booking::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'today'     => $totalToday,
            'by_status' => $byStatus,
        ];
    }

    /**
     * Thống kê bàn được sử dụng nhiều nhất (top 5).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMostUsedTables(): array
    {
        return TableSession::select(
                'billiard_table_id',
                DB::raw('COUNT(*) as session_count'),
                DB::raw('SUM(total_hours) as total_hours')
            )
            ->with('billiardTable')
            ->where('status', 'FINISHED')
            ->groupBy('billiard_table_id')
            ->orderByDesc('session_count')
            ->limit(5)
            ->get()
            ->map(fn(TableSession $item): array => [
                'table_number'  => $item->billiardTable?->table_number,
                'table_type'    => $item->billiardTable?->table_type,
                'session_count' => (int) $item->session_count,
                'total_hours'   => (float) $item->total_hours,
            ])
            ->toArray();
    }

    /**
     * Thống kê sản phẩm bán chạy nhất (top 5).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBestSellingProducts(): array
    {
        return InvoiceDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->with('product.category')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(fn(InvoiceDetail $item): array => [
                'product_name'  => $item->product?->name,
                'category_name' => $item->product?->category?->name,
                'total_sold'    => (int) $item->total_sold,
                'total_revenue' => (float) $item->total_revenue,
            ])
            ->toArray();
    }

    /**
     * Thống kê số bàn theo từng trạng thái hiện tại.
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

    /**
     * Lấy 10 hóa đơn gần nhất.
     *
     * @return Collection<int, Invoice>
     */
    public function getRecentInvoices(): Collection
    {
        return Invoice::with(['tableSession.billiardTable', 'staff'])
            ->latest()
            ->limit(10)
            ->get();
    }

    /**
     * Lấy dữ liệu cho Dashboard Nhân viên.
     *
     * @return array<string, mixed>
     */
    public function getStaffDashboardData(): array
    {
        $today = Carbon::today();

        // Doanh thu hôm nay (tiền mặt/banking từ các hóa đơn đã thanh toán)
        $revenueToday = Invoice::whereDate('created_at', $today)
            ->where('payment_status', 'PAID')
            ->sum('total_amount');

        // Số lượng đặt bàn hôm nay
        $bookingsToday = Booking::whereDate('booking_date', $today)->count();

        // Số bàn đang hoạt động
        $activeTablesCount = BilliardTable::where('status', 'PLAYING')->count();

        // Sơ đồ tất cả bàn chơi kèm theo phiên chơi PLAYING (nếu có)
        $tables = BilliardTable::with(['tableSessions' => function ($query) {
            $query->where('status', 'PLAYING')->with('customer');
        }])->orderBy('table_number')->get();

        // Lấy danh sách booking CONFIRMED trong ngày hôm nay để check-in nhanh
        $todayBookings = Booking::where('booking_date', $today)
            ->where('status', 'CONFIRMED')
            ->with('user')
            ->get()
            ->groupBy('billiard_table_id');

        // Danh sách đặt bàn đang PENDING cần duyệt
        $pendingBookings = Booking::where('status', 'PENDING')
            ->with(['user', 'billiardTable'])
            ->latest()
            ->get();

        return [
            'revenue_today'    => (float) $revenueToday,
            'bookings_today'   => $bookingsToday,
            'active_tables'    => $activeTablesCount,
            'tables'           => $tables,
            'today_bookings'   => $todayBookings,
            'pending_bookings' => $pendingBookings,
        ];
    }

    /**
     * Lấy dữ liệu cho Dashboard Khách hàng.
     *
     * @param int $customerId
     * @return array<string, mixed>
     */
    public function getCustomerDashboardData(int $customerId): array
    {
        // Danh sách bàn chơi để theo dõi trạng thái trống
        $tables = BilliardTable::orderBy('table_number')->get();

        // Danh sách đặt bàn của riêng khách hàng này
        $myBookings = Booking::where('user_id', $customerId)
            ->with('billiardTable')
            ->latest()
            ->limit(5)
            ->get();

        // Danh sách thực đơn đồ uống/món ăn chia theo danh mục
        $categories = \App\Models\Category::with(['products' => function ($query) {
            $query->where('status', true);
        }])->get();

        return [
            'tables'      => $tables,
            'my_bookings' => $myBookings,
            'categories'  => $categories,
        ];
    }
}
