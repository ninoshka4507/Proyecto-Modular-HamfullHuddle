<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HamfuHuddle') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Playfair+Display:ital@1&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </head>
    <body class="antialiased font-[Poppins] bg-gradient-to-br from-[#1a103d] to-[#3b1e75] text-white">
        <div class="min-h-screen">
            
            <x-navbar :pageTitle="$attributes->get('pageTitle')" />

            @isset($header)
                <header class="bg-white/5 backdrop-blur-md border-b border-white/10">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-semibold text-xl text-white leading-tight">
                            {{ $header }}
                        </h2>
                    </div>
                </header>
            @endisset

            <main class="{{ $attributes->has('fullWidth') ? '' : 'py-8 px-6' }}">
                {{ $slot }}
            </main>

            <footer class="py-8 text-center text-white/60 text-sm">
                &copy; {{ date('Y') }} HamfuHuddle - Menos búsqueda Más historias.
            </footer>
        </div>
    </body>
</html>