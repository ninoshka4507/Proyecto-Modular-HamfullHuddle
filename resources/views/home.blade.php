<x-app-layout>
<x-slot name="pageTitle">
    Home
</x-slot>

    <main class="flex flex-col items-center justify-center mt-12 px-4">
        
        <h1 class="text-4xl md:text-5xl font-extrabold text-primary mb-8 text-center tracking-tight">
            Busca una película o serie… y descubre recomendaciones para ti
        </h1>

        <div class="w-full max-w-2xl relative group">

            <input type="text"
                id="searchInput"
                onkeyup="buscar(this.value)"
                placeholder="Busca una película o serie..."
                class="w-full pl-6 pr-14 py-5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-white/60
                focus:ring-2 focus:ring-[#00E5FF] focus:border-[#00E5FF] outline-none shadow-xl text-lg transition">

            <button class="absolute right-5 top-4 text-[#00E5FF] hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <!-- SUGERENCIAS -->
            <div id="suggestions"
                 class="absolute left-0 right-0 bg-white shadow-xl rounded-xl mt-2 overflow-hidden z-50">
            </div>

        </div>

        <p class="mt-6 text-[#B094FF] italic text-sm bg-white/10 px-4 py-2 rounded-full backdrop-blur-md">
            💡 Ejemplo: "Breaking Bad" o "El padrino"
        </p>

        <div class="w-full max-w-7xl mt-20">

            <h2 class="text-2xl font-bold text-primary mb-6 border-l-4 border-[#00E5FF] pl-4">
                Recomendado para ti
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">

                @php
                    $dummyMovies = [
                        ['title' => 'Interstellar', 'year' => '2014', 'img' => 'https://via.placeholder.com/300x450/6B46C1/FFFFFF?text=Interstellar'],
                        ['title' => 'The Batman', 'year' => '2022', 'img' => 'https://via.placeholder.com/300x450/4C1D95/FFFFFF?text=The+Batman'],
                        ['title' => 'Inception', 'year' => '2010', 'img' => 'https://via.placeholder.com/300x450/7C3AED/FFFFFF?text=Inception'],
                        ['title' => 'Spider-Man', 'year' => '2023', 'img' => 'https://via.placeholder.com/300x450/5B21B6/FFFFFF?text=Spider-Man'],
                        ['title' => 'Dune', 'year' => '2021', 'img' => 'https://via.placeholder.com/300x450/2E1065/FFFFFF?text=Dune'],
                    ];
                @endphp

                @foreach($dummyMovies as $movie)
                    <div class="group cursor-pointer">

                        <div class="relative overflow-hidden rounded-xl shadow-lg transition-transform duration-300 transform group-hover:scale-105 group-hover:shadow-2xl">

                            <img src="{{ $movie['img'] }}" 
                                 alt="{{ $movie['title'] }}" 
                                 class="w-full h-auto object-cover">

                            <div class="absolute inset-0 bg-gradient-to-t from-purple-900 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">

                                <button class="bg-white text-purple-900 font-bold py-2 rounded-lg text-sm mb-2">
                                    Ver Detalles
                                </button>

                            </div>

                        </div>

                        <h3 class="mt-3 font-bold text-purple-900 truncate">
                            {{ $movie['title'] }}
                        </h3>

                        <p class="text-purple-500 text-xs">
                            {{ $movie['year'] }}
                        </p>

                    </div>
                @endforeach

            </div>

        </div>

    </main>

<script>

async function buscar(texto)
{

if(texto.length < 2)
{
document.getElementById("suggestions").innerHTML="";
return;
}

let res = await fetch("/search?query=" + texto);

let data = await res.json();

let html="";

data.slice(0,8).forEach(m=>{

let titulo = m.title || m.name;

let year="";

if(m.release_date)
year = m.release_date.substring(0,4);

if(m.first_air_date)
year = m.first_air_date.substring(0,4);

let poster = m.poster_path
? "https://image.tmdb.org/t/p/w92" + m.poster_path
: "https://via.placeholder.com/92x138";

html += `
<div onclick="window.location='/select/${m.id}/${m.media_type ?? 'movie'}'"
class="flex items-center gap-4 p-3 hover:bg-purple-100 cursor-pointer">

<img src="${poster}" class="w-12 rounded">

<div>
<div class="font-semibold text-purple-900">${titulo}</div>
<div class="text-xs text-gray-500">${year}</div>
</div>

</div>
`;

});

document.getElementById("suggestions").innerHTML = html;

}

</script>

</x-app-layout>