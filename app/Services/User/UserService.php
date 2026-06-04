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

class UserService
{
    /**
     * Lấy danh sách tất cả tài khoản (có phân trang, tìm kiếm).
     */
    public function getAllUsers(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        return User::with('role')
            ->when($search, function (Builder $query) use ($search): void {
                $query->where(function (Builder $q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin tài khoản theo ID.
     */
    public function getUserById(int $id): User
    {
        return User::with('role')->findOrFail($id);
    }

    /**
     * Lấy tất cả roles (dùng cho form).
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Tạo tài khoản mới.
     *
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): User
    {
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        $data['password'] = Hash::make($data['password']);
        $data['status']   = $data['status'] ?? true;

        return User::create($data);
    }

    /**
     * Cập nhật thông tin tài khoản.
     *
     * @param array<string, mixed> $data
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            // Xóa ảnh cũ nếu tồn tại
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        // Không cập nhật password trong form edit user thông thường
        unset($data['password'], $data['password_confirmation']);

        $user->update($data);

        return $user->fresh('role');
    }

    /**
     * Khóa / Mở khóa tài khoản.
     */
    public function toggleUserStatus(int $id): User
    {
        $user = $this->getUserById($id);

        $user->update(['status' => ! $user->status]);

        return $user->fresh();
    }

    /**
     * Xóa tài khoản.
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        return (bool) $user->delete();
    }
}
