<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('viewer/css/pdfjs-viewer.min.css') }}">

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/plexus.js'])
        
        @stack('styles')
        
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
    <body class="font-sans antialiased login-page">
    <canvas id="plexus-bg" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
        <div class="min-h-screen">
            @include('layouts.navigation')

            @if (session('success'))
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-4"
                         x-init="setTimeout(() => show = false, 3000)"
                         class="p-4 text-sm text-blue-800 rounded-lg bg-blue-50 shadow-md border border-blue-200"                         role="alert">
                        <span class="font-medium">Success!</span> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (isset($header))
                <header class="bg-transparent py-6">
                    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">{{ $header }}</div>
                </header>
            @endif

            <main>{{ $slot }}</main>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <script type="module">
            import * as pdfjsLib from '{{ asset('viewer/js/pdf.mjs') }}';
            pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('viewer/js/pdf.worker.mjs') }}';
            await import('{{ asset('viewer/js/pdfjs-viewer.js') }}');
        </script>

        <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

        @stack('scripts')
    </body>
</html>