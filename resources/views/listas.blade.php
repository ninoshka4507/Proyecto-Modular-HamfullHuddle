<x-app-layout :pageTitle="$pageTitle">

<div x-data="{ tab: 'me_gusto' }" class="px-12 py-10 min-h-screen text-gray-900">

    <h1 class="text-4xl font-bold mb-6">Mis listas</h1>

    <div class="flex gap-8 border-b border-gray-200 mb-10 overflow-x-auto">

        <button 
            @click="tab = 'me_gusto'"
            :class="tab === 'me_gusto' ? 'text-gray-900 border-b-2 border-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="pb-3 font-semibold whitespace-nowrap transition">
            Me gustó
        </button>

        <button 
            @click="tab = 'neutro'"
            :class="tab === 'neutro' ? 'text-gray-900 border-b-2 border-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="pb-3 font-semibold whitespace-nowrap transition">
            Neutro
        </button>

        <button 
            @click="tab = 'no_me_gusto'"
            :class="tab === 'no_me_gusto' ? 'text-gray-900 border-b-2 border-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="pb-3 font-semibold whitespace-nowrap transition">
            No me gustó
        </button>

        <button 
            @click="tab = 'vistas'"
            :class="tab === 'vistas' ? 'text-gray-900 border-b-2 border-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="pb-3 font-semibold whitespace-nowrap transition">
            Vistas
        </button>

    </div>

    <div>

        <div x-show="tab === 'me_gusto'">
            @if(!empty($listas['me_gusto']))
                <div class="flex gap-4 overflow-x-auto scroll-container pb-4">

                    @foreach($listas['me_gusto'] as $item)

                        <a href="/view/{{ $item['id'] }}/{{ $item['type'] }}" 
                        class="group w-[150px] flex-shrink-0">

                            <div class="overflow-hidden rounded-lg shadow">

                                <img 
                                    src="https://image.tmdb.org/t/p/w500{{ $item['poster'] }}"
                                    class="w-full h-[225px] object-cover 
                                        transition duration-300 
                                        group-hover:scale-110">
                            </div>

                            <div class="mt-2">

                                <div class="text-sm font-semibold truncate 
                                            group-hover:text-purple-600 transition">
                                    {{ $item['title'] }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ substr($item['year'] ?? '', 0, 4) }}
                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>
            @else
                <p class="text-gray-500">No tienes contenido aquí aún.</p>
            @endif
        </div>

        <div x-show="tab === 'neutro'">
            @if(!empty($listas['neutro']))
                <div class="flex gap-4 overflow-x-auto scroll-container pb-4">
                    @foreach($listas['neutro'] as $item)
                        <a href="/view/{{ $item['id'] }}/{{ $item['type'] }}" 
                            class="group w-[150px] flex-shrink-0">

                                <div class="overflow-hidden rounded-lg shadow">

                                    <img 
                                        src="https://image.tmdb.org/t/p/w500{{ $item['poster'] }}"
                                        class="w-full h-[225px] object-cover 
                                            transition duration-300 
                                            group-hover:scale-110">
                                </div>

                                <div class="mt-2">

                                    <div class="text-sm font-semibold truncate 
                                                group-hover:text-purple-600 transition">
                                        {{ $item['title'] }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ substr($item['year'] ?? '', 0, 4) }}
                                    </div>

                                </div>

                            </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No tienes contenido aquí aún.</p>
            @endif
        </div>

        <div x-show="tab === 'no_me_gusto'">
            @if(!empty($listas['no_me_gusto']))
                <div class="flex gap-4 overflow-x-auto scroll-container pb-4">
                    @foreach($listas['no_me_gusto'] as $item)
                        <a href="/view/{{ $item['id'] }}/{{ $item['type'] }}" 
                            class="group w-[150px] flex-shrink-0">

                                <div class="overflow-hidden rounded-lg shadow">

                                    <img 
                                        src="https://image.tmdb.org/t/p/w500{{ $item['poster'] }}"
                                        class="w-full h-[225px] object-cover 
                                            transition duration-300 
                                            group-hover:scale-110">
                                </div>

                                <div class="mt-2">

                                    <div class="text-sm font-semibold truncate 
                                                group-hover:text-purple-600 transition">
                                        {{ $item['title'] }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ substr($item['year'] ?? '', 0, 4) }}
                                    </div>

                                </div>

                            </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No tienes contenido aquí aún.</p>
            @endif
        </div>

        <div x-show="tab === 'vistas'">
            @if(!empty($listas['vistas']))
                <div class="flex gap-4 overflow-x-auto scroll-container pb-4">
                    @foreach($listas['vistas'] as $item)
                        <a href="/view/{{ $item['id'] }}/{{ $item['type'] }}" 
                            class="group w-[150px] flex-shrink-0">

                                <div class="overflow-hidden rounded-lg shadow">

                                    <img 
                                        src="https://image.tmdb.org/t/p/w500{{ $item['poster'] }}"
                                        class="w-full h-[225px] object-cover 
                                            transition duration-300 
                                            group-hover:scale-110">
                                </div>

                                <div class="mt-2">

                                    <div class="text-sm font-semibold truncate 
                                                group-hover:text-purple-600 transition">
                                        {{ $item['title'] }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ substr($item['year'] ?? '', 0, 4) }}
                                    </div>

                                </div>

                            </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No tienes contenido aquí aún.</p>
            @endif
        </div>

    </div>

</div>
</x-app-layout>