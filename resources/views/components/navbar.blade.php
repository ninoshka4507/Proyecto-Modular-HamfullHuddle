@props(['pageTitle'])
<nav class="bg-[#201148]/80 backdrop-blur-md sticky top-0 z-50 text-white shadow-lg"> 
    <div class="w-full px-4 sm:px-6 lg:px-8"> 
        <div class="flex justify-between h-16 items-center relative">
            
            <!-- LOGO -->
            <div class="flex items-center space-x-4">
                <x-logo />
            </div>

            <!-- CENTRO --> 
            @if(!request()->routeIs('login') && !request()->routeIs('register')) 
                <div class="absolute left-1/2 transform -translate-x-1/2 pointer-events-none"> 
                    <span class="font-semibold text-lg text-white tracking-wider transition-all duration-300">
                        {{ $pageTitle ?? 'HamfuHuddle' }}
                    </span> 
                </div> 
            @endif

            <!-- DERECHA -->
            <div class="flex items-center h-full">

                @auth
                <div class="flex items-center gap-5 mr-6">

                    <!-- HOME -->
                    <a href="{{ route('home') }}" class="hover:text-[#9F7CFF] transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l9-9 9 9M4.5 10.5V21h5.25v-6h4.5v6H19.5V10.5" />
                        </svg>
                    </a>

                    <!-- BUSCAR -->
                    <a href="{{ route('explore') }}" class="hover:text-[#9F7CFF] transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a>

                    <!-- LISTAS -->
                    <a href="{{ route('listas') }}" class="hover:text-[#9F7CFF] transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-4-7 4V5z" />
                        </svg>
                    </a>

                </div>

                <!-- USER MENU -->
                <div x-data="{ open: false }" class="h-full flex items-center -mr-4 sm:-mr-6 lg:-mr-8">

                    <button @click="open = !open"
                        :class="open ? 'bg-[#2E1065]' : ''"
                        class="flex items-center gap-2 h-full pl-4 pr-4 sm:pr-6 lg:pr-8 transition-colors duration-200">

                        @php 
                        $userName = trim(auth()->user()->nombre_visible ?: auth()->user()->nombre_usuario ?: 'Usuario'); 
                        $nameParts = explode(' ', $userName); 
                        $initials = count($nameParts) > 1 
                            ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) 
                            : strtoupper(substr($nameParts[0], 0, 2)); 
                        @endphp

                        <!-- AVATAR -->
                        @if(auth()->user()->avatar)
                            <div class="p-[2px] rounded-full bg-gradient-to-r from-[#7C5CFF] to-[#00E5FF]">
                                <img src="{{ auth()->user()->avatar }}" class="w-9 h-9 rounded-full object-cover">
                            </div>    
                        @else
                            <div class="p-[2px] rounded-full bg-gradient-to-r from-[#7C5CFF] to-[#00E5FF]">
                                <div class="w-9 h-9 rounded-full grid place-items-center bg-[#1a103d] text-[#B094FF] font-bold text-sm">
                                    {{ $initials }}
                                </div>
                            </div>
                        @endif

                        <!-- TRIÁNGULO -->
                        <div :class="open ? 'rotate-180' : ''"
                            class="w-0 h-0 
                            border-l-[5px] border-l-transparent
                            border-r-[5px] border-r-transparent
                            border-t-[6px] border-t-[#B094FF]
                            transition-transform duration-300">
                        </div>

                    </button>

                    <!-- DROPDOWN -->
                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-300 transform origin-top"
                        x-transition:enter-start="scale-y-0"
                        x-transition:enter-end="scale-y-100"
                        x-transition:leave="transition ease-in duration-200 transform origin-top"
                        x-transition:leave-start="scale-y-100"
                        x-transition:leave-end="scale-y-0"
                        @click.away="open = false"
                        class="fixed right-0 top-16 w-64  bg-[#0B0B0F] 
                        border-l border-b border-white/10 
                        shadow-2xl overflow-hidden z-50 origin-top">

                        <!-- HEADER -->
                        <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10">

                            @if(auth()->user()->avatar)
                                <div class="p-[2px] rounded-full bg-gradient-to-r from-[#7C5CFF] to-[#00E5FF]">
                                    <img src="{{ auth()->user()->avatar }}" class="w-12 h-12 rounded-full object-cover"> 
                                </div>    
                            @else
                                <div class="p-[2px] rounded-full bg-gradient-to-r from-[#7C5CFF] to-[#00E5FF]">
                                    <div class="w-12 h-12 rounded-full bg-[#1a103d] flex items-center justify-center text-[#B094FF] font-bold text-lg">
                                        {{ $initials }}
                                    </div>
                                </div>
                            @endif

                            <p class="text-white font-semibold text-base truncate">
                                {{ $userName }}
                            </p>

                        </div>

                        <!-- MENU -->
                        <div class="py-2 text-sm">

                            <a href="{{ route('account.index') }}" 
                                class="group flex items-center gap-3.5 px-5 py-3 text-gray-300 hover:bg-[#1a103d] hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5 text-gray-500 group-hover:text-[#B094FF] transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                <span>Editar perfil</span>
                            </a>

                            <a href="#" 
                                class="group flex items-center gap-3.5 px-5 py-3 text-gray-300 hover:bg-[#1a103d] hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5 text-gray-500 group-hover:text-[#B094FF] transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.592c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.004.827c.422.348.53.94.26 1.43l-1.297 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.592c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.296-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>Opciones</span>
                            </a>

                            <div class="border-t border-white/10 my-2"></div>

                            <a href="{{ route('explore') }}"
                                class="group flex items-center gap-3.5 px-5 py-3 text-gray-300 hover:bg-[#1a103d] hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5 text-gray-500 group-hover:text-[#B094FF] transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span>Buscar</span>
                            </a>

                            <a href="{{ route('listas') }}" 
                                class="group flex items-center gap-3.5 px-5 py-3 text-gray-300 hover:bg-[#1a103d] hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5 text-gray-500 group-hover:text-[#B094FF] transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                </svg>
                                <span>Listas</span>
                            </a>

                            <div class="border-t border-white/10 my-2"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="group flex items-center gap-3.5 w-full text-left px-5 py-3 text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors duration-200">
                                    <svg class="h-5 w-5 text-red-500 group-hover:text-red-400 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>

                        </div>

                </div>

                @endauth 

                @guest
                    @if(request()->routeIs('login')) 

                        <!-- BOTÓN REGISTRO --> 
                        <a href="{{ route('register') }}" class="group flex items-center gap-2 px-6 py-3 rounded-full border border-white/40 text-white text-base font-semibold hover:bg-white/10 transition transform hover:-translate-y-0.5"> 
                            Registrarse 
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"> 
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/> 
                            </svg> 
                        </a> 
                    @elseif(request()->routeIs('register')) 

                        <!-- BOTÓN LOGIN --> 
                        <a href="{{ route('login') }}" class="group flex items-center gap-2 px-6 py-3 rounded-full border border-white/40 text-white text-base font-semibold hover:bg-white/10 transition transform hover:-translate-y-0.5"> 
                            Iniciar sesión 
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"> 
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/> 
                            </svg> 
                        </a> 
                    @else 

                        <!-- BOTÓN DEFAULT --> 
                        <a href="{{ route('login') }}" class="group flex items-center gap-2 px-6 py-3 rounded-full border border-white/40 text-white text-base font-semibold hover:bg-white/10 transition transform hover:-translate-y-0.5">
                            Iniciar sesión
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </a>
                    @endif

                @endguest

            </div>
        </div>
    </div>
</nav>