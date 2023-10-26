<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
    </head>
    <body class="bg-white">
        <div class="min-h-screen flex flex-col items-center justify-start bg-gray-100 bg-opacity-50 py-16">
            <div class="mb-8 text-center">
                <a href="{{ config('app.auth_url') }}">
                    <img src="{{ asset('assets/images/easeweldo-logo.png') }}" alt="Easeweldo Logo" class="h-8 w-auto mx-auto">
                </a>
            </div>
            @yield('content')
        </div>
    </body>
    <footer class="absolute bottom-0 w-full">
        <div class="w-full mx-auto max-w-screen-2xl p-4 text-center">
            <span class="text-sm text-gray-800 sm:text-center">© 2023 <a href="{{ url('/') }}" class="hover:underline">Easeweldo™</a>. All Rights Reserved.
            </span>
        </div>
    </footer>
</html>
