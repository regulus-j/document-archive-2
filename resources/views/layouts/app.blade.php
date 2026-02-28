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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

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
            <header class="bg-white border-b border-slate-200/80 shadow-card">
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

    {{-- DocBot AI Chatbot Widget --}}
    @auth
        <x-chatbot-widget />
    @endauth
</body>

</html>
