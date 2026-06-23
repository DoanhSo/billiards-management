{{-- resources/views/staff/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Quản lý nhân viên')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Quản lý nhân viên</h2>
        <p class="text-muted mb-0" style="font-size: 14px;">Danh sách tài khoản nhân viên trong hệ thống</p>
    </div>
    <a href="{{ route('staff.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Thêm nhân viên
    </a>
</div>

{{-- Thống kê --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <x-stat-card title="Tổng nhân viên" :value="$users->total()" icon="person-badge" color="primary" />
    </div>
    <div class="col-6 col-md-4">
        <x-stat-card title="Đang hoạt động" :value="$users->where('status', true)->count()" icon="check-circle" color="success" />
    </div>
    <div class="col-6 col-md-4">
        <x-stat-card title="Đã khóa" :value="$users->where('status', false)->count()" icon="lock" color="danger" />
    </div>
</div>

<x-card>
    {{-- Tìm kiếm & lọc --}}
    <form action="{{ route('staff.index') }}" method="GET" class="ajax-search-form row g-3 mb-4 p-3 border-bottom">
        <div class="col-md-6">
            <x-input name="search" placeholder="Tìm theo tên, email, SĐT..." value="{{ $search }}" />
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select" style="height: 40px;">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ $status === '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ $status === '0' ? 'selected' : '' }}>Đã khóa</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <x-button type="submit" variant="secondary" class="w-100">
                <i class="bi bi-funnel me-1"></i> Lọc
            </x-button>
        </div>
    </form>

    <div id="searchable-content">
        <x-table>
        <x-slot:thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Nhân viên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Trạng thái</th>
                <th class="text-end">Hành động</th>
            </tr>
        </x-slot:thead>

        @forelse($users as $index => $user)
            <tr>
                <td class="text-muted">{{ $users->firstItem() + $index }}</td>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                 alt="{{ $user->name }}"
                                 class="rounded-circle object-fit-cover"
                                 width="40" height="40">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #7c3aed); font-size: 16px;">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <span class="fw-semibold d-block">{{ $user->name }}</span>
                            <x-badge type="primary">Nhân viên</x-badge>
                        </div>
                    </div>
                </td>
                <td class="text-muted">{{ $user->email }}</td>
                <td class="text-muted">{{ $user->phone ?? '—' }}</td>
                <td>
                    @if($user->status)
                        <x-badge type="success">Hoạt động</x-badge>
                    @else
                        <x-badge type="danger">Đã khóa</x-badge>
                    @endif
                </td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1">
                        {{-- Khóa / Mở khóa --}}
                        <form action="{{ route('staff.toggle-status', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm {{ $user->status ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                    title="{{ $user->status ? 'Khóa' : 'Mở khóa' }}"
                                    data-confirm="{{ $user->status ? 'Khóa' : 'Mở khóa' }} tài khoản nhân viên này?"
                                    onclick="return confirm(this.getAttribute('data-confirm'));">
                                <i class="bi {{ $user->status ? 'bi-lock' : 'bi-unlock' }}"></i>
                            </button>
                        </form>
                        {{-- Sửa --}}
                        <a href="{{ route('staff.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        {{-- Xóa --}}
                        <form action="{{ route('staff.destroy', $user->id) }}" method="POST" class="d-inline"
                              data-name="{{ $user->name }}"
                              onsubmit="return confirm('Xóa tài khoản nhân viên ' + this.getAttribute('data-name') + '? Hành động này không thể hoàn tác.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <i class="bi bi-person-badge" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="text-muted mt-3 mb-0">Không tìm thấy nhân viên nào.</p>
                    @if($search || $status !== '')
                        <a href="{{ route('staff.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
                        </a>
                    @endif
                </td>
            </tr>
        @endforelse
        </x-table>

        <div class="px-3 py-2">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</x-card>
@endsection
