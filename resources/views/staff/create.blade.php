{{-- resources/views/staff/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Thêm nhân viên')

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách nhân viên
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Thêm tài khoản nhân viên mới">
            <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data" class="p-3 row g-3">
                @csrf

                {{-- Họ tên --}}
                <div class="col-md-6">
                    <x-input name="name" label="Họ và tên" required="true"
                             placeholder="Nhập họ tên đầy đủ"
                             value="{{ old('name') }}"
                             error="{{ $errors->first('name') }}" />
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <x-input name="email" type="email" label="Email" required="true"
                             placeholder="example@email.com"
                             value="{{ old('email') }}"
                             error="{{ $errors->first('email') }}" />
                </div>

                {{-- Số điện thoại --}}
                <div class="col-md-6">
                    <x-input name="phone" label="Số điện thoại"
                             placeholder="0912 345 678"
                             value="{{ old('phone') }}"
                             error="{{ $errors->first('phone') }}" />
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-6">
                    <label for="status" class="form-label mb-1">Trạng thái</label>
                    <select name="status" id="status" class="form-select" style="height: 40px;">
                        <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Khóa tài khoản</option>
                    </select>
                </div>

                {{-- Mật khẩu --}}
                <div class="col-md-6">
                    <x-input name="password" type="password" label="Mật khẩu" required="true"
                             placeholder="Tối thiểu 8 ký tự"
                             error="{{ $errors->first('password') }}" />
                </div>

                {{-- Xác nhận mật khẩu --}}
                <div class="col-md-6">
                    <x-input name="password_confirmation" type="password" label="Xác nhận mật khẩu" required="true"
                             placeholder="Nhập lại mật khẩu" />
                </div>

                {{-- Avatar --}}
                <div class="col-12">
                    <label for="avatar" class="form-label mb-1">Ảnh đại diện</label>
                    <input type="file" name="avatar" id="avatar"
                           class="form-control {{ $errors->has('avatar') ? 'is-invalid' : '' }}"
                           accept="image/jpeg,image/png,image/webp" style="height: 40px;">
                    <small class="text-muted">JPG, PNG hoặc WebP. Tối đa 2MB.</small>
                    @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Ghi chú vai trò --}}
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-0" style="font-size: 13px;">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Tài khoản này sẽ được tự động gán vai trò <strong>Nhân viên</strong>.</span>
                    </div>
                </div>

                <div class="col-12 mt-4 d-flex gap-2">
                    <x-button type="submit" variant="primary" icon="person-plus">Tạo nhân viên</x-button>
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" style="height: 40px;">Hủy</a>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
