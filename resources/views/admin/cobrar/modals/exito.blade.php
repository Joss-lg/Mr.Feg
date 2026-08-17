{{-- Modal de Pago Exitoso --}}
<div id="modal-exito" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4 transition-all duration-300">
    <div class="relative bg-white border border-slate-200 rounded-[2rem] p-8 max-w-md w-full shadow-2xl animate-in fade-in zoom-in-95 duration-300 overflow-hidden">

        {{-- Resplandor decorativo de fondo --}}
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-emerald-100 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Icono Animado --}}
        <div class="flex justify-center mb-6 relative z-10">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 border border-emerald-100 shadow-sm animate-pulse">
                <i class="fas fa-check text-3xl text-emerald-600"></i>
            </div>
        </div>

        <h2 id="modal-titulo" class="text-2xl font-black text-center text-slate-800 mb-2 tracking-tight relative z-10">¡Pago Exitoso!</h2>

        <p id="modal-descripcion" class="text-center text-slate-500 text-sm mb-6 font-medium relative z-10">
            Se procesó correctamente el pago de <strong id="modal-nombre-persona" class="text-emerald-600 font-black"></strong>
        </p>

        {{-- Resumen de la Operación --}}
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-6 space-y-3 relative z-10">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Monto pagado</span>
                <span class="text-slate-800 font-black text-sm" id="modal-monto-pagado">$0.00</span>
            </div>

            {{-- Línea divisoria sutil --}}
            <div class="w-full h-px bg-slate-200"></div>

            <div class="flex justify-between items-center">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest" id="modal-etiqueta-total">Nuevo total en mesa</span>
                <span class="text-emerald-600 font-black text-sm" id="modal-nuevo-total">$0.00</span>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="flex gap-3 relative z-10">
            <button type="button" id="btn-cerrar-modal-exito" class="flex-1 h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-md shadow-emerald-500/20 active:scale-95 outline-none">
                Continuar
            </button>
            <button type="button" id="btn-liberar-mesa-modal" class="flex-1 h-12 bg-orange-600 hover:bg-orange-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-md shadow-orange-500/20 active:scale-95 outline-none hidden flex items-center justify-center gap-2">
                <i class="fas fa-door-open"></i> Liberar Mesa
            </button>
        </div>
    </div>
</div>

<script>
    // Cierre básico del modal
    document.getElementById('btn-cerrar-modal-exito').addEventListener('click', () => {
        document.getElementById('modal-exito').classList.add('hidden');
    });
</script>