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

        <div class="w-full max-w-6xl bg-[#2a1e5c]/80 backdrop-blur-xl rounded-3xl shadow-2xl flex overflow-hidden">

            <div class="w-full md:w-1/2 p-12 flex flex-col justify-center">

                <h2 class="text-sm text-[#9F7CFF] mb-2 font-semibold">HamfuHuddle</h2>

                <h1 class="text-4xl font-bold mb-6 leading-tight">
                    Verifica tu correo
                </h1>

                <p class="text-purple-200 mb-8 max-w-md leading-relaxed text-sm">
                    ¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos otro. (No olvides revisar tu bandeja de spam)
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 font-medium text-sm text-green-400 bg-green-900/30 p-4 rounded-lg border border-green-500/30">
                        Se ha enviado un nuevo enlace de verificación al correo que registraste.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="w-full max-w-md">
                    @csrf
                    <button type="submit" class="btn-primary">
                        Reenviar enlace
                    </button>
                </form>

            </div>

            <div class="hidden md:flex md:w-1/2 items-center justify-center bg-[#241a4d] relative">
                <div class="absolute w-96 h-96 bg-purple-600/30 rounded-full blur-3xl"></div>

                <img 
                    src="{{ asset('images/hamster.png') }}" 
                    alt="Hamster" 
                    class="w-[380px] drop-shadow-2xl animate-[float_4s_ease-in-out_infinite] z-10"
                >
            </div>

        </div>
    </div>

</body>
</html>