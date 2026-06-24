{{-- resources/views/components/input.blade.php --}}
@props([
    'name',
    'label'       => null,
    'type'        => 'text',
    'value'       => '',
    'placeholder' => '',
    'required'    => false,
    'error'       => null,
])

<div class="d-flex flex-column gap-1 w-100">
    @if($label)
        <label for="{{ $name }}" class="form-label mb-1">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input type="{{ $type }}"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ $value }}"
           placeholder="{{ $placeholder }}"
           {{ $required ? 'required' : '' }}
           {{ $attributes->merge(['class' => 'form-control ' . ($error ? 'is-invalid' : '')]) }}
           style="height: 40px;">

    @if($error)
        <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            <span>{{ $error }}</span>
        </div>
    @endif
</div>

