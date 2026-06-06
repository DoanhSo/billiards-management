@extends('layouts.app')

@section('title', 'Quản lý bàn')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quản lý Bàn chơi</h1>
            <p class="text-muted mb-0">Quản lý thông tin bàn và cập nhật trạng thái hoạt động theo thời gian thực.</p>
        </div>
        <a href="{{ route('tables.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill"></i> Thêm bàn mới
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase text-muted fw-bold mb-1">Bàn trống</div>
                            <div class="h3 mb-0 fw-bold text-success">{{ $summary['AVAILABLE'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-circle bg-success-subtle p-3 text-success">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase text-muted fw-bold mb-1">Đang chơi</div>
                            <div class="h3 mb-0 fw-bold text-primary">{{ $summary['PLAYING'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-circle bg-primary-subtle p-3 text-primary">
                            <i class="bi bi-play-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase text-muted fw-bold mb-1">Đã đặt trước</div>
                            <div class="h3 mb-0 fw-bold text-warning">{{ $summary['RESERVED'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-circle bg-warning-subtle p-3 text-warning">
                            <i class="bi bi-calendar-event fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-secondary">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase text-muted fw-bold mb-1">Đang bảo trì</div>
                            <div class="h3 mb-0 fw-bold text-secondary">{{ $summary['MAINTENANCE'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-circle bg-secondary-subtle p-3 text-secondary">
                            <i class="bi bi-tools fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Views -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('tables.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label for="search" class="form-label text-xs fw-semibold text-muted mb-1">Tìm kiếm bàn</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search" name="search" class="form-control bg-light border-0" placeholder="Nhập số bàn, loại bàn..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label text-xs fw-semibold text-muted mb-1">Trạng thái</label>
                    <select id="status" name="status" class="form-select bg-light border-0">
                        <option value="">Tất cả trạng thái</option>
                        <option value="AVAILABLE" {{ $status === 'AVAILABLE' ? 'selected' : '' }}>Sẵn sàng</option>
                        <option value="PLAYING" {{ $status === 'PLAYING' ? 'selected' : '' }}>Đang chơi</option>
                        <option value="RESERVED" {{ $status === 'RESERVED' ? 'selected' : '' }}>Đã đặt trước</option>
                        <option value="MAINTENANCE" {{ $status === 'MAINTENANCE' ? 'selected' : '' }}>Bảo trì</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc dữ liệu</button>
                    @if($search || $status)
                        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary w-50">Xóa lọc</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- List Views (Grid Card Style) -->
    <div class="row g-3 mb-4">
        @forelse($tables as $index => $table)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden card-hover">
                    <!-- Color ribbon for status -->
                    @php
                        $colorClass = 'secondary';
                        $statusText = 'Bảo trì';
                        if ($table->status === 'AVAILABLE') {
                            $colorClass = 'success';
                            $statusText = 'Sẵn sàng';
                        } elseif ($table->status === 'PLAYING') {
                            $colorClass = 'primary';
                            $statusText = 'Đang chơi';
                        } elseif ($table->status === 'RESERVED') {
                            $colorClass = 'warning';
                            $statusText = 'Đã đặt trước';
                        }
                    @endphp
                    <div class="bg-{{ $colorClass }} position-absolute top-0 start-0 w-100" style="height: 5px;"></div>

                    <div class="card-body pt-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Bàn {{ $table->table_number }}</h5>
                                <span class="badge bg-light text-dark border">{{ $table->table_type }}</span>
                            </div>
                            <span class="badge bg-{{ $colorClass }}-subtle text-{{ $colorClass }} py-1.5 px-3 fs-7">
                                {{ $statusText }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <span class="text-xs text-muted d-block">Giá theo giờ:</span>
                            <span class="fw-bold fs-5 text-dark">{{ number_format($table->price_per_hour, 0, ',', '.') }} VNĐ</span>
                        </div>

                        @if($table->description)
                            <p class="text-muted text-xs mb-3 text-truncate-2" title="{{ $table->description }}">
                                {{ $table->description }}
                            </p>
                        @else
                            <p class="text-muted text-xs mb-3 italic">Không có mô tả.</p>
                        @endif

                        <hr class="my-3 text-muted opacity-25">

                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <!-- Quick Status Update -->
                            <div class="dropdown flex-grow-1">
                                <button class="btn btn-sm btn-light border w-100 d-flex justify-content-between align-items-center text-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span>Trạng thái</span>
                                    <i class="bi bi-chevron-down ms-1"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 text-xs">
                                    <li>
                                        <form action="{{ route('tables.update-status', $table->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="AVAILABLE">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-success {{ $table->status === 'AVAILABLE' ? 'active' : '' }}">
                                                <i class="bi bi-check-circle"></i> Sẵn sàng
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('tables.update-status', $table->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="PLAYING">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-primary {{ $table->status === 'PLAYING' ? 'active' : '' }}">
                                                <i class="bi bi-play-circle"></i> Đang chơi
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('tables.update-status', $table->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="RESERVED">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-warning {{ $table->status === 'RESERVED' ? 'active' : '' }}">
                                                <i class="bi bi-calendar-event"></i> Đã đặt trước
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('tables.update-status', $table->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="MAINTENANCE">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-secondary {{ $table->status === 'MAINTENANCE' ? 'active' : '' }}">
                                                <i class="bi bi-tools"></i> Bảo trì
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-1">
                                <a href="{{ route('tables.edit', $table->id) }}" class="btn btn-sm btn-outline-warning border d-flex align-items-center" title="Sửa thông tin">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($table->status === 'AVAILABLE')
                                    <form action="{{ route('tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bàn này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border d-flex align-items-center" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary border d-flex align-items-center disabled" title="Không thể xóa khi bàn không trống" disabled>
                                        <i class="bi bi-trash text-muted opacity-50"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm py-5">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-circle fs-1 text-muted d-block mb-3"></i>
                        <h5 class="fw-semibold mb-1">Không tìm thấy bàn chơi</h5>
                        <p class="text-muted mb-0">Thử thay đổi từ khóa hoặc bộ lọc của bạn.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end">
        {{ $tables->links() }}
    </div>
</div>

<style>
    .text-xs {
        font-size: 0.75rem;
    }
    .fs-7 {
        font-size: 0.85rem;
    }
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
