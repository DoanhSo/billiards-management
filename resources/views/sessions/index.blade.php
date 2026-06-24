{{-- resources/views/sessions/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Quản lý phiên chơi')

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-play-circle me-2"></i>Quản lý phiên chơi</h1>
            <p class="text-muted mb-0">Theo dõi và quản lý các phiên chơi billiards</p>
        </div>
        <a href="{{ route('sessions.start.form') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Bắt đầu phiên mới
        </a>
    </div>

    {{-- Status Filter --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted me-2"><i class="bi bi-funnel"></i> Lọc:</span>
                <a href="{{ route('sessions.index') }}"
                   class="btn btn-sm {{ $status === '' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Tất cả
                </a>
                <a href="{{ route('sessions.index', ['status' => 'PLAYING']) }}"
                   class="btn btn-sm {{ $status === 'PLAYING' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="bi bi-play-fill"></i> Đang chơi
                </a>
                <a href="{{ route('sessions.index', ['status' => 'FINISHED']) }}"
                   class="btn btn-sm {{ $status === 'FINISHED' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <i class="bi bi-stop-fill"></i> Đã kết thúc
                </a>
            </div>
        </div>
    </div>

    {{-- Sessions Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if ($sessions->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Chưa có phiên chơi nào.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Bàn</th>
                                <th>Khách hàng</th>
                                <th>Bắt đầu</th>
                                <th>Kết thúc</th>
                                <th>Tổng giờ</th>
                                <th>Tiền bàn</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessions as $session)
                                <tr>
                                    <td><strong>{{ $session->id }}</strong></td>
                                    <td>
                                        <i class="bi bi-circle-fill text-info me-1" style="font-size: 0.5rem;"></i>
                                        {{ $session->billiardTable->table_number ?? '—' }}
                                        <small class="text-muted d-block">{{ $session->billiardTable->table_type ?? '' }}</small>
                                    </td>
                                    <td>{{ $session->customer->name ?? 'Khách vãng lai' }}</td>
                                    <td>
                                        <small>{{ $session->start_time->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if ($session->end_time)
                                            <small>{{ $session->end_time->format('d/m/Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($session->status === 'PLAYING')
                                            <span class="badge bg-warning text-dark session-timer"
                                                  data-start="{{ $session->start_time->toIso8601String() }}">
                                                đang tính...
                                            </span>
                                        @else
                                            {{ number_format((float) $session->total_hours, 2) }}h
                                        @endif
                                    </td>
                                    <td>
                                        @if ($session->status === 'FINISHED')
                                            <strong>{{ number_format((float) $session->table_price, 0, ',', '.') }} ₫</strong>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($session->status === 'PLAYING')
                                            <span class="badge bg-success"><i class="bi bi-play-fill"></i> Đang chơi</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-stop-fill"></i> Đã kết thúc</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('sessions.show', $session->id) }}"
                                               class="btn btn-outline-info" title="Chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if ($session->status === 'PLAYING')
                                                <form action="{{ route('sessions.end', $session->id) }}" method="POST"
                                                      data-confirm="Bạn có chắc muốn kết thúc phiên chơi này?">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-danger" title="Kết thúc">
                                                        <i class="bi bi-stop-circle"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($session->status === 'FINISHED' && !$session->invoice)
                                                <a href="{{ route('invoices.create', ['session_id' => $session->id]) }}"
                                                   class="btn btn-outline-warning" title="Lập hóa đơn">
                                                    <i class="bi bi-receipt"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="card-footer d-flex justify-content-center">
                    {{ $sessions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Realtime timer cho phiên đang chơi
    function updateTimers() {
        document.querySelectorAll('.session-timer').forEach(function (el) {
            const start = new Date(el.dataset.start);
            const now   = new Date();
            const diff  = Math.floor((now - start) / 1000);

            const hours   = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;

            el.textContent = (hours > 0 ? hours + 'h ' : '') +
                             String(minutes).padStart(2, '0') + 'm ' +
                             String(seconds).padStart(2, '0') + 's';
        });
    }

    updateTimers();
    setInterval(updateTimers, 1000);
</script>
@endpush

