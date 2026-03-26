<x-app-layout :pageTitle="$pageTitle">

<div x-data="{ tab: 'me_gusto' }" class="max-w-7xl mx-auto px-6 mt-12 mb-20">

    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-8 border-l-4 border-[#00E5FF] pl-4">
        Mis listas
    </h1>

    <div class="flex gap-6 sm:gap-10 border-b border-white/10 mb-10 overflow-x-auto pb-1 custom-scrollbar">

        <button 
            @click="tab = 'me_gusto'"
            :class="tab === 'me_gusto' ? 'text-[#00E5FF] border-b-2 border-[#00E5FF]' : 'text-[#B094FF] hover:text-white border-b-2 border-transparent'"
            class="pb-3 font-semibold text-lg whitespace-nowrap transition-colors duration-300">
            Me gustó
        </button>

        <button 
            @click="tab = 'neutro'"
            :class="tab === 'neutro' ? 'text-[#00E5FF] border-b-2 border-[#00E5FF]' : 'text-[#B094FF] hover:text-white border-b-2 border-transparent'"
            class="pb-3 font-semibold text-lg whitespace-nowrap transition-colors duration-300">
            Regular
        </button>

        <button 
            @click="tab = 'no_me_gusto'"
            :class="tab === 'no_me_gusto' ? 'text-[#00E5FF] border-b-2 border-[#00E5FF]' : 'text-[#B094FF] hover:text-white border-b-2 border-transparent'"
            class="pb-3 font-semibold text-lg whitespace-nowrap transition-colors duration-300">
            No me gustó
        </button>

        <button 
            @click="tab = 'vistas'"
            :class="tab === 'vistas' ? 'text-[#00E5FF] border-b-2 border-[#00E5FF]' : 'text-[#B094FF] hover:text-white border-b-2 border-transparent'"
            class="pb-3 font-semibold text-lg whitespace-nowrap transition-colors duration-300">
            Historial (Vistas)
        </button>

    </div>

    <div class="min-h-[400px]">

        <div x-show="tab === 'me_gusto'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @if(!empty($listas['me_gusto']) && count($listas['me_gusto']) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                    @foreach($listas['me_gusto'] as $item)
                        @include('components.movie-card', ['item' => $item])
                    @endforeach
                </div>
            @else
                @include('components.empty-state', ['mensaje' => 'No has marcado ninguna película o serie que te haya gustado aún.'])
            @endif
        </div>

        <div x-show="tab === 'neutro'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @if(!empty($listas['neutro']) && count($listas['neutro']) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                    @foreach($listas['neutro'] as $item)
                        @include('components.movie-card', ['item' => $item])
                    @endforeach
                </div>
            @else
                @include('components.empty-state', ['mensaje' => 'No tienes películas o series en la lista de regulares.'])
            @endif
        </div>

        <div x-show="tab === 'no_me_gusto'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @if(!empty($listas['no_me_gusto']) && count($listas['no_me_gusto']) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                    @foreach($listas['no_me_gusto'] as $item)
                        @include('components.movie-card', ['item' => $item])
                    @endforeach
                </div>
            @else
                @include('components.empty-state', ['mensaje' => '¡Qué bien! Aún no hay contenido que no te haya gustado.'])
            @endif
        </div>

        <div x-show="tab === 'vistas'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @if(!empty($listas['vistas']) && count($listas['vistas']) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                    @foreach($listas['vistas'] as $item)
                        @include('components.movie-card', ['item' => $item])
                    @endforeach
                </div>
            @else
                @include('components.empty-state', ['mensaje' => 'Aún no tienes historial de reproducciones. ¡Explora contenido nuevo!'])
            @endif
        </div>

    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>

</x-app-layout>