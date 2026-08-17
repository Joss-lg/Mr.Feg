{{-- BACKDROP PARA MÓVIL (Fondo oscuro al abrir el carrito flotante) --}}
<div id="backdropOrdenMobile" onclick="toggleOrdenMobile()"
     class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300">
</div>

{{-- ========================================== --}}
{{-- COLUMNA 1: ACCIONES RÁPIDAS (SOLO DESKTOP ≥1024px) --}}
{{-- ========================================== --}}
<aside id="col-acciones" class="hidden lg:flex w-full lg:w-[190px] xl:w-[220px] flex-shrink-0 h-full flex-col bg-slate-50 border-r border-slate-200 p-4 pb-4 z-20 overflow-y-auto hide-scroll">

    <div class="flex items-center gap-2 mb-6">
        <button type="button" onclick="salirComanda()" class="flex-1 h-10 rounded-xl bg-white border border-slate-200 text-slate-800 font-semibold text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-slate-100 hover:border-blue-300 hover:shadow-md transition-all duration-150 active:scale-95 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50">
            <i class="fas fa-arrow-left text-slate-500 text-[10px]"></i> Mesas
        </button>
    </div>

    <div class="flex items-center justify-between mb-6 p-3 rounded-2xl bg-gradient-to-br from-white to-transparent border border-slate-200 shadow-sm">
        <div class="flex flex-col">
            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-1">Activa</span>
            <h3 class="text-2xl font-black tracking-tight text-slate-800 leading-none">
                @if($mesa->esDelivery())
                    <i class="fas fa-motorcycle text-orange-500"></i> {{ $mesa->plataformaDelivery->nombre ?? 'Delivery' }}
                @else
                    Mesa {{ $mesa->numero ?? '12M' }}
                @endif
            </h3>
        </div>
        <div class="relative flex items-center justify-center">
            <div class="absolute w-4 h-4 rounded-full bg-emerald-500/30 animate-pulse"></div>
            <div class="relative w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.6)] border border-slate-50"></div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2 flex-1 overflow-y-auto hide-scroll pb-2">
        <button type="button" onclick="ajustarPersonas()" class="flex flex-col items-center justify-center p-3 rounded-[16px] bg-white border border-slate-200 hover:bg-slate-100 hover:border-blue-500/30 hover:shadow-md transition-all duration-150 active:scale-95 group shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50">
            <i class="fas fa-users text-slate-500 group-hover:text-blue-500 mb-2 text-sm transition-colors duration-150"></i>
            <span class="text-[10px] font-medium text-slate-500 group-hover:text-slate-800 transition-colors">Personas</span>
            <span id="txtPersonas" class="text-xs font-bold text-slate-800 mt-0.5">{{ $mesa->capacidad ?? 1 }}</span>
        </button>

        <button type="button" onclick="imprimirPrecuenta()" class="flex flex-col items-center justify-center p-3 rounded-[16px] bg-white border border-slate-200 hover:bg-slate-100 hover:border-blue-500/30 hover:shadow-md transition-all duration-150 active:scale-95 group shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50">
            <i class="fas fa-receipt text-slate-500 group-hover:text-blue-500 mb-2 text-sm transition-colors duration-150"></i>
            <span class="text-[10px] font-medium text-slate-500 group-hover:text-slate-800 transition-colors leading-tight text-center">Pre<br>Cuenta</span>
        </button>

        <button type="button" onclick="agregarNota()" class="flex flex-col items-center justify-center p-3 rounded-[16px] bg-white border border-slate-200 hover:bg-slate-100 hover:border-blue-500/30 hover:shadow-md transition-all duration-150 active:scale-95 group shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50">
            <i class="fas fa-pen text-slate-500 group-hover:text-blue-500 mb-2 text-sm transition-colors duration-150"></i>
            <span class="text-[10px] font-medium text-slate-500 group-hover:text-slate-800 transition-colors">Nota</span>
        </button>

        {{-- Traspaso movido aquí, a lado de Nota --}}
        <button type="button" onclick="llamarCapitan()" class="flex flex-col items-center justify-center p-3 rounded-[16px] bg-white border border-slate-200 hover:bg-slate-100 hover:border-indigo-500/30 hover:shadow-md transition-all duration-150 active:scale-95 group shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/50">
            <i class="fas fa-exchange-alt text-slate-500 group-hover:text-indigo-500 mb-2 text-sm transition-colors duration-150"></i>
            <span class="text-[10px] font-medium text-slate-500 group-hover:text-slate-800 transition-colors">Traspaso</span>
        </button>

