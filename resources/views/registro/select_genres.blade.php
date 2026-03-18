<x-app-layout>
    <div class="max-w-5xl mx-auto py-12 px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-purple-900 sm:text-4xl">¡Bienvenido a HamfuHuddle!</h2>
            <p class="mt-4 text-lg text-purple-700">Selecciona tus géneros y formatos favoritos.</p>
        </div>

        <form method="POST" action="{{ route('genres.save') }}" class="space-y-8">
            @csrf
            {{-- Actualizamos el grid para soportar 4 columnas en pantallas grandes --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($generos as $genero)
                <label x-data="{ selected: false }" 
                       class="relative flex flex-col items-center justify-center p-6 rounded-2xl border-2 cursor-pointer transition-all duration-200 shadow-sm"
                       :class="selected ? 'border-purple-600 bg-purple-100 ring-2 ring-purple-600 ring-opacity-50' : 'border-white bg-white hover:border-purple-300'">
                    
                    <input type="checkbox" name="genres[]" value="{{ $genero->id }}" 
                           class="hidden" @click="selected = !selected">
                    
                    {{-- Iconos dinámicos ultra completos --}}
                    <span class="text-4xl mb-2">
                        {{ match($genero->nombre) {
                            'Action'      => '💥',
                            'Adventure'   => '🗺️',
                            'Animation'   => '🎨',
                            'Comedy'      => '😂',
                            'Crime'       => '🕵️‍♂️',
                            'Drama'       => '🎭',
                            'Fantasy'     => '🐉',
                            'Horror'      => '👻',
                            'Mystery'     => '🔍',
                            'Romance'     => '💖',
                            'Sci-Fi'      => '🚀',
                            'Thriller'    => '🔪',
                            'Reality'     => '🤩',
                            'Documentary' => '🌍',
                            'Family'      => '🧸',
                            'Music'       => '🎵',
                            'History'     => '🏛️',
                            'War'         => '⚔️',
                            'Anime'       => '🌸',
                            default       => '🎬'
                        } }}
                    </span>

                    <span class="font-bold text-purple-900">{{ $genero->nombre }}</span>

                    <template x-if="selected">
                        <div class="absolute top-2 right-2">
                            <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </template>
                </label>
                @endforeach
            </div>

            <div class="flex justify-center mt-12">
                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white font-bold py-4 px-12 rounded-full text-lg shadow-lg">
                    ¡Listo, empezar a ver!
                </button>
            </div>
        </form>
    </div>
</x-app-layout>