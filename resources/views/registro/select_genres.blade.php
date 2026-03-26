<x-app-layout :pageTitle="$pageTitle">

    <div class="max-w-6xl mx-auto py-12 px-6 mb-20">
        
        <div class="text-center mb-14">
            @if(!session('genres_selected'))
                <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight">
                    ¡Bienvenido a <span class="text-[#B094FF]">HamfuHuddle</span>!
                </h2>
                <p class="mt-4 text-lg text-gray-400">
                    Selecciona tus géneros y formatos favoritos para empezar.
                </p>
            @else
                <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight">
                    Actualiza tus <span class="text-[#00E5FF]">gustos</span>
                </h2>
                <p class="mt-4 text-lg text-gray-400">
                    Modifica los géneros y formatos de los que quieres recibir recomendaciones.
                </p>
            @endif
        </div>

        <form method="POST" action="{{ route('genres.save') }}" class="space-y-12">
            @csrf
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                @foreach($generos as $genero)
                <label x-data="{ selected: {{ in_array($genero->id, $seleccionados ?? []) ? 'true' : 'false' }} }" 
                       class="relative flex flex-col items-center justify-center p-6 rounded-2xl border transition-all duration-300 cursor-pointer backdrop-blur-md group"
                       :class="selected ? 'border-[#00E5FF] bg-[#00E5FF]/10 shadow-[0_0_20px_rgba(0,229,255,0.2)] scale-105' : 'border-white/10 bg-white/5 hover:bg-white/10 hover:border-white/30'">
                    
                    <input type="checkbox" name="genres[]" value="{{ $genero->id }}" 
                        class="hidden" :checked="selected" @click="selected = !selected">
                    
                    {{-- Iconos dinámicos --}}
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform duration-300">
                        {{ match($genero->nombre) {
                            'Action'          => '💥',
                            'Adventure'       => '🗺️',
                            'Animation'       => '🎨',
                            'Comedy'          => '😂',
                            'Crime'           => '🕵️‍♂️',
                            'Drama'           => '🎭',
                            'Fantasy'         => '🐉',
                            'Horror'          => '👻',
                            'Mystery'         => '🔍',
                            'Romance'         => '💖',
                            'Sci-Fi'          => '🚀',
                            'Thriller'        => '🔪',
                            'Reality'         => '🤩',
                            'Documentary'     => '🌍',
                            'Family'          => '🧸',
                            'Music'           => '🎵',
                            'History'         => '🏛️',
                            'War'             => '⚔️',
                            'Anime'           => '🌸',
                            'K-Drama'         => '🫰',  
                            'C-Drama'         => '🏮',  
                            'J-Drama'         => '⛩️',  
                            'True Crime'      => '🚨',  
                            'Stand-Up'        => '🎤',  
                            'Kids'            => '🎈',  
                            'Soap'            => '📺',  
                            'Western'         => '🤠',  
                            'Talk'            => '🛋️',  
                            'Sports'          => '🏅', 
                            'Indie'           => '🎬',  
                            'LGBTQ+'          => '🌈', 
                            'Classic'         => '🎞️',  
                            'default'         => '🍿'
                        } }}
                    </span>

                    <span class="font-semibold text-center" :class="selected ? 'text-white' : 'text-gray-400'">
                        {{ $genero->nombre }}
                    </span>

                    {{-- Icono de Check Mejorado --}}
                    <template x-if="selected">
                        <div class="absolute top-3 right-3 bg-[#00E5FF] rounded-full p-1 shadow-[0_0_10px_rgba(0,229,255,0.5)] transition-all animate-pulse-short">
                            <svg class="h-4 w-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>
                </label>
                @endforeach
            </div>

            <div class="flex justify-center pt-8 border-t border-white/10 mt-12">
                <button type="submit" 
                    class="inline-flex items-center gap-3 px-10 py-4 bg-[#00E5FF] text-black font-black text-lg rounded-full hover:bg-[#00c8e6] hover:scale-105 transition-all duration-300">
                    
                    @if(!session('genres_selected'))
                        ¡Listo, empezar a ver!
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                    @else
                        Guardar cambios
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif

                </button>
            </div>
            
        </form>
    </div>

<style>
    @keyframes pulse-short {
        0% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-pulse-short {
        animation: pulse-short 0.3s ease-out forwards;
    }
</style>

</x-app-layout>