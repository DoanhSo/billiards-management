@extends('layouts.app')

@section('title', 'Tạo lịch đặt bàn')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-1"><i class="bi bi-calendar-plus-fill me-2" style="color: var(--primary)"></i>Đặt Bàn Mới</h1>
            <p class="text-muted mb-0">Tạo lịch hẹn giữ bàn chơi trước cho khách hàng</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-light d-flex align-items-center gap-2" style="height: 40px;">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ═══ FORM CARD ═══ --}}
    <x-card>
        <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm" class="d-flex flex-column gap-3">
            @csrf
            {{-- Hidden combined datetime fields --}}
            <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
            <input type="hidden" name="end_time"   id="end_time"   value="{{ old('end_time') }}">

            {{-- ─── Customer ID ─── --}}
            @if(auth()->user()->isCustomer())
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            @else
                <div>
                    <x-input name="user_id"
                             label="ID Khách hàng"
                             type="number"
                             placeholder="Nhập ID tài khoản khách hàng (VD: 1, 2...)"
                             value="{{ old('user_id') }}"
                             required="true"
                             error="{{ $errors->first('user_id') }}"
                             min="1" />
                    <div class="form-text text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Nhập mã ID của tài khoản khách hàng trong hệ thống</div>
                </div>
            @endif

            {{-- ─── Table Selection ─── --}}
            <div class="d-flex flex-column gap-1 w-100">
                <label for="billiard_table_id" class="form-label mb-1">
                    Bàn chơi <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-muted" style="background: transparent;"><i class="bi bi-grid-3x3-gap"></i></span>
                    <select id="billiard_table_id" name="billiard_table_id"
                            class="form-select border-start-0 @error('billiard_table_id') is-invalid @enderror" 
                            style="height: 40px; background-color: rgba(255, 255, 255, 0.07); color: #fff;" required>
                        <option value="" disabled {{ !request('table_id') && !old('billiard_table_id') ? 'selected' : '' }}>— Chọn bàn —</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('billiard_table_id', request('table_id')) == $table->id ? 'selected' : '' }} style="background-color: var(--card-bg); color: #fff;">
                                Bàn {{ $table->table_number }} · {{ $table->table_type }}
                                — {{ number_format($table->price_per_hour, 0, ',', '.') }} VNĐ/giờ
                                {{ $table->status === 'RESERVED' ? '[ Đã có booking giờ khác ]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('billiard_table_id')
                    <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
                @enderror
                @if($tables->isEmpty())
                    <div class="form-text mt-1 text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Hiện tại không có bàn nào khả dụng để đặt lịch</div>
                @endif
                <div class="form-text mt-1">
                    <i class="bi bi-info-circle me-1 text-info"></i>
                    Bàn có nhãn <strong>[Đã có booking giờ khác]</strong> vẫn có thể đặt nếu bạn chọn khung giờ khác.
                </div>
            </div>

            {{-- ─── Booking Date ─── --}}
            <div>
                <x-input name="booking_date"
                         label="Ngày đặt bàn"
                         type="date"
                         value="{{ old('booking_date', date('Y-m-d')) }}"
                         required="true"
                         error="{{ $errors->first('booking_date') }}"
                         min="{{ date('Y-m-d') }}" />
            </div>

            {{-- ─── Time Range ─── --}}
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="start_hour" class="form-label mb-1">
                            Giờ bắt đầu <span class="text-danger">*</span>
                        </label>
                        <input type="time"
                               id="start_hour"
                               class="form-control @error('start_time') is-invalid @enderror"
                               style="height: 40px;"
                               required>
                        @error('start_time')
                            <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="end_hour" class="form-label mb-1">
                            Giờ kết thúc <span class="text-danger">*</span>
                        </label>
                        <input type="time"
                               id="end_hour"
                               class="form-control @error('end_time') is-invalid @enderror"
                               style="height: 40px;"
                               required>
                        @error('end_time')
                            <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Duration Preview --}}
            <div id="duration-preview" style="display: none;">
                <div class="p-3 bg-primary-subtle border border-primary-subtle rounded-3" style="font-size: 0.875rem; color: var(--primary);">
                    <i class="bi bi-stopwatch-fill me-2"></i>
                    Thời gian đặt: <strong id="duration-text"></strong>
                </div>
            </div>

            {{-- ─── Note ─── --}}
            <div class="d-flex flex-column gap-1 w-100">
                <label for="note" class="form-label mb-1">Ghi chú <span class="text-muted">(tuỳ chọn)</span></label>
                <textarea id="note"
                          name="note"
                          class="form-control @error('note') is-invalid @enderror"
                          rows="3"
                          placeholder="Yêu cầu đặc biệt, ghi chú về khách...">{{ old('note') }}</textarea>
                @error('note')
                    <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
                @enderror
            </div>

            <hr class="my-3 text-secondary-subtle">

            {{-- ─── Actions ─── --}}
            <div class="d-flex justify-content-end gap-3">
                <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('bookings.index') }}'">
                    Hủy bỏ
                </x-button>
                <x-button type="submit" variant="primary" icon="calendar-plus-fill">
                    Xác nhận đặt bàn
                </x-button>
            </div>
        </form>
    </x-card>

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
