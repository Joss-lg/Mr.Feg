{{-- Estilos para manejo de teclado virtual en PC --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalCrearRol {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        
        body.teclado-virtual-abierto #modalCrearContent {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important; 
        }
    }
</style>

<div id="modalCrearRol" class="fixed inset-0 z-[99999] hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all duration-300">
    
    {{-- Tarjeta Estilo Soft Light --}}
    <div class="bg-[#F2F2F2] border border-slate-200 w-full max-w-md rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[88dvh] sm:max-h-[90dvh]" id="modalCrearContent">
        
        <form action="{{ route('admin.roles.store') }}" method="POST" class="flex flex-col h-full relative z-10 bg-[#F2F2F2]">
            @csrf
            
            {{-- Encabezado con X animada --}}
            <div class="px-6 pt-6 pb-2 flex justify-between items-start gap-3 flex-shrink-0">
                <div>
                    <h3 class="text-xl font-black text-slate-800 flex items-center gap-2 tracking-tight">
                        Crear Rol <i class="fas fa-user-tag text-blue-600 text-base"></i>
                    </h3>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">Crea nuevo rol</p>
                </div>
                <button type="button" onclick="cerrarModalCrear()" class="text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all duration-300 w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:border-rose-200 hover:bg-rose-50 outline-none flex-shrink-0 shadow-sm active:scale-95">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Cuerpo del modal --}}
            <div class="p-6 flex-1 overflow-y-auto space-y-4 overscroll-contain scrollbar-thin" style="-webkit-overflow-scrolling: touch;">
                
                <div>
                    <label for="nombre" class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Nombre del Rol</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-briefcase text-slate-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                        </div>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Cosinero" data-teclado="texto"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 text-sm font-semibold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder:text-slate-400 shadow-sm">
                    </div>
                    @error('nombre') <span class="text-[10px] text-rose-500 mt-2 block font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Pie del modal --}}
            <div class="px-6 py-5 border-t border-slate-200/60 bg-slate-100/50 flex items-center justify-between flex-shrink-0" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
                <button type="button" onclick="cerrarModalCrear()" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors outline-none active:scale-95">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 outline-none">
                    Guardar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mueve el modal al body para asegurar que cubra toda la pantalla
        const modal = document.getElementById('modalCrearRol');
        if (modal) document.body.appendChild(modal);

        if (typeof TecladoVirtual !== 'undefined') {
            TecladoVirtual.attachAll();
        }
    });

    window.abrirModalCrear = function() {
        const modal = document.getElementById('modalCrearRol');
        const content = document.getElementById('modalCrearContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    window.cerrarModalCrear = function() {
        const modal = document.getElementById('modalCrearRol');
        const content = document.getElementById('modalCrearContent');
        
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>