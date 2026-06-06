@extends('layouts.app')

@section('title', 'Tạo lịch đặt bàn')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-calendar-plus-fill me-2" style="color: var(--info)"></i>Đặt Bàn Mới</h1>
            <p class="page-subtitle">Tạo lịch đặt bàn chơi cho khách hàng</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ═══ FORM CARD ═══ --}}
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                @csrf
                {{-- Hidden combined datetime fields --}}
                <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
                <input type="hidden" name="end_time"   id="end_time"   value="{{ old('end_time') }}">

                {{-- ─── Customer ID ─── --}}
                <div class="mb-4">
                    <label class="form-label">
                        ID Khách hàng <span style="color: var(--danger)">*</span>
                    </label>
                    <div class="input-group glow-on-focus">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="number"
                               id="user_id"
                               name="user_id"
                               class="form-control @error('user_id') is-invalid @enderror"
                               placeholder="Nhập ID tài khoản khách hàng (VD: 1, 2...)"
                               value="{{ old('user_id') }}"
                               min="1"
                               required>
                    </div>
                    <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>Nhập mã ID của tài khoản khách hàng trong hệ thống</div>
                    @error('user_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ─── Table Selection ─── --}}
                <div class="mb-4">
                    <label class="form-label">
                        Bàn chơi <span style="color: var(--danger)">*</span>
                    </label>
                    <div class="input-group glow-on-focus">
                        <span class="input-group-text"><i class="bi bi-grid-3x3-gap"></i></span>
                        <select id="billiard_table_id" name="billiard_table_id"
                                class="form-select @error('billiard_table_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('billiard_table_id') ? '' : 'selected' }}>— Chọn bàn trống —</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}" {{ old('billiard_table_id') == $table->id ? 'selected' : '' }}>
                                    Bàn {{ $table->table_number }} · {{ $table->table_type }} — {{ number_format($table->price_per_hour, 0, ',', '.') }} VNĐ/giờ
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('billiard_table_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @if($tables->isEmpty())
                        <div class="form-text mt-1" style="color: var(--warning)"><i class="bi bi-exclamation-triangle me-1"></i>Hiện tại không có bàn trống nào</div>
                    @endif
                </div>

                {{-- ─── Booking Date ─── --}}
                <div class="mb-4">
                    <label class="form-label">
                        Ngày đặt bàn <span style="color: var(--danger)">*</span>
                    </label>
                    <div class="input-group glow-on-focus">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="date"
                               id="booking_date"
                               name="booking_date"
                               class="form-control @error('booking_date') is-invalid @enderror"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('booking_date', date('Y-m-d')) }}"
                               required>
                    </div>
                    @error('booking_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ─── Time Range ─── --}}
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">
                            Giờ bắt đầu <span style="color: var(--danger)">*</span>
                        </label>
                        <div class="input-group glow-on-focus">
                            <span class="input-group-text" style="color: var(--success)"><i class="bi bi-clock"></i></span>
                            <input type="time"
                                   id="start_hour"
                                   class="form-control @error('start_time') is-invalid @enderror"
                                   required>
                        </div>
                        @error('start_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">
                            Giờ kết thúc <span style="color: var(--danger)">*</span>
                        </label>
                        <div class="input-group glow-on-focus">
                            <span class="input-group-text" style="color: var(--danger)"><i class="bi bi-clock-fill"></i></span>
                            <input type="time"
                                   id="end_hour"
                                   class="form-control @error('end_time') is-invalid @enderror"
                                   required>
                        </div>
                        @error('end_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Duration Preview --}}
                <div id="duration-preview" class="mb-4" style="display: none;">
                    <div style="background: var(--primary-glow); border: 1px solid rgba(129,140,248,0.3); border-radius: 8px; padding: 12px 16px; font-size: 0.875rem; color: var(--primary);">
                        <i class="bi bi-stopwatch-fill me-2"></i>
                        Thời gian đặt: <strong id="duration-text"></strong>
                    </div>
                </div>

                {{-- ─── Note ─── --}}
                <div class="mb-5">
                    <label class="form-label">Ghi chú <span style="color: var(--text-muted-c);">(tuỳ chọn)</span></label>
                    <textarea id="note"
                              name="note"
                              class="form-control @error('note') is-invalid @enderror"
                              rows="3"
                              placeholder="Yêu cầu đặc biệt, ghi chú về khách...">{{ old('note') }}</textarea>
                    @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="divider"></div>

                {{-- ─── Actions ─── --}}
                <div class="d-flex justify-content-end gap-3 pt-3">
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-calendar-plus-fill"></i> Xác nhận đặt bàn
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form           = document.getElementById('bookingForm');
    const dateInput      = document.getElementById('booking_date');
    const startHourInput = document.getElementById('start_hour');
    const endHourInput   = document.getElementById('end_hour');
    const startHidden    = document.getElementById('start_time');
    const endHidden      = document.getElementById('end_time');
    const durationDiv    = document.getElementById('duration-preview');
    const durationText   = document.getElementById('duration-text');

    // Pre-populate time inputs from old values
    const oldStart = startHidden.value;
    const oldEnd   = endHidden.value;
    if (oldStart) startHourInput.value = oldStart.split(' ')[1]?.substring(0, 5) ?? '';
    if (oldEnd)   endHourInput.value   = oldEnd.split(' ')[1]?.substring(0, 5)   ?? '';

    function formatTime(t) {
        return t.length === 5 ? t + ':00' : t;
    }

    function updateDuration() {
        const start = startHourInput.value;
        const end   = endHourInput.value;
        if (!start || !end) { durationDiv.style.display = 'none'; return; }

        const [sh, sm] = start.split(':').map(Number);
        const [eh, em] = end.split(':').map(Number);
        const total = (eh * 60 + em) - (sh * 60 + sm);

        if (total > 0) {
            const h = Math.floor(total / 60);
            const m = total % 60;
            durationText.textContent = h > 0
                ? (m > 0 ? `${h} giờ ${m} phút` : `${h} giờ`)
                : `${m} phút`;
            durationDiv.style.display = 'block';
        } else {
            durationDiv.style.display = 'none';
        }
    }

    startHourInput.addEventListener('change', updateDuration);
    endHourInput.addEventListener('change', updateDuration);
    updateDuration();

    form.addEventListener('submit', function (e) {
        const date  = dateInput.value;
        const start = startHourInput.value;
        const end   = endHourInput.value;

        if (date && start && end) {
            startHidden.value = `${date} ${formatTime(start)}`;
            endHidden.value   = `${date} ${formatTime(end)}`;
        }
    });
});
</script>
@endpush