{{-- Tipo Pedido / Cliente (Escritorio) - Solo aparece si es mesa temporal de Llevar o Domicilio --}}
@php
    $esMesaVirtual = \Illuminate\Support\Str::startsWith(strtoupper($mesa->numero), ['DOM', 'LLEVAR']);
@endphp

@if($esMesaVirtual)
<button type="button" id="btnTipoPedidoDesktop" onclick="manejarClickTipoPedido()" class="col-span-2 mt-1 flex flex-col items-center justify-center py-3 px-4 rounded-[16px] bg-white border border-slate-200 hover:bg-slate-100 hover:border-amber-500/30 hover:shadow-md transition-all duration-150 active:scale-95 group shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/50">
    <div class="flex items-center gap-2 mb-1">
        <i class="fas fa-user-tag text-slate-500 group-hover:text-amber-500 text-sm transition-colors duration-150"></i>
        <span id="lbl-titulo-tarjeta" class="text-[10px] font-medium text-slate-500 group-hover:text-slate-800 transition-colors uppercase tracking-wider">Tipo de Pedido</span>
    </div>
    <span id="lbl-tipo-pedido-actual" class="text-[11px] font-black text-blue-500 mt-1">Comedor</span>
</button>
@endif

        @if($esCapitan ?? false)
            <button type="button" onclick="llamarCapitan()" class="col-span-2 mt-1 h-12 flex items-center justify-center gap-2 rounded-[16px] bg-gradient-to-b from-blue-600 to-blue-600 border border-slate-200 hover:opacity-90 hover:shadow-lg transition-all duration-150 active:scale-95 group text-white shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-50">
                <i class="fas fa-shield-alt text-[11px]"></i>
                <span class="text-[10px] font-bold uppercase tracking-widest">Capitán</span>
            </button>
        @endif
    </div>

    <button type="button" onclick="limpiarTicket()" class="mt-4 w-full h-12 rounded-[16px] border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white hover:shadow-md hover:shadow-red-500/20 transition-all duration-150 active:scale-95 flex items-center justify-center gap-2 font-bold text-[10px] uppercase tracking-widest shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50">
        <i class="fas fa-trash-alt text-[11px]"></i> Eliminar Todo
    </button>
</aside>

{{-- ========================================== --}}
{{-- COLUMNA 2: TICKET / COMANDA (CENTRAL)      --}}
{{-- ========================================== --}}
{{-- ═══════════════════════════════════════════════════════════
     BARRA FIJA INFERIOR — Solo móvil (< md)
     Siempre visible, con acciones rápidas del platillo y botón
     para abrir el panel completo del ticket.

     FIX: este contenedor no tenía "flex", así que los botones
     (que usan shrink-0/whitespace-nowrap pensando en un layout de
     fila) se apilaban verticalmente como bloques normales,
     generando la tarjeta flotante rara que aparecía sobre el
     catálogo en móvil. Con flex + overflow-x-auto quedan en una
     sola fila horizontal con scroll, como se pensó originalmente.
     ════════════════════════════════════════════════════════════ --}}
<div id="barra-acciones-mobile-wrapper" class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-slate-50 border-t border-slate-200 shadow-[0_-4px_20px_rgba(0,0,0,0.12)]" style="padding-bottom: env(safe-area-inset-bottom)">

<div id="barra-acciones-mobile"
     class="flex items-center gap-2 overflow-x-auto hide-scroll px-3 py-2.5">

  {{-- Botón Móvil: Tipo de Pedido - Solo para Llevar o Domicilio --}}
@php
    $esMesaVirtual = \Illuminate\Support\Str::startsWith(strtoupper($mesa->numero), ['DOM', 'LLEVAR']);
@endphp

@if($esMesaVirtual)
<button type="button" id="btnTipoPedidoMobile" onclick="manejarClickTipoPedido()"
    class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-[11px] font-bold text-slate-800 shadow-sm active:scale-95 whitespace-nowrap">
    <i class="fas fa-shopping-bag text-amber-500 text-[10px]"></i>
    <span id="lbl-tipo-pedido-mobile">Comedor</span>
