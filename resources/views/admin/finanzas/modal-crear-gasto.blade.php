{{-- Estilos para manejo de teclado virtual en PC --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalCrearGasto {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        
        body.teclado-virtual-abierto #createGastoContainer {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important; 
        }
    }
</style>

<div id="modalCrearGasto" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-opacity duration-300">
    
    <div id="createGastoContainer" class="bg-slate-50 border border-slate-200 w-full max-w-md rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[92dvh]">

        {{-- CABECERA MODAL --}}
        <div class="px-6 pt-6 pb-4 flex justify-between items-start flex-shrink-0">
            <div>
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-2 tracking-tight">
                    Nuevo Gasto <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
                </h3>
                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">Registrar egreso del sistema</p>
            </div>
            <button type="button" onclick="closeModal('modalCrearGasto', 'createGastoContainer')" class="text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all duration-300 w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:border-rose-200 hover:bg-rose-50 outline-none flex-shrink-0 shadow-sm active:scale-95">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.gastos.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf

            <div class="px-6 pb-6 space-y-4 overflow-y-auto flex-1 overscroll-contain scrollbar-thin" style="-webkit-overflow-scrolling: touch;">

                {{-- Campo Concepto --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Concepto</label>
                    <div class="relative group">
                        <i class="fas fa-pen absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="text" name="concepto" required data-teclado="texto"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 text-sm font-semibold text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder:text-slate-400"
                            placeholder="Ej. Compra de insumos">
                    </div>
                </div>

                {{-- Categoria --}}
                <div class="relative">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Categoría</label>
                    <input type="hidden" name="categoria" id="gasto_categoria_input" required>
                    <button type="button" onclick="toggleDropdown('gastoCategoriaMenu', event)" 
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-folder text-slate-400 text-sm"></i>
                            <span id="gastoCategoriaSelected" class="text-slate-400">Seleccionar categoría...</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </button>
                    <div id="gastoCategoriaMenu" class="absolute w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-2">
                        @foreach(['Compra Insumos', 'Servicios', 'Renta', 'Mantenimiento', 'Otro'] as $cat)
                            <button type="button" onclick="selectGastoOption('gastoCategoriaSelected', 'gasto_categoria_input', '{{ $cat }}', '{{ $cat }}')" 
                                class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Monto --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Monto</label>
                    <div class="relative group">
                        <i class="fas fa-dollar-sign absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="text" name="monto" required data-teclado="numerico" data-teclado-decimales="true"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 text-sm font-black text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder:text-slate-400"
                            placeholder="0.00">
                    </div>
                </div>

                {{-- Metodo Pago --}}
                <div class="relative">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Método de Pago</label>
                    <input type="hidden" name="metodo_pago" id="gasto_metodo_input" required>
                    <button type="button" onclick="toggleDropdown('gastoMetodoMenu', event)" 
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-credit-card text-slate-400 text-sm"></i>
                            <span id="gastoMetodoSelected" class="text-slate-400">Seleccionar método...</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </button>
                    <div id="gastoMetodoMenu" class="absolute w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-2">
                        @foreach(['Efectivo', 'Tarjeta', 'Transferencia'] as $metodo)
                            <button type="button" onclick="selectGastoOption('gastoMetodoSelected', 'gasto_metodo_input', '{{ $metodo }}', '{{ $metodo }}')" 
                                class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                                {{ $metodo }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Estado --}}
                <div class="relative">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Estado del Pago</label>
                    <input type="hidden" name="estado" id="gasto_estado_input" value="pagado" required>
                    <button type="button" onclick="toggleDropdown('gastoEstadoMenu', event)" 
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-slate-400 text-sm"></i>
                            <span id="gastoEstadoSelected" class="text-slate-800">Pagado (afecta caja hoy)</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </button>
                    <div id="gastoEstadoMenu" class="absolute w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-2">
                        <button type="button" onclick="selectGastoOption('gastoEstadoSelected', 'gasto_estado_input', 'pagado', 'Pagado (afecta caja hoy)')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Pagado (afecta caja hoy)</button>
                        <button type="button" onclick="selectGastoOption('gastoEstadoSelected', 'gasto_estado_input', 'pendiente', 'Pendiente de pago')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Pendiente de pago</button>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-100/50 flex items-center justify-between flex-shrink-0" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
                <button type="button" onclick="closeModal('modalCrearGasto', 'createGastoContainer')" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors outline-none active:scale-95">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 outline-none">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Movemos el modal al final del body para evitar problemas de Z-index
        const modalElement = document.getElementById('modalCrearGasto');
        if (modalElement) document.body.appendChild(modalElement);
        
        if (typeof TecladoVirtual !== 'undefined') TecladoVirtual.attachAll();
    });

    // Función auxiliar minimalista para actualizar UI de selects
    function selectGastoOption(labelId, inputId, valor, texto) {
        const label = document.getElementById(labelId);
        label.innerText = texto;
        label.classList.remove('text-slate-400');
        label.classList.add('text-slate-800');
        document.getElementById(inputId).value = valor;
    }
</script>