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

<body class="flex flex-col min-h-screen text-white">

    @include('components.navbar')

    <div class="flex-1 flex items-center justify-center px-6 pb-12 mt-8 md:mt-0">

        <div class="w-full max-w-6xl bg-[#241a4d]/90 backdrop-blur-xl rounded-3xl shadow-2xl flex overflow-hidden border border-white/5">

            <div class="w-full md:w-1/2 p-12 flex flex-col justify-center">

                <h2 class="text-sm text-[#9F7CFF] mb-2 font-semibold uppercase tracking-wider">HamfuHuddle</h2>

                <h1 class="text-4xl font-bold mb-6 leading-tight">
                    Establecer nueva<br>contraseña
                </h1>

                <p class="text-purple-200/70 mb-8 max-w-md leading-relaxed text-sm">
                    Ingresa tu correo electrónico y la nueva contraseña para recuperar el acceso a tu cuenta.
                </p>

                <form method="POST" action="{{ route('password.store') }}" class="w-full max-w-md space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label class="text-sm text-gray-300 ml-1">Correo electrónico</label>
                        <input type="email" name="email"
                            value="{{ old('email', $request->email) }}"
                            class="w-full mt-1 px-4 py-3 rounded-xl bg-white/10 text-white border border-white/10 placeholder-gray-500 focus:outline-none focus:border-[#9F7CFF] transition-all @error('email') border-red-500 @enderror"
                            placeholder="correo@ejemplo.com"
                            required autofocus>
                        @error('email')
                            <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-gray-300 ml-1">Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="w-full mt-1 px-4 py-3 rounded-xl bg-white/10 text-white border border-white/10 placeholder-gray-500 focus:outline-none focus:border-[#9F7CFF] transition-all pr-12 @error('password') border-red-500 @enderror"
                                placeholder="••••••••"
                                required>
                            
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-[#9F7CFF] transition toggle-password" data-target="password">
                                <svg class="w-5 h-5 eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                <svg class="w-5 h-5 eye-open hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Usa al menos 8 caracteres, un número y un símbolo.</p>
                        @error('password')
                            <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-gray-300 ml-1">Confirmar contraseña</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full mt-1 px-4 py-3 rounded-xl bg-white/10 text-white border border-white/10 placeholder-gray-500 focus:outline-none focus:border-[#9F7CFF] transition-all pr-12"
                                placeholder="••••••••"
                                required>
                            
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-[#9F7CFF] transition toggle-password" data-target="password_confirmation">
                                <svg class="w-5 h-5 eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                <svg class="w-5 h-5 eye-open hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 mt-6 rounded-xl bg-gradient-to-r from-[#9F7CFF] to-[#7C5CFF] hover:from-[#8860f0] hover:to-[#6a48d1] text-white font-bold transition-all shadow-lg hover:shadow-purple-500/20 transform hover:-translate-y-1">
                        Restablecer contraseña
                    </button>
                </form>

            </div>

            <div class="hidden md:flex md:w-1/2 items-center justify-center relative">
                <div class="absolute w-80 h-80 bg-purple-600/20 rounded-full blur-3xl"></div>
                <img src="{{ asset('images/hamster.png') }}" alt="Mascota" class="w-[450px] max-w-none drop-shadow-2xl animate-[float_4s_ease-in-out_infinite] z-10">
            </div>

        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('form input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.classList.remove('border-red-500');
                const contenedor = this.closest('div');
                const mensajesError = contenedor.querySelectorAll('.error-message');
                mensajesError.forEach(m => m.style.display = 'none');
            });
        });

        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = document.getElementById(this.getAttribute('data-target'));
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