{{-- resources/views/layouts/topbar.blade.php --}}
<header class="topbar d-flex align-items-center justify-content-between px-4">
    <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted">{{ Auth::user()->name ?? 'Khách' }}</span>
        <form action="{{ route('auth.logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </button>
        </form>
    </div>
</header>
