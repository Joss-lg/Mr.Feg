{{-- resources/views/admin/categorias/modal-eliminar.blade.php --}}
{{-- IMPORTANTE: Solo dice "hidden items-center", NO "hidden flex" --}}
<div id="modalEliminar" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-all duration-300">
    
    {{-- Capa trasera para cerrar si hacen click fuera --}}
    <div class="fixed inset-0 bg-transparent" onclick="closeDeleteModal()"></div>
    
    {{-- Contenedor de Alerta Crítica --}}
    <div id="deleteContainer" class="relative bg-white rounded-[2rem] w-full max-w-sm mx-auto shadow-2xl scale-95 opacity-0 transition-all duration-300 border border-slate-200 overflow-hidden flex flex-col">

        {{-- Icono de Advertencia Destacado y Mensaje --}}
        <div class="p-6 sm:p-8 text-center space-y-5">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl bg-rose-50 flex items-center justify-center mx-auto border border-rose-100 shadow-sm shrink-0">
                <i class="far fa-trash-alt text-rose-500 text-2xl sm:text-3xl"></i>
            </div>
            
            <div class="space-y-2">
                <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">¿Eliminar categoría?</h2>
                <p class="text-xs sm:text-sm font-medium text-slate-500 leading-relaxed px-2 break-words">
                    Vas a eliminar permanentemente la categoría <span id="delete_nombre_display" class="text-slate-900 font-black underline decoration-rose-500/40 decoration-2"></span>. Esta acción no se puede revertir.
                </p>
            </div>
        </div>

        {{-- Formulario con Botones Balanceados --}}
        <form id="formEliminar" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            @method('DELETE')
            
            <div class="flex flex-col-reverse sm:flex-row items-center gap-3 px-6 sm:px-8 pb-6 sm:pb-8 pt-6 border-t border-slate-100">
                {{-- Botón Cancelar (Neutro) --}}
                <button type="button" onclick="closeDeleteModal()"
                    class="w-full sm:flex-1 h-12 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-500 hover:text-slate-800 font-black text-[10px] sm:text-[11px] uppercase tracking-widest rounded-2xl transition-all outline-none cursor-pointer">
                    Cancelar
                </button>
                
                {{-- Botón de Destrucción (Peligro) --}}
                <button type="submit"
                    class="w-full sm:flex-1 h-12 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-black text-[10px] sm:text-[11px] uppercase tracking-widest rounded-2xl transition-all shadow-md shadow-rose-500/20 outline-none cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Sí, eliminar
                </button>
            </div>
        </form>
    </div>
</div>