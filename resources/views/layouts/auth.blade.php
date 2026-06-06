{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hệ thống quản lý quán Billiards">
    <title>@yield('title', 'Đăng nhập') — Billiards Management</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        :root {
            --primary: #667eea;
            --primary-hover: #764ba2;
            --background: #0f172a;
            --card-bg: #1e293b;
            --text-main: #ffffff;
            --text-muted: #e2e8f0;
        }

        body {
            background-color: var(--background);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 460px;
        }

        /* Logo & brand */
        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 1.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4);
        }

        .auth-brand-name {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin: 0;
        }

        .auth-brand-sub {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin: 0;
        }

        /* Card */
        .auth-card {
            border-radius: 1.5rem;
            padding: 2.5rem;
        }

        .auth-card-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .auth-card-subtitle {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        /* Form controls */
        .form-label {
            color: #e2e8f0;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.45rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.75rem;
            color: #fff;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            color: #fff;
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.08);
        }

        /* Input group */
        .input-group-text {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.7);
            border-radius: 0.75rem 0 0 0.75rem;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 0.75rem 0.75rem 0;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .btn-toggle-pw {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-left: none;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 0 0.75rem 0.75rem 0;
            padding: 0 0.9rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-toggle-pw:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Error feedback */
        .invalid-feedback {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 0.35rem;
        }

        .invalid-feedback.d-block {
            display: block !important;
        }

        /* Checkbox */
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            width: 1rem;
            height: 1rem;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            color: #cbd5e1;
            font-size: 0.875rem;
        }

        /* Btn submit */
        .btn-auth {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 0.75rem;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.8rem 1rem;
            width: 100%;
            letter-spacing: 0.2px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.4s;
        }

        .btn-auth:hover::before { left: 100%; }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(102, 126, 234, 0.45);
            color: #fff;
        }

        .btn-auth:active { transform: translateY(0); }

        .btn-auth:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
        }

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
        }

        .auth-divider span {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        /* Links */
        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .auth-link:hover { color: var(--primary-hover); }

        .auth-footer-text {
            color: var(--text-muted);
            font-size: 0.875rem;
            text-align: center;
            margin: 0;
        }

        /* Alerts */
        .alert {
            border-radius: 0.75rem;
            font-size: 0.875rem;
            border: none;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">

        {{-- Brand --}}
        <div class="auth-brand">
            <div class="auth-logo">🎱</div>
            <p class="auth-brand-name">Billiards Management</p>
            <p class="auth-brand-sub">Hệ thống quản lý quán bi-a</p>
        </div>

        {{-- Card --}}
        <div class="auth-card glass-panel">
            @yield('content')
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
