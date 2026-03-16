<x-app-layout>

<div class="max-w-7xl mx-auto px-6 mt-10">

<h1 class="text-3xl font-bold mb-6 text-purple-900">

Recomendaciones basadas en
{{ $original['title'] ?? $original['name'] ?? 'Contenido' }}

</h1>

<a href="/share-card/{{ $busquedaId }}"
class="bg-purple-600 text-white px-4 py-2 rounded-lg">

Descargar tarjeta

</a>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mt-8">

@foreach($movies as $movie)

<div class="text-center">

<img
src="{{ isset($movie['poster_path']) 
        ? 'https://image.tmdb.org/t/p/w500'.$movie['poster_path'] 
        : 'https://via.placeholder.com/500x750?text=No+Image' }}"
class="rounded shadow">

<p class="mt-2 font-semibold">

{{ $movie['title'] ?? $movie['name'] ?? 'Sin título' }}

</p>

<p class="text-sm text-gray-500">

⭐ {{ $movie['vote_average'] ?? 'N/A' }}

</p>

</div>

@endforeach

</div>

</div>

</x-app-layout>