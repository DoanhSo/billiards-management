{{-- resources/views/dashboard/customer.blade.php --}}
<div class="page-content-padding pt-0">
    {{-- Banner chào mừng --}}
    <div class="card glass-panel border-0 mb-4 text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%); border: 1px solid rgba(102, 126, 234, 0.3) !important;">
        <div class="card-body p-4 p-md-5 z-1 position-relative">
            <h1 class="display-6 fw-bold text-white mb-2">Xin chào, {{ auth()->user()->name }}!</h1>
            <p class="fs-5 text-light-opacity mb-4" style="color: var(--text-muted);">Chào mừng bạn đến với Hệ thống Billiards Club. Chúc bạn có những giây phút giải trí tuyệt vời!</p>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('bookings.create') }}" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-calendar-plus me-2"></i> Đặt bàn chơi ngay
                </a>
                <a href="{{ route('my-sessions.index') }}" class="btn btn-outline-light px-4 py-2 fw-semibold">
                    <i class="bi bi-controller me-2"></i> Lịch sử chơi
                </a>
                <a href="{{ route('my-invoices.index') }}" class="btn btn-outline-light px-4 py-2 fw-semibold">
                    <i class="bi bi-receipt me-2"></i> Hóa đơn của tôi
                </a>
            </div>
        </div>
        <div class="position-absolute end-0 bottom-0 opacity-10 p-5 d-none d-md-block" style="font-size: 8rem; line-height: 1; transform: translate(10%, 10%);">🎱</div>
    </div>

    {{-- ═══ STAT CARDS ═══ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng lần chơi</div>
                        <div class="stat-value" style="color: var(--info)">{{ number_format($data['total_sessions']) }}</div>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="bi bi-controller"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng giờ chơi</div>
                        <div class="stat-value" style="color: var(--warning)">{{ number_format($data['total_hours'], 1) }}h</div>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Tổng chi tiêu</div>
                        <div class="stat-value" style="color: var(--success)">{{ number_format($data['total_spent'], 0, ',', '.') }}₫</div>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card stat-secondary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Lượt đặt bàn</div>
                        <div class="stat-value" style="color: var(--primary)">{{ number_format($data['total_bookings']) }}</div>
                    </div>
                    <div class="stat-icon icon-primary">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        {{-- Xem trạng thái bàn chơi --}}
        <div class="col-12 col-xl-7">
            <x-card>
                <x-slot:title>
                    <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Theo dõi bàn chơi trực tuyến
                </x-slot>
                
                <div class="row g-3">
                    @foreach($data['tables'] as $table)
                        @php
                            $statusLabels = [
                                'AVAILABLE'   => 'Bàn trống',
                                'PLAYING'     => 'Đang bận',
                                'RESERVED'    => 'Đã đặt',
                                'MAINTENANCE' => 'Bảo trì',
                            ];
                            $statusClass = [
                                'AVAILABLE'   => 'table-status-available',
                                'PLAYING'     => 'table-status-playing',
                                'RESERVED'    => 'table-status-reserved',
                                'MAINTENANCE' => 'table-status-maintenance',
                            ][$table->status] ?? 'table-status-maintenance';
                        @endphp
                        <div class="col-12 col-sm-6">
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="border-color: var(--border-color) !important; background-color: rgba(255,255,255,0.02);">
                                <div>
                                    <h4 class="h5 fw-bold text-white mb-1">Bàn {{ $table->table_number }}</h4>
                                    <small class="text-muted">{{ $table->table_type }} — {{ number_format($table->price_per_hour, 0, ',', '.') }} ₫/h</small>
                                </div>
                                <div class="text-end">
                                    <span class="d-block fw-semibold mb-2 {{ $statusClass }}">
                                        <i class="bi bi-circle-fill me-1"></i> {{ $statusLabels[$table->status] }}
                                    </span>
                                    @if($table->status === 'AVAILABLE')
                                        <a href="{{ route('bookings.create', ['table_id' => $table->id]) }}" class="btn btn-sm btn-outline-success py-1 px-3">
                                            Đặt nhanh
                                        </a>
                                    @else
                                        <span class="btn btn-sm btn-outline-secondary py-1 px-3 disabled">Đóng</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        {{-- Lịch đặt bàn của tôi --}}
        <div class="col-12 col-xl-5">
            <x-card>
                <x-slot:title>
                    <i class="bi bi-calendar2-range me-2 text-warning"></i>Đặt bàn gần đây của tôi
                </x-slot>

                @if($data['my_bookings']->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x fs-2 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Bạn chưa có lịch đặt bàn nào.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($data['my_bookings'] as $myBooking)
                            <div class="p-3 rounded-3 border" style="border-color: var(--border-color) !important; background-color: rgba(255,255,255,0.02);">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-white">Bàn {{ $myBooking->billiardTable->table_number }}</span>
                                    @php
                                        $badgeType = [
                                            'PENDING' => 'warning',
                                            'CONFIRMED' => 'primary',
                                            'CANCELLED' => 'danger',
                                            'COMPLETED' => 'success'
                                        ][$myBooking->status] ?? 'secondary';
                                        
                                        $badgeLabel = [
                                            'PENDING' => 'Chờ xác nhận',
                                            'CONFIRMED' => 'Đã xác nhận',
                                            'CANCELLED' => 'Đã hủy',
                                            'COMPLETED' => 'Đã chơi'
                                        ][$myBooking->status] ?? $myBooking->status;
                                    @endphp
                                    <x-badge type="{{ $badgeType }}">{{ $badgeLabel }}</x-badge>
                                </div>
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-clock me-1"></i> Ngày: {{ \Carbon\Carbon::parse($myBooking->booking_date)->format('d/m/Y') }} <br>
                                    <i class="bi bi-arrow-right-short me-1"></i> Giờ: {{ \Carbon\Carbon::parse($myBooking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($myBooking->end_time)->format('H:i') }}
                                </div>
                                @if($myBooking->status === 'PENDING')
                                    <div class="text-end">
                                        <form action="{{ route('bookings.cancel', $myBooking->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1" data-confirm-click="Bạn có chắc chắn muốn hủy đặt bàn này?">
                                                Hủy lịch
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Menu nước uống & đồ ăn tham khảo --}}
    <h2 class="section-title mb-4"><i class="bi bi-book-half me-2"></i>Thực đơn quán</h2>
    
    @if($data['categories']->isEmpty())
        <x-card>
            <div class="text-center py-5">
                <i class="bi bi-journal-x fs-2 text-muted"></i>
                <p class="text-muted mt-2 mb-0">Chưa có menu món ăn/nước uống.</p>
            </div>
        </x-card>
    @else
        <div class="row g-4">
            @foreach($data['categories'] as $category)
                @if(!$category->products->isEmpty())
                    <div class="col-12 col-md-6 col-xxl-4">
                        <x-card>
                            <x-slot:title>
                                <span class="text-info"><i class="bi bi-tag-fill me-2"></i>{{ $category->name }}</span>
                            </x-slot>
                            
                            <div class="d-flex flex-column gap-3" style="max-height: 400px; overflow-y: auto;">
                                @foreach($category->products as $product)
                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($product->image)
                                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="rounded bg-secondary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-cup-hot fs-4 text-white-50"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h5 class="fw-semibold text-white mb-0" style="font-size: 0.95rem;">{{ $product->name }}</h5>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;">{{ $product->description ?? 'Không có mô tả' }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end fw-bold text-danger">
                                            {{ number_format($product->price, 0, ',', '.') }} ₫
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

