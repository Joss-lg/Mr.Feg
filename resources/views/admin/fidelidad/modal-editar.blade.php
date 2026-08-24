{{-- resources/views/admin/fidelidad/modal-editar.blade.php --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalEditarNivel { align-items: flex-start !important; padding-top: 10px !important; }
        body.teclado-virtual-abierto #editNivelContainer { max-height: 45dvh !important; }
    }
    .unique-scrollbar::-webkit-scrollbar { width: 6px; }
    .unique-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
</style>

<div id="modalEditarNivel" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    <div class="fixed inset-0 bg-transparent" onclick="window.closeModal('modalEditarNivel', 'editNivelContainer')"></div>

    <div id="editNivelContainer" class="relative bg-[#F2F2F2] border border-slate-200 w-full max-w-lg mx-auto rounded-[2rem] shadow-2xl scale-95 opacity-0 transition-all duration-300 flex flex-col overflow-hidden max-h-[95vh] sm:max-h-[92vh]">

        {{-- Encabezado del Modal --}}
        <div class="flex items-center justify-between p-6 sm:p-8 pb-4 sm:pb-5 border-b border-slate-200 shrink-0 bg-[#F2F2F2]">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 border border-blue-100">
                    <i class="fas fa-pen text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight leading-tight truncate">Modificar Nivel</h2>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Actualizar meta y premio</p>
                </div>
            </div>
            <button type="button" onclick="window.closeModal('modalEditarNivel', 'editNivelContainer')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-100 hover:rotate-90 active:scale-95 transition-all duration-300 outline-none shrink-0">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Formulario: el id del nivel se guarda en data-id y se lee por JS al enviar --}}
        <form id="formEditarNivel" class="flex flex-col flex-1 min-h-0 bg-[#F2F2F2]">
            @csrf

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-6 overflow-y-auto flex-1 overscroll-contain unique-scrollbar">

                {{-- Compras requeridas --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-stamp opacity-40"></i> Compras Requeridas (Sellos)
                    </label>
                    <input type="text" name="compras_requeridas" id="edit_compras_requeridas" required pattern="[0-9]*" data-teclado="numerico" autocomplete="off"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                        placeholder="Ej: 8">
                </div>

                {{-- Descripción del premio --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-gift opacity-40"></i> Descripción del Premio
                    </label>
                    <input type="text" name="premio_descripcion" id="edit_premio_descripcion" required data-teclado="texto" autocomplete="off"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                        placeholder="Ej: Frappé sencillo de cortesía">
                </div>

                {{-- Fila: Monto mínimo y Valor del premio --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-dollar-sign opacity-40"></i> Compra Mínima
                        </label>
                        <input type="text" name="monto_minimo" id="edit_monto_minimo" required pattern="[0-9]*\.?[0-9]*" data-teclado="numerico" data-teclado-decimales="true" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej: 150.00">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-tag opacity-40"></i> Valor del Premio
                        </label>
                        <input type="text" name="valor_premio" id="edit_valor_premio" required pattern="[0-9]*\.?[0-9]*" data-teclado="numerico" data-teclado-decimales="true" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej: 65.00 (0 si es cortesía)">
                    </div>
                </div>

            </div>

            {{-- Botones Footer --}}
            <div class="flex items-center gap-4 px-6 sm:px-8 py-6 border-t border-slate-200 shrink-0 bg-[#F2F2F2]">
                <button type="button" onclick="window.closeModal('modalEditarNivel', 'editNivelContainer')"
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 hover:bg-slate-200/60 transition-all outline-none">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-[1.5] h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all active:scale-95 outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Actualizar Nivel
                </button>
            </div>
        </form>
    </div>
</div>