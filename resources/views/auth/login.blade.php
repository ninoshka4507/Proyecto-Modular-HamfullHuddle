<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>HamfuHuddle</title>
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
        @keyframes float {
            0% { transform: translateY(0px);}
            50% { transform: translateY(-12px);}
            100% { transform: translateY(0px);}
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
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full mt-1 px-4 py-3 rounded-lg bg-white/80 text-black placeholder-gray-400 focus:outline-none transition-colors @error('email') border-2 border-red-500 @enderror"
                        placeholder="correo@ejemplo.com">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="text-sm text-gray-300">Contraseña</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="w-full mt-1 px-4 py-3 rounded-lg bg-white/80 text-black placeholder-gray-400 focus:outline-none transition-colors pr-12 @error('password') border-2 border-red-500 @enderror"
                            placeholder="••••••••">
                        
                        <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-[#9F7CFF] transition toggle-password" data-target="password">
                            <svg class="w-5 h-5 eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <svg class="w-5 h-5 eye-open hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forgot -->
                <div class="text-right mt-2">
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
                class="w-[500px] max-w-none drop-shadow-2xl animate-[float_4s_ease-in-out_infinite]">
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('form input');
        
        inputs.forEach(input => {
            const limpiarError = function() {
                this.classList.remove('border-2', 'border-red-500');
                const contenedor = this.parentElement;
                const mensajesError = contenedor.querySelectorAll('p.text-red-400');
                mensajesError.forEach(mensaje => {
                    mensaje.style.display = 'none';
                });
            };

            input.addEventListener('focus', limpiarError);
            input.addEventListener('input', limpiarError);
        });

        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const iconClosed = this.querySelector('.eye-closed');
                const iconOpen = this.querySelector('.eye-open');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    iconClosed.classList.add('hidden');
                    iconOpen.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    iconClosed.classList.remove('hidden');
                    iconOpen.classList.add('hidden');
                }
            });
        });
    });
</script>

</body>
</html>