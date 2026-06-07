{{-- resources/views/components/badge.blade.php --}}
@props(['type' => 'primary'])

@php
    $bgColors = [
        'success' => 'rgba(52, 211, 153, 0.2)',
        'warning' => 'rgba(251, 191, 36, 0.2)',
        'danger' => 'rgba(248, 113, 113, 0.2)',
        'primary' => 'rgba(102, 126, 234, 0.2)',
        'secondary' => 'rgba(148, 163, 184, 0.2)',
    ];
    $textColors = [
        'success' => '#6ee7b7',
        'warning' => '#fcd34d',
        'danger' => '#fca5a5',
        'primary' => '#a5b4fc',
        'secondary' => '#cbd5e1',
    ];
    
    $bgColor = $bgColors[$type] ?? $bgColors['primary'];
    $textColor = $textColors[$type] ?? $textColors['primary'];
@endphp

<span class="badge rounded-pill" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; border: 1px solid {{ $textColor }}; font-weight: 500; padding: 0.5em 0.8em; letter-spacing: 0.5px;">
    {{ $slot }}
</span>
