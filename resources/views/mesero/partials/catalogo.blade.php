{{-- ============================================================
     CATÁLOGO — Panel de productos (Minimalista, sin imágenes)
     ============================================================ --}}
<section id="col-catalogo"
    class="col-mobile-panel flex w-full md:flex-1 md:min-w-[280px] h-full flex-col bg-slate-50 border-l md:border-l-0 md:border-r border-slate-200 z-20">

    {{-- Encabezado: título + categorías --}}
    <div class="p-3 sm:p-4 border-b border-slate-200 bg-slate-50/95 backdrop-blur supports-[backdrop-filter]:bg-slate-50/80 sticky top-0 z-10">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Menú</p>
                <h2 class="text-base sm:text-lg font-black text-slate-800 leading-tight truncate">Catálogo</h2>
            </div>
            <div class="flex-shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-semibold text-slate-500 whitespace-nowrap">
                <i class="fas fa-utensils mr-1"></i> {{ count($productos ?? []) }} Productos
            </div>
        </div>

        {{-- BUSCADOR --}}
        <div class="mt-3 relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            <input type="text" id="buscadorProductos"
                   placeholder="Buscar platillo..."
                   autocomplete="off"
                   class="w-full pl-9 pr-9 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal outline-none focus:border-[#3b82f6] transition-colors">
            <button type="button" id="limpiarBusquedaProductos"
                    class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full text-slate-400 hover:text-slate-800 transition-colors"
                    title="Limpiar búsqueda">
                <i class="fas fa-xmark text-xs"></i>
            </button>
        </div>

        {{-- Categorías --}}
        <div id="menuCategorias"
             class="mt-3 flex items-center gap-2 overflow-x-auto hide-scroll pb-1 -mx-1 px-1 snap-x snap-mandatory">
            {{-- Los botones de categoría se inyectan aquí por JavaScript --}}
        </div>
    </div>

    {{-- Aviso sin resultados --}}
    <div id="catalogoSinResultados" class="hidden px-4 py-10 text-center">
        <i class="fas fa-magnifying-glass text-3xl text-slate-300 opacity-30 mb-2"></i>
        <p class="text-sm font-bold text-slate-500">Sin resultados</p>
        <p class="text-xs text-slate-500 opacity-70 mt-0.5">Prueba con otras letras o cambia de categoría.</p>
    </div>

    {{-- Cuadrícula de productos --}}
    <div id="gridProductos"
         class="flex-1 min-h-0 overflow-y-auto hide-scroll overscroll-contain
                p-3 sm:p-4 pb-[calc(6.5rem+env(safe-area-inset-bottom))] md:pb-4
                grid grid-cols-2 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-[repeat(auto-fill,minmax(140px,1fr))] lg:grid-cols-[repeat(auto-fill,minmax(160px,1fr))]
                gap-3 sm:gap-4
                content-start auto-rows-min">

        @forelse($productos ?? [] as $producto)
        @php
            $precioMostrar = $producto->precio
                ?? $producto->precio_100g
                ?? $producto->precio_gramaje
                ?? $producto->precio_kg
                ?? $producto->costo
                ?? 0;
        @endphp

        <button type="button"
            data-producto-id="{{ $producto->id ?? 0 }}"
            onclick="procesarClicProducto({{ $producto->id ?? 0 }})"
            class="btn-producto group relative flex flex-col justify-between text-left rounded-[20px] border border-blue-200/60
                   bg-white p-4
                   shadow-sm min-h-[130px]
                   hover:border-blue-500 hover:shadow-md hover:-translate-y-0.5
                   active:scale-[0.97] active:translate-y-0
                   focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60
                   transition-all duration-150">

            {{-- Nombre del producto --}}
            <h3 class="text-[14px] sm:text-[15px] font-black text-slate-900 leading-tight uppercase mb-4 pr-2">
                {{ $producto->nombre }}
            </h3>

            {{-- Precio y Botón Agregar --}}
            <div class="mt-auto flex items-center justify-between gap-2 w-full">
                @if($precioMostrar == 0 && $producto->tiene_variantes)
                    <span class="text-[11px] font-black text-blue-500 uppercase tracking-wider border border-blue-200 bg-blue-50 rounded-full px-2.5 py-1 leading-none">
                        {{ $producto->variantes->count() }} TAMAÑOS
                    </span>
                @else
                    <p class="text-[16px] sm:text-[18px] font-black text-slate-900 leading-none tracking-tight">
                        ${{ number_format($precioMostrar, 2) }}
                    </p>
                @endif

                <span class="flex-shrink-0 w-9 h-9 rounded-full bg-[#3b82f6] text-white
                             flex items-center justify-center text-sm font-bold
                             shadow-sm
                             group-hover:bg-blue-600 group-active:scale-90
                             transition-all duration-150">
                    <i class="fas fa-plus"></i>
                </span>
            </div>
        </button>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center gap-3 py-16 text-center">
            <span class="w-14 h-14 rounded-full bg-white border border-slate-200 flex items-center justify-center">
                <i class="fas fa-box-open text-xl text-slate-400"></i>
            </span>
            <div>
                <p class="text-sm font-bold text-slate-800">Sin productos en esta categoría</p>
                <p class="text-xs text-slate-500 mt-1">Prueba con otra categoría del menú superior.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- MINI-BARRA DE ORDEN ACTIVA (solo móvil) --}}
    <button type="button" id="miniCartBar" onclick="toggleOrdenMobile()"
        class="flex md:hidden fixed left-3 right-3 z-40 items-center justify-between gap-3
               rounded-2xl bg-slate-900 text-white
               px-4 py-3 shadow-[0_10px_30px_-8px_rgba(0,0,0,0.4)]
               active:scale-[0.98] transition-transform duration-150"
        style="bottom: calc(68px + env(safe-area-inset-bottom));">
        <span class="flex items-center gap-2 min-w-0">
            <span id="barra-mobile-count" class="shrink-0 w-6 h-6 rounded-full bg-white text-slate-900 text-[11px] font-black flex items-center justify-center">0</span>
            <span class="text-[12px] font-bold truncate">Ver orden</span>
        </span>
        <span class="flex items-center gap-2 shrink-0">
            <span id="barra-mobile-total" class="text-[13px] font-black">$0.00</span>
            <i class="fas fa-chevron-up text-[11px] opacity-70"></i>
        </span>
    </button>
