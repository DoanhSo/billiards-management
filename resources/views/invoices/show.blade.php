{{-- resources/views/invoices/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn #' . $invoice->id)

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">
    <!-- Header Actions (Ẩn khi in) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h1 class="page-title mb-1">Chi Tiết Hóa Đơn #{{ $invoice->id }}</h1>
            <p class="text-muted mb-0">Xem thông tin chi tiết và in hóa đơn cho khách hàng.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.index') }}" class="btn btn-outline-light d-flex align-items-center gap-2">
                <i class="bi bi-house"></i> Trang chủ
            </a>
            <button onclick="window.print()" class="btn btn-success d-flex align-items-center gap-2">
                <i class="bi bi-printer"></i> In Hóa Đơn
            </button>
        </div>
    </div>

    <!-- Hóa đơn chính -->
    <div class="card glass-panel border-0 rounded-4 text-white p-4 p-md-5 invoice-print-box">
        <!-- Header Hóa Đơn -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-white mb-1"><i class="bi bi-circle-fill me-2 text-primary"></i>BILLIARDS CLUB</h2>
            <p class="text-muted mb-3" style="font-size: 0.9rem;">Địa chỉ: Số 123 Đường Láng, Đống Đa, Hà Nội<br>Hotline: 0987.654.321</p>
            <h3 class="h4 fw-bold text-white border-bottom border-secondary pb-3 inline-block" style="text-transform: uppercase; letter-spacing: 1px;">
                Hóa Đơn Thanh Toán
            </h3>
        </div>

        <!-- Thông tin chung -->
        <div class="row g-3 mb-4 fs-6">
            <div class="col-6 col-sm-4">
                <div class="text-muted small">Mã Hóa Đơn:</div>
                <div class="fw-bold text-white">#{{ $invoice->id }}</div>
            </div>
            <div class="col-6 col-sm-4">
                <div class="text-muted small">Ngày thanh toán:</div>
                <div class="text-white">{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="text-muted small">Nhân viên thu ngân:</div>
                <div class="text-white">{{ $invoice->staff->name ?? 'N/A' }}</div>
            </div>
            <div class="col-6 col-sm-4">
                <div class="text-muted small">Khách hàng:</div>
                <div class="text-white fw-medium">{{ $invoice->tableSession->customer->name ?? 'Khách vãng lai' }}</div>
            </div>
            <div class="col-6 col-sm-4">
                <div class="text-muted small">Số điện thoại:</div>
                <div class="text-white">{{ $invoice->tableSession->customer->phone ?? 'N/A' }}</div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="text-muted small">Hình thức thanh toán:</div>
                <div class="text-warning fw-semibold">{{ $invoice->payment_method === 'CASH' ? 'Tiền mặt (Cash)' : 'Chuyển khoản (Banking)' }}</div>
            </div>
        </div>

        <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 1.5rem 0;">

        <!-- Chi tiết tiền giờ chơi -->
        <div class="mb-4">
            <h4 class="h5 text-white mb-3"><i class="bi bi-play-circle me-2 text-danger"></i>1. Tiền giờ chơi</h4>
            <div class="p-3 rounded" style="background-color: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color);">
                <div class="row g-2 align-items-center">
                    <div class="col-5">
                        <span class="fw-bold text-white fs-5">Bàn {{ $invoice->tableSession->billiardTable->table_number }}</span>
                        <span class="text-muted small d-block">({{ $invoice->tableSession->billiardTable->table_type }})</span>
                    </div>
                    <div class="col-7 text-end small text-muted">
                        Thời gian: <span class="text-white fw-medium">{{ $invoice->tableSession->start_time->format('H:i') }} - {{ $invoice->tableSession->end_time->format('H:i') }}</span>
                        ({{ $invoice->tableSession->total_hours }}h x {{ number_format($invoice->tableSession->billiardTable->price_per_hour, 0, ',', '.') }} ₫)
                        <div class="fs-5 fw-bold text-white mt-1">{{ number_format($invoice->tableSession->table_price, 0, ',', '.') }} ₫</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết sản phẩm & dịch vụ -->
        @if(!$invoice->invoiceDetails->isEmpty())
            <div class="mb-5">
                <h4 class="h5 text-white mb-3"><i class="bi bi-cart3 me-2 text-success"></i>2. Dịch vụ & đồ uống</h4>
                <div class="table-responsive">
                    <table class="table table-borderless text-white align-middle">
                        <thead style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                            <tr>
                                <th>Dịch Vụ</th>
                                <th class="text-end" style="width: 120px;">Đơn Giá</th>
                                <th class="text-center" style="width: 80px;">SL</th>
                                <th class="text-end" style="width: 140px;">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->invoiceDetails as $detail)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td>
                                        <div class="fw-semibold text-white">{{ $detail->product->name }}</div>
                                    </td>
                                    <td class="text-end text-white-50">{{ number_format($detail->unit_price, 0, ',', '.') }} ₫</td>
                                    <td class="text-center">{{ $detail->quantity }}</td>
                                    <td class="text-end fw-semibold text-white">{{ number_format($detail->total_price, 0, ',', '.') }} ₫</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Thanh toán tổng cộng -->
        <div class="row justify-content-end fs-6">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tổng tiền hàng:</span>
                    <span class="text-white fw-medium">{{ number_format($invoice->subtotal, 0, ',', '.') }} ₫</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Giảm giá/Chiết khấu ({{ number_format($invoice->discount_percent, 0) }}%):</span>
                    <span class="text-white fw-medium">-{{ number_format($invoice->discount, 0, ',', '.') }} ₫</span>
                </div>
                <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 0.75rem 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-white fs-5">Thành tiền:</span>
                    <span class="h3 fw-bold text-danger mb-0">{{ number_format($invoice->total_amount, 0, ',', '.') }} ₫</span>
                </div>
            </div>
        </div>

        <!-- Footer hóa đơn -->
        <div class="text-center mt-5 pt-4 border-top border-secondary-subtle">
            <p class="mb-1 fw-medium text-white-50" style="font-size: 0.95rem;">Cảm ơn quý khách và hẹn gặp lại!</p>
            <p class="text-muted small mb-0">Powered by Billiards Management System</p>
        </div>
    </div>
</div>

<style>
    /* CSS Tùy biến khi in hóa đơn */
    @media print {
        /* Ẩn các thành phần bên ngoài như Sidebar, Topbar, Actions */
        .sidebar, .topbar, .d-print-none, header, nav, footer, .btn, .d-flex {
            display: none !important;
        }
        
        /* Cấu hình lại Main content */
        .main-content, .container-fluid, body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            width: 100% !important;
        }
        
        /* Biến đổi card glass-panel tối thành bảng in trắng đen */
        .invoice-print-box {
            background: #ffffff !important;
            color: #000000 !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .invoice-print-box h2,
        .invoice-print-box h3,
        .invoice-print-box h4,
        .invoice-print-box .text-white,
        .invoice-print-box .fw-bold {
            color: #000000 !important;
        }

        .invoice-print-box .text-muted,
        .invoice-print-box .text-white-50 {
            color: #555555 !important;
        }

        .invoice-print-box table, 
        .invoice-print-box td, 
        .invoice-print-box th {
            color: #000000 !important;
            border-bottom: 1px solid #ddd !important;
        }
        
        .invoice-print-box .table-responsive {
            overflow: visible !important;
        }

        .invoice-print-box .rounded, 
        .invoice-print-box .p-3 {
            border: 1px solid #000000 !important;
            background-color: transparent !important;
        }

        .invoice-print-box .text-danger {
            color: #000000 !important;
        }
    }
</style>
@endsection
