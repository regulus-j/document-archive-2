<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ config('app.name', 'DocTrack') }} — Document Tracking & Archiving</title>
    <link rel="icon" href="{{ asset('images/logo.svg') }}">

    <!-- Fonts — Figtree (design-system typeface) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap (JS components only — modals, tooltips) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (for legacy Ajax helpers) -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Alpine.js cloak -->
    <style>[x-cloak] { display: none !important; }</style>

    <!-- Vite assets (Tailwind + app JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Design-system: slate-50 background provides a subtle neutral canvas
     that lets white cards stand out with better visual hierarchy. -->
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white border-b border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1">
            @isset($slot)
                {{ $slot }}
            @endisset

            @yield('content')
        </main>

        <!-- Sticky Footer -->
        <footer class="border-t border-slate-200/60 bg-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4
                        flex flex-col sm:flex-row items-center justify-between gap-2
                        text-xs text-slate-400">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'DocTrack') }}. All rights reserved.</span>
                <span>Document Tracking &amp; Archiving System</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lazy-load non-critical images by default.
            document.querySelectorAll('img:not([loading])').forEach(function (img) {
                if (!img.closest('header, nav')) {
                    img.setAttribute('loading', 'lazy');
                    img.setAttribute('decoding', 'async');
                }
            });

            // Global mobile table policy:
            // - default: card/stacked rows on small screens
            // - opt-out with data-mobile-table="scroll" for essential wide tables
            document.querySelectorAll('.overflow-x-auto').forEach(function (wrapper) {
                var table = wrapper.querySelector(':scope > table, :scope > .min-w-full');
                if (!table || table.tagName !== 'TABLE') return;

                if (wrapper.dataset.mobileTable === 'scroll') {
                    wrapper.classList.add('mobile-table-scroll');
                    if (!wrapper.previousElementSibling || !wrapper.previousElementSibling.classList.contains('mobile-scroll-indicator')) {
                        var hint = document.createElement('p');
                        hint.className = 'mobile-scroll-indicator';
                        hint.textContent = 'Swipe left/right to view more columns';
                        wrapper.parentNode.insertBefore(hint, wrapper);
                    }
                } else {
                    wrapper.classList.add('mobile-card-table');
                }
            });

            // Prepare mobile card-table labels using table headers.
            document.querySelectorAll('.mobile-card-table table').forEach(function (table) {
                var headers = Array.from(table.querySelectorAll('thead th')).map(function (th) {
                    return (th.textContent || '').trim() || 'Field';
                });
                if (!headers.length) return;

                table.querySelectorAll('tbody tr').forEach(function (row) {
                    Array.from(row.children).forEach(function (cell, index) {
                        if (cell && cell.tagName === 'TD' && !cell.hasAttribute('data-label')) {
                            cell.setAttribute('data-label', headers[index] || 'Field');
                        }
                    });
                });
            });
        });
    </script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modals = document.querySelectorAll('.modal');
            if(modals.length > 0) {
                modals.forEach(function(modal) {
                    new bootstrap.Modal(modal);
                });
            }
        });
    </script>

    {{-- Global: Prevent button spam / duplicate form submissions --}}
    <script>
    (function() {
        var SPINNER = '<svg class="animate-spin h-4 w-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
        var RESET_MS = 15000; // safety re-enable after 15s if page hasn't navigated

        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            var btns = form.querySelectorAll('[type="submit"]');
            btns.forEach(function(btn) {
                if (btn.disabled) {
                    // Already submitted — prevent spam
                    e.preventDefault();
                    return;
                }
                btn.disabled = true;
                btn.dataset.originalHtml = btn.innerHTML;
                // Show spinner + text based on button context
                var label = btn.textContent.trim();
                btn.innerHTML = SPINNER + (label || 'Processing...');

                // Safety net: re-enable after RESET_MS in case of network error
                setTimeout(function() {
                    if (btn.disabled) {
                        btn.disabled = false;
                        btn.innerHTML = btn.dataset.originalHtml || btn.innerHTML;
                    }
                }, RESET_MS);
            });
        }, true);

        // Re-enable buttons if user navigates back (bfcache)
        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                document.querySelectorAll('[type="submit"][disabled]').forEach(function(btn) {
                    btn.disabled = false;
                    if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
                });
            }
        });
    })();
    </script>

    {{-- DocBot AI Chatbot Widget --}}
    @auth
        <x-chatbot-widget />
    @endauth

    @auth
    {{-- Notification polling: check for new notifications every 60 seconds --}}
    <script>
    (function() {
        var badge = document.getElementById('notification-badge');
        var lastCount = badge ? (parseInt(badge.textContent.trim(), 10) || 0) : 0;
        var pollInterval = null;

        function pollNotifications() {
            fetch('{{ route('notifications.unread-count') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            })
            .then(function(r) {
                // 401 means the session expired — stop polling to avoid console noise
                if (r.status === 401) {
                    if (pollInterval) {
                        clearInterval(pollInterval);
                        pollInterval = null;
                    }
                    return null;
                }
                return r.ok ? r.json() : null;
            })
            .then(function(data) {
                if (!data) return;
                var count = data.count || 0;
                if (!badge) return;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
                lastCount = count;
            })
            .catch(function() {});
        }

        // Poll every 60 seconds
        pollInterval = setInterval(pollNotifications, 60000);
    })();
    </script>
    @endauth
</body>

</html>
