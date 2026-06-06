@extends('layouts.app')

@section('title', 'Thêm bàn mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Thêm Bàn chơi Mới</h1>
            <p class="text-muted mb-0">Tạo bàn chơi mới để đưa vào hoạt động trong hệ thống quản lý.</p>
        </div>
        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf

                <!-- Row 1: Table Number & Type -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="table_number" class="form-label fw-semibold text-muted">Số bàn <span class="text-danger">*</span></label>
                        <input type="text" id="table_number" name="table_number" class="form-control @error('table_number') is-invalid @enderror" placeholder="VD: 01, A02..." value="{{ old('table_number') }}" required>
                        @error('table_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="table_type" class="form-label fw-semibold text-muted">Loại bàn <span class="text-danger">*</span></label>
                        <select id="table_type" name="table_type" class="form-select @error('table_type') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Chọn loại bàn --</option>
                            <option value="POOL" {{ old('table_type') === 'POOL' ? 'selected' : '' }}>Pool (Bida Lỗ)</option>
                            <option value="SNOOKER" {{ old('table_type') === 'SNOOKER' ? 'selected' : '' }}>Snooker</option>
                            <option value="CAROM" {{ old('table_type') === 'CAROM' ? 'selected' : '' }}>Carom (Bida Phăng)</option>
                        </select>
                        @error('table_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Price & Status -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="price_per_hour" class="form-label fw-semibold text-muted">Giá mỗi giờ (VNĐ) <span class="text-danger">*</span></label>
                        <div class="input-group @error('price_per_hour') is-invalid @enderror">
                            <input type="number" id="price_per_hour" name="price_per_hour" class="form-control @error('price_per_hour') is-invalid @enderror" placeholder="VD: 80000" value="{{ old('price_per_hour') }}" min="0" step="1000" required>
                            <span class="input-group-text bg-light">VNĐ</span>
                        </div>
                        @error('price_per_hour')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold text-muted">Trạng thái khởi tạo</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="AVAILABLE" {{ old('status', 'AVAILABLE') === 'AVAILABLE' ? 'selected' : '' }}>Sẵn sàng (Available)</option>
                            <option value="MAINTENANCE" {{ old('status') === 'MAINTENANCE' ? 'selected' : '' }}>Bảo trì (Maintenance)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 3: Description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold text-muted">Mô tả thêm</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Nhập ghi chú thêm về bàn (Ví dụ: Vị trí góc VIP, bàn mới bọc vải...)">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Actions Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tables.index') }}" class="btn btn-light border">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save-fill"></i> Lưu bàn chơi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
