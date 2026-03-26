<x-app-layout :pageTitle="$pageTitle">

    <div class="max-w-7xl mx-auto px-6 mt-12 mb-20">

        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-8 border-l-4 border-[#00E5FF] pl-4">
            Porque viste...
        </h1>

        <div class="flex flex-col md:flex-row items-center gap-8 mb-16 bg-white/5 backdrop-blur-md border border-white/10 p-6 md:p-8 rounded-3xl shadow-2xl">
            @if(isset($original['poster_path']))
            <img src="https://image.tmdb.org/t/p/w300{{ $original['poster_path'] }}"
                 class="rounded-xl shadow-[0_15px_30px_rgba(0,0,0,0.5)] w-32 md:w-44 hover:scale-105 transition-transform duration-500">
            @endif
            
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-3xl md:text-4xl font-black text-white mb-3">
                    {{ $original['display_title'] ?? $original['title'] ?? $original['name'] }}
                </h2>
                
                <div class="flex items-center justify-center md:justify-start gap-4 text-sm font-semibold mb-4">
                    <span class="bg-white/10 px-3 py-1 rounded-full text-[#B094FF]">
                        {{ substr($original['release_date'] ?? $original['first_air_date'] ?? '', 0, 4) }}
                    </span>
                    <span class="text-yellow-400 flex items-center gap-1">
                        ⭐ {{ number_format($original['vote_average'] ?? 0, 1) }}
                    </span>
                </div>

                <p class="text-gray-300 mt-2 max-w-3xl line-clamp-3 text-sm md:text-base leading-relaxed">
                    {{ $original['overview_full'] ?? $original['overview'] ?? 'Sin descripción disponible.' }}
                </p>
            </div>
        </div>

        <div class="flex justify-between items-end mb-8 border-l-4 border-[#00E5FF] pl-4">
            <h2 class="text-2xl md:text-3xl font-bold text-white">
                Deberias ver
            </h2>
            
            <a href="/share-card/{{ $busquedaId }}" target="_blank"
               class="hidden md:inline-flex items-center gap-2 px-6 py-2.5 bg-[#9E78FF] text-black font-extrabold rounded-full hover:bg-[#8A60FF] hover:scale-105 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Descargar Tarjeta
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">

            @foreach($movies as $movie)
                <a href="/view/{{ $movie['id'] }}/{{ $tipo }}" class="group block cursor-pointer">

                    <div class="relative overflow-hidden rounded-xl shadow-lg transition-transform duration-300 transform group-hover:scale-105 group-hover:shadow-2xl">
                        
                        <img src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" 
                             alt="{{ $movie['display_title'] ?? $movie['title'] ?? $movie['name'] }}" 
                             class="w-full h-auto object-cover aspect-[2/3]">

                        <div class="absolute inset-0 bg-gradient-to-t from-[#1a103d] via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <button class="bg-white/20 backdrop-blur-md text-white border border-white/50 font-bold py-2 rounded-lg text-sm mb-2 hover:bg-white hover:text-purple-900 transition">
                                Ver Detalles
                            </button>
                        </div>

                    </div>

                    <h3 class="mt-3 font-semibold text-[#B094FF] truncate group-hover:text-[#00E5FF] transition-colors">
                        {{ $movie['display_title'] ?? $movie['title'] ?? $movie['name'] }}
                    </h3>

                    <p class="text-[#B094FF] text-xs font-semibold flex items-center gap-1 mt-1">
                        <span>{{ substr($movie['release_date'] ?? $movie['first_air_date'] ?? '', 0, 4) }}</span>
                        <span class="text-yellow-400 ml-1">⭐ {{ number_format($movie['vote_average'], 1) }}</span>
                    </p>

                </a>
            @endforeach

        </div>

        <div class="mt-12 text-center md:hidden">
            <a href="/share-card/{{ $busquedaId }}" target="_blank"
               class="inline-flex items-center justify-center gap-2 w-full max-w-xs px-8 py-4 bg-[#9E78FF] text-black font-extrabold rounded-full hover:bg-[#8A60FF] hover:scale-105 transition-all duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Descargar Tarjeta
            </a>
        </div>

    </div>

</x-app-layout>