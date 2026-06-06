@extends('layouts.app')

@section('title', 'Lịch sử đặt bàn')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Lịch sử Đặt bàn</h1>
            <p class="text-muted mb-0">Xem danh sách các lịch đặt bàn đã hoàn thành hoặc đã bị hủy.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại lịch đặt hiện tại
        </a>
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
                            <th class="py-3">Ngày tạo lịch</th>
                            <th class="px-4 py-3 text-end" style="width: 120px;">Thao tác</th>
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
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary fw-bold me-2" style="width: 32px; height: 32px;">
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
                                    @if ($booking->status === 'COMPLETED')
                                        <span class="badge bg-info-subtle text-info py-1 px-2.5">
                                            Hoàn thành
                                        </span>
                                    @elseif ($booking->status === 'CANCELLED')
                                        <span class="badge bg-danger-subtle text-danger py-1 px-2.5">
                                            Đã hủy
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary py-1 px-2.5">
                                            {{ $booking->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-muted">
                                    {{ $booking->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-light border py-1.5" title="Chi tiết">
                                        <i class="bi bi-eye"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-clock-history fs-1 text-muted d-block mb-3"></i>
                                    <h5 class="fw-semibold text-dark mb-1">Không tìm thấy lịch sử đặt bàn</h5>
                                    <p class="text-muted mb-0">Hệ thống chưa ghi nhận các đặt bàn đã kết thúc.</p>
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
