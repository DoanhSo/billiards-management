{{-- resources/views/components/card.blade.php --}}
<<<<<<< HEAD
<div class="card border-0 rounded-3 mb-4 shadow-sm" style="background: var(--bg-surface); border-radius: 12px !important; padding: 24px;">
    @if(isset($title))
        <div class="card-header bg-transparent border-bottom-0 pt-0 pb-3 px-0">
            <h5 class="card-title fw-bold mb-0" style="font-size: 20px; font-weight: 600;">{{ $title }}</h5>
=======
<div class="card mb-4">
    @if(isset($title))
        <div class="card-header border-bottom pb-3">
            <h5 class="card-title fw-bold mb-0">{{ $title }}</h5>
>>>>>>> ca8cca2ef9e9231f3855d2b7a26e3e5fed5164a1
        </div>
    @endif
    <div class="card-body {{ $bodyClass ?? '' }}">
        {{ $slot }}
    </div>
</div>

