@extends('layouts.admin')

@section('title', 'Meseros | Finanzas | Ollintem Pro')
@section('header-title', 'Rendimiento de Meseros')
@section('header-subtitle', 'Ventas por turno y aportes al fondo')

@push('styles')
<style>
    /* Fondo general del sistema en gris extra claro */
    body, html, #app, main, .wrapper, .main-content {
        background-color: #f8fafc !important; /* slate-50 */
    }
    
    /* Textos del header superior en oscuro */
    header, header h1, header h2, header p, header span, .header-title, .header-subtitle {
        color: #0f172a !important; /* slate-900 */
    }

    /* Aumentamos tamaños del header */
    header h1, .header-title {
        font-size: 2.2rem !important; 
        line-height: 1.2 !important;
        font-weight: 800 !important; 
    }
    header p, header span, .header-subtitle {
        font-size: 1rem !important; 
        margin-top: 0.25rem !important;
        font-weight: 500 !important;
        color: #64748b !important; /* slate-500 */
    }

    /* ANIMACIONES DE ENTRADA */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0; 
    }
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6 lg:p-8 xl:p-12 max-w-[1800px] mx-auto w-full space-y-5 sm:space-y-8 flex-1 flex flex-col transition-all duration-300 relative z-10">

    {{-- CABECERA Y FILTRO --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-1 w-full sm:w-auto">
            <a href="{{ route('admin.finanzas.index') }}"
               class="text-xs font-bold text-slate-400 hover:text-blue-600 transition-colors inline-flex items-center gap-1 mb-2">
                <i class="fas fa-arrow-left"></i> Volver a Finanzas
            </a>
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Desglose por Mesero</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500">
                Ventas por turno y aportes al fondo de barra y cocina.
            </p>
        </div>

        {{-- Selector de día --}}
        <form method="GET" action="{{ route('admin.finanzas.meseros') }}" class="flex items-end gap-2 bg-white border border-slate-200 p-2 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
            <div>
                <label for="fecha" class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1 ml-1">Día a consultar</label>
                <input type="date" name="fecha" id="fecha" value="{{ $fecha->toDateString() }}"
                       class="px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 transition-all cursor-pointer">
            </div>
            <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-blue-500/20 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center justify-center gap-2 h-full border border-blue-500">
                <i class="fas fa-search"></i> Ver
            </button>
        </form>
    </div>

    {{-- TARJETAS DE RESUMEN --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-5">
        {{-- Venta del Día --}}
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-blue-900/5 hover:border-blue-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 100ms;">
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] bg-blue-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mt-1 group-hover:text-blue-600 transition-colors">Venta del día</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tabular-nums">${{ number_format($ventaDelDia, 2) }}</p>
        </div>

        {{-- Mesas Atendidas --}}
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-purple-900/5 hover:border-purple-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 200ms;">
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] bg-purple-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mt-1 group-hover:text-purple-600 transition-colors">Mesas atendidas</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tabular-nums">{{ $mesasDelDia }}</p>
        </div>

        {{-- FONDO POR REPARTIR --}}
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-emerald-900/5 hover:border-emerald-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 300ms;">
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] bg-emerald-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] font-black uppercase tracking-wider text-emerald-500 mt-1 group-hover:text-emerald-600 transition-colors flex items-center gap-1.5">
                <i class="fas fa-hand-holding-dollar"></i> Fondo barra y cocina
            </p>
            <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1 tabular-nums" id="fondo-dia-display">
                ${{ number_format($fondoDelDia, 2) }}
            </p>
            <p class="text-[10px] font-medium text-slate-400 mt-1">
                Por repartir del {{ $fecha->format('d/m/Y') }}
            </p>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up" style="animation-delay: 400ms;">
        <div class="overflow-x-auto pb-2">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Mesero</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Turno</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Mesas</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Venta total</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Efectivo</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Tarjeta</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Transfer.</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Aporte al fondo</th>
                        <th class="py-4 px-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($filas as $fila)
                        <tr class="hover:bg-blue-50/50 transition-colors duration-300 group"
                            data-caja="{{ $fila->caja_movimiento_id }}"
                            data-mesero="{{ $fila->mesero_id }}">

                            <td class="py-4 px-5 font-bold text-slate-800 text-sm group-hover:text-blue-800 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                        {{ strtoupper(substr($fila->mesero, 0, 1)) }}
                                    </div>
                                    {{ $fila->mesero }}
                                </div>
                            </td>

                            <td class="py-4 px-5 text-center">
                                <span class="text-[10px] font-black uppercase px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 group-hover:bg-white group-hover:border-blue-200 transition-colors">
                                    {{ $fila->turno ?? 'Sin turno' }}
                                </span>
                            </td>

                            <td class="py-4 px-5 text-center font-bold text-slate-700 text-sm">{{ $fila->mesas_atendidas }}</td>

                            <td class="py-4 px-5 text-right font-black text-slate-900 tabular-nums">${{ number_format($fila->venta_total, 2) }}</td>
                            <td class="py-4 px-5 text-right text-emerald-600 font-semibold tabular-nums">${{ number_format($fila->efectivo, 2) }}</td>
                            <td class="py-4 px-5 text-right text-blue-600 font-semibold tabular-nums">${{ number_format($fila->tarjeta, 2) }}</td>
                            <td class="py-4 px-5 text-right text-purple-600 font-semibold tabular-nums">${{ number_format($fila->transferencia, 2) }}</td>

                            {{-- APORTE AL FONDO --}}
                            <td class="py-4 px-5">
                                @if(auth()->user()->tienePermiso('finanzas.editar'))
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="relative group/input">
                                            <input type="text" inputmode="decimal" data-teclado="numerico"
                                                   class="input-aporte w-[72px] pl-3 pr-6 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 text-center transition-all shadow-sm"
                                                   value="{{ $fila->aporte_porcentaje !== null ? rtrim(rtrim(number_format($fila->aporte_porcentaje, 2, '.', ''), '0'), '.') : $porcentajeSugerido }}">
                                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[11px] font-black text-slate-400 group-focus-within/input:text-emerald-500">%</span>
                                        </div>
                                        <button type="button"
                                            class="btn-aporte px-3 py-2 rounded-xl bg-white border border-emerald-200 hover:bg-emerald-500 text-emerald-600 hover:text-white text-[10px] font-black uppercase tracking-wider transition-all shadow-sm active:scale-95">
                                            Aplicar
                                        </button>
                                    </div>
                                    <p class="monto-aporte text-center text-xs font-black mt-2 {{ $fila->aporte_monto > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $fila->aporte_monto > 0 ? '$' . number_format($fila->aporte_monto, 2) : 'Sin aporte' }}
                                    </p>
                                @else
                                    <p class="text-center text-xs font-black {{ $fila->aporte_monto > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $fila->aporte_monto > 0 ? '$' . number_format($fila->aporte_monto, 2) : 'Sin aporte' }}
                                    </p>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-center">
                                <button type="button"
                                    class="btn-detalle px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-[10px] font-black uppercase tracking-wider hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm whitespace-nowrap active:scale-95">
                                    <i class="fas fa-eye mr-1 text-blue-500"></i> Ver detalles
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-16 text-center bg-slate-50 border-t border-slate-100">
                                <i class="fas fa-user-tie text-4xl text-slate-300 mb-3"></i>
                                <p class="text-sm font-bold text-slate-500">No hay ventas registradas ese día</p>
                                <p class="text-xs font-medium text-slate-400 mt-1">Elige otra fecha en el selector superior.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-xs font-medium text-slate-500 leading-relaxed text-center sm:text-left bg-slate-100/50 p-4 rounded-xl border border-slate-200/50 animate-fade-in-up" style="animation-delay: 500ms;">
        <i class="fas fa-info-circle text-blue-500 mr-1"></i> El aporte se calcula sobre la venta total del mesero en ese turno. El porcentaje sugerido
        (<strong class="text-slate-700">{{ $porcentajeSugerido }}%</strong>) se puede cambiar en cada renglón; escribir <strong class="text-slate-700">0</strong> elimina el aporte.
    </p>