</button>
@endif
        <button type="button" onclick="ajustarPersonas()"
            class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-[11px] font-bold text-slate-800 shadow-sm active:scale-95 whitespace-nowrap">
            <i class="fas fa-users text-blue-500 text-[10px]"></i>
            <span id="txtPersonasMobile">{{ $mesa->capacidad ?? 1 }}</span> Pax
        </button>
        <button type="button" onclick="agregarNota()"
            class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-[11px] font-bold text-slate-800 shadow-sm active:scale-95 whitespace-nowrap">
            <i class="fas fa-pen text-blue-500 text-[10px]"></i> Nota
        </button>
        <button type="button" onclick="llamarCapitan()"
            class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-[11px] font-bold text-slate-800 shadow-sm active:scale-95 whitespace-nowrap">
            <i class="fas fa-exchange-alt text-indigo-500 text-[10px]"></i> Traspaso
        </button>
        <button type="button" onclick="imprimirPrecuenta()"
            class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-[11px] font-bold text-slate-800 shadow-sm active:scale-95 whitespace-nowrap">
            <i class="fas fa-receipt text-blue-500 text-[10px]"></i> Precuenta
        </button>
        <button type="button" onclick="limpiarTicket()"
            class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-[11px] font-bold text-red-500 shadow-sm active:scale-95 whitespace-nowrap">
            <i class="fas fa-trash-alt text-[10px]"></i> Limpiar
        </button>
</div>

    {{-- Degradado indicador de scroll: avisa visualmente que hay más
         botones a la derecha. Se oculta solo con JS cuando ya no queda
         nada por deslizar (ver script más abajo). --}}
    <div id="fade-scroll-acciones" class="pointer-events-none absolute right-0 top-0 bottom-0 w-10 bg-gradient-to-l from-slate-50 to-transparent transition-opacity duration-200"></div>
</div>

<script>
(function () {
    const barra = document.getElementById('barra-acciones-mobile');
    const fade = document.getElementById('fade-scroll-acciones');
    if (!barra || !fade) return;

    function actualizarFade() {
        // Si ya se llegó al final del scroll (con 4px de margen), se oculta el degradado.
        const alFinal = barra.scrollLeft + barra.clientWidth >= barra.scrollWidth - 4;
        fade.style.opacity = alFinal ? '0' : '1';
    }

    barra.addEventListener('scroll', actualizarFade, { passive: true });
    window.addEventListener('resize', actualizarFade);
    // Estado inicial (por si en pantallas grandes ya caben todos los botones)
    actualizarFade();
})();
</script>

<script>
(function () {
    window.salirComanda = async function () {
        const config  = window.ComandaConfig || {};
        const mesa    = config.mesa || {};
        const csrf    = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const dashboard = config.rutas?.dashboard || '/mesero/dashboard';

        // Si es delivery y no tiene items en el ticket, cancelar la mesa virtual
        if (mesa.esDelivery) {
            const ticketItems = document.querySelectorAll('#listaTicket .ticket-item').length;
            const enviadosDB  = (window.platillosEnviadosDB || []).length;

            if (ticketItems === 0 && enviadosDB === 0) {
                try {
                    await fetch(`/mesero/delivery/${mesa.id}/cancelar-vacio`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    });
                } catch (e) {
                    // Si falla, igual redirigir
                }
                window.location.href = dashboard;
                return;
            }
        }

        window.location.href = dashboard;
    };
})();
</script>

<section id="col-ticket" class="
    /* --- ESTILOS PARA TABLET/DESKTOP (≥768px) --- */
    md:flex md:w-[300px] lg:w-[320px] xl:w-[360px] md:flex-shrink-0 md:h-full md:flex-col md:bg-slate-50 md:border-r md:border-slate-200 md:relative md:z-10 md:shadow-[20px_0_40px_-15px_rgba(0,0,0,0.06)] md:translate-y-0 md:rounded-none

    /* --- ESTILOS PARA TELÉFONO (Panel Inferior) --- */
    fixed inset-x-0 bottom-0 z-50 flex flex-col bg-slate-50 rounded-t-[24px]
    h-[85vh] shadow-[0_-10px_40px_rgba(0,0,0,0.15)]
    transition-transform duration-300 translate-y-full
