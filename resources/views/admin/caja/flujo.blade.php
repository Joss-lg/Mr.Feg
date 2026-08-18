@extends('layouts.admin')

@section('content')
{{-- Se reemplazó bg-slate-50 por bg-[#F2F2F2] en el contenedor principal --}}
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-[1800px] mx-auto space-y-6 sm:space-y-8 relative z-10 min-h-screen bg-[#F2F2F2] font-sans transition-colors duration-300">

    {{-- Encabezado y Alertas --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-2 sm:space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 sm:px-4 py-1.5 sm:py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 shadow-sm">
                <i class="fas fa-chart-line"></i> Monitoreo del Turno
            </div>
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight drop-shadow-sm">Gestión de Flujo de Caja</h1>
        </div>

        @if(session('success'))
            <div class="w-full sm:w-auto px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl shadow-sm text-xs font-bold flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500 text-base"></i>
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- FRANJA SUPERIOR: Resumen de Turno --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm p-5 sm:p-8 relative overflow-hidden w-full animate-fade-in-up" style="animation-delay: 150ms;">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-600"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5 sm:mb-6">
            <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
                <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center whitespace-nowrap">
                    <i class="fas fa-cash-register text-blue-600 mr-2"></i> Resumen de Turno
                </h3>
                <span class="text-[10px] sm:text-xs font-bold text-slate-400">ID Caja: <span class="text-slate-700">#{{ $cajaActiva->id }}</span></span>
                <span class="text-[10px] sm:text-xs font-bold text-slate-400">Cajero: <span class="text-slate-700">{{ $cajaActiva->user->nombre ?? 'Admin' }}</span></span>
                <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black bg-blue-50 border border-blue-100 text-blue-600 uppercase tracking-wider">
                    {{ $cajaActiva->turno ?? 'Matutino' }}
                </span>
            </div>

            <div class="flex gap-2.5 w-full md:w-auto">
                <a href="{{ route('admin.caja.reporte.pdf', $cajaActiva->id) }}" target="_blank"
                    class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-slate-50 border border-slate-200 hover:bg-blue-50 hover:border-blue-100 hover:text-blue-600 text-slate-500 font-black text-[10px] sm:text-xs tracking-widest uppercase h-11 sm:h-12 px-5 rounded-xl transition-all shadow-sm whitespace-nowrap">
                     <i class="fas fa-file-export"></i> Exportar
                </a>

                <button id="btnAbrirCierreCaja" type="button" class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-rose-50 border border-rose-100 hover:bg-rose-600 hover:border-rose-600 hover:text-white text-rose-600 font-black text-[10px] sm:text-xs tracking-widest uppercase h-11 sm:h-12 px-5 rounded-xl transition-all shadow-sm cursor-pointer whitespace-nowrap active:scale-95">
                    <i class="fas fa-lock"></i> Cerrar Caja
                </button>
            </div>
        </div>

        {{-- Grid de tarjetas resumen --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col justify-center">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Saldo Inicial</span>
                <span class="font-black text-slate-800 text-sm sm:text-base">${{ number_format($cajaActiva->monto_inicial, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col justify-center">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-money-bill-wave text-emerald-500 mr-1 w-3"></i> Efectivo
                </span>
                <span class="font-black text-emerald-600 text-sm sm:text-base">+${{ number_format($ventasEfectivo, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col justify-center">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-credit-card text-sky-500 mr-1 w-3"></i> Tarjeta
                </span>
                <span class="font-black text-sky-600 text-sm sm:text-base">+${{ number_format($ventasTarjeta, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col justify-center">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-university text-indigo-500 mr-1 w-3"></i> Transf.
                </span>
                <span class="font-black text-indigo-600 text-sm sm:text-base">+${{ number_format($ventasTransferencia, 2) }}</span>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col justify-center">
                <span class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-minus-circle text-rose-500 mr-1 w-3"></i> Gastos
                </span>
                <span class="font-black text-rose-600 text-sm sm:text-base">-${{ number_format($totalGastos, 2) }}</span>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5 flex flex-col justify-center col-span-2 sm:col-span-1">
                <span class="text-[9px] sm:text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1">Saldo Estimado</span>
                <span class="font-black text-blue-600 text-base sm:text-lg">${{ number_format($saldoEstimado, 2) }}</span>
            </div>

        </div>
    </div>

    {{-- TABLAS DE HISTORIAL --}}
    <div class="space-y-6 sm:space-y-8 w-full">

        {{-- BLOQUE 1: Ventas del Turno --}}
        <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden w-full animate-fade-in-up" style="animation-delay: 300ms;">
            <div class="bg-sky-50 p-4 sm:p-5 border-b border-slate-100 flex flex-wrap gap-2 justify-between items-center w-full">
                <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
                    <i class="fas fa-shopping-cart text-sky-600 mr-2"></i> Ventas del Turno
                </h3>
                <span class="text-[10px] sm:text-xs font-black bg-white text-sky-600 px-3 py-1.5 rounded-lg border border-sky-100 whitespace-nowrap">
                    Total: ${{ number_format($totalVentas, 2) }}
                </span>
            </div>

            <div class="overflow-x-auto w-full -webkit-overflow-scrolling-touch">
                @if($historicoVentas->isEmpty())
                    <div class="p-8 sm:p-12 text-center flex flex-col items-center justify-center min-h-[140px] sm:min-h-[180px]">
                        <i class="fas fa-inbox text-2xl sm:text-3xl text-slate-300 mb-3"></i>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">No hay ventas registradas en este turno.</p>
                    </div>
                @else
                    @php
                        $ventasAgrupadas = $historicoVentas->groupBy(fn($v) => $v->flujoable_id ?? 'sin-orden-'.$v->id);
                    @endphp
                    <table class="w-full text-xs sm:text-sm text-center border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 font-black text-[10px] sm:text-xs border-b border-slate-200 uppercase tracking-wider">
                                <th class="py-3 px-2 sm:px-4">Hora</th>
                                <th class="py-3 px-2 sm:px-4">Concepto</th>
                                <th class="py-3 px-2 sm:px-4">Método(s) de Pago</th>
                                <th class="py-3 px-2 sm:px-4">Total</th>
                                <th class="py-3 px-2 sm:px-4"></th>
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
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-[10px] sm:text-xs font-semibold text-slate-400 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($primera->fecha)->format('H:i') }} hrs
                                    </td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 font-bold text-slate-800">{{ $primera->concepto }}</td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            @if($esMixto)
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black bg-amber-50 border border-amber-100 text-amber-600 uppercase tracking-wide whitespace-nowrap">
                                                    <i class="fas fa-layer-group text-[8px] mr-0.5"></i> Pago mixto
                                                </span>
                                                @foreach($pagos as $pago)
                                                    @php
                                                        $colorMetodo = match($pago->metodo_pago) {
                                                            'tarjeta'       => 'sky',
                                                            'transferencia' => 'indigo',
                                                            'descuento'     => 'violet',
                                                            default          => 'emerald',
                                                        };
                                                    @endphp
                                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black tracking-wider bg-{{ $colorMetodo }}-50 border border-{{ $colorMetodo }}-100 text-{{ $colorMetodo }}-600 uppercase">
                                                            {{ $pago->metodo_pago }}
                                                        </span>
                                                        <span class="text-[10px] font-bold text-slate-400">${{ number_format($pago->monto, 2) }}</span>
                                                        @if(!empty($pago->referencia))
                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono bg-slate-50 border border-slate-200 text-slate-400">
                                                                #{{ $pago->referencia }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                @php
                                                    $colorMetodo = match($primera->metodo_pago) {
                                                        'tarjeta'       => 'sky',
                                                        'transferencia' => 'indigo',
                                                        'descuento'     => 'violet',
                                                        default          => 'emerald',
                                                    };
                                                @endphp
                                                <span class="px-2.5 py-1 rounded-md text-[10px] sm:text-[11px] font-black tracking-wider bg-{{ $colorMetodo }}-50 border border-{{ $colorMetodo }}-100 text-{{ $colorMetodo }}-600 uppercase whitespace-nowrap">
                                                    {{ $primera->metodo_pago }}
                                                </span>
                                                @if(!empty($primera->referencia))
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-slate-50 border border-slate-200 text-slate-400 uppercase tracking-wide whitespace-nowrap">
                                                        <i class="fas fa-hashtag text-[8px] text-slate-400 mr-0.5"></i>Ref: {{ $primera->referencia }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 font-black text-emerald-600 whitespace-nowrap">
                                        +${{ number_format($totalFila, 2) }}
                                    </td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                                class="btn-ver-venta flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:bg-blue-50 hover:border-blue-100 hover:text-blue-600 transition-all outline-none active:scale-95"
                                                data-venta="{{ $primera->id }}"
                                                title="Ver detalle">
                                                <i class="fas fa-eye text-xs"></i>
                                            </button>
                                            @if($ordenIdReal)
                                                <a href="{{ route('admin.caja.ticket.imprimir.orden', $ordenIdReal) }}"
                                                   target="_blank"
                                                   class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:bg-amber-50 hover:border-amber-100 hover:text-amber-600 transition-all outline-none active:scale-95"
                                                   title="Reimprimir ticket">
                                                    <i class="fas fa-print text-xs"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- BLOQUE 2: Gastos y Salidas --}}
        <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden w-full animate-fade-in-up" style="animation-delay: 450ms;">
            <div class="bg-rose-50 p-4 sm:p-5 border-b border-slate-100 flex flex-wrap gap-2 justify-between items-center w-full">
                <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
                    <i class="fas fa-hand-holding-usd text-rose-600 mr-2"></i> Gastos y Salidas
                </h3>
                <span class="text-[10px] sm:text-xs font-black bg-white text-rose-600 px-3 py-1.5 rounded-lg border border-rose-100 whitespace-nowrap">
                    Total: ${{ number_format($totalGastos, 2) }}
                </span>
            </div>

            <div class="overflow-x-auto w-full -webkit-overflow-scrolling-touch">
                @if($historicoGastos->isEmpty())
                    <div class="p-8 sm:p-12 text-center flex flex-col items-center justify-center min-h-[140px] sm:min-h-[180px]">
                        <i class="fas fa-receipt text-2xl sm:text-3xl text-slate-300 mb-3"></i>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">No hay gastos o salidas registrados en este turno.</p>
                    </div>
                @else
                    <table class="w-full text-xs sm:text-sm text-center border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 font-black text-[10px] sm:text-xs border-b border-slate-200 uppercase tracking-wider">
                                <th class="py-3 px-2 sm:px-4">Hora</th>
                                <th class="py-3 px-2 sm:px-4">Categoría</th>
                                <th class="py-3 px-2 sm:px-4 text-left">Concepto / Descripción</th>
                                <th class="py-3 px-2 sm:px-4">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($historicoGastos as $gasto)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-[10px] sm:text-xs font-semibold text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($gasto->fecha)->format('H:i') }} hrs</td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-black bg-rose-50 border border-rose-100 text-rose-600 uppercase tracking-wide whitespace-nowrap">
                                            {{ $gasto->categoria }}
                                        </span>
                                    </td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-left font-medium">
                                        <span class="font-bold text-slate-800 block">{{ $gasto->concepto }}</span>
                                        @if($gasto->observaciones)
                                            <span class="text-[10px] sm:text-xs text-slate-400 block mt-0.5">{{ $gasto->observaciones }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 font-black text-rose-600 whitespace-nowrap">-${{ number_format($gasto->monto, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- BLOQUE 3: Cuentas canceladas --}}
        @if(($historicoCancelaciones ?? collect())->isNotEmpty())
            <div class="bg-white border border-rose-200 rounded-[2rem] shadow-sm overflow-hidden w-full animate-fade-in-up" style="animation-delay: 600ms;">
                <div class="bg-rose-50 p-4 sm:p-5 border-b border-slate-100 flex flex-wrap gap-2 justify-between items-center w-full">
                    <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
                        <i class="fas fa-ban text-rose-600 mr-2"></i> Cuentas canceladas (no cobradas)
                    </h3>
                    <span class="text-[10px] sm:text-xs font-black bg-white text-rose-600 px-3 py-1.5 rounded-lg border border-rose-100 whitespace-nowrap">
                        Total: ${{ number_format($totalCancelaciones ?? 0, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto w-full -webkit-overflow-scrolling-touch">
                    <table class="w-full text-xs sm:text-sm text-center border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 font-black text-[10px] sm:text-xs border-b border-slate-200 uppercase tracking-wider">
                                <th class="py-3 px-2 sm:px-4">Hora</th>
                                <th class="py-3 px-2 sm:px-4 text-left">Mesa y motivo</th>
                                <th class="py-3 px-2 sm:px-4">Autorizó</th>
                                <th class="py-3 px-2 sm:px-4">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($historicoCancelaciones as $cancelacion)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-[10px] sm:text-xs font-semibold text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($cancelacion->fecha)->format('H:i') }} hrs</td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-left font-bold text-slate-800">{{ $cancelacion->concepto }}</td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-[10px] sm:text-xs text-slate-400 whitespace-nowrap">{{ $cancelacion->referencia }}</td>
                                    <td class="py-3 sm:py-4 px-2 sm:px-4 font-black text-rose-600 whitespace-nowrap">-${{ number_format($cancelacion->monto, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="px-4 sm:px-5 py-3 text-[10px] sm:text-[11px] text-slate-500 border-t border-slate-100 leading-snug">
                    Este dinero nunca entró al cajón, así que no cuenta como venta ni afecta el efectivo esperado del corte.
                </p>
            </div>
        @endif

    </div>
</div>

{{-- MODAL: DETALLE DE UNA VENTA DEL TURNO --}}
<div id="modal-detalle-venta" class="hidden fixed inset-0 z-[9998] items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-cerrar-venta></div>

    <div class="relative w-full max-w-lg bg-white rounded-[2rem] border border-slate-200 shadow-2xl overflow-hidden max-h-[85vh] flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-black text-slate-800" id="venta-titulo">Detalle de la venta</h3>
                <p class="text-[11px] font-medium text-slate-400" id="venta-subtitulo"></p>
            </div>
            <button type="button" data-cerrar-venta
                class="group w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all shrink-0 outline-none">
                <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
            </button>
        </div>

        <div class="overflow-y-auto flex-1" id="venta-contenido">
            <p class="p-8 text-center text-sm text-slate-400 font-medium">Cargando...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-detalle-venta');
    if (!modal) return;

    const contenido = document.getElementById('venta-contenido');
    const cerrar = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
    modal.querySelectorAll('[data-cerrar-venta]').forEach(el => el.addEventListener('click', cerrar));

    const dinero = n => '$' + Number(n).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const urlBase = @json(url('/caja/venta'));

    document.querySelectorAll('.btn-ver-venta').forEach(btn => {
        btn.addEventListener('click', async () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            contenido.innerHTML = '<p class="p-8 text-center text-sm text-slate-400 font-medium">Cargando...</p>';

            try {
                const res = await fetch(urlBase + '/' + btn.dataset.venta + '/detalle', {
                    headers: { 'Accept': 'application/json' }
                });
                const d = await res.json();

                if (!res.ok || !d.success) {
                    contenido.innerHTML = '<p class="p-8 text-center text-sm text-rose-500 font-medium">No se pudo cargar el detalle.</p>';
                    return;
                }

                document.getElementById('venta-titulo').textContent =
                    d.mesa ? ('Mesa ' + d.mesa) : d.concepto;
                document.getElementById('venta-subtitulo').textContent =
                    [d.orden, d.hora, d.personas ? d.personas + ' pers.' : null].filter(Boolean).join(' \u00b7 ');

                let html = '<div class="p-6 space-y-4">';

                // Quien atendio y quien cobro
                html += '<div class="grid grid-cols-2 gap-3">'
                    + '<div class="rounded-xl border border-slate-200 bg-slate-50 p-3">'
                    + '<p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Mesero que atendió</p>'
                    + '<p class="text-sm font-bold text-slate-800 mt-0.5">' + d.mesero + '</p></div>'
                    + '<div class="rounded-xl border border-slate-200 bg-slate-50 p-3">'
                    + '<p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Cajero que cobró</p>'
                    + '<p class="text-sm font-bold text-slate-800 mt-0.5">' + d.cajero + '</p>'
                    + (d.cajero_aproximado
                        ? '<p class="text-[9px] text-amber-600 mt-0.5 leading-tight">Cobro anterior al registro de cajero: se muestra quien abrió el turno.</p>'
                        : '')
                    + '</div></div>';

                // Cobro
                html += '<div class="rounded-xl bg-emerald-50 border border-emerald-100 px-4 py-3 flex items-center justify-between">'
                    + '<div><p class="text-[10px] font-black uppercase tracking-wider text-emerald-600">'
                    + d.metodo + (d.referencia ? ' \u00b7 ref ' + d.referencia : '') + '</p>'
                    + '<p class="text-[11px] text-slate-500">' + d.concepto + '</p></div>'
                    + '<span class="text-xl font-black text-emerald-600">' + dinero(d.monto) + '</span></div>';

                // Consumo
                if (d.productos.length) {
                    html += '<div><p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Consumo de la mesa</p>'
                        + '<table class="w-full text-xs"><tbody class="divide-y divide-slate-100">';
                    d.productos.forEach(p => {
                        html += '<tr class="' + (p.cancelado ? 'line-through opacity-50' : '') + '">'
                            + '<td class="py-2 text-slate-800">' + p.producto
                            + (p.cancelado ? ' <span class="text-rose-500 font-bold text-[10px] no-underline">CANCELADO</span>' : '')
                            + (p.notas ? '<div class="text-[10px] text-slate-400 italic">' + p.notas + '</div>' : '')
                            + '</td>'
                            + '<td class="py-2 text-center w-12">x' + p.cantidad + '</td>'
                            + '<td class="py-2 text-right w-24 font-bold text-slate-800">' + dinero(p.importe) + '</td></tr>';
                    });
                    html += '</tbody><tfoot><tr class="border-t border-slate-200">'
                        + '<td colspan="2" class="py-2 text-right text-[10px] font-black uppercase tracking-wider text-slate-400">Consumo</td>'
                        + '<td class="py-2 text-right font-black text-slate-800">' + dinero(d.consumo) + '</td>'
                        + '</tr></tfoot></table></div>';

                    if (Math.abs(d.consumo - d.monto) > 0.01) {
                        html += '<p class="text-[10px] text-slate-500 leading-snug">'
                            + 'El consumo y el cobro no coinciden porque esta cuenta se pagó en varias partes '
                            + '(pago combinado o cuenta dividida), o incluye IVA, propina o descuento.</p>';
                    }
                } else {
                    html += '<p class="text-xs text-slate-400">Sin productos ligados a este movimiento.</p>';
                }

                html += '</div>';
                contenido.innerHTML = html;

            } catch (e) {
                console.error('Error al cargar la venta:', e);
                contenido.innerHTML = '<p class="p-8 text-center text-sm text-rose-500 font-medium">Error de conexión.</p>';
            }
        });
    });
});
</script>

@include('admin.caja.corte')
@endsection

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnAbrir = document.getElementById('btnAbrirCierreCaja');
    const modal = document.getElementById('modalCierreCaja');
    const btnCerrarX = document.getElementById('btnCerrarModalX');
    const btnCancelar = document.getElementById('btnCancelarModal');
    const backdrop = document.getElementById('backdropCierreCaja');
    const inputMonto = document.getElementById('monto_final_real');

    if (btnAbrir && modal) {
        btnAbrir.addEventListener('click', () => {
            modal.classList.remove('hidden');
            if (inputMonto) {
                setTimeout(() => inputMonto.focus(), 50);
            }
        });
    }

    const ocultarModal = () => {
        if (modal) modal.classList.add('hidden');
    };

    if (btnCerrarX) btnCerrarX.addEventListener('click', ocultarModal);
    if (btnCancelar) btnCancelar.addEventListener('click', ocultarModal);
    if (backdrop) backdrop.addEventListener('click', ocultarModal);

    // --- Diferencia en vivo mientras se teclea el conteo ---
    const cajaEsperado = document.getElementById('efectivoEsperado');
    const cajaDiferencia = document.getElementById('diferenciaCorte');

    if (inputMonto && cajaEsperado && cajaDiferencia) {
        const esperado = parseFloat(cajaEsperado.dataset.esperado) || 0;

        inputMonto.addEventListener('input', () => {
            const val = parseFloat(inputMonto.value) || 0;
            const diff = val - esperado;

            if (inputMonto.value.trim() === '') {
                cajaDiferencia.classList.add('hidden');
                return;
            }

            cajaDiferencia.classList.remove('hidden');

            if (diff < 0) {
                cajaDiferencia.className = 'mt-2 px-3 py-2 rounded-xl text-sm font-black flex items-center justify-between bg-rose-50 text-rose-600 border border-rose-100';
                cajaDiferencia.innerHTML = '<span>Faltante:</span><span>-$' + Math.abs(diff).toFixed(2) + '</span>';
            } else if (diff > 0) {
                cajaDiferencia.className = 'mt-2 px-3 py-2 rounded-xl text-sm font-black flex items-center justify-between bg-emerald-50 text-emerald-600 border border-emerald-100';
                cajaDiferencia.innerHTML = '<span>Sobrante:</span><span>+$' + diff.toFixed(2) + '</span>';
            } else {
                cajaDiferencia.className = 'mt-2 px-3 py-2 rounded-xl text-sm font-black flex items-center justify-between bg-blue-50 text-blue-600 border border-blue-100';
                cajaDiferencia.innerHTML = '<span>Cuadre exacto</span><span>$0.00</span>';
            }
        });
    }
});
</script>