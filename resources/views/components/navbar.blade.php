<nav class="bg-[#201148] text-white shadow-lg">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center relative">
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" 
                        alt="HamfuHuddle Logo" 
                        class="h-[45px] w-auto object-contain transition-transform group-hover:scale-110">
                    
                    <span class="text-2xl font-bold tracking-wider hidden sm:block">
                        <span class="text-[#9F7CFF]">H</span><span class="text-white">amfu</span><span class="text-[#9F7CFF]">H</span><span class="text-white">uddle</span>
                    </span>
                </a>
            </div>

            <div class="absolute left-1/2 transform -translate-x-1/2 pointer-events-none">
                <span class="font-semibold text-lg text-white tracking-wider">
                    {{ $pageTitle ?? 'Home' }}
                </span>
            </div>

            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="hover:text-[#9F7CFF] transition-colors" title="Buscar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </a>

                <a href="#" class="hover:text-[#9F7CFF] transition-colors" title="Recomendaciones">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </a>

                <a href="#" class="hover:text-[#9F7CFF] transition-colors" title="Mis Calificaciones">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </a>

                <div x-data="{ open: false }" class="relative pointer-events-auto">
                    <button @click="open = !open" class="flex items-center space-x-2 bg-purple-800 px-3 py-1 rounded-full hover:bg-purple-700 transition">
                        <span>{{ Auth::user()->name ?? 'Usuario' }}</span>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-xl py-2 z-50">
                        <a href="{{ route('account.index') }}" class="block px-4 py-2 hover:bg-purple-100">Cuenta</a>
                        <a href="#" class="block px-4 py-2 hover:bg-purple-100">Opciones</a>
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-100 text-red-600">Cerrar Sesión</button>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</nav>