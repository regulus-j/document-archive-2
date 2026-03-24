<!-- guest.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DocTrack - Document Tracking & Archiving System</title>
    <link rel="icon" href="{{ asset('images/logo.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="font-sans text-slate-900 antialiased bg-slate-50">
    <div class="min-h-screen flex">
        <!-- Left side - Indigo brand panel -->
        <div class="hidden md:flex md:w-1/2 text-white flex-col justify-center items-center p-12 relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-indigo-500">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute -top-20 -left-16 w-64 h-64 rounded-full border border-white/30"></div>
                <div class="absolute bottom-12 right-12 w-40 h-40 rounded-full border border-white/25"></div>
                <div class="absolute top-1/2 left-10 w-24 h-24 rounded-full border border-white/25"></div>
            </div>

            <div class="relative z-10 max-w-md text-center space-y-4">
                <p class="text-xs uppercase tracking-[0.3em] text-white/80">DocTrack</p>
                <h1 class="text-4xl font-semibold leading-tight">Document Tracking &amp; Archiving</h1>
                <p class="text-base text-white/90">
                    Securely store, route, and retrieve documents with clear visibility across your organization.
                </p>
                <div class="inline-flex items-center gap-2 text-sm text-white/80">
                    <span class="inline-block h-1 w-8 rounded-full bg-white/70"></span>
                    Built for compliance and clarity
                </div>
            </div>
        </div>

        <!-- Right side - Auth form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                <div class="ds-card p-6 sm:p-8">
                    <x-success-message/>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
