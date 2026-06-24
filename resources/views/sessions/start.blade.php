{{-- resources/views/sessions/start.blade.php --}}
@extends('layouts.app')

@section('title', 'Bắt đầu phiên chơi mới')

@section('content')
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('sessions.index') }}">Phiên chơi</a></li>
            <li class="breadcrumb-item active">Bắt đầu phiên mới</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-play-circle me-2"></i>Bắt đầu phiên chơi mới</h5>
                </div>
                <div class="card-body">
                    @if ($tables->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Hiện tại không có bàn nào trống. Vui lòng quay lại sau.
                        </div>
                        <a href="{{ route('sessions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                    @else
                        <p class="text-muted mb-4">
                            Chọn bàn trống và khách hàng (nếu có) để bắt đầu phiên chơi.
                        </p>

                        {{-- Table Selection --}}
                        <h6 class="mb-3"><i class="bi bi-table me-1"></i> Chọn bàn chơi</h6>
                        <div class="row g-3 mb-4" id="table-selection">
                            @foreach ($tables as $table)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card border table-card" role="button"
                                         data-table-id="{{ $table->id }}"
                                         data-table-name="{{ $table->table_number }}"
                                         data-table-type="{{ $table->table_type }}"
                                         data-price="{{ $table->price_per_hour }}"
                                         onclick="selectTable(this)">
                                        <div class="card-body text-center py-3">
                                            <i class="bi bi-circle-fill text-success mb-2" style="font-size: 0.6rem;"></i>
                                            <h6 class="mb-1">{{ $table->table_number }}</h6>
                                            <small class="text-muted d-block">{{ $table->table_type }}</small>
                                            <strong class="text-primary">{{ number_format((float) $table->price_per_hour, 0, ',', '.') }} ₫/h</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Selected Table Info --}}
                        <div id="selected-table-info" class="alert alert-info d-none mb-4">
                            <i class="bi bi-check-circle me-1"></i>
                            Bàn đã chọn: <strong id="selected-table-name"></strong>
                            — <span id="selected-table-price"></span> ₫/giờ
                        </div>

                        {{-- Customer Selection --}}
                        <h6 class="mb-3"><i class="bi bi-person me-1"></i> Khách hàng (không bắt buộc)</h6>
                        <div class="mb-4 position-relative">
                            <div class="input-group">
                                <span class="input-group-text border-end-0 text-muted" style="background: transparent;">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       class="form-control border-start-0"
                                       id="customer-search"
                                       placeholder="Nhập tên hoặc email khách hàng để tìm..."
                                       autocomplete="off"
                                       style="height: 40px;">
                                <button type="button" class="btn btn-outline-secondary" id="btn-clear-customer" style="display: none;" title="Xóa chọn">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            {{-- Dropdown danh sách khách hàng --}}
                            <div id="customer-dropdown"
                                 class="position-absolute w-100 rounded-3 shadow-lg border mt-1"
                                 style="display: none; max-height: 240px; overflow-y: auto; z-index: 100; background: var(--bg-surface, #1e293b); border-color: var(--border, #334155) !important;">
                                <div class="customer-option px-3 py-2 text-muted" data-id="" style="cursor: pointer; border-bottom: 1px solid var(--border, #334155);">
                                    <i class="bi bi-person-dash me-1"></i> Khách vãng lai (không chọn)
                                </div>
                                @foreach ($customers as $customer)
                                    <div class="customer-option px-3 py-2"
                                         data-id="{{ $customer->id }}"
                                         data-name="{{ $customer->name }}"
                                         data-email="{{ $customer->email }}"
                                         data-phone="{{ $customer->phone }}"
                                         style="cursor: pointer; border-bottom: 1px solid var(--border, #334155);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-glow, rgba(102,126,234,0.15)); color: var(--primary, #667eea); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary, #f1f5f9);">{{ $customer->name }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-secondary, #94a3b8);">{{ $customer->email }}{{ $customer->phone ? ' · ' . $customer->phone : '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Hiển thị khách hàng đã chọn --}}
                            <div id="selected-customer-info" class="alert alert-success d-none mt-2 mb-0 py-2 px-3" style="font-size: 0.875rem;">
                                <i class="bi bi-person-check-fill me-1"></i>
                                Khách hàng: <strong id="selected-customer-name"></strong>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <form id="start-session-form" method="POST" action="">
                            @csrf
                            <input type="hidden" name="customer_id" id="customer-id-input" value="">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="btn-start" disabled>
                                    <i class="bi bi-play-fill me-1"></i> Bắt đầu phiên chơi
                                </button>
                                <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-1"></i> Hủy
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .table-card {
        cursor: pointer;
        transition: all 0.2s;
    }
    .table-card:hover {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }
    .table-card.selected {
        border-color: #0d6efd !important;
        background-color: #e8f0fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.35);
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedTableId = null;

    function selectTable(el) {
        // Remove all selected classes
        document.querySelectorAll('.table-card').forEach(c => c.classList.remove('selected'));

        // Select this one
        el.classList.add('selected');
        selectedTableId = el.dataset.tableId;

        // Update form action
        const form = document.getElementById('start-session-form');
        form.action = '{{ url("sessions") }}/' + selectedTableId + '/start';

        // Show selected info
        const infoEl = document.getElementById('selected-table-info');
        infoEl.classList.remove('d-none');
        document.getElementById('selected-table-name').textContent = el.dataset.tableName + ' (' + el.dataset.tableType + ')';
        document.getElementById('selected-table-price').textContent = new Intl.NumberFormat('vi-VN').format(el.dataset.price);

        // Enable submit button
        document.getElementById('btn-start').disabled = false;
    }

    // ═══ CUSTOMER SEARCHABLE DROPDOWN ═══
    const searchInput    = document.getElementById('customer-search');
    const dropdown       = document.getElementById('customer-dropdown');
    const hiddenInput    = document.getElementById('customer-id-input');
    const btnClear       = document.getElementById('btn-clear-customer');
    const selectedInfo   = document.getElementById('selected-customer-info');
    const selectedName   = document.getElementById('selected-customer-name');
    const allOptions     = dropdown.querySelectorAll('.customer-option');

    // Show dropdown on focus
    searchInput.addEventListener('focus', function () {
        filterOptions(this.value);
        dropdown.style.display = 'block';
    });

    // Filter as user types
    searchInput.addEventListener('input', function () {
        filterOptions(this.value);
        dropdown.style.display = 'block';
    });

    // Filter options by keyword
    function filterOptions(keyword) {
        const kw = keyword.toLowerCase().trim();
        allOptions.forEach(function (opt) {
            const name  = (opt.dataset.name  || '').toLowerCase();
            const email = (opt.dataset.email || '').toLowerCase();
            const phone = (opt.dataset.phone || '').toLowerCase();
            const id    = opt.dataset.id;

            // Always show "Khách vãng lai" option
            if (id === '') {
                opt.style.display = '';
                return;
            }

            if (name.includes(kw) || email.includes(kw) || phone.includes(kw)) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    // Click on option → select it
    allOptions.forEach(function (opt) {
        opt.addEventListener('click', function () {
            const id   = this.dataset.id;
            const name = this.dataset.name || '';

            hiddenInput.value = id;

            if (id && name) {
                searchInput.value = name;
                selectedName.textContent = name + ' (' + (this.dataset.email || '') + ')';
                selectedInfo.classList.remove('d-none');
                btnClear.style.display = '';
            } else {
                // "Khách vãng lai"
                searchInput.value = '';
                selectedInfo.classList.add('d-none');
                btnClear.style.display = 'none';
            }

            dropdown.style.display = 'none';
        });

        // Hover effect
        opt.addEventListener('mouseenter', function () {
            this.style.background = 'var(--bg-hover, rgba(255,255,255,0.06))';
        });
        opt.addEventListener('mouseleave', function () {
            this.style.background = '';
        });
    });

    // Clear button
    btnClear.addEventListener('click', function () {
        hiddenInput.value = '';
        searchInput.value = '';
        selectedInfo.classList.add('d-none');
        btnClear.style.display = 'none';
        searchInput.focus();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target) && !btnClear.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>
@endpush

