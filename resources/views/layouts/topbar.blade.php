{{-- resources/views/layouts/topbar.blade.php --}}
<header class="topbar d-flex align-items-center justify-content-between">

    {{-- Left: Toggle + Breadcrumb --}}
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm" id="sidebarToggle"
                style="background: #f1f5f9; border: 1px solid #e2e8f0; color: var(--text-secondary); border-radius: 8px; padding: 6px 10px;">
            <i class="bi bi-list fs-5"></i>
        </button>

        {{-- Page breadcrumb --}}
        <nav aria-label="breadcrumb" class="d-none d-md-flex">
            <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}" style="color: var(--text-secondary); text-decoration: none;">
                        <i class="bi bi-house-fill me-1"></i>Trang chủ
                    </a>
                </li>
                @php
                    $segment1 = request()->segment(1);
                    $segment2 = request()->segment(2);
                    
                    $translationMap = [
                        'dashboard'       => 'Tổng quan',
                        'staff'           => 'Nhân viên',
                        'customers'       => 'Khách hàng',
                        'tables'          => 'Bàn chơi',
                        'bookings'        => 'Đặt bàn',
                        'sessions'        => 'Phiên chơi',
                        'invoices'        => 'Hóa đơn',
                        'products'        => 'Sản phẩm',
                        'categories'      => 'Danh mục',
                        'change-password' => 'Đổi mật khẩu',
                    ];
                    
                    $translatedSegment1 = $translationMap[$segment1] ?? ($segment1 ? ucfirst(str_replace('-', ' ', $segment1)) : 'Tổng quan');
                @endphp
                
                @if ($segment2)
                    <li class="breadcrumb-item">
                        <a href="{{ url($segment1) }}" style="color: var(--text-secondary); text-decoration: none;">
                            {{ $translatedSegment1 }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-muted">
                        @if ($segment2 === 'create')
                            Thêm mới
                        @elseif ($segment2 === 'edit' || request()->segment(3) === 'edit')
                            Chỉnh sửa
                        @else
                            Chi tiết
                        @endif
                    </li>
                @else
                    <li class="breadcrumb-item active text-muted">
                        {{ $translatedSegment1 }}
                    </li>
                @endif
            </ol>
        </nav>
    </div>

    {{-- Right: User info + Logout --}}
    <div class="d-flex align-items-center gap-3">
        {{-- Current time display --}}
        <div class="d-none d-lg-flex align-items-center gap-1" style="font-size: 0.8rem; color: var(--text-secondary);">
            <i class="bi bi-clock me-1"></i>
            <span id="topbarClock"></span>
        </div>

        <div style="width: 1px; height: 24px; background: #e2e8f0;"></div>

        {{-- User dropdown --}}
        <div class="dropdown">
            <button class="btn btn-sm d-flex align-items-center gap-2 px-2"
                    style="background: transparent; border: none;"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem; background: var(--primary-glow); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
                </div>
                <div class="d-none d-md-block text-start">
                    <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); line-height: 1.2;">{{ Auth::user()->name ?? 'Khách' }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-secondary);">{{ Auth::user()->role->name ?? '' }}</div>
                </div>
                <i class="bi bi-chevron-down d-none d-md-inline" style="font-size: 0.65rem; color: var(--text-secondary);"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 180px; margin-top: 8px;">
                <li>
                    <a class="dropdown-item" href="{{ route('auth.change-password') }}">
                        <i class="bi bi-key-fill" style="color: var(--warning);"></i> Đổi mật khẩu
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('auth.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

@push('scripts')
<script>
(function () {
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        const now = new Date();
        const el = document.getElementById('topbarClock');
        if (el) {
            el.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        }
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
@endpush