">

    {{-- CABECERA MÓVIL (Botón Salir + Manija + Info Mesa) --}}
    <div class="md:hidden w-full flex items-center justify-between px-4 pt-3 pb-2 border-b border-slate-200">
        <button type="button" onclick="window.location.href='{{ route('mesero.dashboard') }}'" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-[11px] font-bold text-red-500 shadow-sm active:scale-95">
            <i class="fas fa-sign-out-alt"></i> Salir
        </button>

        {{-- Zona central para arrastrar --}}
        <div class="flex-1 flex justify-center cursor-pointer px-4 py-2" onclick="toggleOrdenMobile()">
            <div class="w-12 h-1.5 rounded-full bg-slate-200"></div>
        </div>

        <div class="flex items-center gap-1.5 text-[12px] font-black tracking-tight text-slate-800">
            <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></div>
            @if($mesa->esDelivery())
                <i class="fas fa-motorcycle text-orange-500"></i> {{ strtoupper($mesa->plataformaDelivery->nombre ?? 'DELIVERY') }}
            @else
                MESA {{ $mesa->numero ?? '12M' }}
            @endif
        </div>
    </div>

    {{-- CABECERA TABLET (768–1023px): solo nombre de mesa, sin manija de arrastre --}}
    <div class="hidden md:flex lg:hidden items-center justify-between px-4 py-3 border-b border-slate-200">
        <button type="button" onclick="salirComanda()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[11px] font-bold text-slate-800 shadow-sm hover:bg-slate-100 active:scale-95">
            <i class="fas fa-arrow-left text-slate-500"></i> Mesas
        </button>
        <div class="flex items-center gap-1.5 text-[12px] font-black tracking-tight text-slate-800">
            <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></div>
            @if($mesa->esDelivery())
                <i class="fas fa-motorcycle text-orange-500"></i> {{ strtoupper($mesa->plataformaDelivery->nombre ?? 'DELIVERY') }}
            @else
                MESA {{ $mesa->numero ?? '12M' }}
            @endif
        </div>
    </div>

    <div class="p-4 border-b border-slate-200 flex flex-col gap-3 bg-slate-50">
        <div id="barraModificadores" class="lg:hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">Acciones del platillo</span>
                <span id="barraModificadores-hint" class="text-[9px] font-semibold text-slate-500">Toca un producto para modificar</span>
            </div>
            <div id="contenedorBotonesModificadores" class="flex flex-wrap gap-2"></div>
        </div>

        <div class="relative flex w-full bg-white p-1 rounded-xl border border-slate-200 shadow-inner">
            <div class="absolute inset-y-1 left-1 right-1 pointer-events-none">
                <div id="tab-slider" class="h-full w-1/2 rounded-lg bg-slate-800 shadow-md transition-transform duration-300 ease-out"></div>
            </div>
            <button type="button" onclick="cambiarTab('nueva-orden', this)" id="btn-tab-nueva-orden" class="relative z-10 flex-1 py-2.5 md:py-1.5 text-[11px] md:text-[10px] font-bold text-slate-50 transition-colors outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 rounded-lg">Orden</button>
            {{-- Tab "Enviado" oculto: el submenú de productos ya pedidos se ocultó por solicitud --}}
            <button type="button" onclick="cambiarTab('comanda', this)" id="btn-tab-comanda" class="relative z-10 flex-1 py-2.5 md:py-1.5 text-[11px] md:text-[10px] font-bold text-slate-500 hover:text-slate-800 transition-colors outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 rounded-lg">Total</button>
        </div>

        {{-- Fila de Tiempos eliminada por solicitud --}}
    </div>

    {{-- VISTA 1: NUEVA ORDEN --}}
    <div id="vista-nueva-orden" class="panel-fade flex-1 overflow-y-auto hide-scroll p-4 flex flex-col relative bg-slate-50">
        <div class="flex justify-between text-[11px] md:text-[10px] font-bold text-slate-500 mb-3 px-1">
            <span>CANT. / PLATILLO</span>
            <span>MONTO</span>
        </div>

        <div id="listaTicket" class="flex flex-col gap-2.5 pb-4"></div>

        <div id="estadoVacio" class="flex-1 flex flex-col items-center justify-center opacity-40 mt-10 transition-opacity duration-300">
            <i class="fas fa-plate-wheat text-4xl md:text-3xl text-slate-500 mb-3"></i>
            <p class="text-xs md:text-[11px] font-medium text-slate-500 text-center">Sin productos.<br>Comienza a agregar.</p>
        </div>
    </div>

    {{-- VISTA 2: ENVIADOS --}}
    <div id="vista-enviados" class="hidden panel-fade flex-1 overflow-y-auto hide-scroll p-4 flex-col relative bg-slate-50">
        @if(isset($platillosEnviados) && count($platillosEnviados) > 0)
            <div class="flex flex-col gap-2">
                @foreach($platillosEnviados as $item)
                    @php $cancelado = ($item->estado ?? '') === 'cancelado'; @endphp
                    <div id="enviado-item-{{ $item->id }}" class="bg-white border border-slate-200 rounded-xl p-3 flex justify-between items-center shadow-sm hover:shadow-md transition-shadow duration-150 {{ $cancelado ? 'opacity-60' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 md:w-6 md:h-6 rounded-lg bg-slate-50 border border-slate-200 text-slate-800 text-xs md:text-[11px] font-bold flex items-center justify-center shadow-sm">{{ $item->cantidad ?? 1 }}</span>
                            <span class="text-[13px] md:text-[12px] font-medium {{ $cancelado ? 'line-through text-slate-500' : 'text-slate-800' }}">{{ $item->nombre ?? 'Platillo' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($cancelado)
                                <span class="text-[11px] md:text-[10px] font-bold text-red-500 uppercase tracking-wide">Cancelado</span>
                            @else
                                @if(($item->estado ?? 'enviado') == 'preparando')
                                    <span class="text-[11px] md:text-[10px] font-bold text-orange-500 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-orange-500 shadow-[0_0_5px_rgba(249,115,22,0.5)]"></span> Cocina</span>
                                @elseif(($item->estado ?? 'enviado') == 'listo')
                                    <span class="text-[11px] md:text-[10px] font-bold text-emerald-500 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]"></span> Listo</span>
                                @else
                                    <span class="text-[11px] md:text-[10px] font-bold text-slate-500 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Enviado</span>
                                @endif
                                <button type="button" onclick="cancelarProductoEnviado({{ $item->id }}, this)" class="w-7 h-7 rounded-lg text-red-500 bg-red-500/10 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center opacity-40 mt-10">
                <i class="fas fa-clock text-4xl md:text-3xl text-slate-500 mb-3"></i>
                <p class="text-xs md:text-[11px] font-medium text-slate-500 text-center">No hay órdenes en proceso.</p>
            </div>
        @endif
    </div>

    {{-- VISTA 3: COMANDA TOTAL --}}
    <div id="vista-comanda" class="hidden panel-fade flex-1 overflow-y-auto hide-scroll p-4 flex-col relative bg-slate-50">
        <div id="items-db-total" class="flex flex-col gap-2">
            @if(isset($platillosEnviados) && count($platillosEnviados) > 0)
                <div class="text-[11px] md:text-[10px] font-bold text-slate-500 mb-2 px-1 uppercase tracking-wider">Consumo Procesado</div>
                @foreach($platillosEnviados as $item)
                    @php $cancelado = ($item->estado ?? '') === 'cancelado'; @endphp
                    <div class="flex justify-between items-center p-2 rounded-xl hover:bg-slate-100 transition-colors duration-150 {{ $cancelado ? 'opacity-50' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-xs md:text-[11px] text-slate-500 font-bold">{{ $item->cantidad ?? 1 }}x</span>
                            <span class="text-[13px] md:text-[12px] font-medium {{ $cancelado ? 'line-through text-slate-500' : 'text-slate-800' }}">{{ $item->nombre ?? 'Platillo' }}</span>
                            @if($cancelado)
                                <span class="text-[10px] font-bold text-red-500 uppercase">Cancelado</span>
                            @endif
                        </div>
                        <span class="text-[13px] md:text-[12px] font-bold {{ $cancelado ? 'line-through text-slate-500' : 'text-slate-800' }}">${{ number_format(($item->precio ?? 0) * ($item->cantidad ?? 1), 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center mt-2 pt-3 border-t border-slate-200 px-2">
                    <span class="text-xs md:text-[11px] font-medium text-slate-500">Subtotal en mesa:</span>
                    <span class="text-[13px] md:text-[12px] font-bold text-slate-800">
                        ${{ number_format(collect($platillosEnviados)->where('estado', '!=', 'cancelado')->sum(function($i) { return ($i->precio ?? 0) * ($i->cantidad ?? 1); }), 2) }}
                    </span>
                </div>
            @endif
        </div>

        <div id="lista-comanda-total" class="flex flex-col gap-2 mt-4"></div>

        <div id="estadoVacioComanda" class="flex-1 flex-col items-center justify-center opacity-40 mt-10 {{ (isset($platillosEnviados) && count($platillosEnviados) > 0) ? 'hidden' : 'flex' }}">
            <i class="fas fa-receipt text-4xl md:text-3xl text-slate-500 mb-3"></i>
            <p class="text-xs md:text-[11px] font-medium text-slate-500 text-center">No hay cuenta registrada.</p>
        </div>
    </div>

    {{-- Barra de acciones mobile movida a barra-acciones-mobile (fija, fuera del panel) --}}

    {{-- FOOTER DE TOTALES (PREMIUM) --}}
    <div class="p-5 pb-[calc(1.5rem+env(safe-area-inset-bottom))] md:pb-5 border-t border-slate-200 bg-gradient-to-b from-white to-white flex-shrink-0 z-20 shadow-[0_-10px_30px_rgba(0,0,0,0.04)] relative">
        <div class="flex justify-between items-center mb-1">
            <span class="text-xs md:text-[11px] text-slate-500 font-medium">Subtotal</span>
            <span class="text-[13px] md:text-[12px] font-bold text-slate-800" id="txtSubtotal">$0.00</span>
        </div>
@php /* IVA_BLOCK_START — iva_sidebar_display
        <div id="txtIva">IVA $0.00</div>
        IVA_BLOCK_END */ @endphp

        <div class="flex justify-between items-center mb-4">
            <span class="text-xs md:text-[11px] text-slate-500 font-medium">Propina</span>
            <span class="text-[13px] md:text-[12px] font-bold text-emerald-500" id="txtPropina">$0.00</span>
        </div>

        {{-- --- NUEVO: comisión de plataforma de delivery ---
             Oculto por defecto; comanda-ticket.js lo muestra solo cuando la
             mesa es un pedido de Rappi/Uber/DiDi. --}}
        <div id="bloqueComisionDelivery" class="hidden mb-4 p-2.5 rounded-xl bg-orange-500/10 border border-orange-500/20 space-y-1">
            <p class="text-[10px] font-black uppercase tracking-widest text-orange-500 flex items-center gap-1.5">
                <i class="fas fa-motorcycle"></i>
                <span id="txtPlataformaNombre">Delivery</span>
            </p>
            <div class="flex justify-between items-center">
                <span class="text-xs md:text-[11px] text-slate-500 font-medium">
                    Comisión (<span id="txtComisionPorcentaje">0</span>%)
                </span>
                <span class="text-[13px] md:text-[12px] font-bold text-slate-800" id="txtComision">$0.00</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs md:text-[11px] text-slate-500 font-medium">
                    IVA comisión (<span id="txtComisionIvaPorcentaje">0</span>%)
                </span>
                <span class="text-[13px] md:text-[12px] font-bold text-slate-800" id="txtComisionIva">$0.00</span>
            </div>
        </div>

        {{-- Total final (incluye comisión cuando aplica) --}}
        <div class="flex justify-between items-center mb-4 pt-2 border-t border-slate-200">
            <span class="text-[13px] md:text-xs text-slate-800 font-black uppercase tracking-wide">Total</span>
            <span class="text-base md:text-sm font-black text-slate-800" id="txtTotalComanda">$0.00</span>
        </div>

        <button type="button" id="btn-enviar" onclick="enviarACocina()" class="w-full h-13 md:h-12 rounded-xl bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white text-[13px] md:text-[12px] font-bold tracking-wide transition-all duration-150 shadow-[0_8px_20px_-5px_rgba(59,130,246,0.5)] hover:shadow-[0_10px_28px_-5px_rgba(59,130,246,0.6)] active:scale-95 flex items-center justify-center gap-2 outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
            <i class="fas fa-paper-plane text-sm"></i>
            <span>Enviar Orden</span>
        </button>
    </div>
</section>

<style>
    /* Transición suave al cambiar de tab */
    @media (prefers-reduced-motion: no-preference) {
        .panel-fade:not(.hidden) { animation: panelFadeIn .18s ease-out; }
    }
    @keyframes panelFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hide-scroll::-webkit-scrollbar {
        display: none;
    }
    .hide-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>