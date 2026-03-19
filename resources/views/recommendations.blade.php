<x-app-layout>

<div class="max-w-6xl mx-auto px-6 mt-10">

<h1 class="text-3xl font-bold text-purple-900 mb-6">
Recomendaciones basadas en
</h1>

<div class="flex items-center gap-6 mb-12">

<img
src="https://image.tmdb.org/t/p/w300{{ $original['poster_path'] ?? '' }}"
class="rounded shadow w-40">

<div>

<h2 class="text-2xl font-bold">
{{ $original['display_title'] ?? $original['title'] ?? $original['name'] }}
</h2>

<p class="text-gray-600">
{{ substr($original['release_date'] ?? $original['first_air_date'],0,4) }}
</p>

<p class="text-yellow-500 font-semibold">
⭐ {{ number_format($original['vote_average'],1) }}
</p>

</div>

</div>

<h2 class="text-xl font-semibold mb-6">
Recomendaciones para ti
</h2>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">

@foreach($movies as $movie)

<div class="text-center">

<img
src="https://image.tmdb.org/t/p/w300{{ $movie['poster_path'] }}"
class="rounded shadow">

<p class="mt-2 font-semibold">
{{ $movie['display_title'] ?? $movie['title'] ?? $movie['name'] }}
</p>

<p class="text-gray-500 text-sm">
{{ substr($movie['release_date'] ?? $movie['first_air_date'],0,4) }}
</p>

<p class="text-yellow-500 text-sm">
⭐ {{ number_format($movie['vote_average'],1) }}
</p>

</div>

@endforeach

</div>

<div class="mt-12 text-center">

<a href="/share-card/{{ $busquedaId }}"
target="_blank"
class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg shadow">
⬇ Descargar tarjeta
</a>

</div>

</div>

</x-app-layout>