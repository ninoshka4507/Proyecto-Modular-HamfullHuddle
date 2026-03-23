<x-app-layout>
<x-slot name="pageTitle">
    Buscar
</x-slot>

<div class="min-h-screen px-8 py-10">

    <!-- 🔍 BUSCADOR -->
    <div class="mb-10 relative max-w-4xl mx-auto">

        <!-- ICONO -->
        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <input 
            id="searchInput"
            type="text"
            onkeyup="buscarExplore(this.value)"
            placeholder="Encuentra películas, series y más"
            class="w-full pl-14 pr-6 py-5 rounded-full border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:outline-none text-lg shadow-md"
        />
    </div>

    <!-- RESULTADOS -->
    <div id="results" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-6 hidden"></div>

    <!-- CONTENIDO DEFAULT -->
    <div id="defaultContent">

        <!-- 🎬 POPULARES -->
        <h2 class="text-xl font-bold mb-6">Títulos populares</h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 mb-12">

            @foreach(array_slice($movies,0,16) as $item)
                @if($item['poster_path'])
                <a href="/select/{{ $item['id'] }}/movie">
                    <img 
                        src="https://image.tmdb.org/t/p/w500{{ $item['poster_path'] }}"
                        class="rounded-lg hover:scale-110 transition duration-300 shadow"
                    />
                </a>
                @endif
            @endforeach

        </div>

        <!-- 🎭 GÉNEROS -->
        <h2 class="text-xl font-bold mb-6">Explora por género</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">

            <!-- USA LOS MISMOS QUE YA TIENES -->
            <a href="/genero/terror" class="genre">TERROR</a>
            <a href="/genero/comedia" class="genre">COMEDIA</a>
            <a href="/genero/accion" class="genre">ACCIÓN</a>
            <a href="/genero/fantasia" class="genre">FANTASÍA</a>
            <a href="/genero/drama" class="genre">DRAMA</a>
            <a href="/genero/documental" class="genre">DOCUMENTAL</a>
            <a href="/genero/crimen" class="genre">CRIMEN</a>
            <a href="/genero/familiar" class="genre">INFANTIL</a>

        </div>

    </div>

</div>

<style>
.genre {
    height: 120px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:14px;
    font-weight:bold;
    background: linear-gradient(135deg, #6d28d9, #9333ea);
    color:white;
    cursor:pointer;
    transition:.3s;
}
.genre:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(147,51,234,0.4);
}

.group:hover img {
    transform: scale(1.08);
}
</style>

<script>
async function buscarExplore(texto)
{
    let results = document.getElementById("results");
    let defaultContent = document.getElementById("defaultContent");

    if(texto.length < 2)
    {
        results.classList.add("hidden");
        defaultContent.classList.remove("hidden");
        results.innerHTML = "";
        return;
    }

    let res = await fetch("/search?query=" + texto);
    let data = await res.json();

    let html = "";

    data.forEach(m => {

        if(!m.poster_path) return;

        let type = m.media_type ?? 'movie';

        let titulo = m.title || m.name || "Sin título";

        let year = "";
        if(m.release_date) year = m.release_date.substring(0,4);
        if(m.first_air_date) year = m.first_air_date.substring(0,4);

        let poster = "https://image.tmdb.org/t/p/w500" + m.poster_path;

        html += `
        <div class="group cursor-pointer">

            <a href="/view/${m.id}/${type}">
                <img src="${poster}" 
                     class="rounded-lg transition duration-300 shadow group-hover:scale-105">
            </a>

            <div class="mt-2">
                <div class="text-sm font-semibold truncate group-hover:text-purple-400 transition">
                    ${titulo}
                </div>
                <div class="text-xs text-gray-400">
                    ${year}
                </div>
            </div>

        </div>
        `;
    });

    results.innerHTML = html;

    results.classList.remove("hidden");
    defaultContent.classList.add("hidden");
}
</script>

</x-app-layout>