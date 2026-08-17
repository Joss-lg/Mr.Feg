{{-- detalle-cuenta.blade.php --}}
@php
    $division = $division ?? null; // null = mesa sin dividir
    $esDividida = !is_null($division);
    $tipoDivision = $division['tipo'] ?? null;
    $totalPartes = $division['total_partes'] ?? 1;
@endphp
<div class="flex flex-col h-full bg-white text-slate-800">

    <div class="p-4 pb-2">
        @if($esDividida)
            <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-blue-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 mb-0.5">
                            <i class="fas fa-users"></i> Cuenta Dividida
                            · {{ $tipoDivision === 'equitativa' ? 'Partes iguales' : 'Por consumo' }}
                        </p>
                        <p class="text-slate-800 text-xs font-bold">Dividida entre {{ $totalPartes }} personas</p>
                    </div>
                    <button type="button" id="btn-cancelar-division"
                        class="text-[10px] font-black uppercase text-rose-600 hover:text-rose-700 whitespace-nowrap">
                        <i class="fas fa-times"></i> Cancelar división
                    </button>
                </div>
            </div>
        @else
            <div class="flex items-center justify-between gap-3">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                    Personas: {{ $mesa->capacidad ?? 'N/A' }}
                </p>
                <button type="button" id="btn-abrir-division"
                    class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-700 flex items-center gap-1.5">
                    <i class="fas fa-users"></i> Dividir cuenta
                </button>
            </div>

            {{-- Panel para configurar la división, oculto hasta que se pulse "Dividir cuenta" --}}
            <div id="panel-iniciar-division" class="hidden mt-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                <div class="flex gap-2">
                    <button type="button" data-tipo-division="equitativa" class="tipo-division-btn flex-1 py-2 rounded-xl border-2 border-blue-500 bg-blue-50 text-blue-600 font-bold text-xs uppercase">
                        Partes iguales
                    </button>
                    <button type="button" data-tipo-division="por_producto" class="tipo-division-btn flex-1 py-2 rounded-xl border-2 border-slate-200 font-bold text-xs uppercase text-slate-500">
                        Por consumo
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500">N.º de personas</label>
                    <input type="number" id="input-numero-personas" min="2" max="20" value="2"
                        class="w-20 rounded-lg border border-slate-200 bg-white p-2 text-center font-bold text-slate-800" />
                    <button type="button" id="btn-confirmar-division"
                        class="ml-auto px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase transition-all active:scale-95">
                        Dividir
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if($esDividida)
        <div class="px-4 pb-2">
            <div class="flex gap-1.5 overflow-x-auto pb-1.5 -mx-1 px-1" id="tabs-cuentas-division">
                @foreach($division['cuentas'] as $cuenta)
                    @php $esPagada = $cuenta['estado_orden'] === 'pagada'; @endphp
                    <button
                        type="button"
                        class="btn-cuenta px-3 py-1.5 rounded-lg font-bold text-[11px] whitespace-nowrap transition-all flex items-center gap-1.5 border-2 {{ $esPagada ? 'bg-emerald-50 border-emerald-200 text-emerald-600 opacity-70 cursor-not-allowed' : 'bg-slate-50 border-blue-400 text-slate-800 hover:bg-blue-50' }}"
                        data-cuenta-id="{{ $cuenta['id'] }}"
                        data-numero="{{ $cuenta['numero_cuenta'] }}"
                        data-subtotal="{{ number_format($cuenta['subtotal'], 2, '.', '') }}"
                        data-iva="{{ number_format($cuenta['iva'], 2, '.', '') }}"
                        data-propina="{{ number_format($cuenta['propina'], 2, '.', '') }}"
                        data-total="{{ number_format($cuenta['total'], 2, '.', '') }}"
                        {{ $esPagada ? 'disabled' : '' }}>
                        @if($esPagada) <i class="fas fa-check text-[10px]"></i> @endif
                        <span class="texto-cuenta">P{{ $cuenta['numero_cuenta'] }} · <span class="valor-cuenta">${{ number_format($cuenta['total'], 2) }}</span></span>
                    </button>
                @endforeach
            </div>
            @if($tipoDivision === 'por_producto')
                <p class="text-[9px] text-slate-400 font-bold uppercase">
                    Usa + / − para repartir unidades entre personas.
                </p>
            @else
                <p class="text-[9px] text-slate-400 font-bold uppercase">
                    Selecciona una persona para cobrar su parte.
                </p>
            @endif
        </div>
    @endif

    <div class="px-4 pb-3 space-y-1 flex-1 min-h-0 overflow-y-auto" id="productos-container">
        @foreach($ordenes as $ordenActual)
            @foreach($ordenActual->detalles->where('estado', '!=', 'cancelado') as $detalle)
                <div class="producto-row py-1.5 px-2 rounded-lg hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 shrink-0 bg-blue-50 text-blue-600 font-black text-[10px] rounded-md flex items-center justify-center border border-blue-100">
                                {{ $detalle->cantidad }}x
                            </div>
                            <div class="min-w-0">
                                <p class="text-slate-800 font-bold text-[13px] leading-tight truncate">{{ $detalle->producto->nombre ?? 'Producto sin nombre' }}</p>
                                <p class="text-[9px] text-slate-400 font-semibold leading-tight">Unit: ${{ number_format($detalle->precio_unitario, 2) }}</p>

                                @if($detalle->notas)
                                    <p class="text-[9px] text-slate-400 font-bold uppercase italic truncate leading-tight">{{ $detalle->notas }}</p>
                                @endif

                                @if($detalle->promocionAplicada)
                                    <p class="text-[9px] text-emerald-600 font-black uppercase flex items-center gap-1 leading-tight">
                                        <i class="fas fa-tag"></i> {{ $detalle->promocionAplicada->promocion->nombre ?? 'Promo' }}
                                        (-${{ number_format($detalle->promocionAplicada->monto_descuento, 2) }})
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0 flex items-center gap-2">
                            <div>
                                @if($detalle->promocionAplicada)
                                    <span class="text-slate-400 text-[9px] line-through block leading-tight">
                                        ${{ number_format($detalle->precio_unitario * $detalle->cantidad, 2) }}
                                    </span>
                                @endif
                                <span class="text-slate-800 font-black text-[13px]">
                                    ${{ number_format(($detalle->precio_unitario * $detalle->cantidad) - ($detalle->promocionAplicada->monto_descuento ?? 0), 2) }}
                                </span>
                            </div>
                            {{-- Botón cancelar producto desde Caja (requiere NIP de Administrador) --}}
                            @if(!$esDividida)
                                <button type="button"
                                    onclick="cancelarProductoCaja({{ $detalle->id }}, this, {{ $detalle->cantidad }})"
                                    class="w-7 h-7 rounded-lg text-rose-500 bg-rose-50 border border-rose-100 hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all flex items-center justify-center shadow-sm"
                                    title="Cancelar producto">
                                    <i class="fas fa-trash-alt text-[9px]"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($esDividida && $tipoDivision === 'por_producto')
                        @php
                            $asig = $division['asignacionesPorDetalle'][$detalle->id] ?? ['por_persona' => [], 'sin_asignar' => $detalle->cantidad];
                        @endphp
                        <div class="mt-1 pl-9 producto-asignacion" data-detalle-id="{{ $detalle->id }}" data-cantidad-total="{{ $detalle->cantidad }}">
                            <div class="flex flex-wrap items-center gap-1">
                                @for($p = 1; $p <= $totalPartes; $p++)
                                    @php $cantidadPersona = $asig['por_persona'][$p] ?? 0; @endphp
                                    <div class="flex items-center gap-0.5 bg-slate-100 rounded-md pl-1.5 pr-0.5 py-0.5 stepper-persona"
                                        data-detalle-id="{{ $detalle->id }}" data-numero="{{ $p }}">
                                        <span class="text-[8px] font-black text-slate-500">P{{ $p }}</span>
                                        <button type="button" class="btn-stepper-restar w-4 h-4 rounded bg-white border border-slate-200 text-[10px] font-black leading-none flex items-center justify-center text-slate-600">−</button>
                                        <span class="stepper-valor w-3 text-center text-[10px] font-black text-slate-800">{{ $cantidadPersona }}</span>
                                        <button type="button" class="btn-stepper-sumar w-4 h-4 rounded bg-white border border-slate-200 text-[10px] font-black leading-none flex items-center justify-center text-slate-600">+</button>
                                    </div>
                                @endfor
                                <span class="sin-asignar-badge text-[8px] font-black uppercase {{ $asig['sin_asignar'] > 0 ? 'text-amber-600' : 'hidden' }}">
                                    {{ $asig['sin_asignar'] }} sin asignar
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @endforeach
    </div>

    <div class="mt-auto px-4 py-2.5 bg-slate-50 border-t border-slate-200 shadow-sm">
        <div class="space-y-1">
            <div class="space-y-0.5">
                <div class="flex justify-between text-slate-500 text-[11px] font-semibold">
                    <span>Subtotal</span>
                    <span class="font-bold text-slate-800" id="resumen-subtotal">${{ number_format($subtotalBruto ?? 0, 2) }}</span>
                </div>

                @if(($descuentoPromociones ?? 0) > 0)
                    <div class="flex justify-between text-emerald-600 text-[11px] font-semibold">
                        <span>Descuento (promociones)</span>
                        <span class="font-bold">-${{ number_format($descuentoPromociones, 2) }}</span>
                    </div>
                @endif
                @php /* IVA_BLOCK_START — switch_iva_ui
                <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 text-[11px] font-semibold">
                    <span class="flex items-center gap-2">
                        IVA (X%)
                        <label>...</label>
                    </span>
                    <span id="resumen-iva">$0.00</span>
                </div>
                IVA_BLOCK_END */ @endphp

                @if(($descuentoCaja ?? 0) > 0)
                    <div class="flex justify-between text-blue-600 text-[11px] font-semibold">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-percent text-[10px]"></i>
                            Descuento ({{ rtrim(rtrim(number_format($descuentoPorcentaje ?? 0, 2), '0'), '.') }}%)
                        </span>
                        <span class="font-bold">-${{ number_format($descuentoCaja, 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-amber-600 text-[11px] font-semibold {{ ($propina ?? 0) > 0 ? '' : 'hidden' }}" id="resumen-propina-row">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-hand-holding-dollar text-[10px]"></i> Propina
                    </span>
                    <span class="font-bold" id="resumen-propina">${{ number_format($propina ?? 0, 2) }}</span>
                </div>

                @if($esDelivery ?? false)
                    <div class="mt-1 p-2 rounded-lg bg-orange-50 border border-orange-100 space-y-0.5">
                        <p class="text-orange-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fas fa-motorcycle"></i> {{ $plataformaNombre ?? 'Delivery' }}
                        </p>
                        <div class="flex justify-between text-slate-500 text-[11px] font-semibold">
                            <span>Comisión ({{ number_format($comisionPorcentaje ?? 0, 0) }}%)</span>
                            <span class="font-bold text-slate-800">${{ number_format($comisionMonto ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 text-[11px] font-semibold">
                            <span>IVA de la comisión ({{ number_format($comisionIvaPorcentaje ?? 0, 0) }}%)</span>
                            <span class="font-bold text-slate-800">${{ number_format($comisionIvaMonto ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-orange-600 text-[11px] font-black pt-0.5 border-t border-orange-100">
                            <span>Total comisión (se suma al pedido)</span>
                            <span>${{ number_format($comisionTotal ?? 0, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="border-t border-slate-200 pt-1 flex justify-between items-center">
                <span class="text-slate-400 font-black uppercase tracking-[0.15em] text-[10px]" id="resumen-total-label">
                    {{ $esDividida ? 'Total mesa' : 'Total' }}
                </span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 tracking-tighter italic" id="resumen-total">
                    ${{ number_format($totalPagar ?? 0, 2) }}
                </span>
            </div>
            @if($esDividida)
                <p class="text-right text-[10px] text-blue-600 font-bold" id="resumen-persona-seleccionada"></p>
            @endif
        </div>
    </div>
</div>

{{-- MODAL: Cancelar Producto (reemplaza los prompt() nativos del navegador,
     que no se pueden estilizar porque los dibuja el sistema operativo) --}}
<div id="modalCancelarProducto" class="hidden fixed inset-0 z-[9999] items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div id="modalCancelarProductoContainer" class="bg-white border border-slate-200 rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden scale-95 opacity-0 transition-all duration-200">

        {{-- Header --}}
        <div class="flex items-center gap-3 sm:gap-4 p-6 sm:p-8 pb-5 bg-rose-50 border-b border-rose-100">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white flex items-center justify-center text-rose-600 border border-rose-100 shrink-0 shadow-sm">
                <i class="fas fa-trash-alt"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Cancelar Producto</h3>
                <p id="modalCancelarSubtitulo" class="text-xs font-semibold text-slate-500 mt-0.5">Ingresa el NIP del Administrador</p>
            </div>
            <button type="button" id="btnCerrarModalCancelarProducto" class="group w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-rose-100 text-slate-400 hover:text-rose-600 hover:bg-rose-100 transition-all outline-none shrink-0">
                <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
            </button>
        </div>

        {{-- PASO 1: Cantidad a cancelar (solo si hay más de 1 unidad) --}}
        <div id="pasoCantidadCancelar" class="hidden p-6 sm:p-8 space-y-5">
            <label class="block text-center text-[10px] font-black text-slate-500 uppercase tracking-widest">
                Unidades a cancelar (máx. <span id="cantidadCancelarMax">1</span>)
            </label>

            <div class="flex items-center justify-center gap-4">
                <button type="button" id="btnCantidadRestar" class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 font-black text-lg hover:bg-slate-100 active:scale-95 transition-all">−</button>
                <span id="valorCantidadCancelar" class="w-16 text-center text-3xl font-black text-slate-800">1</span>
                <button type="button" id="btnCantidadSumar" class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 font-black text-lg hover:bg-slate-100 active:scale-95 transition-all">+</button>
            </div>

            <button type="button" id="btnContinuarCancelar"
                class="w-full h-12 rounded-xl text-xs font-black uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 active:scale-95 transition-all outline-none">
                Continuar
            </button>
        </div>

        {{-- PASO 2: Teclado numérico para el NIP --}}
        <div id="pasoNipCancelar" class="p-6 sm:p-8 space-y-5">
            <p class="text-center text-sm text-slate-800">
                NIP del <span class="font-black">Administrador</span>
            </p>

            {{-- Puntitos indicadores --}}
            <div class="flex items-center justify-center gap-3" id="dotsNipCancelar">
                <span class="nip-dot w-3.5 h-3.5 rounded-full border-2 border-slate-300 transition-all"></span>
                <span class="nip-dot w-3.5 h-3.5 rounded-full border-2 border-slate-300 transition-all"></span>
                <span class="nip-dot w-3.5 h-3.5 rounded-full border-2 border-slate-300 transition-all"></span>
                <span class="nip-dot w-3.5 h-3.5 rounded-full border-2 border-slate-300 transition-all"></span>
            </div>

            <p id="errorCancelarProducto" class="hidden text-center text-[11px] font-bold text-rose-600"></p>

            {{-- Teclado numérico --}}
            <div class="grid grid-cols-3 gap-2.5">
                @foreach(['1','2','3','4','5','6','7','8','9'] as $tecla)
                    <button type="button" class="btn-tecla-nip h-14 rounded-2xl font-black text-lg bg-slate-50 border border-slate-200 text-slate-800 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 active:scale-95 transition-all" data-tecla="{{ $tecla }}">{{ $tecla }}</button>
                @endforeach

                <button type="button" id="btnNipDel" class="h-14 rounded-2xl font-black bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 active:scale-95 transition-all flex items-center justify-center">
                    <i class="fas fa-delete-left"></i>
                </button>
                <button type="button" class="btn-tecla-nip h-14 rounded-2xl font-black text-lg bg-slate-50 border border-slate-200 text-slate-800 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 active:scale-95 transition-all" data-tecla="0">0</button>
                <button type="button" id="btnNipOk" class="h-14 rounded-2xl font-black text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-500/20 active:scale-95 transition-all flex items-center justify-center gap-1.5 text-sm uppercase tracking-wide">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>

            <button type="button" id="btnVolverCancelar" class="w-full text-center text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </button>
        </div>
    </div>
</div>

{{-- Cancelar producto desde Caja (requiere NIP de Administrador).
     Esta función SÍ se ejecuta en el navegador — vive fuera del bloque
     comentado del switch de IVA para que cancelar productos siga
     funcionando aunque el IVA continúe desactivado. --}}
<script>
    // Abre el modal y devuelve una Promise que resuelve con { cantidad, nip }
    // al confirmar, o null si el usuario cancela por completo. Sustituye a
    // los prompt() nativos del navegador, que no se pueden estilizar.
    function abrirModalCancelarProducto(cantidadTotal) {
        return new Promise((resolve) => {
            const modal = document.getElementById('modalCancelarProducto');
            const container = document.getElementById('modalCancelarProductoContainer');
            const subtitulo = document.getElementById('modalCancelarSubtitulo');

            const pasoCantidad = document.getElementById('pasoCantidadCancelar');
            const pasoNip = document.getElementById('pasoNipCancelar');
            const valorCantidadEl = document.getElementById('valorCantidadCancelar');
            const maxLabel = document.getElementById('cantidadCancelarMax');
            const btnRestar = document.getElementById('btnCantidadRestar');
            const btnSumar = document.getElementById('btnCantidadSumar');
            const btnContinuar = document.getElementById('btnContinuarCancelar');

            const dots = document.querySelectorAll('#dotsNipCancelar .nip-dot');
            const teclas = document.querySelectorAll('.btn-tecla-nip');
            const btnDel = document.getElementById('btnNipDel');
            const btnOk = document.getElementById('btnNipOk');
            const btnVolver = document.getElementById('btnVolverCancelar');
            const btnCerrar = document.getElementById('btnCerrarModalCancelarProducto');
            const errorEl = document.getElementById('errorCancelarProducto');

            const hayPasoCantidad = cantidadTotal > 1;
            let cantidadSeleccionada = 1;
            let nip = '';

            const pintarDots = () => {
                dots.forEach((dot, i) => {
                    const activo = i < nip.length;
                    dot.classList.toggle('bg-rose-600', activo);
                    dot.classList.toggle('border-rose-600', activo);
                    dot.classList.toggle('border-slate-300', !activo);
                });
            };

            const mostrarError = (texto) => {
                errorEl.textContent = texto;
                errorEl.classList.remove('hidden');
            };
            const limpiarError = () => errorEl.classList.add('hidden');

            const irAPasoCantidad = () => {
                pasoNip.classList.add('hidden');
                pasoCantidad.classList.remove('hidden');
                subtitulo.textContent = '¿Cuántas unidades vas a cancelar?';
            };

            const irAPasoNip = () => {
                pasoCantidad.classList.add('hidden');
                pasoNip.classList.remove('hidden');
                subtitulo.textContent = 'Ingresa el NIP del Administrador';
                nip = '';
                pintarDots();
                limpiarError();
            };

            const cerrar = (resultado) => {
                container.classList.remove('scale-100', 'opacity-100');
                container.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    // Limpiar listeners para no acumularlos en la próxima apertura
                    btnRestar.onclick = null;
                    btnSumar.onclick = null;
                    btnContinuar.onclick = null;
                    teclas.forEach(t => t.onclick = null);
                    btnDel.onclick = null;
                    btnOk.onclick = null;
                    btnVolver.onclick = null;
                    btnCerrar.onclick = null;
                }, 150);
                resolve(resultado);
            };

            // --- Paso Cantidad ---
            if (hayPasoCantidad) {
                maxLabel.textContent = cantidadTotal;
                cantidadSeleccionada = cantidadTotal;
                valorCantidadEl.textContent = cantidadSeleccionada;

                btnRestar.onclick = () => {
                    if (cantidadSeleccionada > 1) {
                        cantidadSeleccionada--;
                        valorCantidadEl.textContent = cantidadSeleccionada;
                    }
                };
                btnSumar.onclick = () => {
                    if (cantidadSeleccionada < cantidadTotal) {
                        cantidadSeleccionada++;
                        valorCantidadEl.textContent = cantidadSeleccionada;
                    }
                };
                btnContinuar.onclick = irAPasoNip;
            } else {
                cantidadSeleccionada = 1;
            }

            // --- Paso NIP (teclado numérico) ---
            teclas.forEach(tecla => {
                tecla.onclick = () => {
                    if (nip.length >= 4) return;
                    nip += tecla.dataset.tecla;
                    limpiarError();
                    pintarDots();
                };
            });

            btnDel.onclick = () => {
                nip = nip.slice(0, -1);
                pintarDots();
            };

            btnOk.onclick = () => {
                if (!nip) {
                    mostrarError('Ingresa el NIP del administrador.');
                    return;
                }
                cerrar({ cantidad: cantidadSeleccionada, nip });
            };

            btnVolver.onclick = () => {
                if (hayPasoCantidad) {
                    irAPasoCantidad();
                } else {
                    cerrar(null);
                }
            };

            btnCerrar.onclick = () => cerrar(null);

            // --- Estado inicial al abrir ---
            if (hayPasoCantidad) {
                irAPasoCantidad();
            } else {
                pasoCantidad.classList.add('hidden');
                irAPasoNip();
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 15);
        });
    }

    window.cancelarProductoCaja = async function(detalleId, btn, cantidadTotal) {
        const resultado = await abrirModalCancelarProducto(cantidadTotal);
        if (!resultado) return; // el usuario canceló el modal

        const { cantidad: cantidadCancelar, nip } = resultado;

        btn.disabled = true;
        const icono = btn.querySelector('i');
        if (icono) { icono.className = 'fas fa-spinner fa-spin text-[9px]'; }

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/mesero/comanda/detalle/${detalleId}/cancelar`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ nip: nip, cantidad_cancelar: cantidadCancelar })
            });
            const data = await res.json().catch(() => null);
            if (!res.ok || !data?.success) throw new Error(data?.message || 'No se pudo cancelar');

            window.location.reload();
        } catch (err) {
            alert('Error: ' + err.message);
            btn.disabled = false;
            if (icono) { icono.className = 'fas fa-trash-alt text-[9px]'; }
        }
    };
</script>

@php /* IVA_BLOCK_START — script_switch_iva
{{-- --- Script del switch de IVA (desactivado a propósito) --- --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ivaSwitch = document.getElementById('ivaSwitch');
    if (!ivaSwitch) return;

    ivaSwitch.addEventListener('change', async function (e) {
        const habilitado = e.target.checked;
        const url = e.target.dataset.toggleUrl;
        const csrf = e.target.dataset.csrf;

        ivaSwitch.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ habilitado }),
            });

            if (!response.ok) {
                throw new Error('Respuesta no exitosa del servidor');
            }

            window.location.reload();

        } catch (error) {
            console.error('Error al cambiar el estado del IVA:', error);
            e.target.checked = !habilitado;
            ivaSwitch.disabled = false;
            alert('No se pudo actualizar el IVA. Intenta de nuevo.');
        }
    });
});
</script>
IVA_BLOCK_END */ @endphp