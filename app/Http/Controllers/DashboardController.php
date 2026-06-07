<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * Trang Dashboard chính.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            $stats = $this->dashboardService->getStatistics();
            return view('dashboard.index', compact('stats'));
        }

        if ($user->isStaff()) {
            $data = $this->dashboardService->getStaffDashboardData();
            return view('dashboard.index', compact('data'));
        }

        // Khách hàng
        $data = $this->dashboardService->getCustomerDashboardData($user->id);
        return view('dashboard.index', compact('data'));
    }
}
