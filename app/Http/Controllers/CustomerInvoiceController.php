<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\TableSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerInvoiceController extends Controller
{
    /**
     * Danh sách hóa đơn của khách hàng đang đăng nhập.
     */
    public function index(Request $request): View
    {
        $customerId = Auth::id();

        // Lấy tất cả session IDs của khách hàng
        $sessionIds = TableSession::where('customer_id', $customerId)->pluck('id');

        // Thống kê tổng quan
        $stats = Invoice::whereIn('table_session_id', $sessionIds)
            ->select(
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('COALESCE(SUM(total_amount), 0) as total_spent'),
                DB::raw('COUNT(CASE WHEN payment_status = "PAID" THEN 1 END) as total_paid'),
                DB::raw('COUNT(CASE WHEN payment_status = "UNPAID" THEN 1 END) as total_unpaid')
            )
            ->first();

        // Danh sách hóa đơn phân trang
        $invoices = Invoice::with(['tableSession.billiardTable', 'staff'])
            ->whereIn('table_session_id', $sessionIds)
            ->latest()
            ->paginate(10);

        return view('customer-invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Chi tiết hóa đơn.
     */
    public function show(int $id): View
    {
        $customerId = Auth::id();

        $invoice = Invoice::with(['tableSession.billiardTable', 'staff', 'invoiceDetails.product'])
            ->findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($invoice->tableSession->customer_id !== $customerId) {
            abort(403, 'Bạn không có quyền xem hóa đơn này.');
        }

        return view('customer-invoices.show', compact('invoice'));
    }
}
