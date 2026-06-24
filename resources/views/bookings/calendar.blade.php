@extends('layouts.app')

@section('title', 'Lịch đặt bàn')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color: var(--text-primary); letter-spacing: -0.5px;">Lịch đặt bàn</h4>
            <p class="mb-0" style="color: var(--text-muted-c); font-size: 0.9rem;">
                Hiển thị tiến độ và thời gian đặt bàn trực quan
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius: 8px;">
                <i class="bi bi-list-task"></i> Xem dạng danh sách
            </a>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="background: var(--gradient-primary); border: none; border-radius: 8px;">
                <i class="bi bi-plus-lg"></i> Thêm đặt bàn
            </a>
        </div>
    </div>

    <x-card>
        <div id="calendar" style="min-height: 700px; color: var(--text-primary);"></div>
    </x-card>
</div>

<!-- FullCalendar CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/vi.js"></script>

<style>
/* Tùy chỉnh FullCalendar cho Dark Theme */
.fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--border-light) !important;
}
.fc-theme-standard .fc-scrollgrid {
    border-color: var(--border-light) !important;
}
.fc-col-header-cell-cushion {
    color: var(--text-secondary);
    padding: 12px 8px !important;
    font-weight: 600;
}
.fc-daygrid-day-number {
    color: var(--text-secondary);
    padding: 8px !important;
}
.fc-event {
    border: none !important;
    border-radius: 6px !important;
    padding: 4px 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: transform 0.2s;
    cursor: pointer;
}
.fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}
.fc-event-title {
    font-weight: 600;
    font-size: 0.85rem;
}
.fc-event-time {
    font-size: 0.8rem;
    opacity: 0.9;
}
.fc-button-primary {
    background: var(--bg-elevated) !important;
    border-color: var(--border-light) !important;
    color: var(--text-primary) !important;
    text-transform: capitalize !important;
    box-shadow: none !important;
}
.fc-button-primary:not(:disabled):active,
.fc-button-primary:not(:disabled).fc-button-active {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
}
.fc-day-today {
    background: rgba(59, 130, 246, 0.05) !important; /* Soft blue for today */
}
.fc-timegrid-slot-label-cushion {
    color: var(--text-muted-c);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'vi',
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay,dayGridMonth'
        },
        slotMinTime: '08:00:00', // Giờ mở cửa
        slotMaxTime: '24:00:00', // Giờ đóng cửa
        allDaySlot: false,
        height: 'auto',
        events: '{{ route('api.bookings.events') }}',
        eventClick: function(info) {
            // Hiển thị chi tiết khi click vào sự kiện
            let props = info.event.extendedProps;
            let statusBadge = '';
            
            if (props.status === 'PENDING') statusBadge = '<span class="badge bg-warning">Chờ xác nhận</span>';
            else if (props.status === 'CONFIRMED') statusBadge = '<span class="badge bg-success">Đã xác nhận</span>';
            else if (props.status === 'COMPLETED') statusBadge = '<span class="badge bg-primary">Đã hoàn thành</span>';
            else statusBadge = '<span class="badge bg-secondary">Hủy</span>';

            let html = `
                <div class="p-2 text-start">
                    <p><strong>Khách hàng:</strong> ${props.customer} (${props.phone || 'N/A'})</p>
                    <p><strong>Bàn:</strong> ${props.table_number}</p>
                    <p><strong>Thời gian:</strong> ${info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${info.event.end ? info.event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A'}</p>
                    <p><strong>Trạng thái:</strong> ${statusBadge}</p>
                    ${props.note ? `<p><strong>Ghi chú:</strong> ${props.note}</p>` : ''}
                </div>
            `;
            
            // Dùng Bootstrap Modal thay vì alert
            document.getElementById('eventModalCustomer').innerText = `${props.customer} (${props.phone || 'N/A'})`;
            document.getElementById('eventModalTable').innerText = `Bàn ${props.table_number}`;
            document.getElementById('eventModalTime').innerText = `${info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${info.event.end ? info.event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A'}`;
            document.getElementById('eventModalStatus').innerHTML = statusBadge;
            document.getElementById('eventModalNote').innerText = props.note || 'Không có';
            
            let eventModal = new bootstrap.Modal(document.getElementById('calendarEventModal'));
            eventModal.show();
        }
    });

    calendar.render();
});
</script>

<!-- Bootstrap Modal for Event Details -->
<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="calendarEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-light);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="calendarEventModalLabel"><i class="bi bi-info-circle text-info me-2"></i>Chi tiết đặt bàn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2"><strong>Khách hàng:</strong> <span id="eventModalCustomer"></span></p>
                <p class="mb-2"><strong>Bàn:</strong> <span id="eventModalTable"></span></p>
                <p class="mb-2"><strong>Thời gian:</strong> <span id="eventModalTime"></span></p>
                <p class="mb-2"><strong>Trạng thái:</strong> <span id="eventModalStatus"></span></p>
                <p class="mb-0"><strong>Ghi chú:</strong> <span id="eventModalNote"></span></p>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@endsection

