{{-- modal-eliminar.blade.php --}}
<div id="modalEliminar" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    
    <div class="relative bg-white border border-slate-200 w-full max-w-sm rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-500 scale-95 opacity-0 flex flex-col max-h-[95dvh]" id="deleteContainer">
        
        <div class="p-6 sm:p-10 text-center space-y-5 sm:space-y-6 overflow-y-auto hide-scroll" style="-webkit-overflow-scrolling: touch;">
            
            <div class="mx-auto w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl bg-rose-50 flex items-center justify-center text-rose-500 border border-rose-100 shadow-sm shrink-0">
                <i class="far fa-trash-alt text-2xl sm:text-3xl"></i>
            </div>

            <div class="space-y-2">
                <h3 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">¿Eliminar Insumo?</h3>
                <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed px-2 sm:px-4 break-words">
                    Estás a punto de borrar <span id="delete_nombre_display" class="text-slate-900 font-black"></span>. Esta acción no se puede deshacer.
                </p>
            </div>

            {{-- Formulario para eliminar (Action se llena dinámicamente con JS) --}}
            <form id="formEliminar" action="" method="POST" class="flex flex-col gap-2.5 sm:gap-3 pt-2 sm:pt-4">
                @csrf
                @method('DELETE')
                
                <button type="submit" class="w-full h-12 sm:h-14 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-[10px] sm:text-[11px] font-black uppercase tracking-[0.2em] shadow-md shadow-rose-500/20 active:scale-95 transition-all outline-none">
                    Confirmar Eliminación
                </button>
            </form>

            <button type="button" onclick="closeDeleteModal()" class="w-full h-12 sm:h-14 rounded-2xl text-[10px] sm:text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-800 hover:bg-slate-50 transition-all outline-none">
                No, mantenerlo
            </button>
        </div>
    </div>
</div>