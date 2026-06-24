<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Danh sách nhân viên.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $users  = $this->userService->getAllUsers($search, 'staff', $status);

        return view('staff.index', compact('users', 'search', 'status'));
    }

    /**
     * Form tạo nhân viên.
     */
    public function create(): View
    {
        return view('staff.create');
    }

    /**
     * Lưu nhân viên mới.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['role_id'] = $this->userService->getRoleByName('staff')->id;

        $this->userService->createUser($data);

        return redirect()->route('staff.index')
            ->with('success', 'Tạo tài khoản nhân viên thành công.');
    }

    /**
     * Form chỉnh sửa nhân viên.
     */
    public function edit(int $id): View
    {
        $user = $this->userService->getUserById($id);

        return view('staff.edit', compact('user'));
    }

    /**
     * Cập nhật nhân viên.
     */
    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->updateUser($id, $request->validated());

        return redirect()->route('staff.index')
            ->with('success', 'Cập nhật tài khoản nhân viên thành công.');
    }

    /**
     * Khóa / Mở khóa nhân viên.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $user    = $this->userService->toggleUserStatus($id);
        $message = $user->status ? 'Tài khoản nhân viên đã được mở khóa.' : 'Tài khoản nhân viên đã bị khóa.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Xóa nhân viên.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->userService->deleteUser($id);

        return redirect()->route('staff.index')
            ->with('success', 'Xóa tài khoản nhân viên thành công.');
    }
}
