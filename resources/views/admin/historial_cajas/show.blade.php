@extends('layouts.admin')

@section('title', 'Detalle de Turno | Ollintem Pro')

@section('content')
<div class="px-4 py-6 sm:p-6 lg:p-10 w-full max-w-[1600px] mx-auto space-y-6 sm:space-y-8 relative z-10 font-sans min-h-screen bg-slate-50">

    {{-- Botón de Regresar y Encabezado --}}
    <div class="flex flex-col gap-3 pb-6 border-b border-slate-200">
        <div>
            <a href="{{ route('historial.index') }}" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left text-[10px]"></i> Volver al historial
            </a>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                    Auditoría de Caja #{{ $turno->id }}
                </h1>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">
                    Detalles específicos del flujo financiero capturado en este turno.
                </p>
            </div>

            {{-- Estado del turno + acciones del PDF --}}
            <div class="flex flex-wrap items-center gap-2.5">
                @if($turno->estado === 'abierta')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-sm">
                        ● Caja Activa
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-slate-100 text-slate-600 border border-slate-200 shadow-sm">
                        Turno Cerrado
                    </span>
                @endif

                <a href="{{ route('historial.pdf', $turno->id) }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest bg-blue-600 hover:bg-blue-700 text-white transition-all shadow-md shadow-blue-500/20 active:scale-95">
                    <i class="fas fa-file-pdf text-sm"></i>
                    Ver PDF
                </a>

                <a href="{{ route('historial.pdf', ['id' => $turno->id, 'descargar' => 1]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest border border-slate-200 bg-white text-slate-700 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm active:scale-95">
                    <i class="fas fa-download text-sm"></i>
                    Descargar
                </a>
            </div>
        </div>
    </div>

    {{-- Grid de Información General --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Tarjeta 1: Metadatos del Turno --}}
        <div class="p-6 rounded-[2rem] border border-slate-200 bg-white shadow-sm space-y-4">
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Datos de Apertura</h3>

            <div class="space-y-3">
                <div>
                    <span class="block text-[10px] text-slate-400 uppercase font-black tracking-widest">Empleado Responsable</span>
                    <span class="text-sm font-bold text-slate-800">{{ $turno->user->nombre ?? $turno->user->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] text-slate-400 uppercase font-black tracking-widest">Turno Asignado</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 mt-1">
                        {{ $turno->turno }}
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] text-slate-400 uppercase font-black tracking-widest">Fecha y Hora Apertura</span>
                    <span class="text-xs font-bold text-slate-700">{{ $turno->created_at->format('d/m/Y - h:i A') }}</span>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Balance Financiero --}}
        <div class="p-6 rounded-[2rem] border border-slate-200 bg-white shadow-sm space-y-4">
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Conciliación de Saldos</h3>

            <div class="space-y-3">
                @php
                    $esperadoTurno = (float) ($turno->monto_final_esperado ?? 0);
                    $contadoTurno  = (float) ($turno->monto_final_real ?? 0);
                    $difTurno = $turno->diferencia !== null
                        ? (float) $turno->diferencia
                        : round($contadoTurno - $esperadoTurno, 2);
                @endphp

                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span class="text-xs text-slate-500 font-semibold">Fondo Inicial:</span>
                    <span class="text-xs font-black text-slate-800">${{ number_format($turno->monto_inicial, 2) }}</span>
                </div>

                @if($turno->estado === 'cerrada')
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-xs text-slate-500 font-semibold">Debía haber en caja:</span>
                        <span class="text-xs font-black text-slate-800">${{ number_format($esperadoTurno, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-xs text-slate-500 font-semibold">Efectivo contado:</span>
                        <span class="text-xs font-black text-slate-800">${{ number_format($contadoTurno, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center pt-1">
                        <span class="text-xs font-black text-slate-800">Resultado:</span>
                        @if(abs($difTurno) < 0.01)
                            <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">✓ Caja Cuadrada</span>
                        @elseif($difTurno < 0)
                            <span class="text-xs font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">⚠ Faltante: ${{ number_format(abs($difTurno), 2) }}</span>
                        @else
                            <span class="text-xs font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">⚠ Sobrante: ${{ number_format($difTurno, 2) }}</span>
                        @endif
                    </div>
                @else
                    <div class="text-center pt-4 text-xs font-bold text-slate-400 italic">
                        El balance final se calculará al cerrar el turno.
                    </div>
                @endif
            </div>
        </div>

        {{-- Tarjeta 3: Notas de Auditoría --}}
        <div class="p-6 rounded-[2rem] border border-slate-200 bg-white shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Notas / Observaciones</h3>
                <p class="text-xs font-semibold text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 min-h-[75px]">
                    {{ $turno->observaciones ?? 'Sin comentarios ni incidentes reportados en este turno.' }}
                </p>
            </div>
            @if($turno->estado === 'cerrada')
                <span class="text-[10px] text-slate-400 font-bold block text-right mt-2">
                    Cierre procesado a las: {{ $turno->updated_at->format('h:i A') }}
                </span>
            @endif
        </div>
    </div>

    {{-- FRANJA: Resumen de Turno en formato horizontal de tarjetas --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm p-5 sm:p-6 relative overflow-hidden w-full">
        <div class="absolute top-0 left-0 w-full h-[4px] bg-gradient-to-r from-blue-500 to-indigo-600"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-4 flex-wrap">
                <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center whitespace-nowrap">
                    <i class="fas fa-cash-register text-blue-600 mr-2"></i> Resumen de Turno
                </h3>
                <span class="text-[11px] font-bold text-slate-500">ID Caja: <span class="text-slate-800">#{{ $turno->id }}</span></span>
                <span class="text-[11px] font-bold text-slate-500">Cajero: <span class="text-slate-800">{{ $turno->user->nombre ?? $turno->user->name ?? 'N/A' }}</span></span>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 border border-blue-100 text-blue-600 uppercase tracking-wider">
                    {{ $turno->turno }}
                </span>
            </div>
        </div>

        {{-- Grid de tarjetas --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-3.5 flex flex-col justify-center shadow-sm">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Saldo Inicial</span>
                <span class="font-black text-slate-800 text-sm sm:text-base">${{ number_format($turno->monto_inicial, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-3.5 flex flex-col justify-center shadow-sm">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-money-bill-wave text-emerald-500 mr-1"></i> Efectivo
                </span>
                <span class="font-black text-emerald-600 text-sm sm:text-base">+${{ number_format($ventasEfectivo, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-3.5 flex flex-col justify-center shadow-sm">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-credit-card text-sky-500 mr-1"></i> Tarjeta
                </span>
                <span class="font-black text-sky-600 text-sm sm:text-base">+${{ number_format($ventasTarjeta, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-3.5 flex flex-col justify-center shadow-sm">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-university text-indigo-500 mr-1"></i> Transf.
                </span>
                <span class="font-black text-indigo-600 text-sm sm:text-base">+${{ number_format($ventasTransferencia, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-3.5 flex flex-col justify-center shadow-sm">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-minus-circle text-rose-500 mr-1"></i> Gastos
                </span>
                <span class="font-black text-rose-600 text-sm sm:text-base">-${{ number_format($totalGastos, 2) }}</span>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex flex-col justify-center shadow-sm col-span-2 sm:col-span-1">
                <span class="text-[9px] sm:text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Saldo Estimado</span>
                <span class="font-black text-blue-700 text-base sm:text-lg">${{ number_format($saldoEstimado, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- BLOQUE 1: Ventas del Turno --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden w-full">
        <div class="bg-slate-50/70 p-4 sm:p-5 border-b border-slate-200 flex flex-wrap gap-2 justify-between items-center w-full">
            <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
                <i class="fas fa-shopping-cart text-blue-600 mr-2"></i> Ventas del Turno
            </h3>
            <span class="text-[10px] sm:text-xs font-black bg-blue-50 text-blue-600 px-3 py-1.5 rounded-xl border border-blue-200 whitespace-nowrap">
                Total: ${{ number_format($totalVentas, 2) }}
            </span>
        </div>

        <div class="overflow-x-auto w-full">
            @if($historicoVentas->isEmpty())
                <div class="p-12 text-center flex flex-col items-center justify-center min-h-[160px]">
                    <i class="fas fa-inbox text-3xl text-slate-300 mb-2"></i>
                    <p class="text-xs font-bold text-slate-400">No hay ventas registradas en este turno.</p>
                </div>
            @else
                @php
                    $ventasAgrupadas = $historicoVentas->groupBy(fn($v) => $v->flujoable_id ?? 'sin-orden-'.$v->id);
                @endphp
                <table class="w-full text-xs sm:text-sm text-center border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-black text-[10px] sm:text-xs border-b border-slate-200 uppercase tracking-widest">
                            <th class="py-3.5 px-4">Hora</th>
                            <th class="py-3.5 px-4">Concepto</th>
                            <th class="py-3.5 px-4">Método(s) de Pago</th>
                            <th class="py-3.5 px-4">Total</th>
                            <th class="py-3.5 px-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($ventasAgrupadas as $ordenId => $pagos)
                            @php
                                $primera     = $pagos->first();
                                $totalFila   = $pagos->sum('monto');
                                $esMixto     = $pagos->count() > 1;
                                $ordenIdReal = $primera->flujoable_id;
                            @endphp
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-4 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($primera->fecha)->format('H:i') }} hrs
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-800">{{ $primera->concepto }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        @if($esMixto)
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wide whitespace-nowrap">
                                                <i class="fas fa-layer-group text-[9px] mr-1"></i> Pago mixto
                                            </span>
                                            @foreach($pagos as $pago)
                                                <div class="flex items-center gap-1.5 whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                                        {{ $pago->metodo_pago }}
                                                    </span>
                                                    <span class="text-[10px] font-bold text-slate-600">${{ number_format($pago->monto, 2) }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="px-3 py-1 rounded-lg text-xs font-black uppercase bg-blue-50 text-blue-700 border border-blue-100 whitespace-nowrap">
                                                {{ $primera->metodo_pago }}
                                            </span>
                                            @if(!empty($primera->referencia))
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-slate-100 text-slate-500 border border-slate-200 uppercase">
                                                    Ref: {{ $primera->referencia }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-black text-emerald-600 whitespace-nowrap">
                                    +${{ number_format($totalFila, 2) }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($ordenIdReal)
                                        <button type="button"
                                           onclick="abrirTicketModal('{{ route('admin.caja.ticket.imprimir.orden', $ordenIdReal) }}')"
                                           class="w-9 h-9 rounded-xl border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition-all flex items-center justify-center mx-auto shadow-sm"
                                           title="Reimprimir ticket">
                                            <i class="fas fa-print text-xs"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- BLOQUE 2: Gastos y Salidas --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden w-full">
        <div class="bg-slate-50/70 p-4 sm:p-5 border-b border-slate-200 flex flex-wrap gap-2 justify-between items-center w-full">
            <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
                <i class="fas fa-hand-holding-usd text-rose-500 mr-2"></i> Gastos y Salidas
            </h3>
            <span class="text-[10px] sm:text-xs font-black bg-rose-50 text-rose-600 px-3 py-1.5 rounded-xl border border-rose-200 whitespace-nowrap">
                Total: ${{ number_format($totalGastos, 2) }}
            </span>
        </div>

        <div class="overflow-x-auto w-full">
            @if($historicoGastos->isEmpty())
                <div class="p-12 text-center flex flex-col items-center justify-center min-h-[160px]">
                    <i class="fas fa-receipt text-3xl text-slate-300 mb-2"></i>
                    <p class="text-xs font-bold text-slate-400">No hay gastos o salidas registrados en este turno.</p>
                </div>
            @else
                <table class="w-full text-xs sm:text-sm text-center border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-black text-[10px] sm:text-xs border-b border-slate-200 uppercase tracking-widest">
                            <th class="py-3.5 px-4">Hora</th>
                            <th class="py-3.5 px-4">Categoría</th>
                            <th class="py-3.5 px-4 text-left">Concepto / Descripción</th>
                            <th class="py-3.5 px-4">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($historicoGastos as $gasto)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="py-4 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($gasto->fecha)->format('H:i') }} hrs</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-600 border border-rose-200 uppercase tracking-wide whitespace-nowrap">
                                        {{ $gasto->categoria }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-left font-medium">
                                    <span class="font-bold text-slate-800 block">{{ $gasto->concepto }}</span>
                                    @if($gasto->observaciones)
                                        <span class="text-xs text-slate-500 block mt-0.5">{{ $gasto->observaciones }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 font-black text-rose-600 whitespace-nowrap">-${{ number_format($gasto->monto, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

@include('admin.cobrar.modals.ticket-preview')

@push('scripts')
<script>
function abrirTicketModal(url) {
    const modal      = document.getElementById('modal-ticket-preview');
    const iframe     = document.getElementById('ticket-preview-iframe');
    const btnCerrar  = document.getElementById('btn-cerrar-ticket-preview');
    const btnCerrarX = document.getElementById('btn-cerrar-x-ticket-preview');
    const btnImprimir = document.getElementById('btn-imprimir-ticket-preview');

    if (!modal || !iframe) return;

    iframe.src = url;
    modal.classList.remove('hidden');

    const cerrar = () => { modal.classList.add('hidden'); iframe.src = ''; };
    btnCerrar.onclick  = cerrar;
    btnCerrarX.onclick = cerrar;

    btnImprimir.onclick = () => {
        try { iframe.contentWindow.print(); } catch(e) { window.open(url, '_blank'); }
    };
}
</script>
@endpush

@endsection