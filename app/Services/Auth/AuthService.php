<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Lớp AuthService
 * 
 * Quản lý toàn bộ nghiệp vụ liên quan đến xác thực người dùng bao gồm:
 * - Đăng nhập (Login)
 * - Đăng ký (Register) 
 * - Đăng xuất (Logout)
 * - Đổi mật khẩu (Change Password)
 */
class AuthService
{
    /**
     * Xử lý đăng nhập của người dùng.
     * 
     * Kiểm tra thông tin đăng nhập và trạng thái tài khoản. Nếu tài khoản
     * hợp lệ và đang được kích hoạt (status = 1), tiến hành đăng nhập và
     * tạo mới session để phòng chống tấn công Session Fixation.
     *
     * @param array{email: string, password: string, remember?: bool} $credentials Mảng chứa email, password và cờ remember
     * @return bool Trả về true nếu đăng nhập thành công
     * @throws ValidationException Ném ra ngoại lệ nếu sai thông tin hoặc tài khoản bị khóa
     */
    public function login(array $credentials): bool
    {
        // Lấy cờ ghi nhớ đăng nhập, mặc định là false nếu không truyền lên
        $remember = $credentials['remember'] ?? false;

        // Thực hiện xác thực với Auth facade. Chú ý điều kiện status = 1 để chặn các tài khoản đã bị khóa.
        $attempt = Auth::attempt([
            'email'    => $credentials['email'],
            'password' => $credentials['password'],
            'status'   => 1, // Chỉ cho phép tài khoản đang active
        ], $remember);

        // Nếu xác thực thất bại, ném ra lỗi validation để hệ thống tự động redirect về trang form kèm thông báo
        if (! $attempt) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.'],
            ]);
        }

        // Tạo lại ID cho session để bảo vệ khỏi tấn công cố định phiên (Session Fixation)
        request()->session()->regenerate();

        return true;
    }

    /**
     * Xử lý đăng ký tài khoản khách hàng mới.
     * 
     * Mặc định tài khoản tạo ra sẽ được gán quyền 'customer' và trạng thái kích hoạt.
     * Sau khi tạo xong, hệ thống sẽ tự động đăng nhập cho khách hàng này.
     *
     * @param array{name: string, email: string, phone?: string, password: string} $data Dữ liệu form đăng ký
     * @return User Đối tượng User vừa được tạo
     */
    public function register(array $data): User
    {
        // Lấy thông tin Role dành cho khách hàng
        $customerRole = Role::where('name', 'customer')->first();

        // Khởi tạo user mới vào cơ sở dữ liệu
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']), // Mã hóa mật khẩu bằng Bcrypt
            'role_id'  => $customerRole?->id,
            'status'   => true, // Kích hoạt tài khoản ngay lập tức
        ]);

        // Tự động đăng nhập cho người dùng vừa đăng ký thành công
        Auth::login($user);
        request()->session()->regenerate();

        return $user;
    }

    /**
     * Xử lý đăng xuất khỏi hệ thống.
     * 
     * Xóa thông tin xác thực, vô hiệu hóa session hiện tại và tạo token CSRF mới
     * để đảm bảo an toàn cho các request tiếp theo.
     * 
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();

        // Xóa sạch toàn bộ dữ liệu trong session
        request()->session()->invalidate();
        
        // Tạo lại CSRF token mới để phòng chống tấn công CSRF
        request()->session()->regenerateToken();
    }

    /**
     * Xử lý đổi mật khẩu cho người dùng đang đăng nhập.
     * 
     * Yêu cầu người dùng nhập đúng mật khẩu hiện tại trước khi cập nhật mật khẩu mới.
     *
     * @param User $user Đối tượng user cần đổi mật khẩu (thường lấy từ Auth::user())
     * @param array{current_password: string, new_password: string} $data Mảng chứa mật khẩu cũ và mật khẩu mới
     * @return bool Trả về true nếu đổi thành công
     * @throws ValidationException Ném ra ngoại lệ nếu mật khẩu hiện tại nhập vào không khớp
     */
    public function changePassword(User $user, array $data): bool
    {
        // Kiểm tra xem mật khẩu hiện tại người dùng nhập có khớp với mật khẩu trong DB hay không
        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không đúng.'],
            ]);
        }

        // Mã hóa và cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return true;
    }
}
