@extends('layouts.app')

@section('title', 'Quản lý đặt bàn')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quản lý Đặt bàn</h1>
            <p class="text-muted mb-0">Theo dõi, xác nhận và quản lý danh sách đặt bàn chơi từ khách hàng.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.history') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-clock-history"></i> Lịch sử đặt bàn
            </a>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Đặt bàn mới
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('bookings.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label for="search" class="form-label text-xs fw-semibold text-muted mb-1">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search" name="search" class="form-control bg-light border-0 text-xs" placeholder="Tên khách hàng, số bàn..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label text-xs fw-semibold text-muted mb-1">Trạng thái đặt bàn</label>
                    <select id="status" name="status" class="form-select bg-light border-0 text-xs">
                        <option value="">Tất cả trạng thái</option>
                        <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
                        <option value="CONFIRMED" {{ $status === 'CONFIRMED' ? 'selected' : '' }}>Đã xác nhận (Confirmed)</option>
                        <option value="COMPLETED" {{ $status === 'COMPLETED' ? 'selected' : '' }}>Hoàn thành (Completed)</option>
                        <option value="CANCELLED" {{ $status === 'CANCELLED' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 text-xs py-2">Lọc dữ liệu</button>
                    @if($search || $status)
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary w-50 text-xs py-2">Xóa lọc</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-xs">
                    <thead class="table-light text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">
                        <tr>
                            <th class="px-4 py-3" style="width: 60px;">STT</th>
                            <th class="py-3">Khách hàng</th>
                            <th class="py-3">Bàn chơi</th>
                            <th class="py-3">Ngày đặt</th>
                            <th class="py-3">Khung giờ</th>
                            <th class="py-3">Trạng thái</th>
                            <th class="px-4 py-3 text-end" style="width: 200px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $index => $booking)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-muted">
                                    {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold me-2" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0.5">{{ $booking->user->name }}</div>
                                            <div class="text-muted text-xxs">
                                                <i class="bi bi-telephone text-xxs me-0.5"></i> {{ $booking->user->phone ?? 'Chưa cập nhật SĐT' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="fw-bold text-dark">Bàn {{ $booking->billiardTable->table_number }}</span>
                                    <span class="text-muted d-block text-xxs">{{ $booking->billiardTable->table_type }}</span>
                                </td>
                                <td class="py-3 text-dark fw-medium">
                                    {{ $booking->booking_date->format('d/m/Y') }}
                                </td>
                                <td class="py-3">
                                    <span class="text-dark fw-bold">{{ $booking->start_time->format('H:i') }}</span>
                                    <span class="text-muted">→</span>
                                    <span class="text-dark fw-bold">{{ $booking->end_time->format('H:i') }}</span>
                                </td>
                                <td class="py-3">
                                    @php
                                        $badgeColor = 'secondary';
                                        $statusLabel = 'Chưa xác định';
                                        if ($booking->status === 'PENDING') {
                                            $badgeColor = 'warning';
                                            $statusLabel = 'Chờ xác nhận';
                                        } elseif ($booking->status === 'CONFIRMED') {
                                            $badgeColor = 'success';
                                            $statusLabel = 'Đã xác nhận';
                                        } elseif ($booking->status === 'COMPLETED') {
                                            $badgeColor = 'info';
                                            $statusLabel = 'Hoàn thành';
                                        } elseif ($booking->status === 'CANCELLED') {
                                            $badgeColor = 'danger';
                                            $statusLabel = 'Đã hủy';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} py-1 px-2.5">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- Show Details -->
                                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-light border py-1.5" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i> Chi tiết
                                        </a>

                                        @if($booking->status === 'PENDING')
                                            <!-- Confirm Booking -->
                                            <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" class="m-0">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success py-1.5">
                                                    <i class="bi bi-check-lg"></i> Nhận
                                                </button>
                                            </form>
                                        @endif

                                        @if($booking->status === 'CONFIRMED')
                                            <!-- Complete Booking -->
                                            <form action="{{ route('bookings.complete', $booking->id) }}" method="POST" class="m-0">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-info text-white py-1.5">
                                                    <i class="bi bi-check2-all"></i> Hoàn tất
                                                </button>
                                            </form>
                                        @endif

                                        @if($booking->status === 'PENDING' || $booking->status === 'CONFIRMED')
                                            <!-- Cancel Booking -->
                                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc muốn hủy lịch đặt bàn này?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1.5">
                                                    <i class="bi bi-x-circle"></i> Hủy
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                                    <h5 class="fw-semibold text-dark mb-1">Không có lịch đặt bàn nào</h5>
                                    <p class="text-muted mb-0">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end mt-3">
        {{ $bookings->links() }}
    </div>
</div>

<style>
    .text-xs {
        font-size: 0.85rem;
    }
    .text-xxs {
        font-size: 0.75rem;
    }
    .text-xxs i {
        font-size: 0.7rem;
    }
    .badge {
        font-weight: 600;
        font-size: 0.75rem;
    }
</style>
@endsection
