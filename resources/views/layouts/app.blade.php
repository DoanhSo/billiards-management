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

    {{-- ═══ GLOBAL CONFIRM MODAL ═══ --}}
    <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
            <div class="modal-content" style="background: var(--bg-surface); border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3" style="width: 64px; height: 64px; border-radius: 50%; background: rgba(220,38,38,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="bi bi-exclamation-triangle-fill" id="confirmModalIcon" style="font-size: 1.75rem; color: var(--danger);"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.1rem;" id="confirmModalTitle">Xác nhận</h5>
                    <p class="text-secondary mb-1" style="font-size: 0.9rem;" id="confirmModalText">Bạn có chắc chắn thực hiện hành động này?</p>
                    <p class="text-muted mb-4" style="font-size: 0.8rem;" id="confirmModalSubtext">Hành động này không thể hoàn tác.</p>
    
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="height: 40px; min-width: 100px;">
                            <i class="bi bi-x-lg me-1"></i> Hủy
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmModalSubmitBtn" style="height: 40px; min-width: 120px;">
                            <i class="bi bi-check-lg me-1"></i> Đồng ý
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // ==========================================
        // Global Confirmation Handler
        // ==========================================
        let formToSubmit = null;
        let elementToClick = null;

        document.addEventListener('submit', function(e) {
            if (e.target && e.target.hasAttribute('data-confirm')) {
                e.preventDefault();
                formToSubmit = e.target;
                
                document.getElementById('confirmModalText').innerHTML = formToSubmit.getAttribute('data-confirm');
                
                const modal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
                modal.show();
            }
        });

        document.addEventListener('click', function(e) {
            const el = e.target.closest('[data-confirm-click]');
            if (el) {
                e.preventDefault();
                elementToClick = el;
                
                document.getElementById('confirmModalText').innerHTML = el.getAttribute('data-confirm-click');
                
                const modal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
                modal.show();
            }
        });

        document.getElementById('confirmModalSubmitBtn').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.removeAttribute('data-confirm'); 
                formToSubmit.submit();
                formToSubmit = null;
            } else if (elementToClick) {
                elementToClick.removeAttribute('data-confirm-click');
                elementToClick.click();
                elementToClick = null;
            }
            const modalEl = document.getElementById('globalConfirmModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    </script>
    @stack('scripts')
</body>
</html>

