<x-app-layout :pageTitle="$pageTitle" fullWidth="true">
<div class="relative min-h-screen text-white bg-gray-900 overflow-hidden">

    <!-- FONDO -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <img 
            src="https://image.tmdb.org/t/p/original{{ $data['backdrop_path'] ?? '' }}"
            class="w-full h-full object-cover"
            style="filter: brightness(35%);"
        >
        <div class="absolute inset-0 bg-gradient-to-b from-black via-transparent to-black"></div>
    </div>

    <div class="relative z-10 flex gap-12 items-start max-w-[1600px] mx-auto px-12 py-16">

        <!-- POSTER -->
        <div class="w-[320px] flex-shrink-0">
            <img 
                src="https://image.tmdb.org/t/p/w500{{ $data['poster_path'] ?? '' }}"
                class="w-full rounded-2xl shadow-[0_30px_80px_rgba(0,0,0,0.9)] border border-white/5 hover:scale-105 transition duration-500"
            >
        </div>

        <!-- INFO -->
        <div class="flex-1 max-w-5xl pt-4">

            <!-- TITULO -->
            <h1 class="font-black mb-4 tracking-tighter leading-none text-[90px]">
                {{ $data['display_title'] }}
                <span class="font-light text-gray-400 ml-4 text-[45px]">
                    ({{ substr($data['release_date'] ?? $data['first_air_date'] ?? '', 0, 4) }})
                </span>
            </h1>

            <!-- CALIFICACION -->
            <div class="flex items-center gap-6 mb-6">
                <span class="text-yellow-400 text-2xl font-bold flex items-center gap-2">
                    ⭐ {{ number_format($data['vote_average'] ?? 0, 1) }}
                </span>

                <!-- GENEROS -->
                <div class="flex flex-wrap gap-2">
                    @foreach($data['genres'] ?? [] as $g)
                        <span class="bg-white/10 border border-white/20 px-4 py-1.5 rounded-full text-sm">
                            {{ $g['name'] }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- SINOPSIS -->
            <div class="mb-10 max-w-4xl">
                <h3 class="text-xl font-bold mb-2">Sipnosis</h3>

                <p class="text-gray-200 leading-relaxed text-lg">
                    {{ $data['overview_full'] ?? $data['overview'] ?? 'No hay descripción disponible.' }}
                </p>
            </div>

            <!-- BOTONES -->
            <div class="flex gap-4 mb-14 flex-wrap items-center">

                <!-- TRAILER -->
                @if(isset($data['videos']['results']) && count($data['videos']['results']) > 0)
                <a 
                    href="https://youtube.com/watch?v={{ $data['videos']['results'][0]['key'] }}"
                    target="_blank"
                    class="px-8 py-3 rounded-xl font-semibold text-lg flex items-center gap-3 
                    bg-purple-600 hover:bg-purple-500 active:scale-95
                    transition-all duration-300 hover:scale-105">
                    <!-- ICONO PLAY -->
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        class="w-5 h-5" 
                        fill="currentColor" 
                        viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>

                    Ver trailer
                </a>
                @endif

                <!-- VISTO -->
                <button 
                    id="btn-visto"
                    onclick="toggleVisto({{ $data['id'] }}, '{{ $type }}')"
                    class="group px-7 py-3 rounded-xl text-lg flex items-center gap-3 
                    {{ $visto ? 'bg-green-600 hover:bg-green-500' : 'bg-purple-600 hover:bg-purple-500' }}
                    active:scale-95 hover:scale-105
                    transition-all duration-300 shadow-md">

                    <svg id="icon-visto" xmlns="http://www.w3.org/2000/svg" 
                        class="w-6 h-6"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                        @if($visto)
                            <!-- CHECK -->
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        @else
                            <!-- OJO -->
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M2.458 12C3.732 7.943 7.523 5 12 5 
                                c4.477 0 8.268 2.943 9.542 7 
                                -1.274 4.057-5.065 7-9.542 7 
                                -4.477 0-8.268-2.943-9.542-7z" />
                        @endif
                    </svg>

                    <span id="text-visto">
                        {{ $visto ? 'Visto' : 'Marcar como visto' }}
                    </span>
                </button>

                <!-- CALIFICACION -->
                <div class="flex flex-col gap-3 ml-2">
                    <span class="text-lg font-semibold text-gray-300">¿Qué te pareció?</span>

                    <div class="flex gap-3">

                        <!-- Me gusto -->
                        <button id="btn-like"
                            onclick="calificar({{ $data['id'] }}, '{{ $type }}', 'me_gusto')"
                            class="px-6 py-2 rounded-lg border transition
                            {{ $calificacion === 'me_gusto' ? 'bg-green-600 text-white border-green-600' : 'border-white/20 hover:bg-green-600/30' }}">
                            Me gustó
                        </button>

                        <!-- Neutral -->
                        <button id="btn-neutral"
                            onclick="calificar({{ $data['id'] }}, '{{ $type }}', 'neutro')"
                            class="px-6 py-2 rounded-lg border transition
                            {{ $calificacion === 'neutro' ? 'bg-gray-500 text-white border-gray-500' : 'border-white/20 hover:bg-gray-600/30' }}">
                            Regular
                        </button>

                        <!-- No me gusto -->
                        <button id="btn-dislike"
                            onclick="calificar({{ $data['id'] }}, '{{ $type }}', 'no_me_gusto')"
                            class="px-6 py-2 rounded-lg border transition
                            {{ $calificacion === 'no_me_gusto' ? 'bg-red-600 text-white border-red-600' : 'border-white/20 hover:bg-red-600/30' }}">
                            No me gustó
                        </button>

                    </div>
                </div>

            </div>

            <!-- ACTORES -->
            @if(isset($data['credits']['cast']))
            <div class="mb-12">
                <h3 class="text-2xl font-bold mb-6">Reparto</h3>

                <div class="flex gap-6 overflow-x-auto pb-4 scroll-pro snap-x snap-mandatory">
                    @foreach(collect($data['credits']['cast'])->take(10) as $actor)
                        <div class="w-28 flex-shrink-0 text-center group">

                            <div class="w-28 h-28 rounded-full overflow-hidden">
                                <img 
                                    src="{{ $actor['profile_path'] 
                                        ? 'https://image.tmdb.org/t/p/w200'.$actor['profile_path'] 
                                        : 'https://ui-avatars.com/api/?name='.urlencode($actor['name']).'&background=111827&color=fff' }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                >
                            </div>

                            <div class="text-sm font-semibold mt-2">
                                {{ $actor['name'] }}
                            </div>

                            <div class="text-sm text-gray-400">
                                {{ $actor['character'] }}
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- CREW -->
            @php
            $crew = collect($data['credits']['crew'] ?? []);

            $directors = $crew
                ->where('job', 'Director')
                ->sortByDesc('episodes')
                ->pluck('name')
                ->unique()
                ->take(5);

            $writers = $crew
                ->whereIn('job', ['Writer','Screenplay','Story','Teleplay'])
                ->pluck('name')
                ->unique()
                ->take(5);

            $creators = collect($data['created_by'] ?? [])->pluck('name')->take(5);
            @endphp

            <div class="pt-6 border-t border-white/10 space-y-8">

                @if($creators->isNotEmpty())
                <div class="leading-relaxed space-y-1">
                    <div class="text-xs text-gray-400 uppercase font-bold tracking-widest">
                        CREADOR
                    </div>
                    <div class="text-base font-medium leading-relaxed">
                        {{ $creators->join(', ') }}
                    </div>
                </div>
                @endif

                @if($directors->isNotEmpty())
                <div class="leading-relaxed space-y-1">
                    <div class="text-xs text-gray-400 uppercase font-bold tracking-widest">
                        DIRECTOR
                    </div>
                    <div class="text-base font-medium leading-relaxed">
                        {{ $directors->join(', ') }}
                    </div>
                </div>
                @endif

                @if($writers->isNotEmpty())
                <div class="leading-relaxed space-y-1">
                    <div class="text-xs text-gray-400 uppercase font-bold tracking-widest">
                        GUION
                    </div>
                    <div class="text-base font-medium leading-relaxed">
                        {{ $writers->join(', ') }}
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>
</div>

