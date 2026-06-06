@extends('layouts.app')

@section('title', 'Chi tiết đặt bàn')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chi tiết đặt bàn #{{ $booking->id }}</h1>
            <p class="text-muted mb-0">Xem thông tin đặt bàn của khách hàng và cập nhật tình trạng.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-3">
        <!-- Left Column: Booking Info & Status -->
        <div class="col-12 col-md-7">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Thông tin Lịch đặt</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <span class="text-xs text-muted d-block mb-1">Ngày đặt chơi</span>
                            <span class="fw-bold text-dark fs-6">{{ $booking->booking_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-xs text-muted d-block mb-1">Trạng thái hiện tại</span>
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
                            <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} py-1.5 px-3">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="text-xs text-muted d-block mb-1">Giờ bắt đầu</span>
                            <span class="fw-bold text-dark fs-6"><i class="bi bi-clock me-1 text-primary"></i>{{ $booking->start_time->format('H:i') }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-xs text-muted d-block mb-1">Giờ kết thúc</span>
                            <span class="fw-bold text-dark fs-6"><i class="bi bi-clock me-1 text-danger"></i>{{ $booking->end_time->format('H:i') }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-xs text-muted d-block mb-1">Thời gian tạo lịch</span>
                            <span class="text-dark">{{ $booking->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-xs text-muted d-block mb-1">Ghi chú từ khách hàng</span>
                            <div class="p-3 bg-light rounded text-dark italic" style="font-size: 0.85rem; min-height: 80px;">
                                {{ $booking->note ?? 'Không có ghi chú nào.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex justify-content-end gap-2">
                    @if($booking->status === 'PENDING')
                        <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" class="m-0">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> Xác nhận đặt bàn
                            </button>
                        </form>
                    @endif

                    @if($booking->status === 'PENDING' || $booking->status === 'CONFIRMED')
                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc muốn hủy đặt bàn này?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-2">
                                <i class="bi bi-x-circle-fill"></i> Hủy đặt bàn
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('bookings.index') }}" class="btn btn-light border">Quay lại</a>
                </div>
            </div>
        </div>

        <!-- Right Column: Customer & Table Info -->
        <div class="col-12 col-md-5">
            <!-- Customer Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Khách hàng</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center text-primary fw-bold me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                            {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0.5 text-dark">{{ $booking->user->name }}</h6>
                            <span class="badge bg-light text-muted border">ID: {{ $booking->user_id }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="text-xs text-muted d-block mb-1">Số điện thoại:</span>
                        <a href="tel:{{ $booking->user->phone }}" class="text-dark fw-semibold text-decoration-none">
                            <i class="bi bi-telephone me-1.5 text-muted"></i>{{ $booking->user->phone ?? 'Chưa cập nhật' }}
                        </a>
                    </div>

                    <div>
                        <span class="text-xs text-muted d-block mb-1">Email liên lạc:</span>
                        <a href="mailto:{{ $booking->user->email }}" class="text-dark text-decoration-none">
                            <i class="bi bi-envelope me-1.5 text-muted"></i>{{ $booking->user->email }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Billiard Table Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Bàn chơi liên kết</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">Bàn {{ $booking->billiardTable->table_number }}</h6>
                        <span class="badge bg-light text-dark border">{{ $booking->billiardTable->table_type }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="text-xs text-muted d-block mb-1">Đơn giá giờ chơi:</span>
                        <span class="fw-bold fs-5 text-dark">{{ number_format($booking->billiardTable->price_per_hour, 0, ',', '.') }} VNĐ/giờ</span>
                    </div>

                    <div>
                        <span class="text-xs text-muted d-block mb-1">Mô tả bàn chơi:</span>
                        <p class="text-muted text-xs mb-0 italic">
                            {{ $booking->billiardTable->description ?? 'Không có thông tin mô tả cụ thể.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-xs {
        font-size: 0.75rem;
    }
    .badge {
        font-weight: 600;
    }
</style>
@endsection
