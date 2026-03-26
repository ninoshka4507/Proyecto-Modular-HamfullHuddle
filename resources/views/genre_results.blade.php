<x-app-layout :pageTitle="$pageTitle">

    <div class="max-w-7xl mx-auto px-6 py-12 mb-20 min-h-screen">
        
        <div class="flex items-center gap-4 mb-10">
            <a href="{{ route('explore') }}" class="text-gray-400 hover:text-[#00E5FF] transition-colors bg-white/5 p-3 rounded-full border border-white/10 hover:border-[#00E5FF]/50">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white border-l-4 border-[#00E5FF] pl-4 uppercase tracking-wider">
                {{ $displayTitle }}
            </h1>
        </div>

        @if(!empty($results) && count($results) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                @foreach($results as $item)
                    @if(isset($item['poster_path']))
                        <a href="/view/{{ $item['id'] }}/{{ $item['media_type'] ?? 'movie' }}" class="group block cursor-pointer">

                            <div class="relative overflow-hidden rounded-xl shadow-lg transition-transform duration-300 transform group-hover:scale-105 group-hover:shadow-[0_0_20px_rgba(176,148,255,0.2)] border border-transparent group-hover:border-[#00E5FF]/30">
                                
                                <img src="https://image.tmdb.org/t/p/w500{{ $item['poster_path'] }}" 
                                     alt="{{ $item['title'] ?? $item['name'] }}" 
                                     class="w-full h-auto object-cover aspect-[2/3]">

                                <div class="absolute inset-0 bg-gradient-to-t from-[#1a103d] via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                    <button class="bg-white/20 backdrop-blur-md text-white border border-white/50 font-bold py-2 rounded-lg text-sm mb-2 hover:bg-white hover:text-purple-900 transition shadow-lg">
                                        Ver Detalles
                                    </button>
                                </div>

                            </div>

                            <h3 class="mt-3 font-semibold text-[#B094FF] truncate group-hover:text-[#00E5FF] transition-colors">
                                {{ $item['title'] ?? $item['name'] }}
                            </h3>

                            <div class="text-[#B094FF] text-xs font-semibold flex items-center gap-1 mt-1">
                                <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '', 0, 4) }}</span>
                                <span class="text-yellow-400 ml-1">⭐ {{ number_format($item['vote_average'] ?? 0, 1) }}</span>
                            </div>

                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 px-4 text-center bg-white/5 rounded-3xl border border-white/10 backdrop-blur-sm">
                <svg class="w-20 h-20 text-[#B094FF] mb-6 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-white/60 text-xl font-semibold max-w-md leading-relaxed">
                    No se encontró contenido para este género en este momento.
                </p>
            </div>
        @endif

    </div>

</x-app-layout>