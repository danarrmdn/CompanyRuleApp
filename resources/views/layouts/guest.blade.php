<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/plexus.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased login-page">
    <canvas id="plexus-bg" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="flex justify-center mb-6">
                    <a href="/">
                        <img src="{{ asset('images/side-name-logo-nobg.png') }}" alt="NSI Logo" class="w-auto h-20">
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
