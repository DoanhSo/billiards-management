@extends('layouts.app')

@section('title', 'Quản lý Bàn chơi')

@section('content')
<div class="container-fluid px-0">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-grid-3x3-gap-fill me-2" style="color: var(--primary)"></i>Quản lý Bàn chơi</h1>
            <p class="page-subtitle">Giám sát và cập nhật trạng thái bàn theo thời gian thực</p>
        </div>
        <a href="{{ route('tables.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill"></i> Thêm bàn mới
        </a>
    </div>

    {{-- ═══ STAT CARDS ═══ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Bàn trống</div>
                        <div class="stat-value">{{ $summary['AVAILABLE'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card stat-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Đang chơi</div>
                        <div class="stat-value" style="color: var(--primary)">{{ $summary['PLAYING'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-primary">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card stat-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Đã đặt trước</div>
                        <div class="stat-value" style="color: var(--warning)">{{ $summary['RESERVED'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card stat-secondary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Bảo trì</div>
                        <div class="stat-value" style="color: var(--secondary-color)">{{ $summary['MAINTENANCE'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-secondary">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ FILTER BAR ═══ --}}
    <div class="filter-bar mb-4">
        <form action="{{ route('tables.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label">Tìm kiếm bàn</label>
                    <div class="input-group glow-on-focus">
                        <span class="input-group-text" style="border-right: none"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" style="border-left: none" placeholder="Số bàn, loại bàn..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="AVAILABLE"   {{ $status === 'AVAILABLE'   ? 'selected' : '' }}>✅ Sẵn sàng</option>
                        <option value="PLAYING"     {{ $status === 'PLAYING'     ? 'selected' : '' }}>🎱 Đang chơi</option>
                        <option value="RESERVED"    {{ $status === 'RESERVED'    ? 'selected' : '' }}>📅 Đã đặt trước</option>
                        <option value="MAINTENANCE" {{ $status === 'MAINTENANCE' ? 'selected' : '' }}>🔧 Bảo trì</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel-fill"></i> Lọc
                    </button>
                    @if($search || $status)
                        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ═══ TABLE GRID CARDS ═══ --}}
    <div class="row g-3 mb-4">
        @forelse($tables as $table)
            @php
                $statusConfig = match($table->status) {
                    'AVAILABLE'   => ['badge' => 'badge-available',   'label' => 'Sẵn sàng',     'icon' => 'check-circle-fill',    'bar' => 'var(--success)'],
                    'PLAYING'     => ['badge' => 'badge-playing',     'label' => 'Đang chơi',    'icon' => 'play-circle-fill',     'bar' => 'var(--primary)'],
                    'RESERVED'    => ['badge' => 'badge-reserved',    'label' => 'Đã đặt trước', 'icon' => 'calendar-event-fill',  'bar' => 'var(--warning)'],
                    'MAINTENANCE' => ['badge' => 'badge-maintenance', 'label' => 'Bảo trì',      'icon' => 'tools',                'bar' => 'var(--secondary-color)'],
                    default       => ['badge' => 'badge-maintenance', 'label' => $table->status, 'icon' => 'question-circle',      'bar' => 'var(--secondary-color)'],
                };
                $typeLabel = match($table->table_type) {
                    'POOL'    => 'Pool (Bida Lỗ)',
                    'SNOOKER' => 'Snooker',
                    'CAROM'   => 'Carom (Bida Phăng)',
                    default   => $table->table_type,
                };
            @endphp

            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card table-card h-100" style="border-top: 3px solid {{ $statusConfig['bar'] }} !important;">
                    <div class="card-body d-flex flex-column p-4">

                        {{-- Header: Number + Status --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="text-xs mb-1" style="color: var(--text-muted-c); text-transform: uppercase; letter-spacing: 0.05em;">Bàn số</div>
                                <div class="card-number" style="color: var(--text-primary)">{{ $table->table_number }}</div>
                            </div>
                            <span class="badge {{ $statusConfig['badge'] }}">
                                <i class="bi bi-{{ $statusConfig['icon'] }} me-1"></i>{{ $statusConfig['label'] }}
                            </span>
                        </div>

                        {{-- Type Chip --}}
                        <div class="mb-3">
                            <span style="background: var(--bg-elevated); border: 1px solid var(--border-light); color: var(--text-secondary); font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 6px;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem; vertical-align: middle; color: {{ $statusConfig['bar'] }}"></i>
                                {{ $typeLabel }}
                            </span>
                        </div>

                        {{-- Price --}}
                        <div class="mb-3">
                            <div class="text-xs mb-1" style="color: var(--text-muted-c);">Giá thuê / giờ</div>
                            <div style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em; color: var(--success);">
                                {{ number_format($table->price_per_hour, 0, ',', '.') }}
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">VNĐ</span>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if($table->description)
                            <p class="text-truncate-2 text-xs mb-3" style="color: var(--text-secondary); flex: 1" title="{{ $table->description }}">
                                <i class="bi bi-info-circle me-1"></i>{{ $table->description }}
                            </p>
                        @else
                            <p class="text-xs mb-3" style="color: var(--text-muted-c); flex: 1; font-style: italic;">Chưa có mô tả</p>
                        @endif

                        <div class="divider"></div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            {{-- Status Dropdown --}}
                            <div class="dropdown flex-grow-1">
                                <button class="btn btn-light btn-sm w-100 d-flex justify-content-between align-items-center"
                                        type="button" data-bs-toggle="dropdown">
                                    <span><i class="bi bi-arrow-repeat me-1"></i>Đổi trạng thái</span>
                                    <i class="bi bi-chevron-down" style="font-size: 0.65rem"></i>
                                </button>
                                <ul class="dropdown-menu w-100">
                                    @foreach([
                                        ['AVAILABLE',   'check-circle',    'var(--success)', 'Sẵn sàng'],
                                        ['PLAYING',     'play-circle',     'var(--primary)', 'Đang chơi'],
                                        ['RESERVED',    'calendar-event',  'var(--warning)', 'Đã đặt trước'],
                                        ['MAINTENANCE', 'tools',           'var(--secondary-color)', 'Bảo trì'],
                                    ] as [$val, $ico, $col, $lbl])
                                        <li>
                                            <form action="{{ route('tables.update-status', $table->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $val }}">
                                                <button type="submit" class="dropdown-item {{ $table->status === $val ? 'active' : '' }}">
                                                    <i class="bi bi-{{ $ico }}" style="color: {{ $col }}"></i>
                                                    {{ $lbl }}
                                                    @if($table->status === $val)<i class="bi bi-check ms-auto" style="color: var(--success)"></i>@endif
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Show --}}
                            <a href="{{ route('tables.show', $table->id) }}" class="btn btn-outline-info btn-sm" title="Chi tiết & Phụ kiện">
                                <i class="bi bi-info-circle-fill"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('tables.edit', $table->id) }}" class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            {{-- Delete --}}
                            @if($table->status === 'AVAILABLE')
                                <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa bàn"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-table-id="{{ $table->id }}"
                                    data-table-number="{{ $table->table_number }}"
                                    data-table-type="{{ $table->table_type }}">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            @else
                                <button class="btn btn-sm" style="background: #f1f5f9; border: 1px solid #e2e8f0; cursor: not-allowed;" title="Không thể xóa khi bàn đang {{ strtolower($statusConfig['label']) }}" disabled>
                                    <i class="bi bi-trash3" style="color: #94a3b8"></i>
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <h5>Không tìm thấy bàn chơi nào</h5>
                        <p>Thử thay đổi từ khóa hoặc bộ lọc, hoặc <a href="{{ route('tables.create') }}" style="color: var(--primary)">thêm bàn mới</a>.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ═══ PAGINATION ═══ --}}
    <div class="d-flex justify-content-end">
        {{ $tables->links() }}
    </div>

</div>

{{-- ═══ DELETE CONFIRM MODAL ═══ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content" style="background: var(--bg-surface); border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-body p-4 text-center">
                <div class="mb-3" style="width: 64px; height: 64px; border-radius: 50%; background: rgba(220,38,38,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="bi bi-trash3-fill" style="font-size: 1.75rem; color: var(--danger);"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.1rem;">Xác nhận xóa bàn</h5>
                <p class="text-secondary mb-1" style="font-size: 0.9rem;">
                    Bạn có chắc muốn xóa
                    <strong id="modalTableNumber" style="color: var(--danger);"></strong>
                    (<span id="modalTableType" style="color: var(--text-secondary);"></span>)?
                </p>
                <p class="text-muted mb-4" style="font-size: 0.8rem;">Hành động này <strong>không thể hoàn tác</strong>.</p>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="height: 40px; min-width: 100px;">
                            <i class="bi bi-x-lg me-1"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-danger" style="height: 40px; min-width: 120px;">
                            <i class="bi bi-trash3-fill me-1"></i> Xóa bàn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            const tableId     = btn.getAttribute('data-table-id');
            const tableNumber = btn.getAttribute('data-table-number');
            const tableType   = btn.getAttribute('data-table-type');

            document.getElementById('modalTableNumber').textContent = 'Bàn ' + tableNumber;
            document.getElementById('modalTableType').textContent   = tableType;

            const baseUrl = '{{ url('/tables') }}';
            document.getElementById('deleteForm').action = baseUrl + '/' + tableId;
        });
    }
});
</script>
@endpush
@endsection
