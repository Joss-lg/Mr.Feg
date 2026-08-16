<style>
    /* Ajuste para pantallas grandes */
    @media (min-width: 768px) {
        body.teclado-virtual-abierto .modal-editar-insumo {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        body.teclado-virtual-abierto .modal-editar-insumo-container {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important;
            overflow-y: auto !important;
        }
    }
</style>

{{-- MODAL EDITAR ALIMENTO --}}
<div id="modalEditar-{{ $item->id }}" class="modal-editar-insumo hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4">
    <div id="modalContainer-{{ $item->id }}" class="modal-editar-insumo-container relative bg-white border border-slate-200 rounded-[2rem] w-full max-w-md sm:mx-4 shadow-2xl scale-95 opacity-0 transition-all duration-300 overflow-hidden flex flex-col max-h-[92dvh]">

        {{-- Encabezado --}}
        <div class="flex items-center gap-3 sm:gap-4 p-6 sm:p-8 pb-4 sm:pb-5 shrink-0 border-b border-slate-100">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100">
                <i class="fas fa-pen"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight truncate">Editar Insumo</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 truncate">{{ strtoupper($item->nombre) }}</p>
            </div>
            <button onclick="cerrarModalEspecifico('modalEditar-{{ $item->id }}')"
                class="ml-auto w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 border border-slate-200 transition-all outline-none shrink-0">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.inventario.update', $item->id) }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            @method('PUT')

            {{-- Cuerpo del formulario --}}
            <div class="p-6 sm:p-8 space-y-4 sm:space-y-5 overflow-y-auto flex-1 overscroll-contain">

                {{-- Nombre --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-tag opacity-40"></i> Nombre del Artículo
                    </label>
                    <input type="text" name="nombre" value="{{ $item->nombre }}" required data-teclado="texto" inputmode="none"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                </div>

                {{-- Unidad de Medida --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-scale-balanced opacity-40"></i> Unidad de Medida
                    </label>
                    <select name="unidad_medida" required
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-black text-slate-800 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer shadow-sm">
                        <option value="g" {{ $item->unidad_medida === 'g' ? 'selected' : '' }}>Gramos (g)</option>
                        <option value="ml" {{ $item->unidad_medida === 'ml' ? 'selected' : '' }}>Mililitros (ml)</option>
                        <option value="pz" {{ $item->unidad_medida === 'pz' ? 'selected' : '' }}>Piezas (pz)</option>
                    </select>
                </div>

                {{-- Categoría --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-folder opacity-40"></i> Categoría
                    </label>
                    <select name="categoria_id"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-black text-slate-800 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer shadow-sm">
                        <option value="">Sin categoría</option>
                        @foreach($categorias ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ $item->categoria_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Grid: Stock Actual y Stock Mínimo --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-box opacity-40"></i> Stock Actual
                        </label>
                        <div class="flex items-center h-12 bg-slate-100 border border-slate-200 rounded-xl px-4 cursor-not-allowed">
                            <span class="text-sm font-black text-blue-600 truncate">{{ number_format($item->stock_actual, 2) }}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-bell opacity-40"></i> Stock Mínimo
                        </label>
                        <input type="text" name="stock_minimo" value="{{ $item->stock_minimo }}" pattern="[0-9]*\.?[0-9]*" required data-teclado="numerico" inputmode="none"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-black text-slate-800 focus:border-blue-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Botones Footer --}}
            <div class="flex items-center justify-between px-6 sm:px-8 py-6 border-t border-slate-200 gap-4 shrink-0 bg-slate-50">
                <button type="button" onclick="cerrarModalEspecifico('modalEditar-{{ $item->id }}')"
                    class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition-colors outline-none px-2 py-3">
                    Cancelar
                </button>
                <button type="submit"
                    class="h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl px-8 shadow-lg shadow-emerald-500/20 active:scale-95 transition-all outline-none">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>