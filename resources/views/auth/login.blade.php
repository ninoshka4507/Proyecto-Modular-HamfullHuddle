<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - HamfuHuddle</title>
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

@include('components.navbar')

<div class="flex items-center justify-center min-h-[calc(100vh-80px)] px-6">

    <!-- CONTENEDOR GRANDE -->
    <div class="w-full max-w-6xl bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10 flex items-center justify-between gap-10 shadow-2xl">

        <!-- FORM -->
        <div class="w-full max-w-md bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/10">

            <p class="text-sm text-[#9F7CFF] mb-2">HamfuHuddle</p>

            <h2 class="text-4xl font-bold text-white mb-6">
                Iniciar Sesión
            </h2>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-300">Correo electrónico</label>
                    <input type="email" name="email"
                        class="w-full mt-1 px-4 py-3 rounded-lg bg-white/80 text-black placeholder-gray-400 focus:outline-none"
                        placeholder="correo@ejemplo.com">
                </div>

                <!-- Password -->
                <div>
                    <label class="text-sm text-gray-300">Contraseña</label>
                    <input type="password" name="password"
                        class="w-full mt-1 px-4 py-3 rounded-lg bg-white/80 text-black placeholder-gray-400 focus:outline-none"
                        placeholder="••••••••">
                </div>

                <!-- Forgot -->
                <div class="text-right">
                    <a href="{{ route('password.request') }}" class="text-sm text-[#9F7CFF] hover:underline">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Button -->
                <button type="submit" class="btn-primary">
                    Iniciar sesión
                </button>

                <!-- Divider -->
                <p class="text-center text-sm text-gray-300 mt-4">
                    O continuar con
                </p>

                <!-- GOOGLE ONLY -->
                <div class="flex justify-center mt-3">
                    <a href="{{ route('google.login') }}"
                        class="flex items-center justify-center gap-2 px-5 py-2 rounded-full bg-white hover:bg-gray-100 transition">

                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
                    </a>
                </div>

                <!-- REGISTER -->
                <p class="text-center text-sm text-gray-300 mt-6">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="text-[#9F7CFF] font-semibold hover:underline">
                        Regístrate gratis
                    </a>
                </p>

            </form>
        </div>

        <!-- IMAGEN -->
        <div class="hidden md:flex justify-center items-center flex-1">
            <img src="{{ asset('images/hamster.png') }}"
                class="w-[380px] drop-shadow-2xl animate-[float_4s_ease-in-out_infinite]">
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