</div>

{{-- MODAL DE DETALLE --}}
<div id="modal-detalle-mesero" class="hidden fixed inset-0 z-[9998] items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" data-cerrar-detalle></div>

    <div class="relative w-full max-w-3xl bg-white rounded-[2rem] border border-slate-200 shadow-2xl overflow-hidden max-h-[85vh] flex flex-col animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight" id="detalle-titulo">Detalle</h3>
                <p class="text-xs font-medium text-slate-500 mt-0.5" id="detalle-subtitulo"></p>
            </div>
            <button type="button" data-cerrar-detalle
                class="w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-all shadow-sm">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 bg-slate-50/30 p-4 sm:p-6" id="detalle-contenido">
            <p class="p-8 text-center text-sm font-medium text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-500 mb-3 block"></i> Cargando información...
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // ---------------- APORTE AL FONDO ----------------
    document.querySelectorAll('.btn-aporte').forEach(btn => {
        btn.addEventListener('click', async () => {
            const fila = btn.closest('tr');
            const input = fila.querySelector('.input-aporte');
            const etiqueta = fila.querySelector('.monto-aporte');

            const crudo = (input.value || '').trim().replace(',', '.');
            const porcentaje = crudo === '' ? 0 : parseFloat(crudo);

            if (isNaN(porcentaje) || porcentaje < 0 || porcentaje > 100) {
                etiqueta.textContent = 'Entre 0 y 100';
                etiqueta.className = 'monto-aporte text-center text-xs font-black mt-2 text-rose-500';
                return;
            }

            btn.disabled = true;
            const textoOriginal = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const res = await fetch(@json(route('admin.finanzas.meseros.aporte')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        caja_movimiento_id: fila.dataset.caja,
                        mesero_id: fila.dataset.mesero,
                        porcentaje: porcentaje,
                    }),
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    etiqueta.textContent = data.monto > 0
                        ? '$' + Number(data.monto).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                        : 'Sin aporte';
                    etiqueta.className = 'monto-aporte text-center text-xs font-black mt-2 ' +
                        (data.monto > 0 ? 'text-emerald-600' : 'text-slate-400');

                    document.getElementById('fondo-dia-display').textContent =
                        '$' + Number(data.fondo_dia).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                } else {
                    etiqueta.textContent = data.message || 'Error';
                    etiqueta.className = 'monto-aporte text-center text-xs font-black mt-2 text-rose-500';
                }
            } catch (e) {
                console.error('Error al aplicar aporte:', e);
                etiqueta.textContent = 'Error de conexión';
                etiqueta.className = 'monto-aporte text-center text-xs font-black mt-2 text-rose-500';
            } finally {
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
            }
        });
    });

    // ---------------- DETALLE DE MESAS ----------------
    const modal = document.getElementById('modal-detalle-mesero');
    const contenido = document.getElementById('detalle-contenido');

    const cerrar = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
    modal.querySelectorAll('[data-cerrar-detalle]').forEach(el => el.addEventListener('click', cerrar));

    const dinero = n => '$' + Number(n).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    document.querySelectorAll('.btn-detalle').forEach(btn => {
        btn.addEventListener('click', async () => {
            const fila = btn.closest('tr');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            contenido.innerHTML = '<p class="p-8 text-center text-sm font-medium text-slate-400"><i class="fas fa-spinner fa-spin text-2xl text-blue-500 mb-3 block"></i> Cargando información...</p>';

            const url = @json(route('admin.finanzas.meseros.detalle'))
                + '?caja_movimiento_id=' + fila.dataset.caja
                + '&mesero_id=' + fila.dataset.mesero;

            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    contenido.innerHTML = '<p class="p-8 text-center text-sm font-bold text-rose-500 bg-rose-50 rounded-xl border border-rose-100">No se pudo cargar el detalle.</p>';
                    return;
                }

                document.getElementById('detalle-titulo').innerHTML = '<i class="fas fa-user-tie text-blue-500 mr-2"></i>' + data.mesero;
                document.getElementById('detalle-subtitulo').textContent =
                    'Turno ' + data.turno + ' · ' + data.fecha + ' · ' + data.totales.mesas + ' mesa(s)';

                if (!data.mesas.length) {
                    contenido.innerHTML = '<p class="p-8 text-center text-sm text-slate-400 font-medium">Sin mesas registradas en este turno.</p>';
                    return;
                }

                let html = '<div class="space-y-4">';

                data.mesas.forEach((m, i) => {
                    const hora = m.cerrada_el
                        ? new Date(m.cerrada_el.replace(' ', 'T')).toLocaleTimeString('es-MX', {hour: '2-digit', minute: '2-digit'})
                        : '--';

                    html += '<div class="border border-slate-200 bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">'
                        + '<button type="button" class="btn-mesa w-full px-5 py-4 flex flex-wrap items-center justify-between gap-3 bg-white hover:bg-blue-50/50 transition-colors text-left group border-b border-slate-50">'
                        + '<div class="flex items-center gap-3">'
                        + '<div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors">' + m.mesa + '</div>'
                        + '<div>'
                        + '<span class="font-black text-slate-900 group-hover:text-blue-900 transition-colors">Mesa ' + m.mesa + '</span>'
                        + '<span class="text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md ml-2 border border-slate-200">' + (m.numero_orden || '#') + '</span>'
                        + '<div class="text-[11px] font-medium text-slate-500 mt-1 flex items-center gap-2">'
                        + '<span><i class="fas fa-users text-slate-400"></i> ' + (m.personas || '-') + '</span>'
                        + '<span><i class="fas fa-clock text-slate-400"></i> ' + hora + '</span>'
                        + '<span><i class="fas fa-utensils text-slate-400"></i> ' + m.piezas + ' prod.</span>'
                        + (m.hay_cancelados ? '<span class="text-rose-500 font-bold bg-rose-50 px-1.5 py-0.5 rounded">Cancelados</span>' : '')
                        + '</div></div></div>'
                        + '<div class="text-right">'
                        + '<div class="font-black text-lg text-slate-900">' + dinero(m.total) + '</div>'
                        + '<div class="text-[10px] font-semibold text-slate-400 mt-0.5 flex gap-1 justify-end">'
                        + (m.efectivo > 0 ? '<span class="bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded border border-emerald-100">Efvo ' + dinero(m.efectivo) + '</span>' : '')
                        + (m.tarjeta > 0 ? '<span class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded border border-blue-100">Tarj ' + dinero(m.tarjeta) + '</span>' : '')
                        + (m.transferencia > 0 ? '<span class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded border border-purple-100">Transf ' + dinero(m.transferencia) + '</span>' : '')
                        + '</div></div>'
                        + '</button>';

                    html += '<div class="detalle-consumo bg-slate-50/50 ' + (i === 0 ? '' : 'hidden') + '">';

                    if (!m.productos.length) {
                        html += '<p class="px-5 py-4 text-xs font-medium text-slate-400">Sin productos registrados en esta mesa.</p>';
                    } else {
                        html += '<table class="w-full text-xs text-left">'
                            + '<thead>'
                            + '<tr class="text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-200">'
                            + '<th class="py-3 px-5">Producto</th>'
                            + '<th class="py-3 px-3 text-center">Cant.</th>'
                            + '<th class="py-3 px-3 text-right">P. unit.</th>'
                            + '<th class="py-3 px-5 text-right">Importe</th>'
                            + '</tr></thead><tbody class="divide-y divide-slate-100">';

                        m.productos.forEach(p => {
                            const tachado = p.cancelado ? 'opacity-50 grayscale' : '';
                            html += '<tr class="hover:bg-white transition-colors ' + tachado + '">'
                                + '<td class="py-2.5 px-5 font-bold text-slate-700">'
                                + p.producto
                                + (p.gramaje ? ' <span class="text-slate-400 font-medium ml-1">(' + p.gramaje + 'g)</span>' : '')
                                + (p.cancelado ? ' <span class="text-rose-500 font-black text-[9px] bg-rose-50 border border-rose-100 px-1.5 py-0.5 rounded ml-2">CANCELADO</span>' : '')
                                + (p.notas ? '<div class="text-[10px] text-slate-400 font-medium italic mt-0.5"><i class="fas fa-comment-alt text-slate-300 mr-1"></i>' + p.notas + '</div>' : '')
                                + '</td>'
                                + '<td class="py-2.5 px-3 text-center font-bold text-slate-600">' + p.cantidad + '</td>'
                                + '<td class="py-2.5 px-3 text-right font-medium text-slate-400">' + dinero(p.precio_unitario) + '</td>'
                                + '<td class="py-2.5 px-5 text-right font-black text-slate-800">' + dinero(p.importe) + '</td>'
                                + '</tr>';
                        });

                        html += '</tbody><tfoot>'
                            + '<tr class="border-t border-slate-200 bg-white">'
                            + '<td colspan="3" class="py-3 px-5 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Total Consumo</td>'
                            + '<td class="py-3 px-5 text-right text-sm font-black text-slate-900">' + dinero(m.consumo) + '</td>'
                            + '</tr></tfoot></table>';
                    }

                    html += '</div></div>';
                });

                const t = data.totales;
                html += '<div class="mt-6 rounded-2xl bg-slate-800 text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-lg">'
                    + '<div class="flex items-center gap-3">'
                    + '<div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20"><i class="fas fa-cash-register text-lg"></i></div>'
                    + '<span class="text-sm font-black uppercase tracking-widest text-slate-300">Total del turno</span>'
                    + '</div>'
                    + '<div class="text-left sm:text-right">'
                    + '<div class="text-2xl font-black text-white">' + dinero(t.total) + '</div>'
                    + '<div class="text-[10px] font-bold text-slate-400 mt-1 flex gap-2 flex-wrap sm:justify-end">'
                    + '<span class="bg-white/10 px-2 py-0.5 rounded">Efvo ' + dinero(t.efectivo) + '</span>'
                    + '<span class="bg-white/10 px-2 py-0.5 rounded">Tarj ' + dinero(t.tarjeta) + '</span>'
                    + '<span class="bg-white/10 px-2 py-0.5 rounded">Transf ' + dinero(t.transferencia) + '</span>'
                    + '</div></div></div>';

                html += '</div>';
                contenido.innerHTML = html;

                // Desplegar y contraer cada mesa
                contenido.querySelectorAll('.btn-mesa').forEach(b => {
                    b.addEventListener('click', () => {
                        b.nextElementSibling.classList.toggle('hidden');
                    });
                });
            } catch (e) {
                console.error('Error al cargar detalle:', e);
                contenido.innerHTML = '<p class="p-8 text-center text-sm font-bold text-rose-500 bg-rose-50 rounded-xl border border-rose-100">Error de conexión al cargar datos.</p>';
            }
        });
    });
});
</script>
@endsection