{{-- resources/views/components/card.blade.php --}}
<div class="card glass-panel border-0 rounded-4 mb-4 text-white">
    @if(isset($title))
        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
            <h5 class="card-title fw-semibold mb-0 text-white" style="font-size: 18px;">{{ $title }}</h5>
        </div>
    @endif
    <div class="card-body p-4">
        {{ $slot }}
    </div>
</div>
