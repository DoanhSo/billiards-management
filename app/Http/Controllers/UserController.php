<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Danh sách tài khoản.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $users  = $this->userService->getAllUsers($search);

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Form tạo tài khoản.
     */
    public function create(): View
    {
        $roles = $this->userService->getAllRoles();

        return view('users.create', compact('roles'));
    }

    /**
     * Lưu tài khoản mới.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Tạo tài khoản thành công.');
    }

    /**
     * Form chỉnh sửa tài khoản.
     */
    public function edit(int $id): View
    {
        $user  = $this->userService->getUserById($id);
        $roles = $this->userService->getAllRoles();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Cập nhật tài khoản.
     */
    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->updateUser($id, $request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Cập nhật tài khoản thành công.');
    }

    /**
     * Khóa / Mở khóa tài khoản.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $user = $this->userService->toggleUserStatus($id);

        $message = $user->status ? 'Tài khoản đã được mở khóa.' : 'Tài khoản đã bị khóa.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Xóa tài khoản.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->userService->deleteUser($id);

        return redirect()->route('users.index')
            ->with('success', 'Xóa tài khoản thành công.');
    }
}
