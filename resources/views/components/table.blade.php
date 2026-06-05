{{-- resources/views/components/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover table-borderless align-middle mb-0 text-white">
        @if(isset($thead))
            <thead style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--glass-border);">
                {{ $thead }}
            </thead>
        @endif
        <tbody style="font-size: 14px; color: #cbd5e1;">
            {{ $slot }}
        </tbody>
    </table>
</div>
