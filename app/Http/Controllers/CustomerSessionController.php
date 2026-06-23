<?php

namespace App\Http\Controllers;

use App\Models\TableSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerSessionController extends Controller
{
    /**
     * Danh sách phiên chơi của khách hàng đang đăng nhập.
     */
    public function index(Request $request): View
    {
        $customerId = Auth::id();

        // Thống kê tổng quan
        $stats = TableSession::where('customer_id', $customerId)
            ->where('status', 'FINISHED')
            ->select(
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('COALESCE(SUM(total_hours), 0) as total_hours'),
                DB::raw('COALESCE(SUM(table_price), 0) as total_spent')
            )
            ->first();

        // Danh sách phiên chơi phân trang
        $sessions = TableSession::with('billiardTable')
            ->where('customer_id', $customerId)
            ->where('status', 'FINISHED')
            ->latest('end_time')
            ->paginate(10);

        return view('customer-sessions.index', compact('sessions', 'stats'));
    }
}
