{{-- resources/views/layouts/sidebar.blade.php --}}
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-circle-fill me-2"></i>Billiards</h4>
    </div>
    <ul class="sidebar-menu list-unstyled">
        <li>
            <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Quản lý người dùng
            </a>
        </li>
        <li>
            <a href="{{ route('tables.index') }}" class="{{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <i class="bi bi-table"></i> Quản lý bàn
            </a>
        </li>
        <li>
            <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Đặt bàn
            </a>
        </li>
        <li>
            <a href="{{ route('sessions.index') }}" class="{{ request()->routeIs('sessions.*') ? 'active' : '' }}">
                <i class="bi bi-play-circle"></i> Phiên chơi
            </a>
        </li>
        <li>
            <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Hóa đơn
            </a>
        </li>
        <li>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Sản phẩm
            </a>
        </li>
        <li>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Danh mục
            </a>
        </li>
    </ul>
</nav>