<script>
async function toggleVisto(id, type) {

    const btn = document.getElementById('btn-visto');
    const text = document.getElementById('text-visto');
    const icon = document.getElementById('icon-visto');

    const res = await fetch('/visto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tmdb_id: id, type: type })
    });

    const data = await res.json();

    if (data.visto) {
        btn.classList.remove('bg-purple-600', 'hover:bg-purple-500');
        btn.classList.add('bg-green-600', 'hover:bg-green-500');

        text.innerText = "Visto";

        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" 
            d="M5 13l4 4L19 7" />
        `;
    } else {
        btn.classList.remove('bg-green-600', 'hover:bg-green-500');
        btn.classList.add('bg-purple-600', 'hover:bg-purple-500');

        text.innerText = "Marcar como visto";

        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" 
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" 
            d="M2.458 12C3.732 7.943 7.523 5 12 5 
            c4.477 0 8.268 2.943 9.542 7 
            -1.274 4.057-5.065 7-9.542 7 
            -4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}

async function calificar(id, type, valor) {

    const res = await fetch('/calificar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            tmdb_id: id,
            type: type,
            calificacion: valor
        })
    });

    const data = await res.json();

    const botones = {
        me_gusto: document.getElementById('btn-like'),
        neutro: document.getElementById('btn-neutral'),
        no_me_gusto: document.getElementById('btn-dislike')
    };

    Object.values(botones).forEach(btn => {
        btn.className = "px-6 py-2 rounded-lg border border-white/20 transition";
    });

    if (data.calificacion === 'me_gusto') {
        botones.me_gusto.className += " bg-green-600 text-white border-green-600";
    }

    if (data.calificacion === 'neutro') {
        botones.neutro.className += " bg-gray-500 text-white border-gray-500";
    }

    if (data.calificacion === 'no_me_gusto') {
        botones.no_me_gusto.className += " bg-red-600 text-white border-red-600";
    }
}
</script>

</x-app-layout>