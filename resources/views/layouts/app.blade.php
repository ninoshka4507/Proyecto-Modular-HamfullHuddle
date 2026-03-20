<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HamfuHuddle') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </head>
    <body class="font-sans antialiased bg-purple-50"> <div class="min-h-screen">
            
            <x-navbar />

            @isset($header)
                <header class="bg-white shadow-sm border-b border-purple-100">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-semibold text-xl text-purple-800 leading-tight">
                            {{ $header }}
                        </h2>
                    </div>
                </header>
            @endisset

            <main class="py-6">
                {{ $slot }}
            </main>

            <footer class="py-8 text-center text-purple-400 text-sm">
                &copy; {{ date('Y') }} HamfuHuddle - Menos búsqueda Más historias.
            </footer>
        </div>
    </body>
</html>