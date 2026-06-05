<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Role;
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
     * Xử lý đăng ký tài khoản.
     *
     * @param array{name: string, email: string, phone?: string, password: string} $data
     * @return User
     */
    public function register(array $data): User
    {
        $customerRole = Role::where('name', 'customer')->first();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role_id'  => $customerRole?->id,
            'status'   => true,
        ]);

        // Tự động đăng nhập
        Auth::login($user);
        request()->session()->regenerate();

        return $user;
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
