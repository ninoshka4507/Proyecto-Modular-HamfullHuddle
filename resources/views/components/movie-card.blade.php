<a href="/view/{{ $item['id'] }}/{{ $item['type'] }}" class="group block cursor-pointer">
    <div class="relative overflow-hidden rounded-xl shadow-lg transition-transform duration-300 transform group-hover:scale-105 group-hover:shadow-[0_0_20px_rgba(176,148,255,0.2)]">
        
        <img src="https://image.tmdb.org/t/p/w500{{ $item['poster'] }}" 
             alt="{{ $item['title'] }}" 
             class="w-full h-auto object-cover aspect-[2/3]">

        <div class="absolute inset-0 bg-gradient-to-t from-[#1a103d] via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
            <button class="bg-white/20 backdrop-blur-md text-white border border-white/50 font-bold py-2 rounded-lg text-sm mb-2 hover:bg-white hover:text-purple-900 transition">
                Ver Detalles
            </button>
        </div>
    </div>

    <h3 class="mt-3 font-semibold text-[#B094FF] truncate group-hover:text-[#00E5FF] transition-colors">
        {{ $item['title'] }}
    </h3>

    <div class="text-[#B094FF] text-xs font-semibold flex items-center gap-1 mt-1">
        <span>{{ substr($item['year'] ?? '', 0, 4) }}</span>
    </div>
</a>