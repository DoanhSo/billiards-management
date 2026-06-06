{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')

@section('title', 'Đăng ký tài khoản')

@section('content')
    <h1 class="auth-card-title">Đăng ký thành viên</h1>
    <p class="auth-card-subtitle">Tạo tài khoản để trải nghiệm dịch vụ của chúng tôi.</p>

    {{-- Flash messages --}}
    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Form --}}
    <form id="form-register" method="POST" action="{{ route('auth.register.post') }}" novalidate>
        @csrf

        {{-- Họ Tên --}}
        <div class="mb-3">
            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
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
                <div class="invalid-feedback d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="email@example.com"
                    value="{{ old('email') }}"
                    autocomplete="email"
                >
            </div>
            @error('email')
                <div class="invalid-feedback d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Số điện thoại --}}
        <div class="mb-3">
            <label for="phone" class="form-label">Số điện thoại</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-telephone"></i>
                </span>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder="0912345678"
                    value="{{ old('phone') }}"
                    autocomplete="tel"
                >
            </div>
            @error('phone')
                <div class="invalid-feedback d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Mật khẩu --}}
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    autocomplete="new-password"
                >
                <button type="button" class="btn-toggle-pw" onclick="togglePassword('password', 'icon-eye-1')" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye" id="icon-eye-1"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Xác nhận mật khẩu --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="••••••••"
                    autocomplete="new-password"
                >
                <button type="button" class="btn-toggle-pw" onclick="togglePassword('password_confirmation', 'icon-eye-2')" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye" id="icon-eye-2"></i>
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth" id="btn-register">
            <i class="bi bi-person-plus me-2"></i>Đăng ký
        </button>

        {{-- Link Đăng nhập --}}
        <div class="mt-4 text-center">
            <span class="text-muted">Đã có tài khoản?</span>
            <a href="{{ route('auth.login') }}" class="text-decoration-none fw-semibold" style="color: var(--primary);">Đăng nhập ngay</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Hàm dùng chung cho toggle password
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    }

    // Loading state khi submit
    document.getElementById('form-register').addEventListener('submit', function () {
        const btn = document.getElementById('btn-register');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang xử lý...';
        btn.disabled = true;
    });
</script>
@endpush
