{{-- Modal de Error / Alerta - Reutilizable --}}
<div id="modal-error" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4 transition-all duration-300">
    <div class="relative bg-white border border-slate-200 rounded-[2rem] shadow-2xl p-8 max-w-sm w-full animate-in fade-in zoom-in-95 duration-300">

        {{-- Icono --}}
        <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-50 border border-rose-100 mx-auto mb-6 shadow-sm">
            <i class="fas fa-exclamation-triangle text-rose-600 text-2xl"></i>
        </div>

        {{-- Título --}}
        <h2 id="modal-error-titulo" class="text-2xl font-black text-center text-slate-800 mb-2 tracking-tight">Error</h2>

        {{-- Mensaje --}}
        <p class="text-center text-slate-500 text-sm mb-6 font-medium leading-relaxed">
            <span id="modal-error-mensaje">Ocurrió un problema, inténtalo de nuevo.</span>
        </p>

        {{-- Panel de Datos (Opcional) --}}
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-6">
            <div class="flex justify-between items-center mb-3">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Requerido:</span>
                <span class="text-rose-600 font-black text-sm" id="modal-error-requerido">$0.00</span>
            </div>

            {{-- Línea divisoria sutil --}}
            <div class="w-full h-px bg-slate-200 mb-3"></div>

            <div class="flex justify-between items-center">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Ingresado:</span>
                <span class="text-amber-600 font-black text-sm" id="modal-error-ingresado">$0.00</span>
            </div>
        </div>

        {{-- Botón de cierre --}}
        <button type="button" id="btn-cerrar-modal-error" class="w-full h-12 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-md shadow-rose-500/20 active:scale-95 transition-all outline-none flex items-center justify-center gap-2">
            <i class="fas fa-check"></i> Entendido
        </button>
    </div>
</div>

<script>
    // Script para cerrar el modal
    document.getElementById('btn-cerrar-modal-error').addEventListener('click', () => {
        document.getElementById('modal-error').classList.add('hidden');
    });
</script>