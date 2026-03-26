<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - HamfuHuddle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-favicon />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a103d 0%, #3b1e75 100%);
            min-height: 100vh;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    @include('components.navbar')

    <!-- CONTENIDO -->
    <div class="min-h-[calc(100vh-64px)] flex items-center justify-center 
        bg-gradient-to-br from-[#1a0f3c] via-[#2a1760] to-[#140a2e] px-6">

        <div class="w-full max-w-6xl 
            bg-[#2a1e5c]/80 backdrop-blur-xl 
            rounded-3xl shadow-2xl flex overflow-hidden">

            <!-- LEFT -->
            <div class="w-full md:w-1/2 p-12 text-white flex flex-col justify-center">

                <h2 class="text-sm text-purple-300 mb-2">HamfuHuddle</h2>

                <h1 class="text-4xl font-bold mb-6 leading-tight">
                    Recuperar <br> contraseña
                </h1>

                <p class="text-purple-200 mb-8 max-w-sm">
                    Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
                </p>

                @if (session('status'))
                    <div class="mb-4 text-green-400 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm mb-2">Correo electrónico</label>
                        <input 
                            type="email" 
                            name="email" 
                            required
                            class="w-full px-5 py-4 rounded-xl 
                            bg-[#3b2f70] text-white placeholder-purple-300
                            focus:ring-2 focus:ring-purple-400 outline-none"
                            placeholder="correo@ejemplo.com"
                        >
                    </div>

                    @error('email')
                        <div class="text-red-400 text-sm">
                            {{ $message }}
                        </div>
                    @enderror

                    <button class="btn-primary">
                        Enviar enlace
                    </button>
                </form>

                <p class="text-sm text-purple-300 mt-6">
                    ¿Ya recordaste tu contraseña?
                    <a href="{{ route('login') }}" class="text-purple-400 hover:underline">
                        Iniciar sesión
                    </a>
                </p>
            </div>

            <!-- RIGHT -->
            <div class="hidden md:flex md:w-1/2 
                items-center justify-center 
                bg-[#241a4d] relative">

                <div class="absolute w-96 h-96 bg-purple-600/30 rounded-full blur-3xl"></div>

                <img 
                    src="{{ asset('images/hamster.png') }}" 
                    class="w-[380px] drop-shadow-2xl animate-[float_4s_ease-in-out_infinite]"
                >
            </div>

        </div>
    </div>

<style>
@keyframes float {
    0% { transform: translateY(0px);}
    50% { transform: translateY(-12px);}
    100% { transform: translateY(0px);}
}
</style>

</body>
</html>