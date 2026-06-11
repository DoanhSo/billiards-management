{{-- resources/views/components/button.blade.php --}}
{{-- variant: primary | secondary | danger --}}
@props([
    'type' => 'submit',
    'variant' => 'primary',
    'icon' => null,
])

@php
    $class = match($variant) {
        'primary' => 'btn btn-primary d-inline-flex align-items-center justify-content-center',
        'secondary' => 'btn btn-outline-secondary d-inline-flex align-items-center justify-content-center bg-white border-secondary-subtle text-secondary',
        'danger' => 'btn btn-danger d-inline-flex align-items-center justify-content-center',
        default => 'btn btn-primary d-inline-flex align-items-center justify-content-center',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }} style="height: 40px;">
    @if($icon)
        <i class="bi bi-{{ $icon }} me-1"></i>
    @endif
    {{ $slot }}
</button>
