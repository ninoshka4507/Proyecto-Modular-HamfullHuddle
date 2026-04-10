<x-app-layout :pageTitle="'Opciones'">

    <div class="max-w-4xl mx-auto py-12 px-6 mb-20 text-white">
        
        <h1 class="text-3xl md:text-4xl font-extrabold mb-8 border-l-4 border-[#9F7CFF] pl-4">
            Opciones de Cuenta
        </h1>

        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-2xl mb-8">
            <h3 class="text-2xl font-bold mb-2">Cambiar Contraseña</h3>
            <p class="text-gray-400 text-sm mb-6">Asegúrate de usar una contraseña segura. Al cambiarla, tu sesión se cerrará automáticamente por seguridad.</p>
            
            <form method="post" action="{{ route('password.update') }}" class="space-y-5 max-w-xl">
                @csrf
                @method('put')

                <div>
                    <label class="block text-xs font-bold text-[#B094FF] uppercase mb-1">Contraseña Actual</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required 
                            class="w-full bg-white/5 border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#9F7CFF] transition-colors pr-12 
                            @error('current_password', 'updatePassword') border-red-500 @else border-white/10 @enderror">
                        
                        <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-[#9F7CFF] transition toggle-password" data-target="current_password">
                            <svg class="w-5 h-5 eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            <svg class="w-5 h-5 eye-open hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </div>
                    @error('current_password', 'updatePassword')
                        <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#B094FF] uppercase mb-1">Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" name="password" id="new_password" required 
                                class="w-full bg-white/5 border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#9F7CFF] transition-colors pr-12
                                @error('password', 'updatePassword') border-red-500 @else border-white/10 @enderror">
                            
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-[#9F7CFF] transition toggle-password" data-target="new_password">
                                <svg class="w-5 h-5 eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                <svg class="w-5 h-5 eye-open hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                        @error('password', 'updatePassword')
                            <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#B094FF] uppercase mb-1">Confirmar Nueva</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required 
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#9F7CFF] transition-colors pr-12">
                            
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-[#9F7CFF] transition toggle-password" data-target="password_confirmation">
                                <svg class="w-5 h-5 eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                <svg class="w-5 h-5 eye-open hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <p class="text-xs text-gray-400">Sugerencia: Usa al menos 8 caracteres, un número y un símbolo.</p>

                <div class="pt-2">
                    <button type="submit" class="bg-[#9F7CFF] hover:bg-[#8860f0] text-white font-bold py-3 px-6 rounded-xl transition shadow-lg">Actualizar Contraseña</button>
                </div>
            </form>
        </div>

        <div x-data="{ showEmailModal: {{ $errors->has('email') ? 'true' : 'false' }} }" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-2xl mb-8">
            <h3 class="text-2xl font-bold mb-2">Actualizar Correo Electrónico</h3>
            <p class="text-gray-400 text-sm mb-6">Cambia el correo vinculado a tu cuenta. Se requerirá verificación y tu sesión se cerrará.</p>
            
            <div class="max-w-xl">
                <p class="mb-4"><strong>Correo actual:</strong> <span class="text-[#00E5FF]">{{ auth()->user()->email }}</span></p>
                <button @click="showEmailModal = true" type="button" class="border border-[#00E5FF] text-[#00E5FF] hover:bg-[#00E5FF]/10 font-bold py-3 px-6 rounded-xl transition">
                    Cambiar mi correo
                </button>
            </div>

            <template x-teleport="body">
                <div x-show="showEmailModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                    <div @click.away="showEmailModal = false" class="bg-[#1a103d] border border-white/10 rounded-3xl p-8 w-full max-w-md shadow-2xl">
                        <h3 class="text-xl font-bold mb-4">Nuevo Correo Electrónico</h3>
                        <form method="post" action="{{ route('profile.update_email') }}" class="space-y-4">
                            @csrf
                            @method('patch')
                            
                            <div>
                                <label class="block text-xs font-bold text-[#00E5FF] uppercase mb-1">Nuevo Correo</label>
                                <input type="email" name="email" value="{{ old('email') }}" required 
                                    class="w-full bg-black/30 border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#00E5FF] transition-colors
                                    @error('email') border-red-500 @else border-white/10 @enderror">
                                
                                @error('email')
                                    <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#00E5FF] uppercase mb-1">Confirmar Nuevo Correo</label>
                                <input type="email" name="email_confirmation" required 
                                    class="w-full bg-black/30 border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#00E5FF] transition-colors
                                    @error('email') border-red-500 @else border-white/10 @enderror">
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="showEmailModal = false" class="px-5 py-2 rounded-xl text-gray-400 hover:text-white transition">Cancelar</button>
                                <button type="submit" class="bg-[#00E5FF] text-black font-bold px-5 py-2 rounded-xl hover:bg-[#00c8e6] transition">Confirmar y Cerrar Sesión</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

        <div x-data="{ showResetModal: false }" class="bg-orange-500/10 border border-orange-500/30 rounded-3xl p-8 shadow-2xl mb-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-orange-500"></div>
            <h3 class="text-2xl font-bold text-orange-400 mb-2">Reiniciar Datos (Modo Nuevo Usuario)</h3>
            <p class="text-gray-300 text-sm mb-6">Esta acción borrará todas tus listas, calificaciones, historial y géneros favoritos. Volverás a empezar de cero.</p>
            
            <button @click="showResetModal = true" type="button" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg">
                Limpiar mis datos
            </button>

            <template x-teleport="body">
                <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                    <div @click.away="showResetModal = false" class="bg-[#1a103d] border border-orange-500/30 rounded-3xl p-8 w-full max-w-md shadow-2xl">
                        <h3 class="text-xl font-bold text-orange-400 mb-4">¿Estás seguro?</h3>
                        <p class="text-sm text-gray-300 mb-6">Se eliminarán todas tus interacciones. Se te pedirá que vuelvas a elegir tus géneros favoritos inmediatamente.</p>
                        <form method="post" action="{{ route('profile.reset_data') }}">
                            @csrf
                            @method('delete')
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showResetModal = false" class="px-5 py-2 rounded-xl text-gray-400 hover:text-white transition">Cancelar</button>
                                <button type="submit" class="bg-orange-500 text-white font-bold px-5 py-2 rounded-xl hover:bg-orange-600 transition">Sí, limpiar datos</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

        <div x-data="{ showDeleteModal: {{ $errors->userDeletion->has('password') ? 'true' : 'false' }} }" class="bg-red-500/10 border border-red-500/30 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-red-500"></div>
            <h3 class="text-2xl font-bold text-red-500 mb-2">Zona de Peligro: Eliminar Cuenta</h3>
            <p class="text-gray-300 text-sm mb-6">Una vez eliminada, no hay vuelta atrás. Tu perfil, listas y configuración desaparecerán para siempre y tu nombre de usuario quedará libre.</p>
            
            <button @click="showDeleteModal = true" type="button" class="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold py-3 px-6 rounded-xl transition">
                Eliminar cuenta permanentemente
            </button>

            <template x-teleport="body">
                <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md p-4">
                    <div @click.away="showDeleteModal = false" class="bg-black border border-red-500/50 rounded-3xl p-8 w-full max-w-md shadow-2xl">
                        <h3 class="text-xl font-bold text-red-500 mb-4">Confirmar Eliminación</h3>
                        <p class="text-sm text-gray-400 mb-6">Por favor, ingresa tu contraseña para confirmar que deseas destruir tu cuenta.</p>
                        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                            @csrf
                            @method('delete')
                            <div>
                                <input type="password" name="password" required placeholder="Tu contraseña" 
                                    class="w-full bg-white/5 border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors
                                    @error('password', 'userDeletion') border-red-500 @else border-red-500/30 @enderror">
                                
                                @error('password', 'userDeletion')
                                    <p class="text-red-400 text-xs mt-1 error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="showDeleteModal = false" class="px-5 py-2 rounded-xl text-gray-400 hover:text-white transition">Cancelar</button>
                                <button type="submit" class="bg-red-600 text-white font-bold px-5 py-2 rounded-xl hover:bg-red-700 transition">Destruir Cuenta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

    </div>
    <style>[x-cloak] { display: none !important; }</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('form input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.classList.remove('border-red-500');
                
                if(!this.classList.contains('border-white/10') && !this.classList.contains('border-white/5') && !this.classList.contains('border-red-500/30')) {
                     this.classList.add('border-white/10');
                }

                const contenedor = this.closest('div').parentElement;
                if(contenedor) {
                    const mensajesError = contenedor.querySelectorAll('.error-message');
                    mensajesError.forEach(m => m.style.display = 'none');
                }
            });
        });

        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = document.getElementById(this.getAttribute('data-target'));
                const iconClosed = this.querySelector('.eye-closed');
                const iconOpen = this.querySelector('.eye-open');
                if (input.type === 'password') {
                    input.type = 'text';
                    iconClosed.classList.add('hidden');
                    iconOpen.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    iconClosed.classList.remove('hidden');
                    iconOpen.classList.add('hidden');
                }
            });
        });
    });
</script>

</x-app-layout>