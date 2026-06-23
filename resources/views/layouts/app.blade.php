{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Billiards Management')</title>
    {{-- Google Fonts - Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Vite CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body>
    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Main Content --}}
    <main class="main-content">
        {{-- Topbar --}}
        @include('layouts.topbar')

        <div class="container-fluid p-4">
            {{-- Flash Messages --}}
            @include('layouts.flash')

            {{-- Page Content --}}
            @yield('content')
        </div>
    </main>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Sidebar Toggle Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                if (sidebar) sidebar.classList.toggle('show');
                if (overlay) overlay.classList.toggle('show');
            }

            if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
            if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);
        });

        // ==========================================
        // Live Search (AJAX)
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.ajax-search-form');
            if (forms.length === 0) return;

            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, select');
                let timeout = null;

                const submitForm = () => {
                    const url = new URL(form.action);
                    const formData = new FormData(form);
                    const params = new URLSearchParams();

                    for (const [key, value] of formData.entries()) {
                        if (value) params.append(key, value);
                    }
                    
                    url.search = params.toString();

                    // Update URL without reload
                    window.history.pushState({}, '', url);

                    // Fetch new data
                    const container = document.getElementById('searchable-content');
                    if (!container) return;

                    // Add loading state
                    container.style.opacity = '0.5';
                    container.style.pointerEvents = 'none';

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('searchable-content');

                        if (newContent) {
                            container.innerHTML = newContent.innerHTML;
                        }
                    })
                    .catch(error => console.error('Error fetching search results:', error))
                    .finally(() => {
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                    });
                };

                inputs.forEach(input => {
                    input.addEventListener(input.tagName === 'SELECT' ? 'change' : 'input', () => {
                        clearTimeout(timeout);
                        timeout = setTimeout(submitForm, 400); // 400ms debounce
                    });
                });

                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    submitForm();
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
