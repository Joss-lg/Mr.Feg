<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalCrear {
            align-items: flex-start !important;
            padding-top: 10px !important;
        }
        
        body.teclado-virtual-abierto #createContainer {
            max-height: 45dvh !important;
            transform: translateY(0) scale(0.95) !important;
        }
    }
</style>

<div id="modalCrear" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    <div class="fixed inset-0 bg-transparent" onclick="closeCreateModal()"></div>
    
    <div id="createContainer" class="relative bg-white border border-slate-200 w-full max-w-md mx-auto rounded-[2rem] shadow-2xl scale-95 opacity-0 transition-all duration-200 flex flex-col overflow-hidden max-h-[92vh]">

        {{-- Encabezado del Modal --}}
        <div class="flex items-center justify-between p-6 sm:p-8 pb-4 sm:pb-5 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 border border-blue-100">
                    <i class="fas fa-plus text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight leading-tight truncate">Nueva Categoría</h2>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Agregar al menú</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-100 hover:rotate-90 active:scale-95 transition-all duration-300 outline-none shrink-0">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('admin.categorias.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-5 overflow-y-auto flex-1 overscroll-contain">
                
                {{-- Input: Nombre con teclado virtual --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-tag opacity-40"></i> Nombre de Categoría
                    </label>
                    <input type="text" name="nombre" placeholder="Ej: Platos Fuertes..." required data-teclado="texto"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                </div>

                {{-- Dropdown Personalizado: Área de Impresión --}}
                <div class="space-y-2 relative" id="cajaAreaImpresion">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-print opacity-40"></i> Área de Impresión
                    </label>
                    
                    <input type="hidden" name="area_impresion" id="area_impresion_crear" required>

                    <button type="button" onclick="toggleAreaImpresionMenu(event)" id="areaImpresionBtn" 
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                        <span id="areaImpresionSelected" class="text-slate-400 truncate">Seleccionar área...</span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                    </button>
                    
                    <div id="areaImpresionMenu" class="absolute left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto">
                        <button type="button" onclick="selectAreaImpresion('Cocina', 'Cocina / Calientes')" 
                            class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                            Cocina / Calientes
                        </button>
                        <button type="button" onclick="selectAreaImpresion('Barra', 'Barra / Bebidas')" 
                            class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                            Barra / Bebidas
                        </button>
                    </div>
                </div>

                {{-- Selector de Color --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-palette opacity-40"></i> Color
                    </label>
                    <div class="flex items-center gap-3 w-full h-12 bg-white border border-slate-200 rounded-xl px-3 shadow-sm focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
                        <input type="color" name="color" value="#3B82F6" class="w-8 h-8 rounded-lg cursor-pointer border-0 bg-transparent outline-none p-0">
                        <span class="text-sm text-slate-500 font-semibold">Elige un color visual</span>
                    </div>
                </div>

            </div>

            {{-- Botones Footer --}}
            <div class="flex items-center gap-4 px-6 sm:px-8 py-6 border-t border-slate-200 shrink-0 bg-slate-50">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition-colors outline-none">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-[1.5] h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all active:scale-95 outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mueve el modal fuera del layout directamente al body
        const modal = document.getElementById('modalCrear');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        if (typeof TecladoVirtual !== 'undefined') {
            TecladoVirtual.attachAll();
        }
    });

    // --- Funciones para el Select Custom ---
    function toggleAreaImpresionMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('areaImpresionMenu');
        menu.classList.toggle('hidden');
    }

    function selectAreaImpresion(value, text) {
        const selectedSpan = document.getElementById('areaImpresionSelected');
        selectedSpan.innerText = text;
        selectedSpan.classList.remove('text-slate-400');
        selectedSpan.classList.add('text-slate-800');
        
        document.getElementById('area_impresion_crear').value = value;
        
        const menu = document.getElementById('areaImpresionMenu');
        if (menu) menu.classList.add('hidden');
    }

    // Cerrar el dropdown al hacer clic fuera
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('areaImpresionMenu');
        const btn = document.getElementById('areaImpresionBtn');
        if (menu && !menu.classList.contains('hidden')) {
            if (!btn.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        }
    });
</script>