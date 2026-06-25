@extends('layouts.app')

@section('title', 'Chỉnh sửa bàn #' . $table->table_number)

@section('content')
<div class="container-fluid px-0" style="max-width: 760px;">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
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
        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ═══ FORM CARD ═══ --}}
    <x-card>
        <form novalidate action="{{ route('tables.update', $table->id) }}" method="POST" class="d-flex flex-column gap-3">
            @csrf
            @method('PUT')

            {{-- ─── Row 1: Số bàn + Loại bàn ─── --}}
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <x-input name="table_number"
                             label="Số bàn"
                             placeholder="VD: 01, A02, B01..."
                             value="{{ old('table_number', $table->table_number) }}"
                             required="true"
                             error="{{ $errors->first('table_number') }}" />
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="table_type" class="form-label mb-1">
                            Loại bàn <span class="text-danger">*</span>
                        </label>
                        <select id="table_type" name="table_type"
                                class="form-select @error('table_type') is-invalid @enderror" 
                                style="height: 40px;" required>
                            <option value="POOL"    {{ old('table_type', $table->table_type) === 'POOL'    ? 'selected' : '' }}>🎱 Pool (Bida Lỗ)</option>
                            <option value="SNOOKER" {{ old('table_type', $table->table_type) === 'SNOOKER' ? 'selected' : '' }}>🔴 Snooker</option>
                            <option value="CAROM"   {{ old('table_type', $table->table_type) === 'CAROM'   ? 'selected' : '' }}>⚪ Carom (Bida Phăng)</option>
                        </select>
                        @error('table_type')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span>{{ $message }}</span></div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ─── Row 2: Giá/giờ + Trạng thái ─── --}}
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="price_per_hour" class="form-label mb-1">
                            Giá thuê / giờ (VNĐ) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number"
                                   id="price_per_hour"
                                   name="price_per_hour"
                                   class="form-control @error('price_per_hour') is-invalid @enderror"
                                   placeholder="VD: 80000"
                                   value="{{ old('price_per_hour', (int)$table->price_per_hour) }}"
                                   min="0"
                                   step="1000"
                                   style="height: 40px;"
                                   required>
                            <span class="input-group-text text-secondary" style="background: transparent; font-weight: 700; font-size: 0.8rem; height: 40px;">VNĐ</span>
                        </div>
                        @error('price_per_hour')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span>{{ $message }}</span></div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="status" class="form-label mb-1">Trạng thái</label>
                        <select id="status" name="status"
                                class="form-select @error('status') is-invalid @enderror"
                                style="height: 40px;">
                            <option value="AVAILABLE"   {{ old('status', $table->status) === 'AVAILABLE'   ? 'selected' : '' }}>✅ Sẵn sàng</option>
                            <option value="PLAYING"     {{ old('status', $table->status) === 'PLAYING'     ? 'selected' : '' }}>🎱 Đang chơi</option>
                            <option value="RESERVED"    {{ old('status', $table->status) === 'RESERVED'    ? 'selected' : '' }}>📅 Đã đặt trước</option>
                            <option value="MAINTENANCE" {{ old('status', $table->status) === 'MAINTENANCE' ? 'selected' : '' }}>🔧 Bảo trì</option>
                        </select>
                        @error('status')
                            <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span>{{ $message }}</span></div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ─── Description ─── --}}
            <div class="d-flex flex-column gap-1 w-100">
                <label for="description" class="form-label mb-1">Mô tả thêm <span class="text-muted">(tuỳ chọn)</span></label>
                <textarea id="description"
                          name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="4"
                          placeholder="Ví dụ: Vị trí góc VIP, bàn mới bọc vải nhung xanh...">{{ old('description', $table->description) }}</textarea>
                @error('description')
                    <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span>{{ $message }}</span></div>
                @enderror
            </div>

            {{-- ─── Info Footer ─── --}}
            <div class="p-3 rounded-3 d-flex align-items-center gap-2" style="background: var(--bg-hover); font-size: 0.8rem; color: var(--text-secondary); border: 1px solid var(--border);">
                <i class="bi bi-clock-history text-muted"></i>
                <div>
                    Tạo lúc <strong>{{ $table->created_at->format('H:i — d/m/Y') }}</strong>
                    &nbsp;|&nbsp;
                    Cập nhật lần cuối <strong>{{ $table->updated_at->format('H:i — d/m/Y') }}</strong>
                </div>
            </div>

            <hr class="my-3 text-secondary-subtle">

            {{-- ─── Actions ─── --}}
            <div class="d-flex justify-content-end gap-3">
                @php $cancelUrl = route('tables.index'); @endphp
                <x-button type="button" variant="secondary" onclick="window.location.href='{{ $cancelUrl }}'">
                    Hủy bỏ
                </x-button>
                <x-button type="submit" variant="primary" icon="save-fill">
                    Cập nhật thông tin
                </x-button>
            </div>
        </form>
    </x-card>

</div>
@endsection


