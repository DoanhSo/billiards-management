{{-- resources/views/dashboard/staff.blade.php --}}
<div class="page-content-padding pt-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title mb-1">Màn Hình Vận Hành</h1>
            <p class="text-muted mb-0">Quản lý phiên chơi, đặt bàn và thanh toán hóa đơn nhanh.</p>
        </div>
        <div>
            <span class="text-muted"><i class="bi bi-calendar-event me-1"></i> Ca làm việc: {{ now()->format('d/m/Y') }}</span>
        </div>
    </div>

    {{-- Thống kê nhanh hôm nay --}}
    <div class="row mb-4">
        <div class="col-12 col-md-4">
            <x-stat-card 
                title="Doanh Thu Hôm Nay" 
                value="{{ number_format($data['revenue_today'], 0, ',', '.') }} ₫" 
                icon="bi-cash-coin" 
                color="success" />
        </div>
        <div class="col-12 col-md-4">
            <x-stat-card 
                title="Bàn Đang Hoạt Động" 
                value="{{ $data['active_tables'] }} Bàn" 
                icon="bi-play-circle" 
                color="danger" />
        </div>
        <div class="col-12 col-md-4">
            <x-stat-card 
                title="Số Đặt Bàn Hôm Nay" 
                value="{{ $data['bookings_today'] }} Lượt" 
                icon="bi-calendar-check" 
                color="warning" />
        </div>
    </div>

    {{-- Sơ đồ bàn chơi (Table Map) --}}
    <h2 class="section-title mb-3"><i class="bi bi-grid-3x3-gap me-2"></i>Sơ đồ bàn chơi</h2>
    <div class="row g-3 mb-5">
        @foreach($data['tables'] as $table)
            @php
                $statusLabels = [
                    'AVAILABLE'   => 'TRỐNG',
                    'PLAYING'     => 'ĐANG CHƠI',
                    'RESERVED'    => 'ĐÃ ĐẶT TRƯỚC',
                    'MAINTENANCE' => 'BẢO TRÌ',
                ];
                $statusCardClass = [
                    'AVAILABLE'   => 'table-card-available',
                    'PLAYING'     => 'table-card-playing',
                    'RESERVED'    => 'table-card-reserved',
                    'MAINTENANCE' => 'table-card-maintenance',
                ][$table->status] ?? 'table-card-maintenance';
                $statusLabelClass = [
                    'AVAILABLE'   => 'table-status-available',
                    'PLAYING'     => 'table-status-playing',
                    'RESERVED'    => 'table-status-reserved',
                    'MAINTENANCE' => 'table-status-maintenance',
                ][$table->status] ?? 'table-status-maintenance';
                $activeSession = $table->status === 'PLAYING' ? $table->tableSessions->first() : null;
                $booking = $table->status === 'RESERVED' ? ($data['today_bookings'][$table->id]->first() ?? null) : null;
            @endphp

            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card glass-panel h-100 {{ $statusCardClass }} {{ $table->status === 'PLAYING' ? 'active-table-card' : '' }}"
                     @if($activeSession)
                        data-start-time="{{ $activeSession->start_time->toIso8601String() }}"
                        data-price-per-hour="{{ $table->price_per_hour }}"
                     @endif>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge" style="background-color: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2);">
                                    {{ $table->table_type }}
                                </span>
                                <span class="fw-bold {{ $statusLabelClass }}" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                    {{ $statusLabels[$table->status] }}
                                </span>
                            </div>
                            
                            <h3 class="h2 fw-bold text-white mb-2">Bàn {{ $table->table_number }}</h3>
                            <div class="text-muted small mb-3">
                                Đơn giá: <span class="text-white fw-medium">{{ number_format($table->price_per_hour, 0, ',', '.') }} ₫/h</span>
                            </div>

                            <hr style="border-color: rgba(255,255,255,0.1); margin: 0.75rem 0;">

                            {{-- Nội dung chi tiết theo trạng thái --}}
                            @if($table->status === 'AVAILABLE')
                                <div class="py-3 text-center text-muted-opacity">
                                    <i class="bi bi-check-circle fs-3 text-success d-block mb-1"></i>
                                    Sẵn sàng đón khách
                                </div>
                            @elseif($table->status === 'PLAYING' && $activeSession)
                                <div class="py-2">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="text-muted">Khách chơi:</span>
                                        <span class="text-white fw-semibold">{{ $activeSession->customer->name ?? 'Khách vãng lai' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="text-muted">Vào lúc:</span>
                                        <span class="text-white">{{ $activeSession->start_time->format('H:i') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="text-muted">Thời gian chơi:</span>
                                        <span class="text-warning fw-bold play-duration">00:00:00</span>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Tiền giờ tạm tính:</span>
                                        <span class="text-danger fw-bold estimated-cost">0 ₫</span>
                                    </div>
                                </div>
                            @elseif($table->status === 'RESERVED' && $booking)
                                <div class="py-2">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="text-muted">Người đặt:</span>
                                        <span class="text-white fw-semibold text-truncate" style="max-width: 100px;">{{ $booking->user->name }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="text-muted">Ngày đặt:</span>
                                        <span class="text-white">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Thời gian:</span>
                                        <span class="text-warning fw-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                    </div>
                                </div>
                            @elseif($table->status === 'MAINTENANCE')
                                <div class="py-3 text-center text-muted">
                                    <i class="bi bi-tools fs-3 text-secondary d-block mb-1"></i>
                                    Đang bảo trì thiết bị
                                </div>
                            @endif
                        </div>

                        {{-- Hành động nhanh tương ứng --}}
                        <div class="mt-3">
                            @if($table->status === 'AVAILABLE')
                                <div class="row g-2">
                                    <div class="col-6">
                                        <form action="{{ route('sessions.start', $table->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success w-100 py-2">
                                                <i class="bi bi-play-fill me-1"></i> Bật bàn
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('bookings.create', ['table_id' => $table->id]) }}" class="btn btn-sm btn-outline-warning w-100 py-2">
                                            <i class="bi bi-calendar-plus me-1"></i> Đặt bàn
                                        </a>
                                    </div>
                                </div>
                            @elseif($table->status === 'PLAYING' && $activeSession)
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('sessions.show', $activeSession->id) }}" class="btn btn-sm btn-outline-light w-100 py-2">
                                            <i class="bi bi-plus-circle me-1"></i> Dịch vụ
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <form action="{{ route('sessions.end', $activeSession->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger w-100 py-2" onclick="return confirm('Xác nhận tắt giờ chơi và lập hóa đơn thanh toán?')">
                                                <i class="bi bi-calculator me-1"></i> Tắt bàn
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($table->status === 'RESERVED' && $booking)
                                <div class="row g-2">
                                    <div class="col-6">
                                        <form action="{{ route('sessions.start', $table->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{ $booking->user_id }}">
                                            <button type="submit" class="btn btn-sm btn-warning w-100 py-2">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> Nhận bàn
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-6">
                                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-2" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt bàn này?')">
                                                <i class="bi bi-x-circle me-1"></i> Hủy
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($table->status === 'MAINTENANCE')
                                <form action="{{ route('tables.update-status', $table->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="AVAILABLE">
                                    <button type="submit" class="btn btn-sm btn-secondary w-100 py-2">
                                        <i class="bi bi-check-all me-1"></i> Hoàn thành bảo trì
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Danh sách đặt bàn PENDING cần duyệt --}}
    <div class="row">
        <div class="col-12">
            <x-card>
                <x-slot:title>
                    <i class="bi bi-clock-history me-2 text-warning"></i>Danh sách đặt bàn cần phê duyệt (Chờ xác nhận)
                </x-slot>

                @if($data['pending_bookings']->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x fs-2 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Không có yêu cầu đặt bàn nào đang chờ xử lý.</p>
                    </div>
                @else
                    <x-table>
                        <x-slot:thead>
                            <tr>
                                <th>Khách Hàng</th>
                                <th>Số Điện Thoại</th>
                                <th>Bàn Chơi</th>
                                <th>Ngày Đặt</th>
                                <th>Khung Giờ</th>
                                <th>Ghi Chú</th>
                                <th class="text-end">Thao Tác</th>
                            </tr>
                        </x-slot>
                        @foreach($data['pending_bookings'] as $pending)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-white">{{ $pending->user->name }}</div>
                                </td>
                                <td>{{ $pending->user->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-secondary">Bàn {{ $pending->billiardTable->table_number }}</span>
                                    <small class="text-muted ms-1">({{ $pending->billiardTable->table_type }})</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($pending->booking_date)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="text-warning fw-medium">
                                        {{ \Carbon\Carbon::parse($pending->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($pending->end_time)->format('H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted text-wrap d-block" style="max-width: 200px;">
                                        {{ $pending->note ?? 'Không có' }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <form action="{{ route('bookings.confirm', $pending->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success px-3">
                                                <i class="bi bi-check-lg me-1"></i> Xác nhận
                                            </button>
                                        </form>
                                        <form action="{{ route('bookings.cancel', $pending->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Từ chối lịch đặt bàn này?')">
                                                Từ chối
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateTimers() {
            const now = new Date();
            document.querySelectorAll('.active-table-card').forEach(card => {
                const startTimeStr = card.getAttribute('data-start-time');
                const pricePerHour = parseFloat(card.getAttribute('data-price-per-hour'));
                const durationEl = card.querySelector('.play-duration');
                const costEl = card.querySelector('.estimated-cost');

                if (startTimeStr && durationEl) {
                    const startTime = new Date(startTimeStr);
                    const diffMs = now - startTime;

                    if (diffMs < 0) return; // Tránh thời gian lệch máy chủ/trình duyệt

                    const totalSeconds = Math.floor(diffMs / 1000);
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = totalSeconds % 60;

                    // Định dạng giờ dạng HH:MM:SS
                    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    durationEl.textContent = timeString;

                    // Tính tiền giờ ước tính
                    const totalHours = totalSeconds / 3600;
                    const estimatedCost = Math.round(totalHours * pricePerHour);
                    costEl.textContent = new Intl.NumberFormat('vi-VN').format(estimatedCost) + ' ₫';
                }
            });
        }

        // Chạy lập tức và lặp mỗi 1 giây
        updateTimers();
        setInterval(updateTimers, 1000);
    });
</script>
<style>
    .text-muted-opacity {
        color: rgba(255,255,255,0.4) !important;
    }
</style>
@endpush
