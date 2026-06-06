@extends('layouts.app')

@section('title', 'Chỉnh sửa bàn #' . $table->table_number)

@section('content')
<div class="container-fluid px-0" style="max-width: 760px;">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h1 class="page-title" style="margin: 0;"><i class="bi bi-pencil-square me-2" style="color: var(--warning)"></i>Chỉnh sửa Bàn</h1>
                @php
                    $statusConfig = match($table->status) {
                        'AVAILABLE'   => ['badge' => 'badge-available',   'label' => 'Sẵn sàng'],
                        'PLAYING'     => ['badge' => 'badge-playing',     'label' => 'Đang chơi'],
                        'RESERVED'    => ['badge' => 'badge-reserved',    'label' => 'Đã đặt trước'],
                        'MAINTENANCE' => ['badge' => 'badge-maintenance', 'label' => 'Bảo trì'],
                        default       => ['badge' => 'badge-maintenance', 'label' => $table->status],
                    };
                @endphp
                <span class="badge {{ $statusConfig['badge'] }}">{{ $statusConfig['label'] }}</span>
            </div>
            <p class="page-subtitle">Bàn số <strong style="color: var(--primary)">{{ $table->table_number }}</strong> — {{ $table->table_type }}</p>
        </div>
        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ═══ FORM CARD ═══ --}}
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('tables.update', $table->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ─── Row 1: Số bàn + Loại bàn ─── --}}
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Số bàn <span style="color: var(--danger)">*</span></label>
                        <input type="text"
                               id="table_number"
                               name="table_number"
                               class="form-control @error('table_number') is-invalid @enderror"
                               placeholder="VD: 01, A02, B01..."
                               value="{{ old('table_number', $table->table_number) }}"
                               required>
                        @error('table_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Loại bàn <span style="color: var(--danger)">*</span></label>
                        <select id="table_type" name="table_type"
                                class="form-select @error('table_type') is-invalid @enderror" required>
                            <option value="POOL"    {{ old('table_type', $table->table_type) === 'POOL'    ? 'selected' : '' }}>🎱 Pool (Bida Lỗ)</option>
                            <option value="SNOOKER" {{ old('table_type', $table->table_type) === 'SNOOKER' ? 'selected' : '' }}>🔴 Snooker</option>
                            <option value="CAROM"   {{ old('table_type', $table->table_type) === 'CAROM'   ? 'selected' : '' }}>⚪ Carom (Bida Phăng)</option>
                        </select>
                        @error('table_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ─── Row 2: Giá/giờ + Trạng thái ─── --}}
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Giá thuê / giờ (VNĐ) <span style="color: var(--danger)">*</span></label>
                        <div class="input-group glow-on-focus">
                            <input type="number"
                                   id="price_per_hour"
                                   name="price_per_hour"
                                   class="form-control @error('price_per_hour') is-invalid @enderror"
                                   placeholder="VD: 80000"
                                   value="{{ old('price_per_hour', (int)$table->price_per_hour) }}"
                                   min="0"
                                   step="1000"
                                   required>
                            <span class="input-group-text" style="font-weight: 700; font-size: 0.8rem;">VNĐ</span>
                        </div>
                        @error('price_per_hour')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Trạng thái</label>
                        <select id="status" name="status"
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="AVAILABLE"   {{ old('status', $table->status) === 'AVAILABLE'   ? 'selected' : '' }}>✅ Sẵn sàng</option>
                            <option value="PLAYING"     {{ old('status', $table->status) === 'PLAYING'     ? 'selected' : '' }}>🎱 Đang chơi</option>
                            <option value="RESERVED"    {{ old('status', $table->status) === 'RESERVED'    ? 'selected' : '' }}>📅 Đã đặt trước</option>
                            <option value="MAINTENANCE" {{ old('status', $table->status) === 'MAINTENANCE' ? 'selected' : '' }}>🔧 Bảo trì</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ─── Description ─── --}}
                <div class="mb-5">
                    <label class="form-label">Mô tả thêm <span style="color: var(--text-muted-c);">(tuỳ chọn)</span></label>
                    <textarea id="description"
                              name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="4"
                              placeholder="Ví dụ: Vị trí góc VIP, bàn mới bọc vải nhung xanh...">{{ old('description', $table->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ─── Info Footer ─── --}}
                <div style="background: var(--bg-elevated); border: 1px solid var(--border-light); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; font-size: 0.8rem; color: var(--text-secondary);">
                    <i class="bi bi-clock-history me-1"></i>
                    Tạo lúc <strong>{{ $table->created_at->format('H:i — d/m/Y') }}</strong>
                    &nbsp;|&nbsp;
                    Cập nhật lần cuối <strong>{{ $table->updated_at->format('H:i — d/m/Y') }}</strong>
                </div>

                <div class="divider"></div>

                {{-- ─── Actions ─── --}}
                <div class="d-flex justify-content-end gap-3 pt-3">
                    <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-warning" style="color: #fff">
                        <i class="bi bi-save-fill"></i> Cập nhật thông tin
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
