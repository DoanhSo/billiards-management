<?php

namespace App\Services\Dashboard;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\TableSession;
use App\Services\Invoice\InvoiceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Lớp DashboardService
 * 
 * Đóng vai trò là trung tâm phân tích dữ liệu và thống kê (Analytics) cho hệ thống.
 * Cung cấp số liệu thời gian thực (doanh thu, đặt bàn, tần suất sử dụng) cho các loại
 * Dashboard khác nhau (Admin, Staff, Customer).
 */
class DashboardService
{
    protected InvoiceService $invoiceService;

    /**
     * Dependency Injection: Tiêm InvoiceService vào để tái sử dụng logic
     * tính toán doanh thu và lấy hóa đơn, tránh việc lặp lại code (DRY).
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Lấy tổng quan các chỉ số thống kê dành cho Admin Dashboard.
     * Gộp nhiều báo cáo vào một mảng duy nhất để hiển thị ra View.
     *
     * @return array<string, mixed> Mảng chứa tất cả các thống kê thành phần
     */
    public function getStatistics(): array
    {
        return [
            'revenue'         => $this->getRevenueStatistics(), // Thống kê doanh thu
            'bookings'        => $this->getBookingStatistics(), // Thống kê đặt bàn
            'popular_tables'  => $this->getMostUsedTables(),    // Bàn đắt khách
            'top_products'    => $this->getBestSellingProducts(), // Sản phẩm bán chạy
            'table_status'    => $this->getTableStatusSummary(), // Trạng thái các bàn
            'recent_invoices' => $this->invoiceService->getRecentInvoices(), // Tái sử dụng từ InvoiceService
        ];
    }

    /**
     * Thống kê toàn diện về doanh thu (Chỉ tính những hóa đơn đã thanh toán - PAID).
     * - Doanh thu theo khung thời gian: Hôm nay, Tuần này, Tháng này.
     * - Doanh thu 7 ngày gần nhất để vẽ Biểu đồ đường (Line Chart).
     *
     * @return array<string, mixed>
     */
    public function getRevenueStatistics(): array
    {
        $today     = Carbon::today()->format('Y-m-d');
        $thisWeek  = Carbon::now()->startOfWeek()->format('Y-m-d');
        $thisMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

        // Gọi sang InvoiceService để tính toán doanh thu thay vì tự query DB
        $revenueToday = $this->invoiceService->getInvoiceRevenue($today, $today);
        $revenueWeek  = $this->invoiceService->getInvoiceRevenue($thisWeek);
        $revenueMonth = $this->invoiceService->getInvoiceRevenue($thisMonth);

        // Lấy doanh thu theo từng ngày của 7 ngày gần nhất (để vẽ biểu đồ)
        $last7Days = collect(range(6, 0))->map(function (int $daysAgo): array {
            $date        = Carbon::today()->subDays($daysAgo);
            $dateString  = $date->format('Y-m-d');
            $revenue     = $this->invoiceService->getInvoiceRevenue($dateString, $dateString);

            return [
                'date'    => $date->format('d/m'), // Format dạng Ngày/Tháng
                'revenue' => $revenue,
            ];
        });

        return [
            'today' => $revenueToday,
            'week'  => $revenueWeek,
            'month' => $revenueMonth,
            'chart' => $last7Days->values()->toArray(),
        ];
    }

