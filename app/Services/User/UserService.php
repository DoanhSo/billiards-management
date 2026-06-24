<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Lớp UserService
 * 
 * Chịu trách nhiệm quản lý toàn bộ nghiệp vụ liên quan đến tài khoản người dùng
 * (Staff, Customer), bao gồm: CRUD tài khoản, tìm kiếm, phân trang, xử lý ảnh đại diện
 * và thay đổi trạng thái (Khóa/Mở khóa).
 */
class UserService
{
    /**
     * Lấy danh sách tài khoản theo điều kiện (có phân trang, tìm kiếm).
     * 
     * Hỗ trợ tìm kiếm linh hoạt theo Tên, Email hoặc Số điện thoại.
     * Cung cấp khả năng lọc theo Role (staff/customer) và Trạng thái (đang hoạt động/bị khóa).
     *
     * @param string $search Từ khóa tìm kiếm (tên, email, sđt)
     * @param string $role Tên vai trò (ví dụ: 'staff', 'customer')
     * @param string $status Trạng thái lọc ('1' = hoạt động, '0' = bị khóa, '' = tất cả)
     * @param int $perPage Số lượng bản ghi trên mỗi trang (mặc định 15)
     * @return LengthAwarePaginator Trả về kết quả phân trang chứa danh sách User
     */
    public function getAllUsers(string $search = '', string $role = '', string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        return User::with('role')
            ->when($search, function (Builder $query) use ($search): void {
                // Gom nhóm các điều kiện OR để không làm ảnh hưởng đến các điều kiện AND khác
                $query->where(function (Builder $q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($role, function (Builder $query) use ($role): void {
                // Lọc theo quan hệ bảng roles
                $query->whereHas('role', function (Builder $q) use ($role): void {
                    $q->where('name', $role);
                });
            })
            ->when($status !== '', function (Builder $query) use ($status): void {
                $query->where('status', (bool) $status);
            })
            ->latest() // Sắp xếp tài khoản mới tạo lên đầu
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin chi tiết một tài khoản theo ID.
     * 
     * Lấy kèm thông tin Role để phục vụ hiển thị trên view.
     *
     * @param int $id ID của người dùng
     * @return User Trả về đối tượng User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ném lỗi 404 nếu không tìm thấy
     */
    public function getUserById(int $id): User
    {
        return User::with('role')->findOrFail($id);
    }

    /**
     * Lấy tất cả danh sách quyền (Roles).
     * 
     * Thường được sử dụng để đổ dữ liệu vào các thẻ <select> trong form tạo/sửa user.
     *
     * @return Collection Danh sách Role
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Lấy chi tiết Role theo tên (ví dụ: 'staff', 'customer').
     *
     * @param string $name Tên quyền
     * @return Role
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ném lỗi 404 nếu không tìm thấy
     */
    public function getRoleByName(string $name): Role
    {
        return Role::where('name', $name)->firstOrFail();
    }

    /**
     * Tạo tài khoản người dùng mới (Nhân viên hoặc Khách hàng).
     * 
     * Xử lý upload ảnh đại diện (nếu có), mã hóa mật khẩu trước khi lưu.
     * Mặc định tài khoản được tạo sẽ ở trạng thái kích hoạt.
     *
     * @param array<string, mixed> $data Dữ liệu form đã được validate
     * @return User Trả về đối tượng User vừa tạo
     */
    public function createUser(array $data): User
    {
        // Kiểm tra xem có file ảnh avatar được gửi lên không
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            // Lưu ảnh vào thư mục public/storage/avatars
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        // Mã hóa mật khẩu
        $data['password'] = Hash::make($data['password']);
        
        // Gán trạng thái, nếu không truyền lên thì mặc định là true (hoạt động)
        $data['status']   = $data['status'] ?? true;

        return User::create($data);
    }

    /**
     * Cập nhật thông tin tài khoản hiện có.
     * 
     * Xử lý xóa ảnh đại diện cũ (nếu upload ảnh mới).
     * Lưu ý: Không tự ý cập nhật password qua hàm này (form update thông thường không đổi pass).
     *
     * @param int $id ID của user cần update
     * @param array<string, mixed> $data Dữ liệu form update đã được validate
     * @return User Đối tượng User sau khi đã cập nhật
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);

        // Xử lý cập nhật avatar
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            // Xóa file ảnh cũ khỏi đĩa cứng để giải phóng dung lượng
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Lưu file ảnh mới
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        // Bảo mật: Xóa trường password khỏi data để ngăn chặn việc lỡ ghi đè mật khẩu rỗng
        // Việc đổi mật khẩu có service hoặc form riêng xử lý
        unset($data['password'], $data['password_confirmation']);

        $user->update($data);

        // Lấy lại dữ liệu mới nhất kèm relation
        return $user->fresh('role');
    }

    /**
     * Đảo ngược trạng thái Khóa / Mở khóa của tài khoản.
     *
     * @param int $id ID của người dùng
     * @return User Đối tượng User sau khi thay đổi trạng thái
     */
    public function toggleUserStatus(int $id): User
    {
        $user = $this->getUserById($id);

        // Đảo ngược giá trị boolean: true -> false (khóa) và ngược lại
        $user->update(['status' => ! $user->status]);

        return $user->fresh();
    }

    /**
     * Xóa hoàn toàn một tài khoản khỏi hệ thống.
     * 
     * Cần xóa cả file ảnh đại diện vật lý trên Storage trước khi xóa bản ghi.
     *
     * @param int $id ID của người dùng cần xóa
     * @return bool True nếu xóa thành công
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);

        // Xóa ảnh vật lý khỏi ổ đĩa
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        return (bool) $user->delete();
    }
}
