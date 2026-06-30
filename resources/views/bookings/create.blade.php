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
        <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm" class="d-flex flex-column gap-3" novalidate>
            @csrf
            {{-- Hidden combined datetime fields --}}
            <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
            <input type="hidden" name="end_time"   id="end_time"   value="{{ old('end_time') }}">

            {{-- ─── Customer Search ─── --}}
            @if(auth()->user()->isCustomer())
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            @else
                <div class="d-flex flex-column gap-1 w-100">
                    <label for="user_id_search" class="form-label mb-1">
                        Khách hàng <span class="text-danger">*</span>
                    </label>

                    {{-- Search input --}}
                    <div class="position-relative">
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-muted" style="background: transparent;">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                                   id="user_id_search"
                                   class="form-control border-start-0 @error('user_id') is-invalid @enderror"
                                   placeholder="Nhập tên hoặc số điện thoại khách hàng..."
                                   autocomplete="off"
                                   style="height: 40px;">
                        </div>

                        {{-- Dropdown list --}}
                        <div id="customer-dropdown"
                             class="position-absolute w-100 rounded-3 shadow-lg d-none"
                             style="top: calc(100% + 4px); left: 0; z-index: 1050;
                                    max-height: 220px; overflow-y: auto;
                                    background: var(--bg-surface); border: 1px solid var(--border);">
                        </div>
                    </div>

                    {{-- Hidden real input --}}
                    <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">

                    {{-- Selected customer badge --}}
                    <div id="selected-customer" class="d-none mt-1">
                        <span class="badge d-inline-flex align-items-center gap-2 px-3 py-2"
                              style="background: rgba(99,102,241,0.15); color: var(--primary); border: 1px solid rgba(99,102,241,0.3); font-size: 0.85rem;">
                            <i class="bi bi-person-check-fill"></i>
                            <span id="selected-customer-name"></span>
                            <button type="button" id="clear-customer"
                                    class="btn-close btn-close-white"
                                    style="font-size: 0.6rem; filter: none; opacity: 0.7;"
                                    aria-label="Xóa"></button>
                        </span>
                    </div>

                    {{-- Error message --}}
                    @error('user_id')
                        <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
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
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('billiard_table_id')
                    <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </div>
                @enderror
                @if($tables->isEmpty())
                    <div class="form-text mt-1 text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Hiện tại không có bàn nào khả dụng để đặt lịch</div>
                @endif
            </div>

            {{-- ─── Booking Date ─── --}}
            <div class="d-flex flex-column gap-1 w-100">
                <label for="booking_date" class="form-label mb-1">
                    Ngày đặt bàn <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-muted" style="background: transparent;"><i class="bi bi-calendar3"></i></span>
                    <input type="date"
                           id="booking_date"
                           name="booking_date"
                           class="form-control border-start-0 @error('booking_date') is-invalid @enderror"
                           style="height: 40px;"
                           value="{{ old('booking_date', date('Y-m-d')) }}"
                           min="{{ date('Y-m-d') }}"
                           required>
                </div>
                @error('booking_date')
                    <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- ─── Time Range ─── --}}
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="start_hour" class="form-label mb-1">
                            Giờ bắt đầu <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-muted" style="background: transparent;"><i class="bi bi-clock"></i></span>
                            <input type="time"
                                   id="start_hour"
                                   class="form-control border-start-0 @error('start_time') is-invalid @enderror"
                                   style="height: 40px;"
                                   required>
                        </div>
                        @error('start_time')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="end_hour" class="form-label mb-1">
                            Giờ kết thúc <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-muted" style="background: transparent;"><i class="bi bi-clock-history"></i></span>
                            <input type="time"
                                   id="end_hour"
                                   class="form-control border-start-0 @error('end_time') is-invalid @enderror"
                                   style="height: 40px;"
                                   required>
                        </div>
                        @error('end_time')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
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
                    <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </div>
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
    // ── DateTime logic ───────────────────────────────────────────
    const form           = document.getElementById('bookingForm');
    const dateInput      = document.getElementById('booking_date');
    const startHourInput = document.getElementById('start_hour');
    const endHourInput   = document.getElementById('end_hour');
    const startHidden    = document.getElementById('start_time');
    const endHidden      = document.getElementById('end_time');
    const durationDiv    = document.getElementById('duration-preview');
    const durationText   = document.getElementById('duration-text');

    const oldStart = startHidden.value;
    const oldEnd   = endHidden.value;
    if (oldStart) startHourInput.value = oldStart.split(' ')[1]?.substring(0, 5) ?? '';
    if (oldEnd)   endHourInput.value   = oldEnd.split(' ')[1]?.substring(0, 5)   ?? '';

    function formatTime(t) { return t.length === 5 ? t + ':00' : t; }

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

    // ── Customer Live-Search ─────────────────────────────────────
    @unless(auth()->user()->isCustomer())
    const customers = @json($customers->items());

    const searchInput    = document.getElementById('user_id_search');
    const dropdown       = document.getElementById('customer-dropdown');
    const hiddenInput    = document.getElementById('user_id');
    const selectedBox    = document.getElementById('selected-customer');
    const selectedNameEl = document.getElementById('selected-customer-name');
    const clearBtn       = document.getElementById('clear-customer');

    const oldId = '{{ old('user_id') }}';
    if (oldId) {
        const found = customers.find(c => String(c.id) === String(oldId));
        if (found) selectCustomer(found.id, found.name, found.phone);
    }

    function normalizeStr(str) {
        return (str ?? '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function renderDropdown(list) {
        dropdown.innerHTML = '';
        if (list.length === 0) {
            dropdown.innerHTML = `
                <div class="px-3 py-2" style="font-size:0.85rem; color: var(--text-muted-c);">
                    <i class="bi bi-person-x me-1"></i> Không tìm thấy khách hàng
                </div>`;
            dropdown.classList.remove('d-none');
            return;
        }
        list.forEach(c => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 d-flex align-items-center gap-2';
            item.style.cssText = 'cursor:pointer; font-size:0.875rem; transition: background 0.15s; border-bottom: 1px solid var(--border);';
            item.innerHTML = `
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:32px;height:32px;background:var(--primary-glow);color:var(--primary);font-weight:600;font-size:0.8rem;">
                    ${c.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div style="font-weight:500;color:var(--text-primary);">${c.name}</div>
                    <div style="color:var(--text-muted-c);font-size:0.78rem;">
                        <i class="bi bi-telephone me-1"></i>${c.phone ?? c.email ?? '—'}
                    </div>
                </div>`;
            item.addEventListener('mouseenter', () => item.style.background = 'var(--bg-hover)');
            item.addEventListener('mouseleave', () => item.style.background = 'transparent');
            item.addEventListener('mousedown', (e) => {
                e.preventDefault();
                selectCustomer(c.id, c.name, c.phone);
            });
            dropdown.appendChild(item);
        });
        dropdown.classList.remove('d-none');
    }

    function selectCustomer(id, name, phone) {
        hiddenInput.value          = id;
        searchInput.value          = '';
        selectedNameEl.textContent = name + (phone ? '  ·  ' + phone : '');
        selectedBox.classList.remove('d-none');
        dropdown.classList.add('d-none');
    }

    function clearCustomer() {
        hiddenInput.value = '';
        selectedBox.classList.add('d-none');
        searchInput.value = '';
        searchInput.focus();
    }

    searchInput.addEventListener('input', function () {
        const q = normalizeStr(this.value.trim());
        if (!q) { 
            // Nếu trống, hiện tất cả (tối đa 20 khách để không bị quá tải UI)
            renderDropdown(customers.slice(0, 20));
            return; 
        }
        const results = customers.filter(c =>
            normalizeStr(c.name).includes(q) ||
            normalizeStr(c.phone ?? '').includes(q) ||
            normalizeStr(c.email ?? '').includes(q)
        ).slice(0, 15);
        renderDropdown(results);
    });

    searchInput.addEventListener('focus', function () {
        this.dispatchEvent(new Event('input'));
    });

    searchInput.addEventListener('click', function () {
        this.dispatchEvent(new Event('input'));
    });

    // Sửa lỗi blur: click vào kết quả dropdown có thể bị blur trước khi click event fire, 
    // nên dùng mousedown bên trên là đúng rồi, nhưng vẫn để timeout blur
    searchInput.addEventListener('blur', function () {
        setTimeout(() => dropdown.classList.add('d-none'), 200);
    });

    clearBtn.addEventListener('click', clearCustomer);
    @endunless
});
</script>
@endpush