    /**
     * Thống kê tình hình đặt lịch bàn trong ngày hôm nay.
     * Phân loại số lượng theo từng trạng thái (PENDING, CONFIRMED, CANCELLED).
     *
     * @return array<string, mixed>
     */
    public function getBookingStatistics(): array
    {
        $today = Carbon::today();

        // Tổng lượt đặt trong hôm nay
        $totalToday = Booking::whereDate('created_at', $today)->count();

        // Nhóm và đếm số lượng đặt bàn theo từng trạng thái
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
     * Truy xuất danh sách 5 Bàn Bida được khách hàng chơi nhiều nhất (Phân tích hiệu suất sử dụng).
     * Tính toán dựa trên số lượt chơi (session_count) và tổng số giờ khách ngồi (total_hours).
     *
     * @return array<int, array<string, mixed>> Danh sách top 5 bàn
     */
    public function getMostUsedTables(): array
    {
        return TableSession::select(
                'billiard_table_id',
                DB::raw('COUNT(*) as session_count'),
                DB::raw('SUM(total_hours) as total_hours')
            )
            ->with('billiardTable')
            ->where('status', 'FINISHED') // Chỉ tính các phiên đã hoàn tất có chốt giờ rõ ràng
            ->groupBy('billiard_table_id')
            ->orderByDesc('session_count') // Xếp hạng theo số lượt chơi giảm dần
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
     * Truy xuất danh sách 5 Sản phẩm/Dịch vụ (Nước, đồ ăn) mang lại doanh thu cao nhất.
     * Giúp chủ quán quyết định nhập hàng loại nào nhiều hơn.
     *
     * @return array<int, array<string, mixed>> Danh sách top 5 sản phẩm
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
            ->orderByDesc('total_sold') // Xếp hạng theo số lượng bán ra
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
     * Thống kê nhanh trạng thái hiện thời của toàn bộ số bàn trong quán.
     * (Có bao nhiêu bàn trống, bao nhiêu bàn đang chơi...).
     *
     * @return array<string, int> Mảng key là trạng thái, value là số lượng bàn
     */
    public function getTableStatusSummary(): array
    {
        return BilliardTable::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Gói gọn các dữ liệu cần thiết phục vụ cho màn hình làm việc của Nhân viên (Staff Dashboard).
     * Nhân viên cần cái nhìn thực tế: Sơ đồ bàn, lịch khách đặt đến hạn, thông báo chờ duyệt...
     *
     * @return array<string, mixed>
     */
    public function getStaffDashboardData(): array
    {
        $today = Carbon::today();
        $todayStr = $today->format('Y-m-d');

        // Doanh thu thu được trong ca làm việc hôm nay (Sử dụng InvoiceService)
        $revenueToday = $this->invoiceService->getInvoiceRevenue($todayStr, $todayStr);

        // Lượt khách đã đặt lịch trong hôm nay
        $bookingsToday = Booking::whereDate('booking_date', $today)->count();

        // Số bàn đang có khách ngồi chơi
        $activeTablesCount = BilliardTable::where('status', 'PLAYING')->count();

        // Sơ đồ tất cả bàn chơi kèm theo phiên chơi đang diễn ra (nếu bàn đang PLAYING)
        $tables = BilliardTable::with(['tableSessions' => function ($query) {
            $query->where('status', 'PLAYING')->with('customer');
        }])->orderBy('table_number')->get();

        // Lấy danh sách khách đã được xếp lịch (CONFIRMED) trong ngày hôm nay để check-in nhanh
        $todayBookings = Booking::where('booking_date', $today)
            ->where('status', 'CONFIRMED')
            ->with('user')
            ->get()
            ->groupBy('billiard_table_id');

        // Danh sách đặt bàn online của khách đang PENDING chờ staff duyệt
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
     * Chuẩn bị dữ liệu cho không gian riêng của Khách hàng (Customer Dashboard).
     * Khách hàng vào đây để: xem bàn trống, gọi món từ menu, tra cứu lịch sử chơi và số tiền đã tiêu.
     *
     * @param int $customerId ID của khách hàng
     * @return array<string, mixed>
     */
    public function getCustomerDashboardData(int $customerId): array
    {
        // Hiển thị danh sách bàn để khách xem tình trạng bàn (trống/có khách)
        $tables = BilliardTable::orderBy('table_number')->get();

        // Hiển thị danh sách các booking mà người này đã đặt (5 lịch gần nhất)
        $myBookings = Booking::where('user_id', $customerId)
            ->with('billiardTable')
            ->latest()
            ->limit(5)
            ->get();

        // Lấy ra Menu (Danh mục đồ ăn thức uống) chỉ bao gồm các món đang được bán
        $categories = \App\Models\Category::with(['products' => function ($query) {
            $query->where('status', true);
        }])->get();

        // ----- Phần thống kê chi tiêu cá nhân của khách hàng -----
        $sessionIds = TableSession::where('customer_id', $customerId)->pluck('id');

        // Tổng số lượt ghé quán chơi (số phiên hoàn tất)
        $totalSessions = TableSession::where('customer_id', $customerId)
            ->where('status', 'FINISHED')
            ->count();

        // Tổng số giờ đã ngồi chơi bida
        $totalHours = (float) TableSession::where('customer_id', $customerId)
            ->where('status', 'FINISHED')
            ->sum('total_hours');

        // Tổng số tiền đã thanh toán cho quán
        $totalSpent = (float) Invoice::whereIn('table_session_id', $sessionIds)
            ->where('payment_status', 'PAID')
            ->sum('total_amount');

        // Tổng số lượt đã đặt bàn
        $totalBookings = Booking::where('user_id', $customerId)->count();

        return [
            'tables'         => $tables,
            'my_bookings'    => $myBookings,
            'categories'     => $categories,
            'total_sessions' => $totalSessions,
            'total_hours'    => $totalHours,
            'total_spent'    => $totalSpent,
            'total_bookings' => $totalBookings,
        ];
    }
}
