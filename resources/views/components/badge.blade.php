{{-- resources/views/components/badge.blade.php --}}
@props(['type' => 'primary'])

@php
    $styles = [
        'success'   => 'badge-success',
        'warning'   => 'badge-warning',
        'danger'    => 'badge-danger',
        'primary'   => 'badge-primary',
        'secondary' => 'badge-secondary',
    ];

    $cssClass = $styles[$type] ?? $styles['primary'];
@endphp

<span class="badge rounded-pill badge-custom {{ $cssClass }}">
    {{ $slot }}
</span>
