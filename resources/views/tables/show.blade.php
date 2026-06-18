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

        {{-- ═══ CỘT PHẢI: QUẢN LÝ PHỤ KIỆN ═══ --}}
        <div class="col-12 col-xl-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Phụ kiện của bàn
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                        <i class="bi bi-plus-lg"></i> Thêm phụ kiện
                    </button>
                </div>

                @if($table->equipments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tên phụ kiện</th>
                                <th>Sử dụng tốt</th>
                                <th>Hỏng / Mất</th>
                                <th>Ghi chú</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($table->equipments as $equipment)
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color: var(--text-primary);">{{ $equipment->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success rounded-pill fs-6">{{ $equipment->quantity }}</span>
                                </td>
                                <td>
                                    @if($equipment->broken_quantity > 0)
                                        <span class="badge bg-danger rounded-pill fs-6">{{ $equipment->broken_quantity }}</span>
                                    @else
                                        <span class="text-muted-c">-</span>
                                    @endif
                                </td>
                                <td style="max-width: 200px;" class="text-truncate" title="{{ $equipment->note }}">
                                    {{ $equipment->note ?: '-' }}
                                </td>
                                <td class="text-end">
                                    {{-- Nút Báo hỏng --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#reportBrokenModal{{ $equipment->id }}"
                                            title="Báo hỏng / Báo mất">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </button>
                                    
                                    {{-- Nút Sửa --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary mx-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editEquipmentModal{{ $equipment->id }}"
                                            title="Sửa thông tin">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    {{-- Form Xóa --}}
                                    <form action="{{ route('tables.equipments.destroy', [$table->id, $equipment->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phụ kiện này khỏi bàn?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa phụ kiện">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Sửa --}}
                            <div class="modal fade" id="editEquipmentModal{{ $equipment->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-light);">
                                        <form action="{{ route('tables.equipments.update', [$table->id, $equipment->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-bottom border-secondary">
                                                <h5 class="modal-title" style="color: var(--text-primary);">Sửa phụ kiện: {{ $equipment->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label" style="color: var(--text-secondary);">Tên phụ kiện</label>
                                                    <input type="text" name="name" class="form-control bg-dark text-light border-secondary" value="{{ $equipment->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" style="color: var(--text-secondary);">Số lượng (Sử dụng tốt)</label>
                                                    <input type="number" name="quantity" class="form-control bg-dark text-light border-secondary" min="0" value="{{ $equipment->quantity }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" style="color: var(--text-secondary);">Ghi chú</label>
                                                    <textarea name="note" class="form-control bg-dark text-light border-secondary" rows="2">{{ $equipment->note }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Báo Hỏng/Mất --}}
                            <div class="modal fade" id="reportBrokenModal{{ $equipment->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-light);">
                                        <form action="{{ route('tables.equipments.report', [$table->id, $equipment->id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header border-bottom border-secondary">
                                                <h5 class="modal-title text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Báo hỏng / Báo mất</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p style="color: var(--text-secondary);">Phụ kiện: <strong class="text-light">{{ $equipment->name }}</strong></p>
                                                <p style="color: var(--text-secondary);">Số lượng hiện tại: <strong class="text-success">{{ $equipment->quantity }}</strong></p>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label" style="color: var(--text-secondary);">Số lượng hỏng/mất</label>
                                                    <input type="number" name="broken_amount" class="form-control bg-dark text-light border-secondary" min="1" max="{{ $equipment->quantity }}" value="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" style="color: var(--text-secondary);">Chi tiết tình trạng (Tùy chọn)</label>
                                                    <textarea name="note" class="form-control bg-dark text-light border-secondary" rows="2" placeholder="Ví dụ: Gãy đầu cơ, Mất bi số 8..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-warning">Ghi nhận</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <div class="mb-3" style="font-size: 3rem; color: var(--text-muted-c);">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h5 style="color: var(--text-primary);">Chưa có phụ kiện nào</h5>
                    <p style="color: var(--text-secondary);">Bàn này hiện chưa được gán phụ kiện nào (cơ, lơ, găng tay...).</p>
                    <button type="button" class="btn btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                        <i class="bi bi-plus-lg"></i> Thêm phụ kiện ngay
                    </button>
                </div>
                @endif
            </x-card>
        </div>
    </div>
</div>

{{-- ═══ MODAL THÊM PHỤ KIỆN ═══ --}}
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-light);">
            <form action="{{ route('tables.equipments.store', $table->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title" style="color: var(--text-primary);">Thêm phụ kiện mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--text-secondary);">Tên phụ kiện <span class="text-danger">*</span></label>
                        {{-- Kết hợp vừa chọn (datalist) vừa nhập (input text) --}}
                        <input type="text" name="name" list="equipment-names" class="form-control bg-dark text-light border-secondary" placeholder="Chọn hoặc gõ tên phụ kiện..." required autocomplete="off">
                        <datalist id="equipment-names">
                            <option value="Cơ Bida (Loại thường)"></option>
                            <option value="Cơ Bida (Loại xịn)"></option>
                            <option value="Bộ Bi (Đầy đủ)"></option>
                            <option value="Lơ Bida"></option>
                            <option value="Găng Tay"></option>
                            <option value="Lết Bida"></option>
                            <option value="Bàn chải vệ sinh bàn"></option>
                        </datalist>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--text-secondary);">Số lượng <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control bg-dark text-light border-secondary" min="1" value="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--text-secondary);">Ghi chú</label>
                        <textarea name="note" class="form-control bg-dark text-light border-secondary" rows="2" placeholder="Tình trạng, nhãn hiệu..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Lưu phụ kiện</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
