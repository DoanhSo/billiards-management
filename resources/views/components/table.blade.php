{{-- resources/views/components/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        @if(isset($thead))
            <thead style="font-size: 13px; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border);">
                {{ $thead }}
            </thead>
        @endif
        <tbody style="font-size: 14px;">
            {{ $slot }}
        </tbody>
    </table>
</div>

