{{-- resources/views/layouts/topbar.blade.php --}}
<header class="topbar">
    <button class="btn btn-sm" id="sidebarToggle"
            style="background: var(--bg-elevated); border: 1px solid var(--border-light); color: var(--text-secondary); border-radius: 8px; padding: 6px 10px;">
        <i class="bi bi-list fs-5"></i>
    </button>

    <div class="d-flex align-items-center gap-3">
        {{-- User info --}}
        <div class="d-flex align-items-center gap-2">
            <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
            </div>
            <div class="d-none d-md-block">
                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">{{ Auth::user()->name ?? 'Khách' }}</div>
                <div style="font-size: 0.7rem; color: var(--text-muted-c);">{{ Auth::user()->role->name ?? '' }}</div>
            </div>
        </div>

        <div style="width: 1px; height: 24px; background: var(--border-light);"></div>

        {{-- Logout --}}
        <form action="{{ route('auth.logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-md-inline">Đăng xuất</span>
            </button>
        </form>
    </div>
</header>
