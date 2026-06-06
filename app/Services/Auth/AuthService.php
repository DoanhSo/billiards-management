<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Xử lý đăng nhập.
     *
     * @param array{email: string, password: string, remember?: bool} $credentials
     * @throws ValidationException
     */
    public function login(array $credentials): bool
    {
        $remember = $credentials['remember'] ?? false;

        $attempt = Auth::attempt([
            'email'  => $credentials['email'],
            'password' => $credentials['password'],
            'status' => true,
        ], $remember);

        if (! $attempt) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.'],
            ]);
        }

        request()->session()->regenerate();

        return true;
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Đổi mật khẩu.
     *
     * @param array{current_password: string, new_password: string} $data
     * @throws ValidationException
     */
    public function changePassword(User $user, array $data): bool
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không đúng.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return true;
    }
}