</section>

{{-- MODAL COMANDA: SELECCIONAR TAMAÑO Y COMPLEMENTOS --}}
<div id="modal-variantes-comanda" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-all duration-300 opacity-0" style="display: none;">
    <div class="relative bg-white border border-slate-200/80 w-full max-w-sm sm:max-w-md rounded-[2rem] shadow-2xl p-6 sm:p-7 transform opacity-0 translate-y-8 transition-all duration-300" id="panel-variantes-comanda">
        
        <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight" id="titulo-modal-variantes">Platillo</h3>
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-0.5" id="subtitulo-modal-variantes">Selecciona una opción</p>
            </div>
            <button onclick="cerrarModalVariantes()" type="button" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-rose-500 rounded-xl bg-slate-50 border border-slate-100 transition-all active:scale-95 outline-none">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div id="lista-opciones-variantes" class="space-y-2.5 max-h-[60vh] overflow-y-auto overscroll-contain pr-1">
            <!-- Inyección dinámica de JS -->
        </div>

        {{-- Botón para regresar al paso 1 --}}
        <button id="btn-volver-tamano" type="button" onclick="if(typeof window.volverPasoAnterior === 'function') window.volverPasoAnterior();" class="hidden w-full mt-4 py-2.5 text-xs font-black text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all uppercase tracking-wider outline-none">
            <i class="fas fa-arrow-left mr-1.5"></i> Volver al paso anterior
        </button>
    </div>
</div>

