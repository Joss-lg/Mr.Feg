{{-- Modal de Método de Pago --}}
<div id="modal-metodo" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4 transition-all duration-300">
    <div class="relative bg-white border border-slate-200 rounded-[2rem] p-8 max-w-md w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200 overflow-hidden">

        {{-- Resplandor decorativo --}}
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-100 rounded-full blur-3xl pointer-events-none"></div>

        <h2 id="modal-metodo-titulo" class="text-2xl font-black text-slate-800 mb-6 text-center tracking-tight relative z-10">Método de Pago</h2>

        <div id="seccion-metodos-lista" class="space-y-3 mb-6 relative z-10">
            <button type="button" class="metodo-btn w-full text-left p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:border-emerald-300 hover:bg-emerald-50 transition-all font-bold text-slate-800 group outline-none flex items-center" data-metodo="Efectivo">
                <i class="fas fa-money-bill-wave text-emerald-600 mr-3 group-hover:scale-110 transition-transform"></i> Efectivo
            </button>

            <button type="button" class="metodo-btn w-full text-left p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:border-sky-300 hover:bg-sky-50 transition-all font-bold text-slate-800 group outline-none flex items-center" data-metodo="Transferencia">
                <i class="fas fa-building-columns text-sky-600 mr-3 group-hover:scale-110 transition-transform"></i> Transferencia
            </button>

            <button type="button" class="metodo-btn w-full text-left p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:border-violet-300 hover:bg-violet-50 transition-all font-bold text-slate-800 group outline-none flex items-center" data-metodo="Tarjeta">
                <i class="fas fa-credit-card text-violet-600 mr-3 group-hover:scale-110 transition-transform"></i> Tarjeta Bancaria
            </button>

            <div class="relative py-2">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white px-3 text-slate-400 font-black tracking-widest">Opciones Mixtas</span>
                </div>
            </div>

            <button type="button" id="btn-activar-combinado" class="w-full text-left p-4 rounded-2xl border-2 border-dashed border-slate-300 bg-transparent hover:border-amber-300 hover:bg-amber-50 transition-all font-black text-amber-600 group outline-none flex items-center">
                <i class="fas fa-layer-group mr-3 group-hover:scale-110 transition-transform"></i> Combinar Pagos / Mixto
            </button>
        </div>

        <div id="seccion-metodos-combinado" class="hidden space-y-4 mb-6 relative z-10">
            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-wider">Total Orden</p>
                    <p id="comb-total-requerido" class="text-xl font-black text-slate-800">$0.00</p>
                </div>
                <div>
                    <p id="comb-label-status" class="text-[10px] text-slate-400 uppercase font-black tracking-wider">Restante</p>
                    <p id="comb-monto-status" class="text-xl font-black text-rose-600">$0.00</p>
                </div>
            </div>

            {{-- EFECTIVO --}}
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-[100px] flex items-center gap-1.5">
                        <i class="fas fa-money-bill-wave text-emerald-600 text-xs"></i>
                        <label class="text-xs font-black uppercase text-emerald-600 select-none">Efectivo</label>
                    </div>
                    <div class="flex-1">
                        <input type="number" id="comb-input-efectivo" step="0.01" min="0" placeholder="0.00" class="w-full text-right bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-mono font-bold outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>
                </div>
            </div>

            {{-- TARJETA --}}
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-[100px] flex items-center gap-1.5">
                        <i class="fas fa-credit-card text-violet-600 text-xs"></i>
                        <label class="text-xs font-black uppercase text-violet-600 select-none">Tarjeta</label>
                    </div>
                    <div class="flex-1">
                        <input type="number" id="comb-input-tarjeta" step="0.01" min="0" placeholder="0.00" class="w-full text-right bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-mono font-bold outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 transition-all">
                    </div>
                </div>
                {{-- Referencia Tarjeta justo abajo del monto --}}
                <div class="pl-2">
                    <input type="text" id="comb-ref-tarjeta" placeholder="N° Referencia / Voucher" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 font-mono outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 transition-all">
                </div>
            </div>

            {{-- TRANSFERENCIA --}}
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-[100px] flex items-center gap-1.5">
                        <i class="fas fa-building-columns text-sky-600 text-xs"></i>
                        <label class="text-xs font-black uppercase text-sky-600 select-none">Transf.</label>
                    </div>
                    <div class="flex-1">
                        <input type="number" id="comb-input-transferencia" step="0.01" min="0" placeholder="0.00" class="w-full text-right bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-mono font-bold outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all">
                    </div>
                </div>
                {{-- Referencia Transferencia justo abajo del monto --}}
                <div class="pl-2">
                    <input type="text" id="comb-ref-transferencia" placeholder="Código de Autorización / Clave" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 font-mono outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all">
                </div>
            </div>

            <button type="button" id="btn-confirmar-combinado" disabled class="w-full h-12 bg-slate-100 text-slate-400 font-black text-sm uppercase tracking-wider rounded-2xl border border-slate-200 cursor-not-allowed transition-all outline-none">
                Confirmar Pago Combinado
            </button>
        </div>

        <button type="button" id="btn-cerrar-modal-metodo" class="w-full h-12 bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-800 font-black text-xs uppercase tracking-widest rounded-2xl border border-slate-200 transition-all outline-none relative z-10">
            Cancelar
        </button>
    </div>
</div>