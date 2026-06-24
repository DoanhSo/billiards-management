@extends('layouts.app')

@section('title', 'Chi tiết Bàn chơi')

@section('content')
<div class="container-fluid px-0">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">
                <a href="{{ route('tables.index') }}" class="text-decoration-none text-muted-c me-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <i class="bi bi-display-fill me-2" style="color: var(--primary)"></i>Chi tiết Bàn {{ $table->table_number }}
            </h1>
            <p class="page-subtitle">Quản lý thông tin và phụ kiện theo bàn</p>
        </div>
        <div>
            <a href="{{ route('tables.edit', $table->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil-fill"></i> Sửa thông tin bàn
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- ═══ CỘT TRÁI: THÔNG TIN BÀN ═══ --}}
        <div class="col-12 col-xl-4">
            <x-card>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Thông tin chung</h5>
                    @php
                        $statusColors = [
                            'AVAILABLE' => 'success',
                            'PLAYING' => 'primary',
                            'RESERVED' => 'warning',
                            'MAINTENANCE' => 'danger'
                        ];
                        $statusLabels = [
                            'AVAILABLE' => 'Sẵn sàng',
                            'PLAYING' => 'Đang chơi',
                            'RESERVED' => 'Đã đặt',
                            'MAINTENANCE' => 'Bảo trì'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$table->status] ?? 'secondary' }} rounded-pill px-3 py-2">
                        {{ $statusLabels[$table->status] ?? $table->status }}
                    </span>
                </div>

                <div class="mb-3">
                    <label class="text-muted-c small fw-bold mb-1">Số hiệu bàn</label>
                    <div class="fs-5 fw-bold" style="color: var(--text-primary);">{{ $table->table_number }}</div>
                </div>

                <div class="mb-3">
                    <label class="text-muted-c small fw-bold mb-1">Loại bàn</label>
                    <div class="fs-6" style="color: var(--text-secondary);">{{ $table->table_type }}</div>
                </div>

                <div class="mb-3">
                    <label class="text-muted-c small fw-bold mb-1">Giá mỗi giờ</label>
                    <div class="fs-6 fw-bold text-success">{{ number_format($table->price_per_hour, 0, ',', '.') }} VNĐ</div>
                </div>

                @if($table->description)
                <div class="mb-3">
                    <label class="text-muted-c small fw-bold mb-1">Mô tả</label>
                    <div class="fs-6" style="color: var(--text-secondary);">{{ $table->description }}</div>
                </div>
                @endif
            </x-card>
        </div>



@endsection

