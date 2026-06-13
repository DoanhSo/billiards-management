{{-- resources/views/customers/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
<div class="mb-4">
    <a href="{{ route('customers.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách khách hàng
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Chỉnh sửa khách hàng: {{ $user->name }}">
            <form action="{{ route('customers.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-3 row g-3">
                @csrf
                @method('PUT')

                {{-- Avatar hiện tại --}}
                <div class="col-12 mb-2">
                    <div class="d-flex align-items-center gap-3">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                 class="rounded-circle object-fit-cover shadow-sm" width="64" height="64">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                 style="width: 64px; height: 64px; background: linear-gradient(135deg, #16a34a, #059669); font-size: 24px;">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }} · <x-badge type="success">Khách hàng</x-badge></small>
                        </div>
                    </div>
                </div>
                <hr class="my-2">

                {{-- Họ tên --}}
                <div class="col-md-6">
                    <x-input name="name" label="Họ và tên" required="true"
                             placeholder="Nhập họ tên đầy đủ"
                             value="{{ old('name', $user->name) }}"
                             error="{{ $errors->first('name') }}" />
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <x-input name="email" type="email" label="Email" required="true"
                             placeholder="example@email.com"
                             value="{{ old('email', $user->email) }}"
                             error="{{ $errors->first('email') }}" />
                </div>

                {{-- Số điện thoại --}}
                <div class="col-md-6">
                    <x-input name="phone" label="Số điện thoại"
                             placeholder="0912 345 678"
                             value="{{ old('phone', $user->phone) }}"
                             error="{{ $errors->first('phone') }}" />
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-6">
                    <label for="status" class="form-label mb-1">Trạng thái</label>
                    <select name="status" id="status" class="form-select" style="height: 40px;">
                        <option value="1" {{ old('status', $user->status ? '1' : '0') === '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ old('status', $user->status ? '1' : '0') === '0' ? 'selected' : '' }}>Khóa tài khoản</option>
                    </select>
                </div>

                {{-- Avatar mới --}}
                <div class="col-12">
                    <label for="avatar" class="form-label mb-1">Thay đổi ảnh đại diện</label>
                    @if($user->avatar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                 width="80" height="80" class="rounded object-fit-cover border">
                        </div>
                    @endif
                    <input type="file" name="avatar" id="avatar"
                           class="form-control {{ $errors->has('avatar') ? 'is-invalid' : '' }}"
                           accept="image/jpeg,image/png,image/webp" style="height: 40px;">
                    <small class="text-muted">Chỉ chọn ảnh mới nếu muốn thay đổi. Tối đa 2MB.</small>
                    @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Ghi chú mật khẩu --}}
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-0" style="font-size: 13px;">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Mật khẩu không thể thay đổi tại đây. Khách hàng tự đổi mật khẩu trong phần <strong>Đổi mật khẩu</strong>.</span>
                    </div>
                </div>

                <div class="col-12 mt-4 d-flex gap-2">
                    <x-button type="submit" variant="primary" icon="check-lg">Cập nhật khách hàng</x-button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" style="height: 40px;">Hủy</a>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
