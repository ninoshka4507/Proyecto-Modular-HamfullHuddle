<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>HamfuHuddle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-favicon />

    <!-- Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Playfair+Display:ital@1&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>

@include('components.navbar')

<section class="hero">

    <div class="hero-text">
        <span class="badge">Menos búsqueda. Más historias.</span>

        <h2 class="hero-title">
            Descubre <span class="italic-highlight">nuevas</span> historias
            que realmente van <span class="purple-highlight">contigo.</span>
        </h2>

        <p class="description">
            Una plataforma que te recomienda contenido según tus gustos y
            preferencias, de manera rápida y personalizada.
        </p>

        <a href="{{ route('register') }}" class="btn-primary">
            Comenzar gratis
            <svg class="arrow-icon" viewBox="0 0 24 24" fill="none">
                <path d="M5 12h14M13 6l6 6-6 6"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
        </a>
    </div>

    <div class="hero-image">
        <img src="{{ asset('images/hamster.png') }}">
    </div>

</section>

<script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>