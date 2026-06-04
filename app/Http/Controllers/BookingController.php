<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StoreBookingRequest;
use App\Services\Booking\BookingService;
use App\Services\Table\TableService;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly TableService  $tableService,
        private readonly UserService   $userService,
    ) {}

    /**
     * Danh sách đặt bàn.
     */
    public function index(Request $request): View
    {
        $search   = $request->string('search')->toString();
        $status   = $request->string('status')->toString();
        $bookings = $this->bookingService->getAllBookings($search, $status);

        return view('bookings.index', compact('bookings', 'search', 'status'));
    }

    /**
     * Form đặt bàn mới.
     */
    public function create(): View
    {
        $tables = $this->tableService->getAvailableTables();

        return view('bookings.create', compact('tables'));
    }

    /**
     * Lưu đặt bàn mới.
     */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $this->bookingService->createBooking($request->validated());

        return redirect()->route('bookings.index')
            ->with('success', 'Đặt bàn thành công. Vui lòng chờ xác nhận.');
    }

    /**
     * Chi tiết đặt bàn.
     */
    public function show(int $id): View
    {
        $booking = $this->bookingService->getBookingById($id);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Xác nhận đặt bàn.
     */
    public function confirm(int $id): RedirectResponse
    {
        $this->bookingService->confirmBooking($id);

        return redirect()->back()
            ->with('success', 'Đặt bàn đã được xác nhận thành công.');
    }

    /**
     * Hủy đặt bàn.
     */
    public function cancel(int $id): RedirectResponse
    {
        $this->bookingService->cancelBooking($id);

        return redirect()->back()
            ->with('success', 'Đặt bàn đã được hủy.');
    }

    /**
     * Lịch sử đặt bàn.
     */
    public function history(Request $request): View
    {
        $bookings = $this->bookingService->getBookingHistory();

        return view('bookings.history', compact('bookings'));
    }
}
