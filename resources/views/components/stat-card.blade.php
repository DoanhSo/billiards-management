{{-- resources/views/components/stat-card.blade.php --}}
@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="stat-card stat-{{ $color }} mb-0">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <p class="stat-label mb-1">{{ $title }}</p>
            <h3 class="stat-value mb-0">{{ $value }}</h3>
        </div>
        <div class="stat-icon icon-{{ $color }}">
            <i class="bi bi-{{ $icon }}"></i>
        </div>
    </div>
</div>

