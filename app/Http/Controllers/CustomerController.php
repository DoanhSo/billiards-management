<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Danh sách khách hàng.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $users  = $this->userService->getAllUsers($search, 'customer', $status);

        return view('customers.index', compact('users', 'search', 'status'));
    }

    /**
     * Form tạo khách hàng.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Lưu khách hàng mới.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['role_id'] = $this->userService->getRoleByName('customer')->id;

        $this->userService->createUser($data);

        return redirect()->route('customers.index')
            ->with('success', 'Tạo tài khoản khách hàng thành công.');
    }

    /**
     * Form chỉnh sửa khách hàng.
     */
    public function edit(int $id): View
    {
        $user = $this->userService->getUserById($id);

        return view('customers.edit', compact('user'));
    }

    /**
     * Cập nhật khách hàng.
     */
    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->updateUser($id, $request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Cập nhật tài khoản khách hàng thành công.');
    }

    /**
     * Khóa / Mở khóa khách hàng.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $user    = $this->userService->toggleUserStatus($id);
        $message = $user->status ? 'Tài khoản khách hàng đã được mở khóa.' : 'Tài khoản khách hàng đã bị khóa.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Xóa khách hàng.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->userService->deleteUser($id);

        return redirect()->route('customers.index')
            ->with('success', 'Xóa tài khoản khách hàng thành công.');
    }
}
