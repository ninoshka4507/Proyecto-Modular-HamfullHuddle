<x-app-layout :pageTitle="$pageTitle">

    <div x-data="{ 
        showAvatarModal: false, 
        avatarUrl: '{{ $user->avatar }}',
        defaultAvatar: 'https://ui-avatars.com/api/?name={{ urlencode($user->nombre_usuario) }}&background=1a103d&color=00E5FF'
    }" class="max-w-4xl mx-auto py-12 px-6 mb-20">
        
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-8 border-l-4 border-[#00E5FF] pl-4">
            Ajustes de la cuenta
        </h1>

        @if(session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 4000)" 
             x-show="show" 
             x-transition.opacity.duration.500ms
             class="bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-2xl mb-8 flex items-center gap-3">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 6000)" 
             x-show="show" 
             x-transition.opacity.duration.500ms
             class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-2xl mb-8">
            <ul class="list-disc pl-5 font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('account.update') }}" method="POST" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-2xl">
            @csrf
            
            <div class="flex flex-col sm:flex-row items-center gap-8 mb-10 pb-10 border-b border-white/10">
                
                <div class="relative group cursor-pointer" @click="showAvatarModal = true">
                    <div class="p-[3px] rounded-full bg-gradient-to-r from-[#7C5CFF] to-[#00E5FF]">
                        <img :src="avatarUrl || defaultAvatar" class="w-28 h-28 rounded-full object-cover">
                    </div>
                    
                    <div class="absolute bottom-1 right-1 bg-[#00E5FF] p-2.5 rounded-full shadow-[0_0_15px_rgba(0,229,255,0.6)] group-hover:scale-110 group-hover:bg-white transition-all duration-300">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                </div>

                <div class="text-center sm:text-left">
                    <h2 class="text-2xl font-bold text-white">{{ $user->nombre_usuario }}</h2>
                    <p class="text-gray-400 mt-1">{{ $user->email }}</p>
                    <p class="text-xs font-semibold text-[#B094FF] mt-2 bg-white/5 inline-block px-3 py-1 rounded-full">
                        Miembro desde el {{ $user->created_at->format('d M, Y') }}
                    </p>
                </div>
            </div>

            <input type="hidden" name="avatar" :value="avatarUrl">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-xs font-bold text-[#B094FF] uppercase tracking-widest mb-2">Nombre Visible (Personalizado)</label>
                    <input type="text" name="nombre_visible" value="{{ old('nombre_visible', $user->nombre_visible) }}" placeholder="¿Cómo quieres que te llamemos?" 
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white focus:outline-none focus:ring-2 focus:ring-[#00E5FF] focus:border-transparent transition-all shadow-inner">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#B094FF] uppercase tracking-widest mb-2">Nombre de Usuario (Login)</label>
                    <input type="text" value="{{ $user->nombre_usuario }}" disabled 
                           class="w-full bg-black/30 border border-white/5 rounded-xl px-4 py-3.5 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-2">El nombre de usuario no se puede cambiar.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-[#00E5FF] text-black font-extrabold rounded-full hover:bg-[#00c8e6] hover:scale-105 transition-all duration-300">
                    Guardar Cambios
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-10 bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-2xl">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h3 class="text-xl font-bold text-white border-l-4 border-[#B094FF] pl-3">Mis Géneros Favoritos</h3>
                <a href="{{ route('genres.select') }}" class="group flex items-center gap-2 text-[#00E5FF] text-sm font-bold hover:text-white transition-colors">
                    Editar preferencias
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="flex flex-wrap gap-3">
                @forelse($generosSeleccionados as $genero)
                    <span class="px-5 py-2 bg-[#B094FF]/10 border border-[#B094FF]/30 text-[#B094FF] rounded-full text-sm font-bold shadow-sm hover:bg-[#B094FF]/20 hover:border-[#B094FF]/50 transition-colors cursor-default">
                        {{ $genero->nombre }}
                    </span>
                @empty
                    <p class="text-gray-500 italic">Aún no has seleccionado géneros.</p>
                @endforelse
            </div>
        </div>

        <div x-show="showAvatarModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="showAvatarModal = false" class="bg-[#1a103d] border border-white/10 rounded-3xl p-6 w-full max-w-lg shadow-2xl transform transition-all"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 translate-y-4" x-transition:enter-end="scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="scale-100 translate-y-0" x-transition:leave-end="scale-95 translate-y-4">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Elige tu Avatar</h3>
                    <button @click="showAvatarModal = false" class="text-gray-400 hover:text-white transition">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <button @click="avatarUrl = ''; showAvatarModal = false" 
                        class="w-full mb-6 py-3 rounded-xl border border-red-500/30 text-red-400 font-bold hover:bg-red-500/10 hover:border-red-500/50 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Quitar foto actual
                </button>

                <div class="grid grid-cols-3 sm:grid-cols-4 gap-4 max-h-[50vh] overflow-y-auto custom-scrollbar pr-2 pb-2">
                    @php
                        // Avatares gratuitos y modernos predeterminados usando una API abierta
                        $avatares = [
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Destiny',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Jocelyn',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Emery',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Jameson',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Sara',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Eden',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Riley',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Vivian',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Ryker',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Jessica',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Adrian',
                            'https://api.dicebear.com/9.x/thumbs/svg?seed=Sarah',
                        ];
                    @endphp

                    @foreach($avatares as $av)
                        <div @click="avatarUrl = '{{ $av }}'; showAvatarModal = false" 
                             class="cursor-pointer rounded-xl overflow-hidden border-2 transition-all hover:scale-110"
                             :class="avatarUrl === '{{ $av }}' ? 'border-[#00E5FF]' : 'border-transparent'">
                            <img src="{{ $av }}" class="w-full h-auto object-cover bg-white">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

</x-app-layout>