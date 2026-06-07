{{-- resources/views/components/card.blade.php --}}
<div class="card border-0 rounded-3 mb-4 shadow-sm" style="background: white; border-radius: 12px !important; padding: 24px;">
    @if(isset($title))
        <div class="card-header bg-transparent border-bottom-0 pt-0 pb-3 px-0">
            <h5 class="card-title fw-bold mb-0 text-dark" style="font-size: 20px; font-weight: 600;">{{ $title }}</h5>
        </div>
    @endif
    <div class="card-body p-0">
        {{ $slot }}
    </div>
</div>

