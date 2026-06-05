<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ── Trang chủ redirect ─────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('auth.login');
});

// ── Auth: Guest only (chưa đăng nhập) ─────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');
});

// ── Auth: Đã đăng nhập ────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('auth.change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password.post');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // ── Placeholder routes — các thành viên sẽ thay bằng controller thật ──
    // Module: User (Thành viên 1)
    Route::get('/users', fn () => redirect()->route('dashboard.index'))->name('users.index');

    // Module: Table (Thành viên 2)
    Route::get('/tables', fn () => redirect()->route('dashboard.index'))->name('tables.index');

    // Module: Booking (Thành viên 2)
    Route::get('/bookings', fn () => redirect()->route('dashboard.index'))->name('bookings.index');

    // Module: Session (Thành viên 3)
    Route::get('/sessions', fn () => redirect()->route('dashboard.index'))->name('sessions.index');

    // Module: Invoice (Thành viên 3)
    Route::get('/invoices', fn () => redirect()->route('dashboard.index'))->name('invoices.index');

    // Module: Product (Thành viên 4)
    Route::get('/products', fn () => redirect()->route('dashboard.index'))->name('products.index');

    // Module: Category (Thành viên 4)
    Route::get('/categories', fn () => redirect()->route('dashboard.index'))->name('categories.index');
});
