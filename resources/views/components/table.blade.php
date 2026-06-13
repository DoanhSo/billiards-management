{{-- resources/views/components/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        @if(isset($thead))
            <thead>
                {{ $thead }}
            </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

