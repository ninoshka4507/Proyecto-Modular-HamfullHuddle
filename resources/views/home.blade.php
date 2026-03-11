<x-app-layout>
    <main class="flex flex-col items-center justify-center mt-12 px-4">
        <h1 class="text-4xl md:text-5xl font-extrabold text-purple-900 mb-8 text-center tracking-tight">
            ¿Qué película buscaremos hoy?
        </h1>

        <div class="w-full max-w-2xl relative group">
            <input type="text" 
                   placeholder="Busca una película por título, género o actor..." 
                   class="w-full pl-6 pr-14 py-4 rounded-full border-2 border-purple-200 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 focus:outline-none shadow-xl text-lg transition-all duration-300">
            
            <button class="absolute right-4 top-3 text-purple-600 hover:text-purple-900 transform hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>

        <p class="mt-6 text-purple-500 italic text-sm bg-purple-100 px-4 py-1 rounded-full">
            💡 Tip: Prueba con "Ciencia ficción de los 90" o "Películas como Inception"
        </p>

        <div class="w-full max-w-7xl mt-20">
            <h2 class="text-2xl font-bold text-purple-800 mb-6 border-l-4 border-purple-600 pl-4">Recomendaciones para ti</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @php
                    // Esto es un placeholder hasta que conectes la API o DB
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
                            <img src="{{ $movie['img'] }}" alt="{{ $movie['title'] }}" class="w-full h-auto object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-purple-900 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                <button class="bg-white text-purple-900 font-bold py-2 rounded-lg text-sm mb-2">Ver Detalles</button>
                            </div>
                        </div>
                        <h3 class="mt-3 font-bold text-purple-900 truncate">{{ $movie['title'] }}</h3>
                        <p class="text-purple-500 text-xs">{{ $movie['year'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</x-app-layout>