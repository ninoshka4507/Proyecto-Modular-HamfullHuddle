<x-app-layout>
    
    <x-slot name="pageTitle">
        Mi Cuenta
    </x-slot>
    <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            
            <div class="flex items-center space-x-6 mb-8">
                <img class="h-24 w-24 rounded-full object-cover border-4 border-purple-200" 
                     src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.$user->nombre_usuario }}" 
                     alt="{{ $user->nombre_usuario }}">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $user->nombre_usuario }}</h2>
                    <p class="text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            <hr class="my-6 border-gray-100">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 uppercase">Nombre Visible</label>
                    <p class="mt-1 text-lg text-gray-900 bg-gray-50 p-2 rounded">{{ $user->nombre_visible ?? 'No definido' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 uppercase">Miembro desde</label>
                    <p class="mt-1 text-lg text-gray-900 bg-gray-50 p-2 rounded">{{ $user->created_at->format('d M, Y') }}</p>
                </div>
            </div>

            <div class="mt-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-purple-900">Mis Géneros Favoritos</h3>
                    <a href="{{ route('genres.select') }}" class="text-sm font-bold text-purple-600 hover:text-purple-800 underline">
                        Editar preferencias
                    </a>
                </div>

                <div class="flex flex-wrap gap-3">
                    @forelse($generosSeleccionados as $genero)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200">
                            {{ $genero->nombre }}
                        </span>
                    @empty
                        <p class="text-gray-400 italic">Aún no has seleccionado géneros.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>