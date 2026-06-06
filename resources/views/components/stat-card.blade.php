{{-- resources/views/components/stat-card.blade.php --}}
@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="card glass-panel border-0 rounded-4 mb-4 text-white position-relative overflow-hidden">
    <div class="card-body p-4 position-relative z-1">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <p class="text-white-50 mb-1" style="font-size: 14px; font-weight: 500; letter-spacing: 0.5px;">{{ $title }}</p>
                <h3 class="mb-0 fw-bold text-white">{{ $value }}</h3>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background-color: rgba(var(--bs-{{ $color }}-rgb), 0.2); color: var(--{{ $color }}); box-shadow: 0 0 15px rgba(var(--bs-{{ $color }}-rgb), 0.3);">
                <i class="bi {{ $icon }} fs-3"></i>
            </div>
        </div>
    </div>
</div>
