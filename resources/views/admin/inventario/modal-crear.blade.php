{{-- Estilos para manejo de teclado virtual en PC --}}
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

<div id="modalCrear" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    
    <div id="createContainer" class="bg-white border border-slate-200 w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-500 scale-95 opacity-0 flex flex-col max-h-[95dvh] sm:max-h-[92dvh]">
        
        <div class="p-6 sm:p-8 pb-4 shrink-0 flex justify-between items-center border-b border-slate-100">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shrink-0">
                    <i class="fas fa-layer-group text-lg sm:text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight uppercase m-0 leading-tight truncate">Nuevo Artículo</h3>
                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] mt-1">Añadir al Inventario</p>
                </div>
            </div>
            
            <button type="button" onclick="closeCreateModal()" class="w-10 h-10 rounded-xl flex items-center justify-center bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all outline-none shrink-0 border border-slate-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.inventario.store') }}" method="POST" class="flex flex-col flex-1 min-h-0">
            @csrf

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-5 overflow-y-auto flex-1 overscroll-contain">

                {{-- Campo Nombre --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-tag opacity-40"></i> Nombre del Artículo
                    </label>
                    <input type="text" name="nombre" required data-teclado="texto"
                        class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all placeholder:text-slate-400"
                        placeholder="Ej: Harina">
                </div>
                
                {{-- Selects --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-folder opacity-40"></i> Categoría
                    </label>
                    <select name="categoria_id" required class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                        <option value="">Selecciona una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-scale-balanced opacity-40"></i> Unidad de Medida
                    </label>
                    <select name="unidad_medida" required class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                        <option value="g">Gramos (g)</option>
                        <option value="ml">Mililitros (ml)</option>
                        <option value="pz">Piezas (pz)</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Campo Precio Compra --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                            <i class="fas fa-dollar-sign opacity-40"></i> Precio Compra
                        </label>
                        <input type="text" name="precio_compra" data-teclado="numerico" data-teclado-decimales="true"
                            class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-black text-slate-800 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400"
                            placeholder="0.00">
                    </div>
                    {{-- Campo Stock Mínimo --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                            <i class="fas fa-bell opacity-40"></i> Stock Mínimo
                        </label>
                        <input type="text" name="stock_minimo" required data-teclado="numerico"
                            class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400"
                            placeholder="0">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 px-6 sm:px-8 py-6 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-800 hover:bg-slate-50 transition-all outline-none">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-[1.5] h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-600/20 transition-all active:scale-95 outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>