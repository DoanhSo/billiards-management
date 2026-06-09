{{-- resources/views/layouts/sidebar.blade.php --}}
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-between px-4" style="height: 64px; border-bottom: 1px solid var(--border-light);">
        <div class="d-flex align-items-center gap-2">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; color: white;">
                <i class="bi bi-heptagon-fill fs-5"></i>
            </div>
            <span class="fw-bold fs-5" style="color: var(--text-primary); letter-spacing: -0.5px;">BilliardsPro</span>
        </div>
        {{-- Close button for mobile --}}
        <button id="sidebarClose" class="btn btn-sm d-md-none text-secondary">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-scrollable" style="overflow-y: auto; overflow-x: hidden; padding: 16px;">
        <ul class="sidebar-menu list-unstyled m-0 d-flex flex-column gap-1">
            <li class="sidebar-label text-uppercase mb-2 mt-1 px-3" style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted-c); letter-spacing: 0.5px;">Trang chủ</li>
            <li>
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Tổng quan
                </a>
            </li>

            <li class="sidebar-label text-uppercase mb-2 mt-4 px-3" style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted-c); letter-spacing: 0.5px;">Dịch vụ & Bàn</li>
            @if(auth()->user()->isAdmin())
            <li>
                <a href="{{ route('tables.index') }}" class="{{ request()->routeIs('tables.*') ? 'active' : '' }}">
                    <i class="bi bi-layout-wtf"></i> Quản lý bàn
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check-fill"></i> Đặt bàn trước
                </a>
            </li>
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <li>
                <a href="{{ route('sessions.index') }}" class="{{ request()->routeIs('sessions.*') ? 'active' : '' }}">
                    <i class="bi bi-play-circle-fill"></i> Phiên chơi
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <li class="sidebar-label text-uppercase mb-2 mt-4 px-3" style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted-c); letter-spacing: 0.5px;">Cửa hàng</li>
            <li>
                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i> Sản phẩm
                </a>
            </li>
            @if(auth()->user()->isAdmin())
            <li>
                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i> Danh mục
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i> Hóa đơn
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin())
            <li class="sidebar-label text-uppercase mb-2 mt-4 px-3" style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted-c); letter-spacing: 0.5px;">Hệ thống</li>
            <li>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Người dùng
                </a>
            </li>
            @endif
        </ul>
    </div>
</nav>

{{-- Overlay for mobile sidebar --}}
<div id="sidebarOverlay" class="sidebar-overlay d-md-none"></div>

<style>
/* CSS cho Sidebar Overlay */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    z-index: 995;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Sidebar Responsive Logic */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    .sidebar.show {
        transform: translateX(0);
    }
    .main-content {
        margin-left: 0 !important;
    }
}
</style>
