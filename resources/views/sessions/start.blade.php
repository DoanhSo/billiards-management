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
                        <div class="mb-4">
                            <select class="form-select" id="customer-select">
                                <option value="">— Khách vãng lai —</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }} — {{ $customer->email }}
                                    </option>
                                @endforeach
                            </select>
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

    // Sync customer select → hidden input
    document.getElementById('customer-select').addEventListener('change', function () {
        document.getElementById('customer-id-input').value = this.value;
    });
</script>
@endpush
