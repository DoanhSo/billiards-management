{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')

@section('title', 'Đăng ký tài khoản')

@section('content')
    <h1 class="auth-card-title">Tạo tài khoản mới</h1>
    <p class="auth-card-subtitle">Tham gia hệ thống quản lý quán billiards của chúng tôi.</p>



    {{-- Form --}}
    <form id="form-register" method="POST" action="{{ route('auth.register.post') }}" novalidate>
        @csrf

        {{-- Họ và tên --}}
        <div class="mb-3">
            <label for="name" class="form-label">Họ và tên</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-person"></i>
                </span>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Nguyễn Văn A"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    autofocus
                >
            </div>
            @error('name')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="nguyenvana@gmail.com"
                    value="{{ old('email') }}"
                    autocomplete="email"
                >
            </div>
            @error('email')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Số điện thoại --}}
        <div class="mb-3">
            <label for="phone" class="form-label">Số điện thoại <span class="text-muted" style="text-transform: none; font-size: 0.75rem;">(Tuỳ chọn)</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-telephone"></i>
                </span>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder="0912 345 678"
                    value="{{ old('phone') }}"
                >
            </div>
            @error('phone')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Mật khẩu --}}
        <div class="mb-3">
            <label for="password" class="form-label mb-1">Mật khẩu</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Tối thiểu 8 ký tự"
                    autocomplete="new-password"
                >
                <button type="button" class="btn-toggle-pw" data-target="password" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Xác nhận mật khẩu --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label mb-1">Xác nhận mật khẩu</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="Nhập lại mật khẩu"
                    autocomplete="new-password"
                >
                <button type="button" class="btn-toggle-pw" data-target="password_confirmation" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth w-100 mb-3" id="btn-register">
            <i class="bi bi-person-plus me-2"></i>Đăng ký
        </button>

        {{-- Link Đăng nhập --}}
        <div class="text-center mt-3">
            <span class="text-muted" style="font-size: 0.85rem;">Đã có tài khoản?</span>
            <a href="{{ route('auth.login') }}" class="text-decoration-none fw-semibold" style="color: var(--primary); font-size: 0.85rem;">Đăng nhập</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Toggle hiện/ẩn mật khẩu cho từng input
    document.querySelectorAll('.btn-toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input    = document.getElementById(targetId);
            const icon     = this.querySelector('i');
            const isHidden = input.type === 'password';
            input.type     = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // Loading state khi submit
    document.getElementById('form-register').addEventListener('submit', function () {
        const btn = document.getElementById('btn-register');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang đăng ký...';
        btn.disabled  = true;
    });
</script>
@endpush


