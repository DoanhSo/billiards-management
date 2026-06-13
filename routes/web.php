<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TableSessionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ── Trang chủ redirect ─────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('auth.login');
})->name('home');

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

    // Admin Only
    Route::middleware('role:admin')->group(function () {
        // Staff (nhân viên)
        Route::resource('staff', StaffController::class)->except(['show']);
        Route::patch('staff/{id}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.toggle-status');
    });

    // Admin & Staff
    Route::middleware('role:admin,staff')->group(function () {
        // Customers (khách hàng)
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::patch('customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    });

    // Tables
    Route::resource('tables', TableController::class);
    Route::patch('tables/{id}/status', [TableController::class, 'updateStatus'])->name('tables.update-status');

    // Bookings
    Route::get('bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('api/bookings/events', [BookingController::class, 'getEvents'])->name('api.bookings.events');
    Route::get('bookings/history', [BookingController::class, 'history'])->name('bookings.history');
    Route::resource('bookings', BookingController::class);
    Route::patch('bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::patch('bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('bookings/{id}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

    // Sessions (Table Sessions)
    Route::get('sessions', [TableSessionController::class, 'index'])->name('sessions.index');
    Route::post('sessions/start/{tableId}', [TableSessionController::class, 'start'])->name('sessions.start');
    Route::get('sessions/{id}', [TableSessionController::class, 'show'])->name('sessions.show');
    Route::post('sessions/{id}/end', [TableSessionController::class, 'end'])->name('sessions.end');


    // Invoices
    Route::get('invoices/history', [InvoiceController::class, 'history'])->name('invoices.history');
    Route::resource('invoices', InvoiceController::class);

    // Products
    Route::resource('products', ProductController::class);
    Route::patch('products/{id}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

    // Categories
    Route::resource('categories', CategoryController::class);
});

