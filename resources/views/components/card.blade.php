{{-- resources/views/components/card.blade.php --}}
<div class="card mb-4">
    @if(isset($title))
        <div class="card-header border-bottom pb-3">
            <h5 class="card-title fw-bold mb-0">{{ $title }}</h5>
        </div>
    @endif
    <div class="card-body {{ $bodyClass ?? '' }}">
        {{ $slot }}
    </div>
</div>

