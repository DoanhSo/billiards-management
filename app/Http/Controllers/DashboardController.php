<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
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
        $stats = $this->dashboardService->getStatistics();

        return view('dashboard.index', compact('stats'));
    }
}
