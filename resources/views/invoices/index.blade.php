{{-- resources/views/invoices/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Quản lý hóa đơn')

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-receipt me-2"></i>Quản lý hóa đơn</h1>
            <p class="text-muted mb-0">Xem và quản lý tất cả hóa đơn thanh toán</p>
        </div>
        <a href="{{ route('invoices.history') }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-1"></i> Lịch sử hóa đơn
        </a>
    </div>

    {{-- Status Filter --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted me-2"><i class="bi bi-funnel"></i> Lọc:</span>
                <a href="{{ route('invoices.index') }}"
                   class="btn btn-sm {{ $status === '' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Tất cả
                </a>
                <a href="{{ route('invoices.index', ['status' => 'PAID']) }}"
                   class="btn btn-sm {{ $status === 'PAID' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="bi bi-check-circle"></i> Đã thanh toán
                </a>
                <a href="{{ route('invoices.index', ['status' => 'UNPAID']) }}"
                   class="btn btn-sm {{ $status === 'UNPAID' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="bi bi-exclamation-circle"></i> Chưa thanh toán
                </a>
            </div>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if ($invoices->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Chưa có hóa đơn nào.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã HĐ</th>
                                <th>Bàn</th>
                                <th>Nhân viên</th>
                                <th class="text-end">Tổng cộng</th>
                                <th class="text-end">Giảm giá</th>
                                <th class="text-end">Thanh toán</th>
                                <th>Phương thức</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td><strong>#{{ $invoice->id }}</strong></td>
                                    <td>
                                        {{ $invoice->tableSession->billiardTable->table_number ?? '—' }}
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
                                    <td>
                                        @if ($invoice->payment_status === 'PAID')
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã TT</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Chưa TT</span>
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

