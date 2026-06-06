@extends('layouts.app')

@section('title', 'Đặt bàn mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tạo Lịch Đặt bàn</h1>
            <p class="text-muted mb-0">Tạo lịch đặt bàn chơi mới cho khách hàng.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                @csrf

                <!-- Hidden inputs for start_time & end_time -->
                <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
                <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time') }}">

                <!-- Customer ID -->
                <div class="mb-3">
                    <label for="user_id" class="form-label fw-semibold text-muted">ID Khách hàng <span class="text-danger">*</span></label>
                    <input type="number" id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror" placeholder="Nhập ID tài khoản khách hàng (VD: 1, 2...)" value="{{ old('user_id') }}" required>
                    <div class="form-text text-muted">Vui lòng nhập mã ID của tài khoản khách hàng trong hệ thống.</div>
                    @error('user_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Billiard Table -->
                <div class="mb-3">
                    <label for="billiard_table_id" class="form-label fw-semibold text-muted">Chọn Bàn chơi <span class="text-danger">*</span></label>
                    <select id="billiard_table_id" name="billiard_table_id" class="form-select @error('billiard_table_id') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Chọn bàn chơi trống --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('billiard_table_id') == $table->id ? 'selected' : '' }}>
                                Bàn {{ $table->table_number }} ({{ $table->table_type }}) — {{ number_format($table->price_per_hour, 0, ',', '.') }} VNĐ/giờ
                            </option>
                        @endforeach
                    </select>
                    @error('billiard_table_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Booking Date -->
                <div class="mb-3">
                    <label for="booking_date" class="form-label fw-semibold text-muted">Ngày đặt bàn <span class="text-danger">*</span></label>
                    <input type="date" id="booking_date" name="booking_date" class="form-control @error('booking_date') is-invalid @enderror" min="{{ date('Y-m-d') }}" value="{{ old('booking_date', date('Y-m-d')) }}" required>
                    @error('booking_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Start and End Time inputs (user-friendly) -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="start_hour" class="form-label fw-semibold text-muted">Giờ bắt đầu <span class="text-danger">*</span></label>
                        <input type="time" id="start_hour" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_hour') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="end_hour" class="form-label fw-semibold text-muted">Giờ kết thúc <span class="text-danger">*</span></label>
                        <input type="time" id="end_hour" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_hour') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Ghi chú -->
                <div class="mb-4">
                    <label for="note" class="form-label fw-semibold text-muted">Ghi chú cuộc hẹn</label>
                    <textarea id="note" name="note" class="form-control @error('note') is-invalid @enderror" rows="3" placeholder="Nhập yêu cầu đặc biệt của khách (nếu có)...">{{ old('note') }}</textarea>
                    @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Actions Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('bookings.index') }}" class="btn btn-light border">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bookingForm');
        const bookingDateInput = document.getElementById('booking_date');
        const startHourInput = document.getElementById('start_hour');
        const endHourInput = document.getElementById('end_hour');
        const startTimeHidden = document.getElementById('start_time');
        const endTimeHidden = document.getElementById('end_time');

        // Prepopulate start_hour and end_hour if old values exist
        const oldStartTime = startTimeHidden.value;
        const oldEndTime = endTimeHidden.value;
        
        if (oldStartTime) {
            const timePart = oldStartTime.split(' ')[1];
            if (timePart) startHourInput.value = timePart.substring(0, 5);
        }
        if (oldEndTime) {
            const timePart = oldEndTime.split(' ')[1];
            if (timePart) endHourInput.value = timePart.substring(0, 5);
        }

        form.addEventListener('submit', function(e) {
            const date = bookingDateInput.value;
            const startHour = startHourInput.value;
            const endHour = endHourInput.value;

            if (date && startHour && endHour) {
                // Ensure time has seconds format (HH:MM:SS)
                const formatTime = (timeStr) => {
                    const parts = timeStr.split(':');
                    if (parts.length === 2) {
                        return `${timeStr}:00`;
                    }
                    return timeStr;
                };

                startTimeHidden.value = `${date} ${formatTime(startHour)}`;
                endTimeHidden.value = `${date} ${formatTime(endHour)}`;
            }
        });
    });
</script>
@endpush
