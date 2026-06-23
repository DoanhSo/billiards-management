{{-- resources/views/customer-invoices/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn #' . $invoice->id)

@section('content')
<div class="container-fluid px-0" style="max-width: 960px;">

    @php
        $paymentConfig = match($invoice->payment_status) {
            'PAID'   => ['badge' => 'badge-confirmed', 'label' => 'Đã thanh toán', 'icon' => 'check-circle-fill', 'color' => 'var(--success)'],
            'UNPAID' => ['badge' => 'badge-pending',   'label' => 'Chưa thanh toán', 'icon' => 'hourglass-split', 'color' => 'var(--warning)'],
            default  => ['badge' => 'badge-maintenance', 'label' => $invoice->payment_status, 'icon' => 'question-circle', 'color' => 'var(--secondary-color)'],
        };
    @endphp

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <h1 class="page-title" style="margin: 0;">Chi tiết hóa đơn</h1>
                <span style="font-size: 1.1rem; font-weight: 800; color: var(--text-muted-c);">#{{ $invoice->id }}</span>
                <span class="badge {{ $paymentConfig['badge'] }} d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <i class="bi bi-{{ $paymentConfig['icon'] }}"></i>{{ $paymentConfig['label'] }}
                </span>
            </div>
            <p class="page-subtitle">Ngày lập: {{ $invoice->created_at->format('H:i — d/m/Y') }}</p>
        </div>
        <a href="{{ route('my-invoices.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left me-1"></i> Danh sách
        </a>
    </div>

    <div class="row g-4">

        {{-- ═══ LEFT — Invoice Details ═══ --}}
        <div class="col-12 col-lg-7">

            {{-- Thông tin phiên chơi --}}
            <x-card>
                <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                    <i class="bi bi-controller me-2" style="color: var(--primary)"></i>Thông tin phiên chơi
                </h5>

                <div class="info-row">
                    <div class="info-label">Bàn chơi</div>
                    <div class="info-value" style="font-weight: 700; font-size: 1.1rem;">
                        Bàn {{ $invoice->tableSession->billiardTable->table_number ?? 'N/A' }}
                        <span class="text-muted ms-2" style="font-size: 0.8rem; font-weight: 500;">{{ $invoice->tableSession->billiardTable->table_type ?? '' }}</span>
                    </div>
                </div>

                <div class="row g-0 my-2">
                    <div class="col-6">
                        <div class="info-row" style="padding-right: 16px;">
                            <div class="info-label">Giờ bắt đầu</div>
                            <div class="info-value" style="color: var(--success); font-size: 1.3rem; font-weight: 800;">
                                <i class="bi bi-play-fill me-1" style="font-size: 1rem;"></i>{{ $invoice->tableSession->start_time ? \Carbon\Carbon::parse($invoice->tableSession->start_time)->format('H:i') : '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-row">
                            <div class="info-label">Giờ kết thúc</div>
                            <div class="info-value" style="color: var(--danger); font-size: 1.3rem; font-weight: 800;">
                                <i class="bi bi-stop-fill me-1" style="font-size: 1rem;"></i>{{ $invoice->tableSession->end_time ? \Carbon\Carbon::parse($invoice->tableSession->end_time)->format('H:i') : '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tổng giờ chơi</div>
                    <div class="info-value">
                        <span class="badge" style="background: var(--primary-glow); color: var(--primary); padding: 6px 12px; font-size: 0.85rem;">
                            <i class="bi bi-stopwatch-fill me-1"></i>{{ number_format($invoice->tableSession->total_hours, 1) }} giờ
                        </span>
                    </div>
                </div>

                <div class="info-row" style="border-bottom: none;">
                    <div class="info-label">Tiền thuê bàn</div>
                    <div class="info-value" style="font-weight: 700; font-size: 1.1rem; color: var(--success);">
                        {{ number_format($invoice->tableSession->table_price, 0, ',', '.') }} ₫
                    </div>
                </div>
            </x-card>

            {{-- Danh sách sản phẩm đi kèm --}}
            @if($invoice->invoiceDetails && $invoice->invoiceDetails->count() > 0)
                <x-card>
                    <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                        <i class="bi bi-cart-fill me-2" style="color: var(--warning)"></i>Sản phẩm đã mua
                    </h5>

                    <x-table>
                        <x-slot:thead>
                            <tr>
                                <th style="padding-left: 16px !important;">Sản phẩm</th>
                                <th style="text-align: center;">SL</th>
                                <th style="text-align: right;">Đơn giá</th>
                                <th style="text-align: right; padding-right: 16px !important;">Thành tiền</th>
                            </tr>
                        </x-slot:thead>

                        @foreach($invoice->invoiceDetails as $detail)
                            <tr>
                                <td style="padding-left: 16px !important;">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($detail->product && $detail->product->image)
                                            <img src="{{ asset($detail->product->image) }}" alt="" class="rounded" style="width: 36px; height: 36px; object-fit: cover;">
                                        @else
                                            <div class="rounded d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(255,255,255,0.06);">
                                                <i class="bi bi-cup-hot text-muted"></i>
                                            </div>
                                        @endif
                                        <span style="font-weight: 600; color: var(--text-primary);">{{ $detail->product->name ?? 'Sản phẩm đã xóa' }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center; font-weight: 600; color: var(--text-primary);">{{ $detail->quantity }}</td>
                                <td style="text-align: right; color: var(--text-secondary);">{{ number_format($detail->unit_price, 0, ',', '.') }} ₫</td>
                                <td style="text-align: right; padding-right: 16px !important; font-weight: 700; color: var(--text-primary);">{{ number_format($detail->total_price, 0, ',', '.') }} ₫</td>
                            </tr>
                        @endforeach
                    </x-table>
                </x-card>
            @endif
        </div>

        {{-- ═══ RIGHT — Payment Summary ═══ --}}
        <div class="col-12 col-lg-5">

            {{-- Tổng kết thanh toán --}}
            <x-card>
                <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                    <i class="bi bi-calculator-fill me-2" style="color: var(--success)"></i>Tổng kết thanh toán
                </h5>

                <div class="info-row">
                    <div class="info-label">Tiền thuê bàn</div>
                    <div class="info-value" style="font-weight: 600;">{{ number_format($invoice->tableSession->table_price, 0, ',', '.') }} ₫</div>
                </div>

                @if($invoice->invoiceDetails && $invoice->invoiceDetails->count() > 0)
                    <div class="info-row">
                        <div class="info-label">Tiền sản phẩm</div>
                        <div class="info-value" style="font-weight: 600;">
                            {{ number_format($invoice->subtotal - $invoice->tableSession->table_price, 0, ',', '.') }} ₫
                        </div>
                    </div>
                @endif

                <div class="info-row">
                    <div class="info-label">Tổng phụ (Subtotal)</div>
                    <div class="info-value" style="font-weight: 600;">{{ number_format($invoice->subtotal, 0, ',', '.') }} ₫</div>
                </div>

                @if($invoice->discount > 0)
                    <div class="info-row">
                        <div class="info-label">Giảm giá ({{ number_format($invoice->discount_percent, 0) }}%)</div>
                        <div class="info-value" style="font-weight: 600; color: var(--danger);">-{{ number_format($invoice->discount, 0, ',', '.') }} ₫</div>
                    </div>
                @endif

                <div class="info-row" style="border-bottom: none; padding-top: 12px; margin-top: 8px; border-top: 2px solid var(--border);">
                    <div class="info-label" style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">TỔNG CỘNG</div>
                    <div class="info-value" style="font-weight: 900; font-size: 1.5rem; color: var(--success);">
                        {{ number_format($invoice->total_amount, 0, ',', '.') }} ₫
                    </div>
                </div>
            </x-card>

            {{-- Thông tin thanh toán --}}
            <x-card>
                <h5 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">
                    <i class="bi bi-credit-card-fill me-2" style="color: var(--info)"></i>Phương thức thanh toán
                </h5>

                <div class="info-row">
                    <div class="info-label">Hình thức</div>
                    <div class="info-value">
                        <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--text-primary); padding: 6px 12px; font-size: 0.85rem;">
                            <i class="bi bi-{{ $invoice->payment_method === 'CASH' ? 'cash-coin' : 'phone' }} me-1"></i>
                            {{ $invoice->payment_method === 'CASH' ? 'Tiền mặt' : 'Chuyển khoản' }}
                        </span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">
                        <span class="badge {{ $paymentConfig['badge'] }}" style="padding: 6px 12px; font-size: 0.85rem;">
                            <i class="bi bi-{{ $paymentConfig['icon'] }} me-1"></i>{{ $paymentConfig['label'] }}
                        </span>
                    </div>
                </div>

                <div class="info-row" style="border-bottom: none;">
                    <div class="info-label">Nhân viên phụ trách</div>
                    <div class="info-value" style="font-weight: 600; color: var(--text-primary);">
                        {{ $invoice->staff->name ?? 'Không rõ' }}
                    </div>
                </div>
            </x-card>

        </div>
    </div>

</div>
@endsection
