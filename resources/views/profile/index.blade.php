{{-- resources/views/profile/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-person-circle me-2" style="color: var(--primary)"></i>Hồ sơ cá nhân</h1>
            <p class="page-subtitle">Xem và cập nhật thông tin tài khoản của bạn</p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row g-4">
        {{-- ═══ LEFT — Avatar Card ═══ --}}
        <div class="col-12 col-lg-4">
            <x-card>
                <div class="text-center">
                    {{-- Avatar --}}
                    <div class="mx-auto mb-3" style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary); box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100"
                                 style="background: var(--primary-glow); color: var(--primary); font-size: 3rem; font-weight: 800;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h5 class="fw-bold mb-1" style="color: var(--text-primary);">{{ $user->name }}</h5>
                    <p class="text-muted small mb-3">{{ $user->email }}</p>

                    <span class="badge" style="background: var(--primary-glow); color: var(--primary); padding: 6px 16px; font-size: 0.8rem; border-radius: 20px;">
                        <i class="bi bi-person-fill me-1"></i>{{ ucfirst($user->role->name ?? 'Khách hàng') }}
                    </span>

                    <hr class="my-3" style="border-color: var(--border);">

                    <div class="text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Trạng thái</span>
                            @if($user->status)
                                <span class="badge bg-success bg-opacity-25 text-success" style="padding: 4px 10px;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Hoạt động
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-25 text-danger" style="padding: 4px 10px;">
                                    <i class="bi bi-x-circle-fill me-1"></i>Bị khóa
                                </span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Ngày tham gia</span>
                            <span class="small fw-semibold" style="color: var(--text-primary);">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- ═══ RIGHT — Edit Form ═══ --}}
        <div class="col-12 col-lg-8">
            <x-card>
                <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                    <i class="bi bi-pencil-square me-2" style="color: var(--warning)"></i>Chỉnh sửa thông tin
                </h5>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                    @csrf
                    @method('PUT')

                    {{-- Họ tên --}}
                    <div>
                        <x-input name="name"
                                 label="Họ và tên"
                                 type="text"
                                 placeholder="Nhập họ tên đầy đủ"
                                 value="{{ old('name', $user->name) }}"
                                 required="true"
                                 error="{{ $errors->first('name') }}" />
                    </div>

                    {{-- Email (read-only) --}}
                    <div class="d-flex flex-column gap-1 w-100">
                        <label class="form-label mb-1">Email <span class="text-muted">(không thể thay đổi)</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-muted" style="background: transparent;"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control border-start-0" value="{{ $user->email }}" disabled
                                   style="height: 40px; background-color: rgba(255,255,255,0.03); color: var(--text-secondary);">
                        </div>
                    </div>

                    {{-- Số điện thoại --}}
                    <div>
                        <x-input name="phone"
                                 label="Số điện thoại"
                                 type="text"
                                 placeholder="Nhập số điện thoại (VD: 0912345678)"
                                 value="{{ old('phone', $user->phone) }}"
                                 error="{{ $errors->first('phone') }}" />
                    </div>

                    {{-- Avatar upload --}}
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="avatar" class="form-label mb-1">Ảnh đại diện <span class="text-muted">(tùy chọn)</span></label>
                        <input type="file"
                               id="avatar"
                               name="avatar"
                               class="form-control @error('avatar') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/webp"
                               style="height: 40px;">
                        <div class="form-text text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Hỗ trợ JPG, PNG, WEBP. Tối đa 2MB.</div>
                        @error('avatar')
                            <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Preview ảnh mới --}}
                    <div id="avatar-preview-container" style="display: none;">
                        <label class="form-label mb-1 text-muted small">Xem trước ảnh mới</label>
                        <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary);">
                            <img id="avatar-preview" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>

                    <hr class="my-2" style="border-color: var(--border);">

                    {{-- Actions --}}
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-primary" style="height: 40px; display: inline-flex; align-items: center;">
                            <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </x-card>

            {{-- Quick Links --}}
            <x-card>
                <h6 style="font-weight: 700; color: var(--text-secondary); margin-bottom: 16px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em;">
                    Liên kết nhanh
                </h6>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('auth.change-password') }}" class="btn btn-outline-warning" style="height: 40px; display: inline-flex; align-items: center;">
                        <i class="bi bi-shield-lock me-1"></i> Đổi mật khẩu
                    </a>
                    <a href="{{ route('bookings.create') }}" class="btn btn-outline-primary" style="height: 40px; display: inline-flex; align-items: center;">
                        <i class="bi bi-calendar-plus me-1"></i> Đặt bàn
                    </a>
                </div>
            </x-card>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Preview avatar trước khi upload
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const container = document.getElementById('avatar-preview-container');
        const preview = document.getElementById('avatar-preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            container.style.display = 'none';
        }
    });
</script>
@endpush
