{{-- resources/views/components/input.blade.php --}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'error' => null,
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
        <div class="invalid-feedback d-block mt-1" style="color: var(--danger);">
            {{ $error }}
        </div>
    @endif
</div>
