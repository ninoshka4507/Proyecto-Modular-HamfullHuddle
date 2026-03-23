<x-app-layout>

<div class="relative min-h-screen text-white bg-gray-900 overflow-hidden">

    <div class="absolute inset-0 z-0 pointer-events-none" style="background-color: black;">
        <img 
            src="https://image.tmdb.org/t/p/original{{ $data['backdrop_path'] ?? '' }}"
            class="w-full h-full object-cover"
            style="filter: brightness(35%);"
            alt="Fondo">
        
        <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 20%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 20%);"></div> <!--Cambiar el color por el color actual del fondo-->
    </div>

    <div class="relative z-10 flex gap-12 items-start max-w-[1600px] mx-auto" style="padding: 50px 50px;">

        <div class="w-[320px] flex-shrink-0">
            <img 
                src="https://image.tmdb.org/t/p/w500{{ $data['poster_path'] ?? '' }}"
                class="w-full rounded-2xl shadow-[0_30px_80px_rgba(0,0,0,0.9)] border border-white/5"
                alt="Poster"
            >
        </div>

        <div class="flex-1 max-w-5xl pt-4">

            <h1 class="font-black mb-4 tracking-tighter drop-shadow-2xl leading-none" style="font-size: 100px;">
                {{ $data['display_title'] }}
                <span class="font-light text-gray-400 align-baseline ml-4 tracking-normal" style="font-size: 50px;">
                    ({{ substr($data['release_date'] ?? $data['first_air_date'] ?? '', 0, 4) }})
                </span>
            </h1>

            <div class="flex flex-wrap items-center gap-6 mb-8">
                <div class="flex items-center gap-2 text-yellow-400 text-xl font-bold bg-black/40 px-4 py-2 rounded-lg border border-white/5">
                    ⭐ {{ number_format($data['vote_average'] ?? 0, 1) }}
                </div>
                
                <div class="flex flex-wrap gap-2">
                    @foreach($data['genres'] ?? [] as $g)
                        <span class="bg-white/10 border border-white/20 px-4 py-1.5 rounded-full text-sm font-medium">
                            {{ $g['name'] }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="mb-10 max-w-4xl">
                <p class="text-gray-200 leading-relaxed text-lg drop-shadow-md">
                    {{ $data['overview'] ?: 'No hay descripción disponible para este contenido.' }}
                </p>
            </div>

            <div class="flex gap-4 mb-14 flex-wrap">
                @if(isset($data['videos']['results']) && count($data['videos']['results']) > 0)
                <a 
                    href="https://youtube.com/watch?v={{ $data['videos']['results'][0]['key'] }}"
                    target="_blank"
                    class="bg-white text-black px-8 py-3 rounded-xl font-bold text-lg hover:scale-105 transition shadow-lg flex items-center gap-2"
                >
                    ▶ Ver trailer
                </a>
                @endif

                <button onclick="marcarVisto({{ $data['id'] }}, '{{ $type }}')"
                    class="bg-white/10 border border-white/20 px-8 py-3 rounded-xl text-lg hover:bg-white/20 transition shadow-lg flex items-center gap-2">
                    👁 Marcar como visto
                </button>

                <div class="flex bg-black/50 border border-white/10 rounded-xl overflow-hidden shadow-lg">
                    <button onclick="calificar({{ $data['id'] }}, '{{ $type }}', 'me_gusto')" class="px-5 py-3 hover:bg-green-600 transition text-xl">👍</button>
                    <button onclick="calificar({{ $data['id'] }}, '{{ $type }}', 'neutro')" class="px-5 py-3 hover:bg-gray-600 transition border-x border-white/10 text-xl">😐</button>
                    <button onclick="calificar({{ $data['id'] }}, '{{ $type }}', 'no_me_gusto')" class="px-5 py-3 hover:bg-red-600 transition text-xl">👎</button>
                </div>
            </div>

            @if(isset($data['credits']['cast']) && count($data['credits']['cast']) > 0)
            <div class="mb-10">
                <h3 class="text-2xl font-bold mb-4 drop-shadow-md">Reparto Principal</h3>
                <div class="flex gap-5 overflow-x-auto pb-4">
                    @foreach(array_slice($data['credits']['cast'], 0, 10) as $actor)
                        @if(isset($actor['profile_path']) && $actor['profile_path'])
                        <div class="w-24 flex-shrink-0 group">
                            <img 
                                src="https://image.tmdb.org/t/p/w200{{ $actor['profile_path'] }}"
                                class="w-24 h-24 object-cover rounded-full mb-2 shadow-lg border-2 border-transparent group-hover:border-white/50 transition"
                                alt="{{ $actor['name'] }}"
                            >
                            <div class="text-xs font-bold text-center leading-tight mb-1 group-hover:text-white text-gray-200">
                                {{ $actor['name'] }}
                            </div>
                            <div class="text-[10px] text-gray-400 text-center leading-tight">
                                {{ $actor['character'] }}
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @php
                $crew = collect($data['credits']['crew'] ?? []);
                $directors = $crew->where('job', 'Director')->pluck('name')->unique();
                $writers = $crew->whereIn('job', ['Writer', 'Screenplay', 'Story', 'Teleplay'])->pluck('name')->unique();
                $creators = collect($data['created_by'] ?? [])->pluck('name');
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 border-t border-white/10">
                @if($creators->isNotEmpty())
                    <div>
                        <span class="block text-gray-400 text-xs mb-1 uppercase tracking-widest font-bold">Creador</span>
                        <span class="text-sm font-medium">{{ $creators->join(', ') }}</span>
                    </div>
                @endif
                @if($directors->isNotEmpty())
                    <div>
                        <span class="block text-gray-400 text-xs mb-1 uppercase tracking-widest font-bold">Director</span>
                        <span class="text-sm font-medium">{{ $directors->take(3)->join(', ') }}</span>
                    </div>
                @endif
                @if($writers->isNotEmpty())
                    <div>
                        <span class="block text-gray-400 text-xs mb-1 uppercase tracking-widest font-bold">Guion</span>
                        <span class="text-sm font-medium">{{ $writers->take(3)->join(', ') }}</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
async function marcarVisto(id, type) {
    await fetch('/visto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tmdb_id: id, type: type })
    });
}

async function calificar(id, type, tipo) {
    await fetch('/calificar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tmdb_id: id, type: type, calificacion: tipo })
    });
}
</script>

</x-app-layout>