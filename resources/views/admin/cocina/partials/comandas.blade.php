{{-- resources/views/admin/cocina/partials/comandas.blade.php --}}

{{-- Estilos para las animaciones de parpadeo (alertas de tiempo) --}}
<style>
    @keyframes parpadeoAmarillo {
        0%, 100% { border-color: #f59e0b; box-shadow: 0 0 15px rgba(245, 158, 11, 0.4); }
        50% { border-color: transparent; box-shadow: none; }
    }
    @keyframes parpadeoRojo {
        0%, 100% { border-color: #e11d48; box-shadow: 0 0 20px rgba(225, 29, 72, 0.5); background-color: rgba(225, 29, 72, 0.05); }
        50% { border-color: transparent; box-shadow: none; background-color: transparent; }
    }
    .alerta-amarilla {
        animation: parpadeoAmarillo 1.5s infinite !important;
        border-width: 2px !important;
    }
    .alerta-roja {
        animation: parpadeoRojo 1s infinite !important;
        border-width: 2px !important;
    }
</style>

@if($comandas->isEmpty())
    <div class="bg-white rounded-[2rem] px-6 py-16 sm:py-24 text-center border border-slate-200 shadow-sm mt-6 sm:mt-8 flex flex-col items-center justify-center">
        <div class="w-20 h-20 bg-emerald-50 rounded-[1.5rem] flex items-center justify-center border border-emerald-100 shadow-sm mb-5">
            <i class="fas fa-check-double text-3xl text-emerald-500"></i>
        </div>
        <h2 class="text-xl sm:text-2xl font-black text-slate-800">¡{{ $areaSeleccionada }} Despejada!</h2>
    </div>
@else
    <div class="grid gap-3 sm:gap-6 grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 mt-4 sm:mt-8 items-start w-full">
        @foreach($comandas as $comanda)
            @php
                // Parseo seguro de fecha
                $fechaCarbon = !empty($comanda->creado_en) ? \Carbon\Carbon::parse($comanda->creado_en) : now();
                $minutosEspera = $fechaCarbon->diffInMinutes(now());

                // Determinamos la clase de parpadeo según los minutos
                $claseAlerta = '';
                if ($minutosEspera >= 15) {
                    $claseAlerta = 'alerta-roja';
                } elseif ($minutosEspera >= 10) {
                    $claseAlerta = 'alerta-amarilla';
                }

                // Formateo del número de mesa para evitar "Mesa mesa 45"
                $numMesa = $comanda->mesa->numero ?? 'S/N';
                $labelMesa = \Illuminate\Support\Str::startsWith(strtolower($numMesa), 'mesa') 
                    ? $numMesa 
                    : 'Mesa ' . $numMesa;

                // Delivery
                $esDelivery  = $comanda->mesa && $comanda->mesa->esDelivery();
                $plataforma  = $esDelivery ? optional($comanda->mesa->plataformaDelivery)->nombre : null;
                $colorBorde  = $esDelivery ? 'border-t-orange-500' : 'border-t-emerald-500';
            @endphp

            <article class="bg-white w-full rounded-[1.5rem] border border-slate-200 border-t-[6px] {{ $colorBorde }} shadow-sm flex flex-col h-full overflow-hidden relative transition-all duration-300 comanda-card {{ $claseAlerta }}"
                     data-comanda-id="{{ $comanda->id }}"
                     data-lote="{{ $comanda->detalles->first()?->lote_envio ?? $comanda->id }}"
                     data-tiempo-inicio="{{ $fechaCarbon->getTimestampMs() }}">
                <div class="p-4 border-b border-slate-100 min-w-0 flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <h3 class="font-black text-lg break-words uppercase leading-tight text-slate-800">{{ $labelMesa }}</h3>
                            @if($esDelivery)
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-500 text-white text-[9px] font-black uppercase tracking-wider shadow-sm">
                                    <i class="fas fa-motorcycle text-[8px]"></i>
                                    {{ $plataforma ?? 'Delivery' }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 font-semibold truncate">Mesero: {{ $comanda->mesero->nombre ?? 'N/A' }}</p>
                    </div>
                    <span
                        class="tiempo-espera shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-lg border text-[10px] font-black uppercase tracking-wide whitespace-nowrap bg-slate-50 border-slate-200 text-slate-400"
                        data-creado="{{ $fechaCarbon->toIso8601String() }}"
                    >
                        <i class="fas fa-clock"></i>
                        <span class="tiempo-texto">--</span>
                    </span>
                </div>

                <div class="p-4 flex-1 min-w-0">
                    <ul class="space-y-2.5">
                        @foreach($comanda->detalles as $detalle)
                            @php
                                $tiempoClases = [
                                    'sin-tiempo'     => ['label' => 'S', 'clase' => 'text-slate-500 bg-slate-50 border-slate-200'],
                                    'primer-tiempo'  => ['label' => '1', 'clase' => 'text-blue-600 bg-blue-50 border-blue-100'],
                                    'segundo-tiempo' => ['label' => '2', 'clase' => 'text-violet-600 bg-violet-50 border-violet-100'],
                                    'tercer-tiempo'  => ['label' => '3', 'clase' => 'text-rose-600 bg-rose-50 border-rose-100'],
                                ];
                                $tInfo = $tiempoClases[$detalle->tiempo] ?? null;

                                // Desglose limpio entre Variante/Tamaño y Nota/Alerta Especial
                                $textoVariante = null;
                                $textoAlerta   = null;

                                if (!empty($detalle->notas)) {
                                    if (str_contains($detalle->notas, '|||')) {
                                        [$textoVariante, $textoAlerta] = explode('|||', $detalle->notas, 2);
                                        $textoVariante = trim($textoVariante);
                                        $textoAlerta   = trim($textoAlerta);
                                    } else {
                                        // Si no trae delimitador, se muestra como pastilla morada de especificación
                                        $textoVariante = trim($detalle->notas);
                                    }
                                }
                            @endphp

                            <li class="flex flex-col text-sm gap-1.5 detalle-item"
                                data-detalle-id="{{ $detalle->id }}"
                                data-estado="{{ $detalle->estado_preparacion }}">
                                
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold break-words flex flex-wrap items-center gap-1.5 nombre-producto transition-all
                                              {{ in_array($detalle->estado_preparacion, ['listo_cocina','servida']) ? 'line-through opacity-40 text-slate-400' : 'text-slate-800' }}">
                                            {{ $detalle->cantidad }}x {{ $detalle->producto->nombre ?? 'Producto Eliminado' }}
                                            
                                            @if($tInfo)
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-wide px-1.5 py-0.5 rounded-md border {{ $tInfo['clase'] }}">
                                                    <i class="fas fa-clock"></i>Tiempo {{ $tInfo['label'] }}
                                                </span>
                                            @endif
                                            @if($detalle->gramaje)
                                                @php
                                                    $gramajeLimpio = rtrim(rtrim(number_format((float) $detalle->gramaje, 2, '.', ''), '0'), '.');
                                                @endphp
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-wide text-orange-600 bg-orange-50 border border-orange-100 px-1.5 py-0.5 rounded-md">
                                                    <i class="fas fa-weight-hanging"></i>{{ $gramajeLimpio }}g
                                                </span>
                                            @endif
                                        </span>

                                        {{-- 1. Badge Morado: Tamaño y Complementos (ej: 6pz - con papas) --}}
                                        @if(!empty($textoVariante))
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-700 bg-purple-50 border border-purple-200/80 px-2.5 py-1 rounded-lg mt-1 w-fit shadow-xs">
                                                <i class="fas fa-layer-group text-[10px] text-purple-500"></i>
                                                <span>{{ $textoVariante }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Botón de tachar --}}
                                    <button type="button"
                                        class="btn-tachar shrink-0 w-8 h-8 rounded-xl border-2 transition-all flex items-center justify-center
                                               {{ in_array($detalle->estado_preparacion, ['listo_cocina','servida'])
                                                   ? 'bg-emerald-600 border-emerald-600 text-white scale-95'
                                                   : 'border-slate-300 text-slate-400 hover:border-emerald-500 hover:text-emerald-600 hover:scale-105' }}"
                                        title="{{ in_array($detalle->estado_preparacion, ['listo_cocina','servida']) ? 'Desmarcar' : 'Marcar como listo' }}">
                                        <i class="fas fa-check text-[11px]"></i>
                                    </button>
                                </div>

                                {{-- 2. Recuadro Rojo: Únicamente para notas especiales/alertas escritas --}}
                                @if(!empty($textoAlerta))
                                    <span class="flex items-start gap-2 px-3 py-2 rounded-lg bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold w-full break-words leading-snug">
                                        <i class="fas fa-exclamation-circle mt-0.5 shrink-0"></i>
                                        <span>{{ $textoAlerta }}</span>
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Formulario con ÚNICO Botón para finalizar la comanda --}}
                <div class="p-3 sm:p-4 bg-slate-50 border-t border-slate-100">
                    <form action="{{ route('admin.cocina.orden.estado', $comanda->orden_id) }}" method="POST" class="form-avanzar-estado">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="servida">
                        <input type="hidden" name="lote" value="{{ $comanda->lote }}">
                        <input type="hidden" name="area" value="{{ strtolower($areaSeleccionada) }}">
                        <button type="submit"
                            class="w-full h-12 rounded-xl font-black uppercase text-[12px] tracking-[0.1em] transition-all active:scale-95 bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-500/20">
                            MARCAR COMO LISTA
                        </button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
@endif