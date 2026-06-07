{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Đăng nhập')

@section('content')
    <h1 class="auth-card-title">Chào mừng trở lại!</h1>
    <p class="auth-card-subtitle">Đăng nhập để quản lý quán billiards của bạn.</p>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Form --}}
    <form id="form-login" method="POST" action="{{ route('auth.login.post') }}" novalidate>
        @csrf

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
                    placeholder="admin@billiards.com"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    autofocus
                >
            </div>
            @error('email')
                <div class="invalid-feedback d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Mật khẩu --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">Mật khẩu</label>
            </div>
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
                    autocomplete="current-password"
                >
                <button type="button" class="btn-toggle-pw" id="btn-toggle-password" title="Hiện/ẩn mật khẩu">
                    <i class="bi bi-eye" id="icon-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Ghi nhớ đăng nhập --}}
        <div class="mb-4">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="remember"
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth" id="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
        </button>

        {{-- Link Đăng ký --}}
        <div class="mt-4 text-center">
            <span class="text-muted">Chưa có tài khoản?</span>
            <a href="{{ route('auth.register') }}" class="text-decoration-none fw-semibold" style="color: var(--primary);">Đăng ký ngay</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Toggle hiện / ẩn mật khẩu
    document.getElementById('btn-toggle-password').addEventListener('click', function () {
        const input   = document.getElementById('password');
        const icon    = document.getElementById('icon-eye');
        const isHidden = input.type === 'password';
        input.type     = isHidden ? 'text' : 'password';
        icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    // Loading state khi submit
    document.getElementById('form-login').addEventListener('submit', function () {
        const btn = document.getElementById('btn-login');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang đăng nhập...';
        btn.disabled  = true;
    });
</script>
@endpush
