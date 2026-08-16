{{-- MODAL: Eliminar Cliente --}}
<div id="modalEliminarCliente" class="hidden fixed inset-0 z-[60] items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div id="eliminarClienteModalContainer" class="bg-white border border-slate-200 rounded-[2rem] shadow-xl w-full max-w-md p-6 sm:p-8 scale-95 opacity-0 transition-all duration-200">

        {{-- Icono de advertencia --}}
        <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 shadow-sm mb-5">
            <i class="fas fa-triangle-exclamation text-2xl"></i>
        </div>

        <div class="text-center space-y-2 mb-8">
            <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight">¿Eliminar cliente?</h2>
            <p class="text-xs sm:text-sm font-medium text-slate-500 leading-relaxed">
                Estás por eliminar a <span id="delete_cliente_nombre_display" class="font-black text-slate-800"></span>.
                Toda su información y direcciones registradas se eliminarán de forma permanente. Esta acción no se puede deshacer.
            </p>
        </div>

        <form id="formEliminarCliente" method="POST" class="flex items-center justify-end gap-3">
            @csrf
            @method('DELETE')

            <button type="button" onclick="window.closeDeleteModalCliente()"
                class="px-5 h-12 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all flex items-center justify-center outline-none">
                Cancelar
            </button>
            <button type="submit"
                class="bg-rose-600 hover:bg-rose-700 text-white px-6 h-12 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-rose-500/20 transition-all active:scale-95 flex items-center justify-center gap-2 outline-none">
                <i class="fas fa-trash-alt"></i> Sí, eliminar
            </button>
        </form>
    </div>
</div>