{{-- resources/views/admin/caja/corte.blade.php --}}
<div id="modalCierreCaja" class="fixed inset-0 z-[9999] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" id="backdropCierreCaja"></div>
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="relative inline-block bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 p-6 sm:p-8 z-10">

            {{-- Encabezado --}}
            <div class="flex items-center justify-between gap-4 mb-6 border-b border-slate-100 pb-5">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100 shrink-0">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight truncate">Realizar Corte de Caja</h3>
                </div>
                <button type="button" id="btnCerrarModalX" class="group w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all outline-none shrink-0 cursor-pointer">
                    <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
                </button>
            </div>

            {{-- Desglose de propinas pendientes de entregar en este turno --}}
            @if(isset($propinasPendientes) && $propinasPendientes->isNotEmpty())
                <div class="mb-5 rounded-2xl border border-amber-100 bg-amber-50 overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-amber-100 flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 flex items-center gap-1.5">
                            <i class="fas fa-hand-holding-dollar"></i> Propinas a entregar
                        </span>
                        <span class="text-xs font-black text-amber-600">
                            ${{ number_format($totalPropinasPendientes, 2) }}
                        </span>
                    </div>

                    <ul class="divide-y divide-amber-100 max-h-40 overflow-y-auto">
                        @foreach($propinasPendientes as $fila)
                            <li class="px-4 py-2 flex items-center justify-between text-sm">
                                <span class="text-slate-700 font-semibold truncate">{{ $fila->mesero }}</span>
                                <span class="font-black text-amber-600 shrink-0 ml-3">${{ number_format($fila->total, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="px-4 py-2 text-[10px] text-amber-700 bg-amber-100/60 leading-snug">
                        Este monto se descontará automáticamente del efectivo esperado al confirmar el cierre.
                    </p>
                </div>
            @endif

            <form action="{{ route('admin.caja.cerrar') }}" method="POST" class="space-y-5">
                @csrf

                {{-- EFECTIVO QUE DEBE HABER
                     Se muestra ANTES de contar para que el cajero sepa contra
                     qué está comparando. Es el mismo cálculo que usa el cierre
                     (CajaService::calcularEfectivoEsperado), ya con las
                     propinas pendientes descontadas. --}}
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 space-y-1.5 text-xs bg-slate-50">
                        <div class="flex justify-between text-slate-500">
                            <span>Fondo inicial</span>
                            <span class="font-bold text-slate-800">${{ number_format($efectivo['monto_inicial'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-600">
                            <span>(+) Entradas en efectivo</span>
                            <span class="font-bold">${{ number_format($efectivo['ingresos_efectivo'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-rose-600">
                            <span>(−) Salidas en efectivo</span>
                            <span class="font-bold">${{ number_format($efectivo['egresos_efectivo'] ?? 0, 2) }}</span>
                        </div>
                        @if(($totalPropinasPendientes ?? 0) > 0)
                            <div class="flex justify-between text-amber-600">
                                <span>(−) Propinas por entregar</span>
                                <span class="font-bold">${{ number_format($totalPropinasPendientes, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="px-4 py-3.5 bg-blue-600 flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest text-blue-100">Debe haber en caja</span>
                        <span class="text-lg sm:text-xl font-black text-white"
                              id="efectivoEsperado"
                              data-esperado="{{ $efectivoEsperadoAlCierre ?? 0 }}">
                            ${{ number_format($efectivoEsperadoAlCierre ?? 0, 2) }}
                        </span>
                    </div>
                    @if(($efectivo['ingresos_no_efectivo'] ?? 0) > 0)
                        <p class="px-4 py-2 text-[10px] text-slate-500 bg-slate-100 leading-snug">
                            No se cuentan aquí ${{ number_format($efectivo['ingresos_no_efectivo'], 2) }} de tarjeta y transferencia: ese dinero no pasa por el cajón.
                        </p>
                    @endif
                </div>

                <p class="text-xs font-semibold text-slate-500">
                    Ingresa el monto total en efectivo que tienes físicamente en la caja para realizar la conciliación automática.
                </p>

                <div>
                    <label for="monto_final_real" class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Efectivo Físico en Caja</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 text-sm font-bold">$</span>
                        </div>
                        <input type="text" inputmode="decimal" data-teclado="numerico" name="monto_final_real" id="monto_final_real" required
                            class="w-full h-12 pl-8 pr-4 rounded-xl border border-slate-200 bg-white text-slate-800 font-semibold focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 outline-none transition-all shadow-sm"
                            placeholder="0.00" onfocus="this.select()">
                    </div>

                    {{-- Diferencia calculada en vivo mientras se teclea el conteo --}}
                    <div id="diferenciaCorte" class="hidden mt-2 px-3 py-2 rounded-xl text-sm font-black flex items-center justify-between"></div>
                </div>

                <div>
                    <label for="comentarios" class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5 ml-1">Notas de Auditoría (Opcional)</label>
                    <textarea name="comentarios" id="comentarios" rows="3" maxlength="500"
                        class="w-full p-4 rounded-xl border border-slate-200 bg-white text-slate-800 font-medium focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 outline-none transition-all resize-none shadow-sm placeholder:text-slate-400"
                        placeholder="Observaciones sobre faltantes, sobrantes o incidentes en el turno..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" id="btnCancelarModal"
                        class="h-12 px-6 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 hover:bg-slate-50 transition-all outline-none cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="h-12 px-6 rounded-xl text-xs font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-500/20 active:scale-95 transition-all outline-none cursor-pointer">
                        Cerrar Turno Actual
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>