<script>
    window.productoActivoParaComanda = null;
    window.estadoPersonalizacion = {};
    window.volverPasoAnterior = null;

    window.procesarClicProducto = function(idProducto) {
        const prod = (typeof productosDB !== 'undefined') 
            ? productosDB.find(p => p.id === Number(idProducto)) 
            : null;
        
        if (!prod) return;

        window.productoActivoParaComanda = prod;
        const nombreLower = (prod.nombre || '').toLowerCase();
        const catLower = (prod.categoria?.nombre || '').toLowerCase();

        window.estadoPersonalizacion = {
            id: prod.id,
            nombreBase: prod.nombre,
            categoria: catLower,
            precioBase: parseFloat(prod.precio) || 0,
            tamanoSeleccionado: null,
            varianteIdSeleccionada: null,
            detalles: [],
            extraAcumulado: 0,
            // Salsas (alitas / boneless)
            salsasElegidas: [],
            salsasGourmetExtras: [],
            extraSalsas: 0,
            totalSalsas: 0,
        };

        // 1. Caso Bebidas con variantes
        if (catLower.includes('bebida') || catLower.includes('frapp') || catLower.includes('malteada') || catLower.includes('smoothie')) {
            if (prod.tiene_variantes && prod.variantes?.length > 0) {
                mostrarSelectorTamanos(prod.variantes, 'tamano_bebida');
                abrirContenedorModal();
                return;
            }
        }

        // 2. Caso Alitas, Boneless y Platillos con Variantes/Tamaños
        if (prod.tiene_variantes && prod.variantes?.length > 0) {
            mostrarSelectorTamanos(prod.variantes, 'tamano_snack');
            abrirContenedorModal();
            return;
        }

        // 3. Caso Banderillas Coreanas (Cubiertas + Papas) — solo si tiene modificadores
        if ((nombreLower.includes('banderilla') || catLower.includes('banderilla')) && prod.modificadores && prod.modificadores.length > 0) {
            mostrarPasoCubiertasBanderilla();
            abrirContenedorModal();
            return;
        }

        // 4. Caso Snacks individuales sin variantes pero con complementos globales
        if (prod.modificadores && prod.modificadores.length > 0) {
            evaluarPapasPorTamano(null, null);
            abrirContenedorModal();
            return;
        }

        // 5. Producto estándar directo al ticket
        agregarAlTicket(
            prod.id, 
            prod.nombre, 
            parseFloat(prod.precio) || 0, 
            prod.categoria?.nombre || 'Menú', 
            prod.modificadores || [], 
            !!prod.se_vende_por_peso, 
            parseFloat(prod.precio_por_100g || 0)
        );
    };

    function abrirContenedorModal() {
        const modal = document.getElementById('modal-variantes-comanda');
        const panel = document.getElementById('panel-variantes-comanda');
        if (modal && panel) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            setTimeout(() => { 
                modal.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-8'); 
            }, 10);
        }
    }

    // --- SELECTOR DE TAMAÑOS (PASO 1) ---
    function mostrarSelectorTamanos(variantes, tipoFlujo) {
        const prod = window.productoActivoParaComanda;
        document.getElementById('titulo-modal-variantes').textContent = prod.nombre;
        document.getElementById('subtitulo-modal-variantes').textContent = 'Paso 1: Selecciona la presentación';
        document.getElementById('btn-volver-tamano').classList.add('hidden');

        // Limpiar botón de confirmar salsas si quedó de un paso anterior
        const btnConfPrev = document.getElementById('btn-confirmar-salsas');
        if (btnConfPrev) btnConfPrev.remove();
        _salsasMarcadas = [];

        const cont = document.getElementById('lista-opciones-variantes');
        cont.innerHTML = '';

        variantes.forEach(v => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = "w-full flex justify-between items-center bg-white border border-slate-200 p-4 rounded-2xl hover:border-blue-500 hover:ring-2 hover:ring-blue-500/20 transition-all text-left shadow-sm active:scale-95 outline-none cursor-pointer";
            
            const precioNum = parseFloat(v.precio) || 0;
            const textoPrecio = precioNum > 0 
                ? `<span class="font-black text-blue-600 text-sm sm:text-base">$${precioNum.toFixed(2)}</span>` 
                : `<span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Elegir opción</span>`;

            btn.onclick = (e) => {
                e.stopPropagation();
                window.estadoPersonalizacion.precioBase = precioNum;
                window.estadoPersonalizacion.tamanoSeleccionado = v.nombre;
                window.estadoPersonalizacion.varianteIdSeleccionada = v.id;

                // ─── PASO INTERMEDIO: Selector de salsas para Alitas y Boneless ───
                const prod = window.productoActivoParaComanda;
                const nombreP = (prod?.nombre || '').toLowerCase();
                const catP    = (prod?.categoria?.nombre || '').toLowerCase();
                const esAlitaOBoneless =
                    nombreP.includes('alita') || nombreP.includes('boneless') ||
                    catP.includes('alita')    || catP.includes('boneless');

                if (esAlitaOBoneless) {
                    const numSalsas = calcularNumeroSalsas(v.nombre);
                    mostrarSelectorSalsas(numSalsas, () => {
                        mostrarSelectorTamanos(prod.variantes, tipoFlujo);
                    });
                } else {
                    evaluarPapasPorTamano(v.nombre, v);
                }
            };

            btn.innerHTML = `
                <span class="font-bold text-slate-800 text-sm sm:text-base">${v.nombre}</span>
                ${textoPrecio}
            `;
            cont.appendChild(btn);
        });
    }

    // ─── EVALUAR EXTRAS / COMPLEMENTOS FILTRADOS POR VARIANTE (PASO 2) ────────
    function evaluarPapasPorTamano(nombreTamano, varianteObj = null) {
        const prod = window.productoActivoParaComanda;
        const varId = window.estadoPersonalizacion.varianteIdSeleccionada;

        let extrasDisponibles = [];

        if (varianteObj && Array.isArray(varianteObj.modificadores) && varianteObj.modificadores.length > 0) {
            extrasDisponibles = varianteObj.modificadores;
        } else if (prod && Array.isArray(prod.modificadores)) {
            if (varId) {
                extrasDisponibles = prod.modificadores.filter(m => Number(m.variante_id) === Number(varId));
            } else {
                extrasDisponibles = prod.modificadores.filter(m => !m.variante_id);
            }
        }

        // Para alitas/boneless siempre mostramos el paso de papas
        // sin importar si tienen modificadores de papas en BD.
        const nombreP2 = (prod?.nombre || '').toLowerCase();
        const catP2    = (prod?.categoria?.nombre || '').toLowerCase();
        const esAlitaOBoneless2 =
            nombreP2.includes('alita') || nombreP2.includes('boneless') ||
            catP2.includes('alita')    || catP2.includes('boneless');

        if (esAlitaOBoneless2) {
            const cbVolverSalsas = () => {
                const ns = calcularNumeroSalsas(window.estadoPersonalizacion.tamanoSeleccionado);
                mostrarSelectorSalsas(ns, () => mostrarSelectorTamanos(prod.variantes, 'tamano_snack'));
            };
            mostrarSelectorPapasGenerico(20, 35, cbVolverSalsas);
            return;
        }

        if (extrasDisponibles.length > 0) {
            const opciones = [];

            if (window.estadoPersonalizacion.precioBase > 0) {
                opciones.push({ nombre: 'Sin Extras / Complementos', extra: 0 });
            }

            extrasDisponibles.forEach(mod => {
                opciones.push({
                    nombre: mod.nombre,
                    extra: parseFloat(mod.precio) || 0
                });
            });

            const tam = window.estadoPersonalizacion.tamanoSeleccionado;
            document.getElementById('titulo-modal-variantes').textContent = tam ? `${prod.nombre} (${tam})` : prod.nombre;
            document.getElementById('subtitulo-modal-variantes').textContent = window.estadoPersonalizacion.precioBase === 0
                ? 'Paso 2: Selecciona la base o sabor'
                : 'Paso 2: ¿Deseas agregar complementos?';

            const btnVolver = document.getElementById('btn-volver-tamano');
            if (prod.tiene_variantes && prod.variantes?.length > 0) {
                btnVolver.classList.remove('hidden');
                window.volverPasoAnterior = () => mostrarSelectorTamanos(prod.variantes, 'tamano_snack');
            } else {
                btnVolver.classList.add('hidden');
            }

            renderOpcionesFinales(opciones);
        } else {
            finalizarTicket(window.estadoPersonalizacion.precioBase);
        }
    }

    // --- BANDERILLAS: PASO 1 (SOLO CUBIERTAS / SABORES) ---
    function mostrarPasoCubiertasBanderilla() {
        const prod = window.productoActivoParaComanda;
        document.getElementById('titulo-modal-variantes').textContent = prod.nombre;
        document.getElementById('subtitulo-modal-variantes').textContent = 'Paso 1: Elige la cubierta / sabor';
        document.getElementById('btn-volver-tamano').classList.add('hidden');

        const cont = document.getElementById('lista-opciones-variantes');
        cont.innerHTML = '';

        const todosMods = prod.modificadores || [];

        // Filtro estricto: Las papas de acompañamiento empiezan con "c/n" o "con" o "gajo"
        const esAcompanamiento = (nombre) => {
            const n = (nombre || '').toLowerCase().trim();
            return n.startsWith('c/n') || n.startsWith('con ') || n.includes('p.gajo') || n.includes('francesa');
        };

        // En el Paso 1 solo mostramos las CUBIERTAS (excluyendo acompañamientos c/n)
        const cubiertas = todosMods.filter(m => !esAcompanamiento(m.nombre));

        if (cubiertas.length === 0) {
            cont.innerHTML = `
                <div class="p-4 text-center text-slate-400 text-xs font-semibold">
                    No hay cubiertas configuradas para este producto.
                </div>
            `;
            return;
        }

        cubiertas.forEach(m => {
            const precioExtra = parseFloat(m.precio || 0);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = "w-full flex justify-between items-center bg-white border border-slate-200 p-3.5 rounded-2xl hover:border-blue-500 hover:ring-2 hover:ring-blue-500/20 transition-all text-left shadow-sm active:scale-95 outline-none cursor-pointer";
            
            btn.onclick = (e) => {
                e.stopPropagation();
                window.estadoPersonalizacion.detalles = [m.nombre]; // Asigna la cubierta
                window.estadoPersonalizacion.extraAcumulado = precioExtra;
                
                // Pasa al Paso 2 con los acompañamientos de papas
                mostrarSelectorPapasGenerico(20, 35, () => mostrarPasoCubiertasBanderilla());
            };

            btn.innerHTML = `
                <div>
                    <span class="font-bold text-slate-800 text-sm block">${m.nombre}</span>
                    <span class="text-[10px] uppercase font-bold ${precioExtra > 0 ? 'text-amber-500' : 'text-slate-400'}">
                        ${precioExtra > 0 ? 'Especial' : 'Clásica'}
                    </span>
                </div>
                <span class="font-black ${precioExtra > 0 ? 'text-blue-600' : 'text-slate-400'} text-sm">
                    ${precioExtra > 0 ? '+$' + precioExtra.toFixed(2) : 'Incluida'}
                </span>
            `;
            cont.appendChild(btn);
        });
    }

    // --- BANDERILLAS: PASO 2 (SOLO ACOMPAÑAMIENTOS DE PAPAS) ---
    function mostrarSelectorPapasGenerico(extraFrancesa = 20, extraGajo = 35, callbackVolver = null) {
        const prod = window.productoActivoParaComanda;
        const tuveSalsas = Array.isArray(window.estadoPersonalizacion?.salsasElegidas) &&
                           window.estadoPersonalizacion.salsasElegidas.length > 0;
        const pasoPapas = tuveSalsas ? 3 : 2;
        document.getElementById('titulo-modal-variantes').textContent = prod.nombre;
        document.getElementById('subtitulo-modal-variantes').textContent = `Paso ${pasoPapas}: ¿Agregar Papas?`;

        const cont = document.getElementById('lista-opciones-variantes');
        cont.innerHTML = '';

        const btnVolver = document.getElementById('btn-volver-tamano');
        if (btnVolver && callbackVolver) {
            btnVolver.classList.remove('hidden');
            btnVolver.onclick = (e) => {
                e.stopPropagation();
                window.volverPasoAnterior = callbackVolver;
                callbackVolver();
            };
        }

        const todosMods = prod.modificadores || [];

        // Filtro estricto: Solo acompañamientos (que empiezan por c/n, con, p.gajo o francesa)
        const esAcompanamiento = (nombre) => {
            const n = (nombre || '').toLowerCase().trim();
            return n.startsWith('c/n') || n.startsWith('con ') || n.includes('p.gajo') || n.includes('francesa');
        };

        const modsPapas = todosMods.filter(m => esAcompanamiento(m.nombre));

        let opcionesPapas = [];

        // 1. Opción base siempre presente
        opcionesPapas.push({ nombre: 'Sin Papas', extra: 0, subtitulo: 'Sin costo extra' });

        // 2. Si vienen dados de alta en BD (ej. "C/N PAPAS X-TRAS", "C/N P.GAJO")
        if (modsPapas.length > 0) {
            modsPapas.forEach(m => {
                const precio = parseFloat(m.precio || 0);
                opcionesPapas.push({
                    nombre: m.nombre,
                    extra: precio,
                    subtitulo: precio > 0 ? `+$${precio.toFixed(2)}` : 'Sin costo extra'
                });
            });
        } else {
            // Valores por defecto si no están registrados
            opcionesPapas.push({ nombre: 'c/ Papas Francesa', extra: Number(extraFrancesa), subtitulo: `+$${Number(extraFrancesa).toFixed(2)}` });
            opcionesPapas.push({ nombre: 'c/ Papas Gajo', extra: Number(extraGajo), subtitulo: `+$${Number(extraGajo).toFixed(2)}` });
        }

        // precioBase ya incluye precio de variante + extras de salsas.
        // extraAcumulado cubre cubiertas de banderilla u otros extras de paso anterior.
        const precioBaseAcumulado = (window.estadoPersonalizacion.precioBase || parseFloat(prod.precio || 0))
            + (window.estadoPersonalizacion.extraAcumulado || 0);

        opcionesPapas.forEach(op => {
            const totalFinalOpcion = precioBaseAcumulado + op.extra;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = "w-full flex justify-between items-center bg-white border border-slate-200 p-3.5 rounded-2xl hover:border-blue-500 hover:ring-2 hover:ring-blue-500/20 transition-all text-left shadow-sm active:scale-95 outline-none cursor-pointer";
            
            btn.onclick = (e) => {
                e.stopPropagation();
                if (op.nombre !== 'Sin Papas') {
                    window.estadoPersonalizacion.detalles.push(op.nombre);
                }
                finalizarTicket(totalFinalOpcion);
            };

            btn.innerHTML = `
                <div>
                    <span class="font-bold text-slate-800 text-sm block">${op.nombre}</span>
                    <span class="text-[10px] font-bold text-slate-400">${op.subtitulo}</span>
                </div>
                <span class="font-black text-blue-600 text-sm">
                    $${totalFinalOpcion.toFixed(2)}
                </span>
            `;
            cont.appendChild(btn);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SELECTOR DE SALSAS — Un solo modal, selección múltiple simultánea
    // ─────────────────────────────────────────────────────────────────────────

    // Reglas de cuántas salsas lleva cada tamaño.
    // Alitas: 6pz→1, 10pz→2 | Boneless: 250g→1, 1/2→2, 1k→4
    function calcularNumeroSalsas(nombreVariante) {
        const n = (nombreVariante || '').toLowerCase();
        if (n.includes('1k') || n.includes('1 k') || n.includes('1kg'))   return 4;
        if (n.includes('1/2') || n.includes('medio'))                      return 2;
        if (n.includes('250'))                                              return 1;
        if (n.includes('10') || n.includes('12') || n.includes('15'))      return 2;
        if (n.includes('6')  || n.includes('8'))                           return 1;
        return 1;
    }

    const SALSAS_INCLUIDAS = [
        'Mango Habanero', 'Fresa Hot', 'Maracuyá Habanero', 'Búfalo',
        'Tamarindo Chipotle', 'Pelón Pelo Rico', 'Frutos Rojos Chipotle',
        'Pimienta Limón', 'BBQ', 'Tradicional'
    ];
    const SALSAS_GOURMET = [
        { nombre: 'Naranja Mezcal', extra: 8 },
        { nombre: 'Ajo Parmesano',  extra: 10 },
    ];

    // ─── Estado local del selector de salsas ───────────────────────────────
    // Guarda qué salsas están marcadas y sus extras mientras el modal está abierto.
    let _salsasMarcadas = [];   // [{ nombre, extra }]
    let _salsasTotal    = 1;

function mostrarSelectorSalsas(totalSalsas, callbackVolver) {
        _salsasMarcadas = [];
        _salsasTotal    = totalSalsas;

        const prod = window.productoActivoParaComanda;
        const tam  = window.estadoPersonalizacion.tamanoSeleccionado;

        // ── Encabezado del modal ─────────────────────────────────────────────
        document.getElementById('titulo-modal-variantes').textContent =
            tam ? `${prod.nombre} (${tam})` : prod.nombre;

        // CAMBIO: Modificamos el texto para decir "hasta X salsas"
        const labelSalsas = totalSalsas === 1 ? '1 salsa' : `hasta ${totalSalsas} salsas`;
        document.getElementById('subtitulo-modal-variantes').textContent =
            `Paso 2: Elige ${labelSalsas} — 0 / ${totalSalsas} seleccionadas`;

        // ── Botón Volver ─────────────────────────────────────────────────────
        const btnVolver = document.getElementById('btn-volver-tamano');
        btnVolver.classList.remove('hidden');
        btnVolver.onclick = (e) => { e.stopPropagation(); callbackVolver(); };

        // ── Contenedor principal ─────────────────────────────────────────────
        const cont = document.getElementById('lista-opciones-variantes');
        cont.innerHTML = '';

        // Barra de progreso + contador
        const barraWrap = document.createElement('div');
        barraWrap.className = 'mb-3';
        barraWrap.innerHTML = `
            <div class="flex justify-between items-center mb-1.5">
                <span id="lbl-salsa-contador"
                      class="text-xs font-black text-slate-500">
                    0 / ${totalSalsas} seleccionada${totalSalsas > 1 ? 's' : ''}
                </span>
                <span id="lbl-salsa-extra"
                      class="text-xs font-black text-amber-500 hidden">
                    +$0.00 extra
                </span>
            </div>
            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div id="barra-salsa-progreso"
                     class="h-full bg-blue-500 rounded-full transition-all duration-300"
                     style="width:0%"></div>
            </div>
        `;
        cont.appendChild(barraWrap);

        // Botón Confirmar
        const panel = document.getElementById('panel-variantes-comanda');
        const prevBtn = panel.querySelector('#btn-confirmar-salsas');
        if (prevBtn) prevBtn.remove();

        const btnConfirmar = document.createElement('button');
        btnConfirmar.id        = 'btn-confirmar-salsas';
        btnConfirmar.type      = 'button';
        btnConfirmar.disabled  = true; // Sigue deshabilitado hasta que elija 1
        btnConfirmar.className = 'w-full mt-4 min-h-[46px] rounded-2xl bg-blue-500 text-white text-sm font-black uppercase tracking-wider shadow-md shadow-blue-500/20 transition-all duration-150 disabled:opacity-40 disabled:cursor-not-allowed active:scale-95';
        
        // CAMBIO: Texto por defecto del botón
        btnConfirmar.textContent = totalSalsas === 1 ? 'Elige 1 salsa' : `Elige hasta ${totalSalsas} salsas`;
        
        btnConfirmar.onclick = _confirmarSalsas;
        panel.appendChild(btnConfirmar);

        // ── Sección Incluidas ────────────────────────────────────────────────
        const hIncluidas = document.createElement('p');
        hIncluidas.className = 'text-[10px] font-black text-slate-400 uppercase tracking-widest px-1 mb-2 mt-1';
        hIncluidas.textContent = 'Salsas Incluidas';
        cont.appendChild(hIncluidas);

        SALSAS_INCLUIDAS.forEach(nombre =>
            cont.appendChild(_crearChipSalsa(nombre, 0))
        );

        // ── Sección Gourmet ──────────────────────────────────────────────────
        const hGourmet = document.createElement('p');
        hGourmet.className = 'text-[10px] font-black text-amber-500 uppercase tracking-widest px-1 mb-2 mt-4';
        hGourmet.innerHTML = '✦ Gourmet';
        cont.appendChild(hGourmet);

        SALSAS_GOURMET.forEach(({ nombre, extra }) =>
            cont.appendChild(_crearChipSalsa(nombre, extra))
        );
    }

    // Crea un chip de salsa (toggle: seleccionar / deseleccionar)
    function _crearChipSalsa(nombre, extra) {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.dataset.nombre = nombre;
        chip.dataset.extra  = extra;
        chip.className = [
            'w-full flex justify-between items-center',
            'bg-white border border-slate-200',
            'p-3 rounded-2xl transition-all text-left shadow-sm',
            'active:scale-95 outline-none cursor-pointer',
            'mb-2'
        ].join(' ');

        chip.innerHTML = `
            <div class="flex items-center gap-3">
                <span class="chip-check w-5 h-5 rounded-full border-2 border-slate-300
                             flex items-center justify-center shrink-0 transition-all">
                </span>
                <span class="font-bold text-slate-800 text-sm">${nombre}</span>
            </div>
            <span class="font-black text-sm ${extra > 0 ? 'text-amber-500' : 'text-slate-400'}">
                ${extra > 0 ? '+$' + Number(extra).toFixed(2) : 'Incluida'}
            </span>
        `;

        chip.onclick = (e) => {
            e.stopPropagation();
            const yaEsta = _salsasMarcadas.findIndex(s => s.nombre === nombre);

            if (yaEsta >= 0) {
                // Deseleccionar
                _salsasMarcadas.splice(yaEsta, 1);
                chip.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500/20', 'bg-blue-50/40');
                chip.classList.add('border-slate-200', 'bg-white');
                chip.querySelector('.chip-check').innerHTML = '';
                chip.querySelector('.chip-check').classList.remove('bg-blue-500', 'border-blue-500');
                chip.querySelector('.chip-check').classList.add('border-slate-300');
            } else if (_salsasMarcadas.length < _salsasTotal) {
                // Seleccionar (mientras no se haya llenado el cupo)
                _salsasMarcadas.push({ nombre, extra: Number(extra) });
                chip.classList.add('border-blue-500', 'ring-2', 'ring-blue-500/20', 'bg-blue-50/40');
                chip.classList.remove('border-slate-200', 'bg-white');
                chip.querySelector('.chip-check').innerHTML =
                    '<i class="fas fa-check text-[9px] text-white"></i>';
                chip.querySelector('.chip-check').classList.add('bg-blue-500', 'border-blue-500');
                chip.querySelector('.chip-check').classList.remove('border-slate-300');
            } else {
                // Cupo lleno — agitar el chip para indicar que no puede
                chip.classList.add('animate-[wiggle_0.3s_ease-in-out]');
                setTimeout(() => chip.classList.remove('animate-[wiggle_0.3s_ease-in-out]'), 300);
                return;
            }

            _actualizarEstadoSalsas();
        };

        return chip;
    }

    // Actualiza contador, barra de progreso, extra total y estado del botón Confirmar
function _actualizarEstadoSalsas() {
        const sel   = _salsasMarcadas.length;
        const total = _salsasTotal;
        const extra = _salsasMarcadas.reduce((s, x) => s + x.extra, 0);

        // Subtítulo del modal
        const prod = window.productoActivoParaComanda;
        const tam  = window.estadoPersonalizacion.tamanoSeleccionado;
        
        // CAMBIO: Texto dinámico amigable que indica el límite máximo
        const labelLimite = total === 1 ? '1 salsa' : `hasta ${total} salsas`;
        document.getElementById('subtitulo-modal-variantes').textContent =
            `Paso 2: Elige ${labelLimite} — ${sel} / ${total} seleccionadas`;

        // Contador pequeño
        const lblCont = document.getElementById('lbl-salsa-contador');
        if (lblCont) {
            lblCont.textContent = `${sel} / ${total} seleccionada${total > 1 ? 's' : ''}`;
            // CAMBIO: Si ya eligió al menos 1, lo mostramos en azul
            lblCont.className = sel > 0
                ? 'text-xs font-black text-blue-600'
                : 'text-xs font-black text-slate-500';
        }

        // Extra gourmet
        const lblExtra = document.getElementById('lbl-salsa-extra');
        if (lblExtra) {
            if (extra > 0) {
                lblExtra.textContent = `+$${extra.toFixed(2)} extra`;
                lblExtra.classList.remove('hidden');
            } else {
                lblExtra.classList.add('hidden');
            }
        }

        // Barra progreso
        const barra = document.getElementById('barra-salsa-progreso');
        if (barra) {
            const pct = total > 0 ? (sel / total) * 100 : 0;
            barra.style.width = pct + '%';
            // CAMBIO: La barra se pinta azul si hay al menos 1 seleccionada
            barra.className = `h-full rounded-full transition-all duration-300 ${
                sel > 0 ? 'bg-blue-500' : 'bg-blue-300'
            }`;
        }

        // Botón Confirmar
        const btnConf = document.getElementById('btn-confirmar-salsas');
        if (btnConf) {
            // CAMBIO PRINCIPAL: Se habilita si seleccionó 1 o más salsas (no obligamos a llegar al total)
            btnConf.disabled = (sel === 0); 
            
            if (sel > 0) {
                const label = sel === 1
                    ? `Confirmar: ${_salsasMarcadas[0].nombre}`
                    : `Confirmar ${sel} salsas`;
                btnConf.textContent = label;
            } else {
                btnConf.textContent = total === 1 ? 'Elige 1 salsa' : `Elige hasta ${total} salsas`;
            }
        }
    }

    // Se llama al presionar Confirmar
    function _confirmarSalsas() {
        const extra = _salsasMarcadas.reduce((s, x) => s + x.extra, 0);

        window.estadoPersonalizacion.salsasElegidas  = _salsasMarcadas.map(s => s.nombre);
        window.estadoPersonalizacion.extraSalsas     = extra;
        window.estadoPersonalizacion.detalles.push(...window.estadoPersonalizacion.salsasElegidas);
        window.estadoPersonalizacion.precioBase     += extra;

        // Eliminar el botón Confirmar que se añadió al panel
        const btnConf = document.getElementById('btn-confirmar-salsas');
        if (btnConf) btnConf.remove();

        evaluarPapasPorTamano(window.estadoPersonalizacion.tamanoSeleccionado, null);
    }

    // --- FINALIZACIÓN AL TICKET ---
    function renderOpcionesFinales(opciones) {
        const cont = document.getElementById('lista-opciones-variantes');
        cont.innerHTML = '';

        opciones.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = "w-full flex justify-between items-center bg-white border border-slate-200 p-4 rounded-2xl hover:border-blue-500 hover:ring-2 hover:ring-blue-500/20 transition-all text-left shadow-sm active:scale-95 outline-none cursor-pointer";

            const precioFinal = window.estadoPersonalizacion.precioBase + window.estadoPersonalizacion.extraAcumulado + opt.extra;

            btn.onclick = (e) => {
                e.stopPropagation();
                if (opt.nombre !== 'Sin Extras / Complementos') {
                    window.estadoPersonalizacion.detalles.push(opt.nombre);
                }
                finalizarTicket(precioFinal);
            };

            const subtexto = window.estadoPersonalizacion.precioBase === 0 
                ? 'Precio total' 
                : (opt.extra > 0 ? '+$' + opt.extra.toFixed(2) : 'Sin costo extra');

            btn.innerHTML = `
                <div class="flex flex-col">
                    <span class="font-bold text-slate-800 text-sm sm:text-base">${opt.nombre}</span>
                    <span class="text-[11px] font-semibold text-slate-400">${subtexto}</span>
                </div>
                <span class="font-black text-blue-600 text-sm sm:text-base">$${precioFinal.toFixed(2)}</span>
            `;
            cont.appendChild(btn);
        });
    }

    function finalizarTicket(precioTotal) {
        cerrarModalVariantes();
        const est = window.estadoPersonalizacion;
        const prod = window.productoActivoParaComanda;

        let partes = [];
        if (est.tamanoSeleccionado) partes.push(est.tamanoSeleccionado);
        if (est.detalles && est.detalles.length > 0) partes.push(est.detalles.join(', '));

        const nombreFinal = partes.length > 0 ? `${est.nombreBase} (${partes.join(' - ')})` : est.nombreBase;
        const notasParaCocina = partes.length > 0 ? partes.join(' - ') : null;

        agregarAlTicket(
            prod.id, 
            nombreFinal, 
            precioTotal, 
            prod.categoria?.nombre || 'Menú', 
            partes,
            false, 
            0,
            notasParaCocina
        );
    }

    window.cerrarModalVariantes = function() {
        const modal = document.getElementById('modal-variantes-comanda');
        const panel = document.getElementById('panel-variantes-comanda');
        if (panel) panel.classList.add('opacity-0', 'translate-y-8');
        if (modal) modal.classList.add('opacity-0');
        // Limpiar el botón Confirmar de salsas si quedó montado
        const btnConf = document.getElementById('btn-confirmar-salsas');
        if (btnConf) btnConf.remove();
        _salsasMarcadas = [];
        setTimeout(() => {
            if (modal) { modal.classList.add('hidden'); modal.style.display = 'none'; }
            window.productoActivoParaComanda = null;
        }, 300);
    };
</script>