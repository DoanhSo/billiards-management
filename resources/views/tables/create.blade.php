@extends('layouts.app')

@section('title', 'Thêm bàn mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 760px;">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-plus-circle-fill me-2" style="color: var(--primary)"></i>Thêm Bàn chơi Mới</h1>
            <p class="page-subtitle">Tạo bàn chơi và đưa vào hoạt động trong hệ thống</p>
        </div>
        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ═══ FORM CARD ═══ --}}
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf

                {{-- ─── Row 1: Số bàn + Loại bàn ─── --}}
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Số bàn <span style="color: var(--danger)">*</span></label>
                        <input type="text"
                               id="table_number"
                               name="table_number"
                               class="form-control @error('table_number') is-invalid @enderror"
                               placeholder="VD: 01, A02, B01..."
                               value="{{ old('table_number') }}"
                               autofocus
                               required>
                        @error('table_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Loại bàn <span style="color: var(--danger)">*</span></label>
                        <select id="table_type" name="table_type"
                                class="form-select @error('table_type') is-invalid @enderror" required>
                            <option value="" disabled {{ old('table_type') ? '' : 'selected' }}>— Chọn loại bàn —</option>
                            <option value="POOL"    {{ old('table_type') === 'POOL'    ? 'selected' : '' }}>🎱 Pool (Bida Lỗ)</option>
                            <option value="SNOOKER" {{ old('table_type') === 'SNOOKER' ? 'selected' : '' }}>🔴 Snooker</option>
                            <option value="CAROM"   {{ old('table_type') === 'CAROM'   ? 'selected' : '' }}>⚪ Carom (Bida Phăng)</option>
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
                                   value="{{ old('price_per_hour') }}"
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
                        <label class="form-label">Trạng thái khởi tạo</label>
                        <select id="status" name="status"
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="AVAILABLE"   {{ old('status', 'AVAILABLE') === 'AVAILABLE'   ? 'selected' : '' }}>✅ Sẵn sàng (Available)</option>
                            <option value="MAINTENANCE" {{ old('status') === 'MAINTENANCE' ? 'selected' : '' }}>🔧 Bảo trì (Maintenance)</option>
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
                              placeholder="Ví dụ: Vị trí góc VIP, bàn mới bọc vải nhung xanh, khu vực yên tĩnh...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="divider"></div>

                {{-- ─── Actions ─── --}}
                <div class="d-flex justify-content-end gap-3 pt-3">
                    <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save-fill"></i> Lưu bàn chơi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
