{{-- resources/views/invoices/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Lịch sử hóa đơn')

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-clock-history me-2"></i>Lịch sử hóa đơn</h1>
            <p class="text-muted mb-0">Tra cứu và thống kê hóa đơn theo khoảng thời gian</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0" style="font-size: 1rem;"><i class="bi bi-funnel me-1"></i>Bộ lọc thời gian</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('invoices.history') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="from" class="form-label text-muted small fw-bold">Từ ngày</label>
                    <input type="date" class="form-control" id="from" name="from" value="{{ $from }}">
                </div>
                <div class="col-md-4">
                    <label for="to" class="form-label text-muted small fw-bold">Đến ngày</label>
                    <input type="date" class="form-control" id="to" name="to" value="{{ $to }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Lọc kết quả
                    </button>
                    @if($from || $to)
                        <a href="{{ route('invoices.history') }}" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i> Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Statistics Summary --}}
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded p-3 me-3">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-white-50">Tổng doanh thu</h6>
                        <h3 class="card-title mb-0 fw-bold">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }} ₫</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-info text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded p-3 me-3">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-white-50">Tổng số hóa đơn</h6>
                        <h3 class="card-title mb-0 fw-bold">{{ $invoices->total() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if ($invoices->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Không tìm thấy hóa đơn nào trong khoảng thời gian này.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã HĐ</th>
                                <th>Bàn</th>
                                <th>Nhân viên lập</th>
                                <th class="text-end">Tổng cộng</th>
                                <th class="text-end">Giảm giá</th>
                                <th class="text-end">Thành tiền</th>
                                <th>Phương thức</th>
                                <th>Ngày lập</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td><strong>#{{ $invoice->id }}</strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $invoice->tableSession->billiardTable->table_number ?? '—' }}
                                        </span>
                                    </td>
                                    <td>{{ $invoice->staff->name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format((float) $invoice->subtotal, 0, ',', '.') }} ₫</td>
                                    <td class="text-end">
                                        @if ((float) $invoice->discount > 0)
                                            <span class="text-danger">-{{ number_format((float) $invoice->discount, 0, ',', '.') }} ₫</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format((float) $invoice->total_amount, 0, ',', '.') }} ₫</strong>
                                    </td>
                                    <td>
                                        @if ($invoice->payment_method === 'CASH')
                                            <span class="badge bg-success"><i class="bi bi-cash"></i> Tiền mặt</span>
                                        @else
                                            <span class="badge bg-primary"><i class="bi bi-bank"></i> Chuyển khoản</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $invoice->created_at->format('d/m/Y H:i') }}</small></td>
                                    <td class="text-center">
                                        <a href="{{ route('invoices.show', $invoice->id) }}"
                                           class="btn btn-sm btn-outline-info" title="Chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="card-footer d-flex justify-content-center">
                    {{ $invoices->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
