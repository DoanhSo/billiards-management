{{-- resources/views/customer-sessions/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Lịch sử phiên chơi')

@section('content')
<div class="container-fluid px-0">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-controller me-2" style="color: var(--primary)"></i>Lịch sử phiên chơi</h1>
            <p class="page-subtitle">Xem lại các phiên chơi billiards đã hoàn thành của bạn</p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left me-1"></i> Tổng quan
        </a>
    </div>

    {{-- ═══ STAT CARDS ═══ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <div class="stat-card stat-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng lần chơi</div>
                        <div class="stat-value" style="color: var(--info)">{{ number_format($stats->total_sessions) }}</div>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="bi bi-controller"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="stat-card stat-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng giờ chơi</div>
                        <div class="stat-value" style="color: var(--warning)">{{ number_format($stats->total_hours, 1) }}h</div>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="stat-card stat-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng chi phí bàn</div>
                        <div class="stat-value" style="color: var(--success)">{{ number_format($stats->total_spent, 0, ',', '.') }}₫</div>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ DATA TABLE ═══ --}}
    <x-card>
        <x-table>
            <x-slot:thead>
                <tr>
                    <th style="width: 50px; padding-left: 20px !important;">#</th>
                    <th>Bàn chơi</th>
                    <th>Ngày chơi</th>
                    <th>Giờ bắt đầu</th>
                    <th>Giờ kết thúc</th>
                    <th>Tổng giờ</th>
                    <th style="text-align: right; padding-right: 20px !important;">Tiền bàn</th>
                </tr>
            </x-slot:thead>

            @forelse($sessions as $index => $session)
                <tr>
                    <td style="padding-left: 20px !important; color: var(--text-secondary); font-weight: 600;">
                        {{ ($sessions->currentPage() - 1) * $sessions->perPage() + $index + 1 }}
                    </td>

                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--primary-glow); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem;">
                                {{ $session->billiardTable->table_number ?? '?' }}
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">Bàn {{ $session->billiardTable->table_number ?? 'N/A' }}</div>
                                <div style="color: var(--text-secondary); font-size: 0.75rem;">{{ $session->billiardTable->table_type ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('d/m/Y') : '—' }}</div>
                    </td>

                    <td>
                        <span style="font-weight: 700; color: var(--success)">
                            <i class="bi bi-play-fill me-1"></i>{{ $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('H:i') : '—' }}
                        </span>
                    </td>

                    <td>
                        <span style="font-weight: 700; color: var(--danger)">
                            <i class="bi bi-stop-fill me-1"></i>{{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '—' }}
                        </span>
                    </td>

                    <td>
                        <span class="badge" style="background: var(--primary-glow); color: var(--primary); padding: 5px 10px; font-size: 0.8rem;">
                            <i class="bi bi-clock me-1"></i>{{ number_format($session->total_hours, 1) }}h
                        </span>
                    </td>

                    <td style="text-align: right; padding-right: 20px !important;">
                        <span style="font-weight: 700; font-size: 0.95rem; color: var(--success);">
                            {{ number_format($session->table_price, 0, ',', '.') }} ₫
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-controller fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-dark">Chưa có lịch sử phiên chơi</h5>
                            <p class="text-secondary">Bạn chưa có phiên chơi nào được ghi nhận. Hãy <a href="{{ route('bookings.create') }}" style="color: var(--primary); font-weight: 600;">đặt bàn</a> để bắt đầu chơi!</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    {{-- ═══ PAGINATION ═══ --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $sessions->links() }}
    </div>

</div>
@endsection

