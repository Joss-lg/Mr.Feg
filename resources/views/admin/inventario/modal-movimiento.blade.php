<style>
    @media (min-width: 768px) {
        /* Ajuste para teclado virtual */
        body.teclado-virtual-abierto #modalMovimiento {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        body.teclado-virtual-abierto #movimientoContainer {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important;
        }
    }
</style>

<div id="modalMovimiento" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    
    <div class="relative bg-white border border-slate-200 w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-500 scale-95 opacity-0 flex flex-col max-h-[95dvh] sm:max-h-[92dvh]" id="movimientoContainer">
        
        <div class="p-6 sm:p-8 pb-4 sm:pb-5 flex justify-between items-center border-b border-slate-100 shrink-0 gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shrink-0">
                    <i class="fas fa-exchange-alt text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight uppercase m-0 leading-tight truncate" id="movimientoNombreInsumo">Cargando...</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Registrar Movimiento</p>
                </div>
            </div>
            
            <button type="button" onclick="closeModalMovimiento()" class="w-10 h-10 rounded-xl flex items-center justify-center bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all outline-none shrink-0 border border-slate-200">
                <i class="fas fa-times text-xs sm:text-sm"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.inventario.movimiento') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            <input type="hidden" name="insumo_id" id="movimientoInsumoId">

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-5 overflow-y-auto flex-1 overscroll-contain">

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-arrows-alt-v opacity-40"></i> Tipo de Movimiento
                    </label>
                    <div class="relative">
                        <select name="tipo" required class="w-full h-12 bg-white border border-slate-200 rounded-xl px-5 text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer shadow-sm">
                            <option value="entrada" class="text-emerald-600 font-black">🟢 ENTRADA (Suma al stock)</option>
                            <option value="salida" class="text-rose-600 font-black">🔴 SALIDA (Resta al stock)</option>
                            <option value="ajuste" class="text-orange-600 font-black">🟠 MERMA / DESPERDICIO</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-hashtag opacity-40"></i> Cantidad a mover
                    </label>
                    <input type="text" name="cantidad" pattern="[0-9]*\.?[0-9]*" required data-teclado="numerico" inputmode="none"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-5 text-sm font-black text-slate-800 focus:border-blue-500 outline-none transition-all shadow-sm placeholder:text-slate-400"
                        placeholder="Ej: 50">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-comment-alt opacity-40"></i> Motivo o Justificación
                    </label>
                    <input type="text" name="motivo" required data-teclado="texto" inputmode="none"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-5 text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all shadow-sm placeholder:text-slate-400"
                        placeholder="Ej: Factura #1234 / Se rompió">
                </div>
            </div>

            <div class="flex items-center gap-4 px-6 sm:px-8 py-6 border-t border-slate-200 shrink-0 bg-slate-50">
                <button type="button" onclick="closeModalMovimiento()" 
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all outline-none">
                    Cancelar
                </button>
                <button type="submit" 
                    class="flex-[1.5] h-12 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/20 active:scale-95 transition-all outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Registrar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalMovimiento(id, nombre) {
        const modal = document.getElementById('modalMovimiento');
        const container = document.getElementById('movimientoContainer');
        
        document.getElementById('movimientoInsumoId').value = id;
        document.getElementById('movimientoNombreInsumo').innerText = nombre;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModalMovimiento() {
        const modal = document.getElementById('modalMovimiento');
        const container = document.getElementById('movimientoContainer');
        
        modal.classList.add('opacity-0');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.querySelector('form').reset();
        }, 300);
    }
</script>