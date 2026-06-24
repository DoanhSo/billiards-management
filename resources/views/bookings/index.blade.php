@extends('layouts.app')

@section('title', 'Quản lý Đặt bàn')

@section('content')
<div class="container-fluid px-0">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-calendar-check-fill me-2" style="color: var(--primary)"></i>Quản lý Đặt bàn</h1>
            <p class="page-subtitle">Theo dõi, xác nhận và quản lý lịch đặt bàn từ khách hàng</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.history') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
                <i class="bi bi-clock-history"></i> Lịch sử
            </a>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary" style="height: 40px; display: inline-flex; align-items: center;">
                <i class="bi bi-plus-circle-fill"></i> Đặt bàn mới
            </a>
        </div>
    </div>

    {{-- ═══ STAT CARDS ═══ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Chờ xác nhận</div>
                        <div class="stat-value" style="color: var(--warning)">{{ $summary['PENDING'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Đã xác nhận</div>
                        <div class="stat-value" style="color: var(--success)">{{ $summary['CONFIRMED'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Hoàn thành</div>
                        <div class="stat-value" style="color: var(--info)">{{ $summary['COMPLETED'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-secondary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Đã hủy</div>
                        <div class="stat-value" style="color: var(--danger)">{{ $summary['CANCELLED'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon icon-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ FILTER BAR ═══ --}}
    <div class="filter-bar mb-4" style="background: var(--bg-surface); border-radius: 12px; padding: 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <form action="{{ route('bookings.index') }}" method="GET" class="ajax-search-form" novalidate>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted border-end-0" style="background: transparent;"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" style="height: 40px;"
                               placeholder="Tên, SĐT khách, số bàn..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Trạng thái đặt bàn</label>
                    <select name="status" class="form-select" style="height: 40px;">
                        <option value="">Tất cả trạng thái</option>
                        <option value="PENDING"   {{ $status === 'PENDING'   ? 'selected' : '' }}>🟡 Chờ xác nhận</option>
                        <option value="CONFIRMED" {{ $status === 'CONFIRMED' ? 'selected' : '' }}>🟢 Đã xác nhận</option>
                        <option value="COMPLETED" {{ $status === 'COMPLETED' ? 'selected' : '' }}>🔵 Hoàn thành</option>
                        <option value="CANCELLED" {{ $status === 'CANCELLED' ? 'selected' : '' }}>🔴 Đã hủy</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-funnel-fill me-1"></i> Lọc
                    </button>
                    @if($search || $status)
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div id="searchable-content">
        {{-- ═══ DATA TABLE ═══ --}}
        <x-card>
            <x-table>
            <x-slot:thead>
                <tr>
                    <th style="width: 50px; padding-left: 20px !important;">#</th>
                    <th>Khách hàng</th>
                    <th>Bàn chơi</th>
                    <th>Ngày đặt</th>
                    <th>Khung giờ</th>
                    <th>Trạng thái</th>
                    <th style="width: 210px; text-align: right; padding-right: 20px !important;">Thao tác</th>
                </tr>
            </x-slot:thead>

            @forelse($bookings as $index => $booking)
                @php
                    $statusConfig = match($booking->status) {
                        'PENDING'   => ['badge' => 'badge-pending',   'label' => 'Chờ xác nhận'],
                        'CONFIRMED' => ['badge' => 'badge-confirmed', 'label' => 'Đã xác nhận'],
                        'COMPLETED' => ['badge' => 'badge-completed', 'label' => 'Hoàn thành'],
                        'CANCELLED' => ['badge' => 'badge-cancelled', 'label' => 'Đã hủy'],
                        default     => ['badge' => 'badge-maintenance', 'label' => $booking->status],
                    };
                @endphp
                <tr>
                    {{-- STT --}}
                    <td style="padding-left: 20px !important; color: var(--text-secondary); font-weight: 600;">
                        {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}
                    </td>

                    {{-- Khách hàng --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="background: var(--primary-glow); color: var(--primary); font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($booking->user?->name ?? 'K', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">{{ $booking->user?->name ?? 'Khách vãng lai' }}</div>
                                <div class="text-xxs" style="color: var(--text-secondary); font-size: 0.75rem;">
                                    <i class="bi bi-telephone me-1"></i>{{ $booking->user?->phone ?? 'Chưa có SĐT' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Bàn chơi --}}
                    <td>
                        <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">Bàn {{ $booking->billiardTable->table_number }}</div>
                        <div class="text-xxs" style="color: var(--text-secondary);">{{ $booking->billiardTable->table_type }}</div>
                    </td>

                    {{-- Ngày đặt --}}
                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $booking->booking_date->format('d/m/Y') }}</div>
                    </td>

                    {{-- Khung giờ --}}
                    <td>
                        <div style="font-size: 0.875rem; color: var(--text-primary);">
                            <span style="font-weight: 700; color: var(--success)">{{ $booking->start_time->format('H:i') }}</span>
                            <span style="color: var(--text-secondary); margin: 0 4px;">→</span>
                            <span style="font-weight: 700; color: var(--danger)">{{ $booking->end_time->format('H:i') }}</span>
                        </div>
                    </td>

                    {{-- Trạng thái --}}
                    <td>
                        <span class="badge {{ $statusConfig['badge'] }}">{{ $statusConfig['label'] }}</span>
                    </td>

                    {{-- Thao tác --}}
                    <td style="text-align: right; padding-right: 16px !important;">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('bookings.show', $booking->id) }}"
                               class="btn btn-light btn-sm" style="height: 32px; font-size: 0.8rem; display: inline-flex; align-items: center;" title="Xem chi tiết">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>

                            @if(($booking->status === 'PENDING') && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                                <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" class="m-0">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm" style="height: 32px; font-size: 0.8rem; display: inline-flex; align-items: center;" title="Xác nhận đặt bàn">
                                        <i class="bi bi-check-lg"></i> Nhận
                                    </button>
                                </form>
                            @endif

                            @if(($booking->status === 'CONFIRMED') && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                                <form action="{{ route('bookings.complete', $booking->id) }}" method="POST" class="m-0">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-info btn-sm text-white" style="height: 32px; font-size: 0.8rem; display: inline-flex; align-items: center;" title="Hoàn tất">
                                        <i class="bi bi-check2-all"></i> Xong
                                    </button>
                                </form>
                            @endif

                            @if($booking->status === 'PENDING' || $booking->status === 'CONFIRMED')
                                <button type="button" class="btn btn-outline-danger btn-sm" style="height: 32px; font-size: 0.8rem; display: inline-flex; align-items: center;" title="Hủy đặt bàn"
                                        data-bs-toggle="modal" data-bs-target="#cancelModal"
                                        data-booking-id="{{ $booking->id }}"
                                        data-booking-user="{{ addslashes($booking->user?->name ?? 'Khách vãng lai') }}">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-dark">Không có lịch đặt bàn nào</h5>
                            <p class="text-secondary">Thử thay đổi bộ lọc hoặc <a href="{{ route('bookings.create') }}" style="color: var(--primary); font-weight: 600;">tạo lịch đặt bàn mới</a>.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

        {{-- ═══ PAGINATION ═══ --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $bookings->links() }}
        </div>
    </div>

</div>

{{-- ═══ CANCEL CONFIRM MODAL ═══ --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content" style="background: var(--bg-surface); border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-body p-4 text-center">
                <div class="mb-3" style="width: 64px; height: 64px; border-radius: 50%; background: rgba(220,38,38,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="bi bi-x-circle-fill" style="font-size: 1.75rem; color: var(--danger);"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.1rem;">Xác nhận hủy đặt bàn</h5>
                <p class="text-secondary mb-1" style="font-size: 0.9rem;">
                    Bạn có chắc muốn hủy lịch đặt bàn của <strong id="modalBookingUser" style="color: var(--danger);"></strong>?
                </p>
                <p class="text-muted mb-4" style="font-size: 0.8rem;">Hành động này <strong>không thể hoàn tác</strong>.</p>

                <form id="cancelForm" method="POST" novalidate>
                    @csrf
                    @method('PATCH')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="height: 40px; min-width: 100px;">
                            <i class="bi bi-arrow-return-left me-1"></i> Quay lại
                        </button>
                        <button type="submit" class="btn btn-danger" style="height: 40px; min-width: 120px;">
                            <i class="bi bi-x-circle-fill me-1"></i> Hủy lịch
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
    const cancelModal = document.getElementById('cancelModal');
    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            const bookingId   = btn.getAttribute('data-booking-id');
            const bookingUser = btn.getAttribute('data-booking-user');

            document.getElementById('modalBookingUser').textContent = bookingUser;
            
            // Cập nhật action cho form
            const form = document.getElementById('cancelForm');
            form.action = `/bookings/${bookingId}/cancel`;
        });
    }
});
</script>
@endpush
@endsection
 
 


