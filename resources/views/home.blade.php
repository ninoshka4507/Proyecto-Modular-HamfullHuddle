<x-app-layout :pageTitle="$pageTitle">

    <main class="flex flex-col items-center justify-center mt-12 px-4">
        
        <h1 class="text-4xl md:text-5xl font-extrabold text-primary mb-8 text-center tracking-tight">
            Menos búsqueda. Más historias.
        </h1>

        <div class="w-full max-w-2xl relative group">

            <input type="text"
                id="searchInput"
                onkeyup="buscar(this.value)"
                onkeydown="if(event.key === 'Enter') buscar(this.value)" 
                placeholder="Busca una película o serie..."
                class="w-full pl-6 pr-14 py-5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-white/60
                focus:ring-2 focus:ring-[#00E5FF] focus:border-[#00E5FF] outline-none shadow-xl text-lg transition">

            <button onclick="buscar(document.getElementById('searchInput').value)" class="absolute right-5 top-4 text-[#00E5FF] hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <div id="suggestions"
                 class="absolute left-0 right-0 bg-[#1a103d]/95 backdrop-blur-xl border border-white/20 shadow-2xl rounded-2xl mt-3 z-50 hidden max-h-[400px] overflow-y-auto custom-scrollbar">
            </div>

        </div>

        <p class="mt-6 text-[#B094FF] italic text-sm bg-white/10 px-4 py-2 rounded-full backdrop-blur-md">
            💡 Ejemplo: "Breaking Bad" o "El padrino"
        </p>

        <div class="w-full max-w-7xl mt-20">

            <h2 class="text-2xl font-bold text-primary mb-6 border-l-4 border-[#00E5FF] pl-4">
                Recomendado para ti
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6">

                @foreach($recommendedMovies as $movie)
                    <a href="/view/{{ $movie['id'] }}/{{ $movie['media_type'] ?? 'movie' }}" class="group block cursor-pointer">

                        <div class="relative overflow-hidden rounded-xl shadow-lg transition-transform duration-300 transform group-hover:scale-105 group-hover:shadow-2xl">

                            <img src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" 
                                 alt="{{ $movie['title'] ?? $movie['name'] }}" 
                                 class="w-full h-auto object-cover aspect-[2/3]">

                            <div class="absolute inset-0 bg-gradient-to-t from-[#1a103d] via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                <button class="bg-white/20 backdrop-blur-md text-white border border-white/50 font-bold py-2 rounded-lg text-sm mb-2 hover:bg-white hover:text-purple-900 transition">
                                    Ver Detalles
                                </button>
                            </div>

                        </div>

                        <h3 class="mt-3 font-semibold text-secondary truncate group-hover:text-accent transition-colors">
                            {{ $movie['title'] ?? $movie['name'] }}
                        </h3>

                        <p class="text-secondary text-xs font-semibold flex items-center gap-1 mt-1">
                            <span>{{ substr($movie['release_date'] ?? $movie['first_air_date'] ?? '', 0, 4) }}</span>
                            <span class="text-yellow-400 ml-1">⭐ {{ number_format($movie['vote_average'], 1) }}</span>
                        </p>

                    </a>
                @endforeach

            </div>

        </div>

    </main>

<script>
async function buscar(texto) {
    const suggestionsBox = document.getElementById("suggestions");

    if(texto.length < 2) {
        suggestionsBox.innerHTML = "";
        suggestionsBox.classList.add("hidden");
        return;
    }

    suggestionsBox.innerHTML = `
        <div class="flex flex-col items-center justify-center p-6 text-white/60">
            <svg class="animate-spin h-8 w-8 text-[#00E5FF] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-semibold">Buscando "${texto}"...</span>
        </div>
    `;
    suggestionsBox.classList.remove("hidden");

    let res = await fetch("/search?query=" + texto);
    let data = await res.json();

    if(data.length === 0) {
        suggestionsBox.innerHTML = `
            <div class="p-4 text-center text-white/60">
                No se encontraron resultados para "${texto}"
            </div>
        `;
        return;
    }

    let html = "";

    data.slice(0, 20).forEach(m => {
        let titulo = m.title || m.name || "Sin título";
        let year = "";

        if(m.release_date) year = m.release_date.substring(0, 4);
        if(m.first_air_date) year = m.first_air_date.substring(0, 4);

        let posterHtml = m.poster_path
            ? `<img src="https://image.tmdb.org/t/p/w92${m.poster_path}" class="w-12 h-16 object-cover rounded shadow-sm">`
            : `<div class="w-12 h-16 bg-white/5 border border-white/10 rounded flex items-center justify-center text-white/30 text-xs text-center p-1">N/A</div>`;

        html += `
        <div onclick="window.location='/select/${m.id}/${m.media_type ?? 'movie'}'"
             class="group flex items-center gap-4 p-3 hover:bg-white/10 cursor-pointer transition-colors duration-200 border-b border-white/5 last:border-none">
            
            ${posterHtml}

            <div class="flex-1">
                <div class="font-semibold text-white group-hover:text-[#00E5FF] transition-colors">${titulo}</div>
                <div class="text-sm text-[#B094FF]">${year}</div>
            </div>

            <svg class="w-5 h-5 text-white/20 group-hover:text-[#00E5FF] transition-colors mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>

        </div>
        `;
    });

    suggestionsBox.innerHTML = html;
}
</script>

</x-app-layout>