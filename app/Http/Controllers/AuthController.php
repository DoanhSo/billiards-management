<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Hiển thị form đăng nhập.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $this->authService->login($request->validated());

        return redirect()->route('dashboard.index')
            ->with('success', 'Đăng nhập thành công. Chào mừng ' . Auth::user()->name . '!');
    }

    /**
     * Hiển thị form đăng ký.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $this->authService->register($request->validated());

        return redirect()->route('dashboard.index')
            ->with('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với hệ thống.');
    }

    /**
     * Đăng xuất.
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('auth.login')
            ->with('success', 'Đăng xuất thành công.');
    }

    /**
     * Hiển thị form đổi mật khẩu.
     */
    public function showChangePasswordForm(): View
    {
        return view('auth.change-password');
    }

    /**
     * Xử lý đổi mật khẩu.
     */
    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $this->authService->changePassword(Auth::user(), $request->validated());

        return redirect()->back()
            ->with('success', 'Đổi mật khẩu thành công.');
    }
}
