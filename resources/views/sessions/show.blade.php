{{-- resources/views/sessions/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chi tiết phiên chơi #' . $session->id)

@section('content')
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('sessions.index') }}">Phiên chơi</a></li>
            <li class="breadcrumb-item active">Chi tiết #{{ $session->id }}</li>
        </ol>
    </nav>

    <div class="row">
        {{-- Session Info Card --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-play-circle me-2"></i>Thông tin phiên chơi</h5>
                    @if ($session->status === 'PLAYING')
                        <span class="badge bg-success fs-6"><i class="bi bi-play-fill"></i> Đang chơi</span>
                    @else
                        <span class="badge bg-secondary fs-6"><i class="bi bi-stop-fill"></i> Đã kết thúc</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Bàn chơi</small>
                                <strong class="fs-5">{{ $session->billiardTable->table_number ?? '—' }}</strong>
                                <span class="badge bg-info ms-2">{{ $session->billiardTable->table_type ?? '' }}</span>
                                <small class="text-muted d-block mt-1">
                                    Giá: {{ number_format((float) $session->billiardTable->price_per_hour, 0, ',', '.') }} ₫/giờ
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Khách hàng</small>
                                <strong class="fs-5">{{ $session->customer->name ?? 'Khách vãng lai' }}</strong>
                                @if ($session->customer)
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-envelope"></i> {{ $session->customer->email }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted d-block">Bắt đầu</small>
                                <strong>{{ $session->start_time->format('d/m/Y H:i:s') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted d-block">Kết thúc</small>
                                @if ($session->end_time)
                                    <strong>{{ $session->end_time->format('d/m/Y H:i:s') }}</strong>
                                @else
                                    <span class="text-muted">Chưa kết thúc</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted d-block">Tổng giờ</small>
                                @if ($session->status === 'PLAYING')
                                    <strong class="text-success session-timer"
                                            data-start="{{ $session->start_time->toIso8601String() }}">
                                        đang tính...
                                    </strong>
                                @else
                                    <strong>{{ number_format((float) $session->total_hours, 2) }}h</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Invoice info if exists --}}
            @if ($session->invoice)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Hóa đơn liên kết</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Mã hóa đơn</small>
                                <strong>#{{ $session->invoice->id }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Tổng tiền</small>
                                <strong class="text-primary">{{ number_format((float) $session->invoice->total_amount, 0, ',', '.') }} ₫</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Thanh toán</small>
                                <span class="badge bg-{{ $session->invoice->payment_method === 'CASH' ? 'success' : 'primary' }}">
                                    {{ $session->invoice->payment_method === 'CASH' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Trạng thái</small>
                                <span class="badge bg-{{ $session->invoice->payment_status === 'PAID' ? 'success' : 'danger' }}">
                                    {{ $session->invoice->payment_status === 'PAID' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                </span>
                            </div>
                        </div>

                        {{-- Invoice Details --}}
                        @if ($session->invoice->invoiceDetails->isNotEmpty())
                            <hr>
                            <h6>Chi tiết sản phẩm</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($session->invoice->invoiceDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $detail->quantity }}</td>
                                            <td class="text-end">{{ number_format((float) $detail->unit_price, 0, ',', '.') }} ₫</td>
                                            <td class="text-end">{{ number_format((float) $detail->total_price, 0, ',', '.') }} ₫</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        <a href="{{ route('invoices.show', $session->invoice->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> Xem chi tiết hóa đơn
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Action Panel --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Tổng tiền bàn</h5>
                </div>
                <div class="card-body text-center">
                    @if ($session->status === 'FINISHED')
                        <h2 class="text-primary mb-0">{{ number_format((float) $session->table_price, 0, ',', '.') }} ₫</h2>
                        <small class="text-muted">{{ number_format((float) $session->total_hours, 2) }}h × {{ number_format((float) $session->billiardTable->price_per_hour, 0, ',', '.') }} ₫/h</small>
                    @else
                        <div class="session-price-live"
                             data-start="{{ $session->start_time->toIso8601String() }}"
                             data-price-per-hour="{{ $session->billiardTable->price_per_hour }}">
                            <h2 class="text-success mb-0 live-price">0 ₫</h2>
                            <small class="text-muted live-hours">0.00h × {{ number_format((float) $session->billiardTable->price_per_hour, 0, ',', '.') }} ₫/h</small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Thao tác</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    @if ($session->status === 'PLAYING')
                        <form action="{{ route('sessions.end', $session->id) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn kết thúc phiên chơi này?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-stop-circle me-1"></i> Kết thúc phiên chơi
                            </button>
                        </form>
                    @endif

                    @if ($session->status === 'FINISHED' && !$session->invoice)
                        <a href="{{ route('invoices.create', ['session_id' => $session->id]) }}"
                           class="btn btn-warning w-100">
                            <i class="bi bi-receipt me-1"></i> Lập hóa đơn thanh toán
                        </a>
                    @endif

                    @if ($session->invoice)
                        <a href="{{ route('invoices.show', $session->invoice->id) }}"
                           class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye me-1"></i> Xem hóa đơn
                        </a>
                    @endif

                    <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Realtime timer cho phiên đang chơi
    function updateTimers() {
        document.querySelectorAll('.session-timer').forEach(function (el) {
            const start = new Date(el.dataset.start);
            const now   = new Date();
            const diff  = Math.floor((now - start) / 1000);
            const hours   = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;

            el.textContent = (hours > 0 ? hours + 'h ' : '') +
                             String(minutes).padStart(2, '0') + 'm ' +
                             String(seconds).padStart(2, '0') + 's';
        });
    }

    // Realtime price calculator
    function updateLivePrice() {
        document.querySelectorAll('.session-price-live').forEach(function (el) {
            const start = new Date(el.dataset.start);
            const pricePerHour = parseFloat(el.dataset.pricePerHour);
            const now   = new Date();
            const hours = (now - start) / (1000 * 60 * 60);
            const price = Math.round(hours * pricePerHour);

            el.querySelector('.live-price').textContent = new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
            el.querySelector('.live-hours').textContent  = hours.toFixed(2) + 'h × ' +
                new Intl.NumberFormat('vi-VN').format(pricePerHour) + ' ₫/h';
        });
    }

    updateTimers();
    updateLivePrice();
    setInterval(updateTimers, 1000);
    setInterval(updateLivePrice, 1000);
</script>
@endpush
