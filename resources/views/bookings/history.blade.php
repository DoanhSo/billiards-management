@extends('layouts.app')

@section('title', 'Lịch sử Đặt bàn')

@section('content')
<div class="container-fluid px-0">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-clock-history me-2" style="color: var(--secondary-color)"></i>Lịch sử Đặt bàn</h1>
            <p class="page-subtitle">Danh sách các lịch đặt đã hoàn thành hoặc đã bị hủy</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left"></i> Đặt bàn hiện tại
        </a>
    </div>

    {{-- ═══ SUMMARY STRIP ═══ --}}
    <div class="filter-bar mb-4" style="background: var(--bg-surface); border-radius: 12px; padding: 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                Tổng cộng:
            </div>
            <span style="background: var(--success-glow); color: var(--success); border: 1px solid rgba(22,163,74,0.3); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                <i class="bi bi-check2-all me-1"></i>Hoàn thành: {{ $bookings->where('status','COMPLETED')->count() }}
            </span>
            <span style="background: var(--danger-glow); color: var(--danger); border: 1px solid rgba(220,38,38,0.3); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                <i class="bi bi-x-circle me-1"></i>Đã hủy: {{ $bookings->where('status','CANCELLED')->count() }}
            </span>
            <span style="margin-left: auto; color: var(--text-secondary); font-size: 0.8rem;">
                Trang {{ $bookings->currentPage() }} / {{ $bookings->lastPage() }}
            </span>
        </div>
    </div>

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
                    <th>Ngày tạo lịch</th>
                    <th style="width: 100px; text-align: right; padding-right: 20px !important;">Thao tác</th>
                </tr>
            </x-slot:thead>

            @forelse($bookings as $index => $booking)
                <tr>
                    {{-- STT --}}
                    <td style="padding-left: 20px !important; color: var(--text-secondary); font-weight: 600;">
                        {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}
                    </td>

                    {{-- Khách hàng --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="background: var(--bg-deep); color: var(--text-secondary); font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($booking->user?->name ?? 'K', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">{{ $booking->user?->name ?? 'Khách vãng lai' }}</div>
                                <div class="text-xxs" style="color: var(--text-secondary); font-size: 0.75rem;">
                                    <i class="bi bi-telephone me-1"></i>{{ $booking->user?->phone ?? '—' }}
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
                            <span style="font-weight: 700;">{{ $booking->start_time->format('H:i') }}</span>
                            <span style="color: var(--text-secondary); margin: 0 4px;">→</span>
                            <span style="font-weight: 700;">{{ $booking->end_time->format('H:i') }}</span>
                        </div>
                    </td>

                    {{-- Trạng thái --}}
                    <td>
                        @if($booking->status === 'COMPLETED')
                            <span class="badge badge-completed"><i class="bi bi-check2-all me-1"></i>Hoàn thành</span>
                        @elseif($booking->status === 'CANCELLED')
                            <span class="badge badge-cancelled"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
                        @else
                            <span class="badge badge-maintenance">{{ $booking->status }}</span>
                        @endif
                    </td>

                    {{-- Ngày tạo --}}
                    <td>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                            {{ $booking->created_at->format('d/m/Y') }}
                        </div>
                        <div class="text-xxs" style="color: var(--text-secondary);">
                            {{ $booking->created_at->format('H:i') }}
                        </div>
                    </td>

                    {{-- Thao tác --}}
                    <td style="text-align: right; padding-right: 16px !important;">
                        <a href="{{ route('bookings.show', $booking->id) }}"
                           class="btn btn-light btn-sm" style="height: 32px; font-size: 0.8rem; display: inline-flex; align-items: center;" title="Xem chi tiết">
                            <i class="bi bi-eye"></i> Chi tiết
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-clock-history fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-dark">Chưa có lịch sử đặt bàn</h5>
                            <p class="text-secondary">Lịch sử sẽ xuất hiện khi các đặt bàn được hoàn tất hoặc bị hủy.</p>
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
@endsection

