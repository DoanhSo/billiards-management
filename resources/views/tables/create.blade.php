@extends('layouts.app')

@section('title', 'Thêm bàn mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 760px;">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-plus-circle-fill me-2" style="color: var(--primary)"></i>Thêm Bàn chơi Mới</h1>
            <p class="page-subtitle">Tạo bàn chơi và đưa vào hoạt động trong hệ thống</p>
        </div>
        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ═══ FORM CARD ═══ --}}
    <x-card>
        <form action="{{ route('tables.store') }}" method="POST" class="d-flex flex-column gap-3">
            @csrf

            {{-- ─── Row 1: Số bàn + Loại bàn ─── --}}
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <x-input name="table_number"
                             label="Số bàn"
                             placeholder="VD: 01, A02, B01..."
                             value="{{ old('table_number') }}"
                             required="true"
                             error="{{ $errors->first('table_number') }}"
                             autofocus />
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="table_type" class="form-label mb-1">
                            Loại bàn <span class="text-danger">*</span>
                        </label>
                        <select id="table_type" name="table_type"
                                class="form-select @error('table_type') is-invalid @enderror" 
                                style="height: 40px;" required>
                            <option value="" disabled {{ old('table_type') ? '' : 'selected' }}>— Chọn loại bàn —</option>
                            <option value="POOL"    {{ old('table_type') === 'POOL'    ? 'selected' : '' }}>🎱 Pool (Bida Lỗ)</option>
                            <option value="SNOOKER" {{ old('table_type') === 'SNOOKER' ? 'selected' : '' }}>🔴 Snooker</option>
                            <option value="CAROM"   {{ old('table_type') === 'CAROM'   ? 'selected' : '' }}>⚪ Carom (Bida Phăng)</option>
                        </select>
                        @error('table_type')
                            <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
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
                                   value="{{ old('price_per_hour') }}"
                                   min="0"
                                   step="1000"
                                   style="height: 40px;"
                                   required>
                            <span class="input-group-text bg-light text-secondary" style="font-weight: 700; font-size: 0.8rem; height: 40px;">VNĐ</span>
                        </div>
                        @error('price_per_hour')
                            <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-1 w-100">
                        <label for="status" class="form-label mb-1">Trạng thái khởi tạo</label>
                        <select id="status" name="status"
                                class="form-select @error('status') is-invalid @enderror"
                                style="height: 40px;">
                            <option value="AVAILABLE"   {{ old('status', 'AVAILABLE') === 'AVAILABLE'   ? 'selected' : '' }}>✅ Sẵn sàng (Available)</option>
                            <option value="MAINTENANCE" {{ old('status') === 'MAINTENANCE' ? 'selected' : '' }}>🔧 Bảo trì (Maintenance)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
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
                          placeholder="Ví dụ: Vị trí góc VIP, bàn mới bọc vải nhung xanh, khu vực yên tĩnh...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">{{ $message }}</div>
                @enderror
            </div>

            <hr class="my-3 text-secondary-subtle">

            {{-- ─── Actions ─── --}}
            <div class="d-flex justify-content-end gap-3">
                <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('tables.index') }}'">
                    Hủy bỏ
                </x-button>
                <x-button type="submit" variant="primary" icon="save-fill">
                    Lưu bàn chơi
                </x-button>
            </div>
        </form>
    </x-card>

</div>
@endsection
