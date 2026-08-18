{{-- Estilos para manejo de teclado virtual en PC --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalEliminarRol {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        body.teclado-virtual-abierto #deleteModalContent {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important; 
        }
    }
</style>

<div id="modalEliminarRol" class="fixed inset-0 z-[99999] hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all duration-300">
    
    {{-- Tarjeta Estilo Soft Light --}}
    <div class="bg-[#F2F2F2] border border-slate-200 w-full max-w-md rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[88dvh] sm:max-h-[90dvh]" id="deleteModalContent">
        
        {{-- Header con aviso de peligro --}}
        <div class="p-6 pb-4 border-b border-rose-100 bg-rose-50/50 flex items-start gap-4 shrink-0">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 flex items-center justify-center flex-shrink-0 text-rose-600 shadow-sm">
                <i class="fas fa-exclamation-triangle text-lg"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">¿Eliminar Rol?</h3>
                <p class="text-[9px] text-rose-600 font-bold uppercase tracking-widest mt-1">Esta acción es irreversible</p>
            </div>
        </div>

        {{-- Cuerpo del modal --}}
        <div class="p-6 flex-1 overflow-y-auto space-y-4 overscroll-contain scrollbar-thin" style="-webkit-overflow-scrolling: touch;">
            <p class="text-sm text-slate-600 font-medium leading-relaxed">
                Estás a punto de eliminar el rol 
                <span class="font-black text-slate-900 px-2.5 py-1 rounded-lg bg-white border border-slate-200 inline-block my-1 shadow-sm break-words" id="nombreRolEliminar"></span>.
            </p>
            
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/60 shadow-sm">
                <p class="text-xs text-amber-800 font-bold flex items-start gap-2.5 leading-relaxed">
                    <i class="fas fa-info-circle mt-0.5 text-amber-600 text-sm"></i>
                    Asegúrate de que no haya empleados activos asignados a este rol antes de proceder.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-5 border-t border-slate-200/60 bg-slate-100/50 flex flex-col-reverse sm:flex-row sm:justify-between items-center gap-3 shrink-0" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
            <button type="button" onclick="cerrarModalEliminar()" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors outline-none active:scale-95">
                Cancelar
            </button>
            
            <form id="formEliminarRol" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white px-6 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-rose-500/20 hover:shadow-lg transition-all active:scale-95 outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt text-xs"></i> Confirmar Eliminación
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    window.abrirModalEliminar = function(btn) {
        const modal = document.getElementById('modalEliminarRol');
        const content = document.getElementById('deleteModalContent');
        
        // Asignar acción al formulario y nombre al span
        const form = document.getElementById('formEliminarRol');
        form.action = `{{ url('admin/roles') }}/${btn.getAttribute('data-id')}`;
        
        // Asigna el nombre correctamente
        document.getElementById('nombreRolEliminar').innerText = btn.getAttribute('data-nombre') || 'Rol';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    window.cerrarModalEliminar = function() {
        const modal = document.getElementById('modalEliminarRol');
        const content = document.getElementById('deleteModalContent');
        
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }
</script>