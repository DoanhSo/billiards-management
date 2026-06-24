{{-- resources/views/auth/change-password.blade.php --}}
@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-header border-0 pt-4 px-4" style="background: transparent; border-radius: 1rem 1rem 0 0;">
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>Đổi mật khẩu
                </h4>
                <p class="text-muted small mb-0">Cập nhật mật khẩu để bảo mật tài khoản của bạn.</p>
            </div>

            <div class="card-body px-4 pb-4">

                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center mt-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif



                <form id="form-change-password"
                      method="POST"
                      action="{{ route('auth.change-password.post') }}"
                      class="mt-3"
                      novalidate>
                    @csrf

                    {{-- Mật khẩu hiện tại --}}
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-medium">
                            Mật khẩu hiện tại
                        </label>
                        <div class="input-group input-group-password">
                            <span class="input-group-text" style="background: transparent;">
                                <i class="bi bi-lock text-secondary"></i>
                            </span>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="Nhập mật khẩu hiện tại"
                                autocomplete="current-password"
                            >
                            <button type="button" class="btn-toggle-pw"
                                    data-target="current_password" title="Hiện/ẩn">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
                        @enderror
                    </div>

                    {{-- Mật khẩu mới --}}
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-medium">
                            Mật khẩu mới
                        </label>
                        <div class="input-group input-group-password">
                            <span class="input-group-text" style="background: transparent;">
                                <i class="bi bi-key text-secondary"></i>
                            </span>
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror"
                                placeholder="Tối thiểu 8 ký tự"
                                autocomplete="new-password"
                            >
                            <button type="button" class="btn-toggle-pw"
                                    data-target="new_password" title="Hiện/ẩn">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
                        @enderror
                    </div>

                    {{-- Xác nhận mật khẩu mới --}}
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label fw-medium">
                            Xác nhận mật khẩu mới
                        </label>
                        <div class="input-group input-group-password">
                            <span class="input-group-text" style="background: transparent;">
                                <i class="bi bi-shield-check text-secondary"></i>
                            </span>
                            <input
                                type="password"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                placeholder="Nhập lại mật khẩu mới"
                                autocomplete="new-password"
                            >
                            <button type="button" class="btn-toggle-pw"
                                    data-target="new_password_confirmation" title="Hiện/ẩn">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('new_password_confirmation')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-primary px-4" id="btn-submit">
                            <i class="bi bi-check-lg me-1"></i>Lưu thay đổi
                        </button>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-light px-4">
                            Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
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
    document.getElementById('form-change-password').addEventListener('submit', function () {
        const btn = document.getElementById('btn-submit');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang lưu...';
        btn.disabled  = true;
    });
</script>
@endpush


