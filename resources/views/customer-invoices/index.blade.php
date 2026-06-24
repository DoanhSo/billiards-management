{{-- resources/views/customer-invoices/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Hóa đơn của tôi')

@section('content')
<div class="container-fluid px-0">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-receipt-cutoff me-2" style="color: var(--primary)"></i>Hóa đơn của tôi</h1>
            <p class="page-subtitle">Xem lại lịch sử thanh toán và hóa đơn chơi billiards</p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left me-1"></i> Tổng quan
        </a>
    </div>

    {{-- ═══ STAT CARDS ═══ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng hóa đơn</div>
                        <div class="stat-value" style="color: var(--info)">{{ number_format($stats->total_invoices) }}</div>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng chi tiêu</div>
                        <div class="stat-value" style="color: var(--success)">{{ number_format($stats->total_spent, 0, ',', '.') }}₫</div>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Đã thanh toán</div>
                        <div class="stat-value" style="color: var(--warning)">{{ number_format($stats->total_paid) }}</div>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-secondary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Chưa thanh toán</div>
                        <div class="stat-value" style="color: var(--danger)">{{ number_format($stats->total_unpaid) }}</div>
                    </div>
                    <div class="stat-icon icon-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
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
                    <th>Mã HĐ</th>
                    <th>Bàn chơi</th>
                    <th>Ngày</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th style="width: 100px; text-align: right; padding-right: 20px !important;">Thao tác</th>
                </tr>
            </x-slot:thead>

            @forelse($invoices as $index => $invoice)
                @php
                    $paymentConfig = match($invoice->payment_status) {
                        'PAID'   => ['badge' => 'badge-confirmed', 'label' => 'Đã thanh toán'],
                        'UNPAID' => ['badge' => 'badge-pending',   'label' => 'Chưa thanh toán'],
                        default  => ['badge' => 'badge-maintenance', 'label' => $invoice->payment_status],
                    };
                @endphp
                <tr>
                    <td style="padding-left: 20px !important; color: var(--text-secondary); font-weight: 600;">
                        {{ ($invoices->currentPage() - 1) * $invoices->perPage() + $index + 1 }}
                    </td>

                    <td>
                        <span style="font-weight: 700; color: var(--primary);">#{{ $invoice->id }}</span>
                    </td>

                    <td>
                        <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">
                            Bàn {{ $invoice->tableSession->billiardTable->table_number ?? 'N/A' }}
                        </div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">
                            {{ $invoice->tableSession->billiardTable->table_type ?? '' }}
                        </div>
                    </td>

                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $invoice->created_at->format('d/m/Y') }}</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">{{ $invoice->created_at->format('H:i') }}</div>
                    </td>

                    <td>
                        <span style="font-weight: 800; font-size: 0.95rem; color: var(--success);">
                            {{ number_format($invoice->total_amount, 0, ',', '.') }} ₫
                        </span>
                    </td>

                    <td>
                        <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--text-secondary); padding: 4px 10px; font-size: 0.75rem;">
                            <i class="bi bi-{{ $invoice->payment_method === 'CASH' ? 'cash-coin' : 'phone' }} me-1"></i>
                            {{ $invoice->payment_method === 'CASH' ? 'Tiền mặt' : 'Banking' }}
                        </span>
                    </td>

                    <td>
                        <span class="badge {{ $paymentConfig['badge'] }}">{{ $paymentConfig['label'] }}</span>
                    </td>

                    <td style="text-align: right; padding-right: 16px !important;">
                        <a href="{{ route('my-invoices.show', $invoice->id) }}"
                           class="btn btn-light btn-sm" style="height: 32px; font-size: 0.8rem; display: inline-flex; align-items: center;" title="Xem chi tiết">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-receipt fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-dark">Chưa có hóa đơn nào</h5>
                            <p class="text-secondary">Bạn chưa có hóa đơn thanh toán nào được ghi nhận.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    {{-- ═══ PAGINATION ═══ --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $invoices->links() }}
    </div>

</div>
@endsection

