<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Services\Invoice\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {}

    /**
     * Danh sách hóa đơn.
     */
    public function index(Request $request): View
    {
        $status   = $request->string('status')->toString();
        $invoices = $this->invoiceService->getAllInvoices($status);

        return view('invoices.index', compact('invoices', 'status'));
    }

    /**
     * Tạo hóa đơn từ phiên chơi.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $sessionId = $request->integer('session_id');

        $data = $this->invoiceService->prepareInvoice($sessionId);

        return view('invoices.create', $data);
    }

    /**
     * Lưu hóa đơn và thanh toán.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Thanh toán thành công! Tổng tiền: ' . number_format((float) $invoice->total_amount, 0, ',', '.') . ' VNĐ');
    }

    /**
     * Chi tiết hóa đơn.
     */
    public function show(int $id): View
    {
        $invoice = $this->invoiceService->getInvoiceById($id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Lịch sử hóa đơn (lọc theo ngày).
     */
    public function history(Request $request): View
    {
        $from     = $request->string('from')->toString();
        $to       = $request->string('to')->toString();
        $invoices = $this->invoiceService->getInvoiceHistory($from, $to);

        return view('invoices.history', compact('invoices', 'from', 'to'));
    }
}
