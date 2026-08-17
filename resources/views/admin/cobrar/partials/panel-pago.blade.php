{{-- panel-pago.blade.php --}}
@php $anchoDerecha = $anchoDerecha ?? 'lg:w-3/5'; @endphp
<div class="w-full {{ $anchoDerecha }} p-4 lg:p-8 bg-slate-50 overflow-hidden h-full">
    <div class="max-w-xl mx-auto h-full flex flex-col">

        {{-- Inputs de Estado Ocultos --}}
        <input id="mesa-id" type="hidden" value="{{ $mesa->id }}">
        <input id="orden-id" type="hidden" value="{{ $ordenes->first()->id ?? '' }}">
        <input id="metodo-pago" type="hidden" value="Efectivo">
        {{-- NUEVO: cuando la mesa está dividida, aquí va el id de la persona seleccionada --}}
        <input id="cuenta-division-id" type="hidden" value="">

        @if(!empty($division))
            <div class="mb-2 p-3 rounded-2xl bg-blue-50 border border-blue-100 text-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600" id="aviso-division-panel">
                    Selecciona una persona en el panel izquierdo para cobrar su parte
                </p>
            </div>
        @endif

        <div class="flex-1 space-y-5 sm:space-y-6 overflow-y-auto pr-2">

            {{-- 1. Selector de Método --}}
            <div class="flex items-center justify-center pt-2">
                <div class="text-center">
                    <p class="text-slate-400 uppercase tracking-[0.35em] text-[10px] font-black mb-3">Método de pago</p>
                    <button id="btn-abrir-modal-metodo" type="button" class="inline-flex items-center gap-3 rounded-full bg-blue-50 px-6 py-3 text-blue-600 font-black uppercase tracking-[0.25em] border border-blue-100 hover:bg-blue-100 transition-all shadow-sm">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                        <span id="metodo-pago-label">Efectivo</span>
                    </button>
                </div>
            </div>

            {{-- Sección de Referencia (justo debajo del método de pago) --}}
            <div id="non-cash-section" class="hidden space-y-4 bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm">
                <label class="text-slate-500 uppercase tracking-[0.2em] text-[10px] font-black block text-center">Referencia de operación</label>
                <input id="referencia" type="text" placeholder="Referencia de operación"
                    class="touch-input w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-slate-800 font-semibold outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" />
            </div>

            {{-- 2. Display de Montos --}}
            <div class="relative bg-gradient-to-br from-blue-600 to-indigo-600 rounded-[2rem] overflow-hidden shadow-lg shadow-blue-500/20">
                <div class="px-6 pt-6 pb-5 text-center">
                    <p class="text-blue-100 text-[9px] font-black uppercase tracking-[0.3em] mb-3">Monto a cobrar</p>
                    <div class="text-5xl font-black text-white tracking-tighter" id="monto-input">$0.00</div>
                    <div class="mt-4 flex items-center justify-center gap-6 text-xs">
                        <div class="flex flex-col items-center gap-0.5">
                            <span class="text-blue-100 uppercase tracking-widest text-[9px] font-bold">Total</span>
                            <strong class="text-white font-black text-sm" id="total-pagar-derecha">${{ number_format($totalPagar ?? 0, 2) }}</strong>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div class="flex flex-col items-center gap-0.5">
                            <span class="text-blue-100 uppercase tracking-widest text-[9px] font-bold">Cambio</span>
                            <strong class="text-emerald-300 font-black text-sm" id="display-cambio">$0.00</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Teclado Numérico --}}
            <div id="cash-section" class="select-none">
                {{-- Filas 1-9 --}}
                <div class="grid grid-cols-3 gap-2 mb-2">
                    @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                        <button type="button"
                            class="btn-tecla h-14 rounded-2xl font-black text-lg
                                   bg-white
                                   border border-slate-200
                                   text-slate-800
                                   shadow-sm
                                   hover:bg-blue-50
                                   hover:border-blue-200 hover:text-blue-600
                                   active:scale-95 active:bg-blue-100
                                   transition-all duration-100"
                            data-value="{{ $key }}">{{ $key }}</button>
                    @endforeach
                </div>
                {{-- Fila . / 0 / 00 / DEL --}}
                <div class="grid grid-cols-4 gap-2">
                    @foreach(['.','0','00'] as $key)
                        <button type="button"
                            class="btn-tecla h-14 rounded-2xl font-black text-lg
                                   bg-white
                                   border border-slate-200
                                   text-slate-800
                                   shadow-sm
                                   hover:bg-blue-50
                                   hover:border-blue-200 hover:text-blue-600
                                   active:scale-95 active:bg-blue-100
                                   transition-all duration-100"
                            data-value="{{ $key }}">{{ $key }}</button>
                    @endforeach
                    <button type="button"
                        class="btn-tecla h-14 rounded-2xl font-black
                               bg-rose-50
                               border border-rose-100
                               text-rose-600
                               shadow-sm
                               hover:bg-rose-100
                               hover:border-rose-200
                               active:scale-95
                               transition-all duration-100 flex items-center justify-center gap-1.5"
                        data-value="DEL">
                        <i class="fas fa-delete-left text-base"></i>
                    </button>
                </div>
            </div>

            {{-- Selector de Propina — siempre habilitado, incluso con la cuenta dividida --}}
            <div class="bg-white border border-slate-200 rounded-[2rem] p-5 sm:p-6 shadow-sm">
                <p class="text-slate-500 uppercase tracking-[0.25em] text-[10px] font-black mb-3 text-center">
                    ¿Cuánta propina desea dejar?
                </p>

                @if(!empty($division))
                    <p class="text-center text-[9px] text-blue-600 font-black uppercase mb-3">
                        <i class="fas fa-info-circle"></i> Se repartirá entre las personas que aún no han pagado
                    </p>
                @endif

                <div class="grid grid-cols-4 gap-2 mb-3" id="propina-porcentaje-botones">
                    <button type="button" class="propina-btn h-12 rounded-xl border border-slate-200 bg-slate-50 font-black text-xs text-slate-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-all" data-porcentaje="0">Sin propina</button>
                    <button type="button" class="propina-btn h-12 rounded-xl border border-slate-200 bg-slate-50 font-black text-xs text-slate-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-all" data-porcentaje="10">10%</button>
                    <button type="button" class="propina-btn h-12 rounded-xl border border-slate-200 bg-slate-50 font-black text-xs text-slate-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-all" data-porcentaje="15">15%</button>
                    <button type="button" class="propina-btn h-12 rounded-xl border border-slate-200 bg-slate-50 font-black text-xs text-slate-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-all" data-porcentaje="20">20%</button>
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
                        <input id="propina-manual-input" type="number" step="0.01" min="0" placeholder="Otro monto"
                            data-teclado="numerico"
                            class="touch-input pl-7 pr-4 h-12 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold" />
                    </div>
                    <button type="button" id="btn-aplicar-propina-manual" class="h-12 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider transition-all shadow-sm active:scale-95">
                        Aplicar
                    </button>
                </div>

                <p class="text-center text-[11px] text-slate-400 mt-3">
                    Propina actual: <strong class="text-slate-700" id="propina-actual-display">${{ number_format($orden->propina ?? 0, 2) }}</strong>
                </p>
            </div>

            {{-- 6. Botones Finales --}}
            <div class="grid grid-cols-2 gap-4 pb-12">
                <button id="btn-ticket" class="bg-slate-50 hover:bg-slate-100 text-slate-800 font-black py-5 rounded-2xl border border-slate-200 transition-all shadow-sm active:scale-95">TICKET</button>
                {{-- ID btn-procesar-pago para JS --}}
                <button id="btn-procesar-pago" data-dividido="{{ !empty($division) ? '1' : '0' }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-5 rounded-2xl transition-all shadow-md shadow-emerald-500/20 active:scale-95">FINALIZAR</button>
            </div>
        </div>
    </div>
</div>