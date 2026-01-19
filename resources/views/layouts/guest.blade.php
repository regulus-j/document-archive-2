<!-- guest.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
   

    <title>DocTrack - Document Tracking & Archiving System</title>
    <link rel="icon" href="{{ asset('images/logo.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex">
        <!-- Left side - Blue background with welcome message -->
        <div
            class="hidden md:flex md:w-1/2 bg-blue-500 text-white flex-col justify-center items-center p-12 relative overflow-hidden">
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-blue-500 opacity-90"></div>
                <div class="absolute inset-0 bg-[url('/images/grid-pattern.png')] bg-cover bg-center opacity-20"></div>
                <!-- Decorative circles -->
                <div class="absolute top-20 left-20 w-32 h-32 rounded-full border border-blue-300 opacity-30"></div>
                <div class="absolute bottom-40 right-10 w-24 h-24 rounded-full border border-blue-300 opacity-30"></div>
                <div class="absolute top-1/2 left-10 w-16 h-16 rounded-full border border-blue-300 opacity-30"></div>
                <!-- Decorative dots -->
                <div class="absolute top-1/4 right-1/4 w-2 h-2 rounded-full bg-blue-300 opacity-70"></div>
                <div class="absolute bottom-1/3 left-1/3 w-2 h-2 rounded-full bg-blue-300 opacity-70"></div>
                <div class="absolute top-2/3 right-1/3 w-2 h-2 rounded-full bg-blue-300 opacity-70"></div>
            </div>

            <div class="relative z-10 max-w-md text-center">
                <div class="mb-20">
                    <p class="text-sm uppercase tracking-wider mb-2">DOCTRACK</p>
                </div>

                <p class="text-lg mb-2">Nice to see you</p>
                <h1 class="text-5xl font-bold mb-6">Welcome to     Document Tracking</h1>
                <div class="w-16 h-1 bg-white mx-auto mb-6"></div>
                <p class="text-sm opacity-80 mb-8">
                    Access your secure document repository with ease. DocTrack provides efficient storage,
                    organization, and retrieval of all your important documents. Your digital archive is just a login
                    away.
                </p>
            </div>
        </div>

        <!-- Right side - Login form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>