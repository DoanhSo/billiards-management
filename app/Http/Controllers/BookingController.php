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
        $summary  = $this->bookingService->getBookingStatusSummary();

        return view('bookings.index', compact('bookings', 'search', 'status', 'summary'));
    }

    /**
     * Form đặt bàn mới.
     */
    public function create(): View
    {
        // Lấy bàn AVAILABLE + RESERVED (bàn RESERVED vẫn có thể đặt khung giờ khác)
        $tables = $this->tableService->getBookableTables();

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
     * Hoàn tất đặt bàn.
     */
    public function complete(int $id): RedirectResponse
    {
        $this->bookingService->completeBooking($id);

        return redirect()->back()
            ->with('success', 'Đặt bàn đã được hoàn thành.');
    }

    /**
     * Lịch sử đặt bàn.
     */
    public function history(Request $request): View
    {
        $bookings = $this->bookingService->getBookingHistory();

        return view('bookings.history', compact('bookings'));
    }

    /**
     * Giao diện lịch đặt bàn trực quan.
     */
    public function calendar(): View
    {
        // Lấy danh sách bàn để làm resource cho timeline (nếu dùng timeline view)
        $tables = $this->tableService->getAvailableTables();
        return view('bookings.calendar', compact('tables'));
    }

    /**
     * API trả về JSON events cho FullCalendar
     */
    public function getEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        if (!$start || !$end) {
            return response()->json([]);
        }

        $events = $this->bookingService->getEventsForCalendar($start, $end);
        return response()->json($events);
    }
}
