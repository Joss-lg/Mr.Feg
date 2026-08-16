{{-- resources/views/admin/promociones/modal-eliminar.blade.php --}}
<div id="modalEliminar" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-all duration-300 p-4">
    {{-- Capa trasera para cerrar si hacen clic fuera --}}
    <div class="fixed inset-0 bg-transparent" onclick="window.closeDeleteModal()"></div>

    <div id="deleteContainer" class="relative bg-white border border-slate-200 rounded-[2rem] w-full max-w-md shadow-2xl scale-95 opacity-0 transition-all duration-300 overflow-hidden flex flex-col">

        {{-- Contenido del Aviso --}}
        <div class="p-6 sm:p-8 text-center space-y-4">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl bg-rose-50 border border-rose-100 flex items-center justify-center mx-auto shadow-sm shrink-0">
                <i class="far fa-trash-alt text-rose-500 text-2xl sm:text-3xl"></i>
            </div>
            <div class="space-y-2">
                <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">¿Eliminar promoción?</h2>
                <p class="text-xs sm:text-sm font-medium text-slate-500 leading-relaxed px-2 break-words">
                    Vas a eliminar permanentemente la oferta <span id="delete_nombre_display" class="text-slate-900 font-black underline decoration-rose-500/40 decoration-2"></span> del sistema. Esta acción no se puede revertir.
                </p>
            </div>
        </div>

        {{-- Formulario de Acción --}}
        <form id="formEliminar" method="POST" action="" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            @method('DELETE')
            
            <div class="flex flex-col-reverse sm:flex-row items-center gap-3 px-6 sm:px-8 pb-6 sm:pb-8 pt-6 border-t border-slate-100">
                <button type="button" onclick="window.closeDeleteModal()"
                    class="w-full sm:flex-1 h-12 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-500 hover:text-slate-800 font-black text-[10px] sm:text-[11px] uppercase tracking-widest rounded-2xl transition-all outline-none cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                    class="w-full sm:flex-1 h-12 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-black text-[10px] sm:text-[11px] uppercase tracking-widest rounded-2xl transition-all shadow-md shadow-rose-500/20 outline-none cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Sí, eliminar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof window.openDeleteModal === 'undefined') {
        window.openDeleteModal = function(id, nombre) {
            const modal = document.getElementById('modalEliminar');
            const container = document.getElementById('deleteContainer');
            const form = document.getElementById('formEliminar');
            const txtNombre = document.getElementById('delete_nombre_display');

            if (!modal || !container) return;

            // Configurar la ruta correcta con el prefijo /admin/
            form.action = "{{ url('admin/promociones') }}/" + id;
            if (txtNombre) txtNombre.textContent = `"${nombre}"`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 15);
        };

        window.closeDeleteModal = function() {
            const modal = document.getElementById('modalEliminar');
            const container = document.getElementById('deleteContainer');

            if (!modal || !container) return;

            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        };
    }
</script>