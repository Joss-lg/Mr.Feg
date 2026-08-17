{{-- Modal de Promociones --}}
<div id="modal-promociones" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4 transition-all duration-300">
    <div class="relative bg-white border border-slate-200 rounded-[2rem] p-8 max-w-md w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200 max-h-[85vh] flex flex-col overflow-hidden">

        {{-- Resplandor decorativo sutil en el fondo --}}
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-blue-100 rounded-full blur-3xl pointer-events-none"></div>

        <h2 class="text-2xl font-black text-slate-800 mb-6 text-center tracking-tight relative z-10">Promociones Activas</h2>

        {{-- Contenedor con scroll propio --}}
        <div class="space-y-3 mb-6 overflow-y-auto pr-2 custom-scrollbar relative z-10" id="promos-list">
            {{-- Ejemplo de estructura que tu JS debería inyectar: --}}
            <div class="flex items-center justify-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        </div>

        <button type="button" id="btn-cerrar-modal-promos" class="w-full h-12 bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-800 font-black text-xs uppercase tracking-widest rounded-2xl border border-slate-200 transition-all outline-none relative z-10">
            Cerrar
        </button>
    </div>
</div>

<script>
    // Cierre del modal
    document.getElementById('btn-cerrar-modal-promos').addEventListener('click', () => {
        document.getElementById('modal-promociones').classList.add('hidden');
    });
</script>

<style>
    /* Scrollbar minimalista */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>