@extends('layouts.app')

@section('title', 'Chi tiết đặt bàn #' . $booking->id)

@section('content')
<div class="container-fluid px-0" style="max-width: 960px;">

    @php
        $statusConfig = match($booking->status) {
            'PENDING'   => ['badge' => 'badge-pending',   'label' => 'Chờ xác nhận', 'icon' => 'hourglass-split',   'color' => 'var(--warning)'],
            'CONFIRMED' => ['badge' => 'badge-confirmed', 'label' => 'Đã xác nhận',  'icon' => 'check-circle-fill', 'color' => 'var(--success)'],
            'COMPLETED' => ['badge' => 'badge-completed', 'label' => 'Hoàn thành',   'icon' => 'check2-all',        'color' => 'var(--info)'],
            'CANCELLED' => ['badge' => 'badge-cancelled', 'label' => 'Đã hủy',       'icon' => 'x-circle-fill',     'color' => 'var(--danger)'],
            default     => ['badge' => 'badge-maintenance', 'label' => $booking->status, 'icon' => 'question-circle', 'color' => 'var(--secondary-color)'],
        };
    @endphp

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <h1 class="page-title" style="margin: 0;">Chi tiết đặt bàn</h1>
                <span style="font-size: 1.1rem; font-weight: 800; color: var(--text-muted-c);">#{{ $booking->id }}</span>
                <span class="badge {{ $statusConfig['badge'] }} d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <i class="bi bi-{{ $statusConfig['icon'] }}"></i>{{ $statusConfig['label'] }}
                </span>
            </div>
            <p class="page-subtitle">Tạo lúc {{ $booking->created_at->format('H:i — d/m/Y') }}</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left"></i> Danh sách
        </a>
    </div>

    {{-- ═══ STATUS FLOW ═══ --}}
    <div class="filter-bar mb-4" style="background: white; border-radius: 12px; padding: 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div class="status-flow flex-wrap d-flex align-items-center gap-2">
            @foreach([
                ['PENDING',   'hourglass-split',   'var(--warning)', 'Chờ xác nhận'],
                ['CONFIRMED', 'check-circle-fill',  'var(--success)', 'Đã xác nhận'],
                ['COMPLETED', 'check2-all',          'var(--info)',    'Hoàn thành'],
            ] as [$s, $ico, $col, $lbl])
                @php
                    $isActive    = $booking->status === $s;
                    $isCompleted = ($s === 'PENDING' && in_array($booking->status, ['CONFIRMED','COMPLETED']))
                                || ($s === 'CONFIRMED' && $booking->status === 'COMPLETED');
                @endphp
                <div class="status-step {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}"
                     style="{{ $isActive ? "background: var(--bg-deep); border: 1px solid {$col}; color: {$col};" : "color: var(--text-secondary);" }} padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; opacity: {{ $isActive ? '1' : ($isCompleted ? '0.8' : '0.4') }}">
                    <i class="bi bi-{{ $ico }}" style="color: {{ $col }}"></i>
                    {{ $lbl }}
                </div>
                @if($s !== 'COMPLETED')
                    <i class="bi bi-chevron-right status-arrow" style="color: var(--text-muted-c);"></i>
                @endif
            @endforeach

            @if($booking->status === 'CANCELLED')
                <div class="ms-auto">
                    <span class="badge badge-cancelled"><i class="bi bi-x-circle-fill me-1"></i>Đặt bàn đã bị hủy</span>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- ═══ LEFT — Booking Info ═══ --}}
        <div class="col-12 col-lg-7">

            {{-- Thông tin lịch đặt --}}
            <x-card>
                <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                    <i class="bi bi-calendar2-week-fill me-2" style="color: var(--primary)"></i>Thông tin Lịch đặt
                </h5>

                <div class="info-row">
                    <div class="info-label">Ngày đặt bàn</div>
                    <div class="info-value large" style="font-size: 1.2rem; font-weight: 700;">{{ $booking->booking_date->format('d/m/Y') }}</div>
                </div>

                <div class="row g-0 my-2">
                    <div class="col-6">
                        <div class="info-row" style="padding-right: 16px;">
                            <div class="info-label">Giờ bắt đầu</div>
                            <div class="info-value" style="color: var(--success); font-size: 1.5rem; font-weight: 800;">
                                <i class="bi bi-clock me-1" style="font-size: 1rem;"></i>{{ $booking->start_time->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-row">
                            <div class="info-label">Giờ kết thúc</div>
                            <div class="info-value" style="color: var(--danger); font-size: 1.5rem; font-weight: 800;">
                                <i class="bi bi-clock-fill me-1" style="font-size: 1rem;"></i>{{ $booking->end_time->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $durationMin = $booking->start_time->diffInMinutes($booking->end_time);
                    $dH = floor($durationMin / 60);
                    $dM = $durationMin % 60;
                @endphp
                <div class="info-row">
                    <div class="info-label">Thời lượng dự kiến</div>
                    <div class="info-value">
                        <span class="badge" style="background: var(--primary-glow); color: var(--primary); padding: 6px 12px; font-size: 0.85rem;">
                            <i class="bi bi-stopwatch-fill me-1"></i>
                            {{ $dH > 0 ? "{$dH} giờ" : '' }}{{ $dM > 0 ? " {$dM} phút" : '' }}
                        </span>
                    </div>
                </div>

                <div class="info-row" style="border-bottom: none;">
                    <div class="info-label">Ghi chú từ khách hàng</div>
                    <div class="p-3 bg-light rounded-3 text-secondary mt-2" style="border: 1px solid var(--border); font-size: 0.9rem;">
                        {{ $booking->note ?: 'Không có ghi chú.' }}
                    </div>
                </div>
            </x-card>

            {{-- Actions Card --}}
            @if($booking->status === 'PENDING' || $booking->status === 'CONFIRMED')
                <x-card>
                    <h6 style="font-weight: 700; color: var(--text-secondary); margin-bottom: 16px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em;">
                        Thao tác nhanh
                    </h6>
                    <div class="d-flex flex-wrap gap-3">
                        @if($booking->status === 'PENDING')
                            <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-success" style="height: 40px; display: inline-flex; align-items: center;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Xác nhận đặt bàn
                                </button>
                            </form>
                        @endif

                        @if($booking->status === 'CONFIRMED')
                            <form action="{{ route('bookings.complete', $booking->id) }}" method="POST" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-info text-white" style="height: 40px; display: inline-flex; align-items: center;">
                                    <i class="bi bi-check2-all me-1"></i> Hoàn tất đặt bàn
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="m-0"
                              onsubmit="return confirm('Hủy lịch đặt bàn này? Hành động không thể hoàn tác!')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger" style="height: 40px; display: inline-flex; align-items: center;">
                                <i class="bi bi-x-circle-fill me-1"></i> Hủy đặt bàn
                            </button>
                        </form>
                    </div>
                </x-card>
            @endif
        </div>

        {{-- ═══ RIGHT — Customer + Table ═══ --}}
        <div class="col-12 col-lg-5">

            {{-- Customer Card --}}
            <x-card>
                <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                    <i class="bi bi-person-fill me-2" style="color: var(--primary)"></i>Khách hàng
                </h5>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar-circle avatar-circle-lg" style="background: var(--primary-glow); color: var(--primary); font-weight: 700; width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        {{ strtoupper(substr($booking->user?->name ?? 'K', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">{{ $booking->user?->name ?? 'Khách vãng lai' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">ID #{{ $booking->user_id }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value">
                        @if($booking->user?->phone)
                            <a href="tel:{{ $booking->user->phone }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                <i class="bi bi-telephone-fill me-1"></i>{{ $booking->user->phone }}
                            </a>
                        @else
                            <span style="color: var(--text-secondary); font-style: italic;">Chưa cập nhật</span>
                        @endif
                    </div>
                </div>

                <div class="info-row" style="border-bottom: none;">
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        @if($booking->user?->email)
                            <a href="mailto:{{ $booking->user->email }}" style="color: var(--text-secondary); text-decoration: none; font-size: 0.875rem;">
                                <i class="bi bi-envelope-fill me-1"></i>{{ $booking->user->email }}
                            </a>
                        @else
                            <span style="color: var(--text-secondary); font-style: italic;">Chưa cập nhật</span>
                        @endif
                    </div>
                </div>
            </x-card>

            {{-- Table Card --}}
            <x-card>
                <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                    <i class="bi bi-grid-3x3-gap-fill me-2" style="color: var(--warning)"></i>Bàn chơi
                </h5>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="font-size: 2rem; font-weight: 900; letter-spacing: -0.04em; color: var(--text-primary);">
                        {{ $booking->billiardTable->table_number }}
                    </div>
                    <span style="background: var(--bg-deep); border: 1px solid var(--border); color: var(--text-secondary); font-size: 0.75rem; font-weight: 700; padding: 5px 12px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.04em;">
                        {{ $booking->billiardTable->table_type }}
                    </span>
                </div>

                <div class="info-row">
                    <div class="info-label">Giá thuê / giờ</div>
                    <div class="info-value" style="color: var(--success); font-size: 1.3rem; font-weight: 800;">
                        {{ number_format($booking->billiardTable->price_per_hour, 0, ',', '.') }}
                        <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">VNĐ/giờ</span>
                    </div>
                </div>

                @php
                    $estimatedCost = ($durationMin / 60) * $booking->billiardTable->price_per_hour;
                @endphp
                <div class="info-row">
                    <div class="info-label">Chi phí ước tính</div>
                    <div class="info-value text-primary font-semibold" style="font-size: 1.15rem; font-weight: 700;">
                        {{ number_format($estimatedCost, 0, ',', '.') }} VNĐ
                    </div>
                </div>

                <div class="info-row" style="border-bottom: none;">
                    <div class="info-label">Mô tả bàn</div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem; font-style: italic; margin-top: 4px;">
                        {{ $booking->billiardTable->description ?: 'Không có mô tả.' }}
                    </div>
                </div>
            </x-card>

        </div>
    </div>

</div>
@endsection
