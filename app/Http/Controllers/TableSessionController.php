<?php

namespace App\Http\Controllers;

use App\Services\Session\TableSessionService;
use App\Services\Table\TableService;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableSessionController extends Controller
{
    public function __construct(
        private readonly TableSessionService $tableSessionService,
        private readonly TableService        $tableService,
        private readonly UserService         $userService,
    ) {}

    /**
     * Danh sách phiên chơi.
     */
    public function index(Request $request): View
    {
        $status   = $request->string('status')->toString();
        $sessions = $this->tableSessionService->getAllSessions($status);

        return view('sessions.index', compact('sessions', 'status'));
    }

    /**
     * Bắt đầu phiên chơi cho một bàn.
     */
    public function start(Request $request, int $tableId): RedirectResponse
    {
        $customerId = $request->integer('customer_id') ?: null;

        $session = $this->tableSessionService->startSession($tableId, $customerId);

        return redirect()->route('sessions.show', $session->id)
            ->with('success', "Đã bắt đầu phiên chơi cho bàn {$session->billiardTable->table_number}.");
    }

    /**
     * Chi tiết phiên chơi.
     */
    public function show(int $id): View
    {
        $session = $this->tableSessionService->getSessionById($id);

        return view('sessions.show', compact('session'));
    }

    /**
     * Kết thúc phiên chơi.
     */
    public function end(int $id): RedirectResponse
    {
        $session = $this->tableSessionService->endSession($id);

        return redirect()->route('invoices.create', ['session_id' => $session->id])
            ->with('success', "Phiên chơi kết thúc. Tổng giờ: {$session->total_hours}h — Tiền bàn: " . number_format((float) $session->table_price, 0, ',', '.') . ' VNĐ');
    }
}
