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
        class="hidden md:hidden fixed left-3 right-3 z-40 items-center justify-between gap-3
               rounded-2xl bg-slate-900 text-white
               px-4 py-3 shadow-[0_10px_30px_-8px_rgba(0,0,0,0.4)]
               active:scale-[0.98] transition-transform duration-150"
        style="bottom: calc(68px + env(safe-area-inset-bottom));">
        <span class="flex items-center gap-2 min-w-0">
            <span id="miniCartCount" class="shrink-0 w-6 h-6 rounded-full bg-white text-slate-900 text-[11px] font-black flex items-center justify-center">0</span>
            <span class="text-[12px] font-bold truncate">Ver orden</span>
        </span>
        <span class="flex items-center gap-2 shrink-0">
            <span id="miniCartTotal" class="text-[13px] font-black">$0.00</span>
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
            extraAcumulado: 0
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

        // 3. Caso Banderillas Coreanas (Cubiertas + Papas)
        if (nombreLower.includes('banderilla') || catLower.includes('banderilla')) {
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
                
                evaluarPapasPorTamano(v.nombre, v);
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
        document.getElementById('titulo-modal-variantes').textContent = prod.nombre;
        document.getElementById('subtitulo-modal-variantes').textContent = 'Paso 2: ¿Agregar Papas?';

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

        const precioBaseAcumulado = parseFloat(prod.precio || 0) + (window.estadoPersonalizacion.extraAcumulado || 0);

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
        setTimeout(() => {
            if (modal) { modal.classList.add('hidden'); modal.style.display = 'none'; }
            window.productoActivoParaComanda = null;
        }, 300);
    };
</script>

{{-- TECLADO VIRTUAL --}}
<div id="teclado-virtual-overlay"
     class="hidden fixed inset-0 z-[9999]"
     onclick="if(event.target===this) cerrarTecladoVirtual()">

    <div id="teclado-virtual"
         class="absolute bottom-0 inset-x-0 bg-slate-50 border-t border-slate-200 shadow-2xl rounded-t-3xl pb-safe">

        {{-- Barra superior --}}
        <div class="flex items-center gap-3 px-4 pt-4 pb-3 border-b border-slate-200">
            <div class="flex-1 flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2.5 min-h-[40px]">
                <i class="fas fa-magnifying-glass text-slate-400 text-xs shrink-0"></i>
                <span id="tv-display" class="flex-1 text-sm font-semibold text-slate-800 break-all"></span>
                <span class="w-0.5 h-4 bg-blue-500 animate-pulse rounded-full"></span>
            </div>
            <button type="button" onclick="cerrarTecladoVirtual()"
                class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 flex items-center justify-center shrink-0 active:scale-95">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Teclado QWERTY --}}
        <div class="px-2 py-3 space-y-1.5 select-none">
            @php
                $filas = [
                    ['Q','W','E','R','T','Y','U','I','O','P'],
                    ['A','S','D','F','G','H','J','K','L'],
                    ['Z','X','C','V','B','N','M'],
                ];
            @endphp

            @foreach($filas as $fila)
                <div class="flex justify-center gap-1">
                    @foreach($fila as $letra)
                        <button type="button"
                            onclick="tvEscribir('{{ $letra }}')"
                            class="tv-key flex-1 max-w-[38px] h-11 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 active:bg-blue-100 transition-all duration-75">
                            {{ $letra }}
                        </button>
                    @endforeach
                </div>
            @endforeach

            {{-- Fila inferior --}}
            <div class="flex justify-center gap-1 mt-1">
                <button type="button" onclick="tvEscribir('1')" class="tv-key w-10 h-11 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 transition-all duration-75">1</button>
                <button type="button" onclick="tvEscribir('2')" class="tv-key w-10 h-11 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 transition-all duration-75">2</button>
                <button type="button" onclick="tvEscribir('3')" class="tv-key w-10 h-11 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 transition-all duration-75">3</button>
                <button type="button" onclick="tvEscribir(' ')"
                    class="tv-key flex-1 h-11 rounded-xl bg-white border border-slate-200 text-slate-500 font-bold text-xs shadow-sm active:scale-95 transition-all duration-75">
                    ESPACIO
                </button>
                <button type="button" onclick="tvEscribir('4')" class="tv-key w-10 h-11 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 transition-all duration-75">4</button>
                <button type="button" onclick="tvEscribir('5')" class="tv-key w-10 h-11 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 transition-all duration-75">5</button>
                <button type="button"
                    onclick="tvBorrar()"
                    class="tv-key w-14 h-11 rounded-xl bg-red-50 border border-red-200 text-red-500 shadow-sm active:scale-95 flex items-center justify-center transition-all duration-75">
                    <i class="fas fa-delete-left text-base"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    let tvValor = '';
    const overlay  = document.getElementById('teclado-virtual-overlay');
    const display  = document.getElementById('tv-display');
    const inputReal = document.getElementById('buscadorProductos');

    window.abrirTecladoVirtual = function () {
        tvValor = inputReal.value || '';
        display.textContent = tvValor;
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.cerrarTecladoVirtual = function () {
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        inputReal.value = tvValor;
        inputReal.dispatchEvent(new Event('input', { bubbles: true }));
    };

    function necesitaTecladoVirtual() {
        return window.innerWidth > 768;
    }

    inputReal.addEventListener('focus', function (e) {
        if (necesitaTecladoVirtual()) {
            e.preventDefault();
            inputReal.blur();
            abrirTecladoVirtual();
        }
    });

    inputReal.addEventListener('click', function (e) {
        if (necesitaTecladoVirtual()) {
            e.preventDefault();
            inputReal.blur();
            abrirTecladoVirtual();
        }
    });

    window.abrirTecladoVirtual_orig = window.abrirTecladoVirtual;
    window.abrirTecladoVirtual = function () {
        window.abrirTecladoVirtual_orig();
        window.setInputVirtualActivo && window.setInputVirtualActivo('buscadorProductos');
        document._tvKeyHandler = function (e) {
            if (!document.getElementById('teclado-virtual-overlay') ||
                document.getElementById('teclado-virtual-overlay').classList.contains('hidden')) return;
            if (e.key === 'Backspace') { e.preventDefault(); tvBorrar(); }
            else if (e.key === 'Escape') { e.preventDefault(); cerrarTecladoVirtual(); }
            else if (e.key === 'Enter') { e.preventDefault(); cerrarTecladoVirtual(); }
            else if (e.key.length === 1) { e.preventDefault(); tvEscribir(e.key.toUpperCase()); }
        };
        document.addEventListener('keydown', document._tvKeyHandler);
    };

    window.cerrarTecladoVirtual_orig = window.cerrarTecladoVirtual;
    window.cerrarTecladoVirtual = function () {
        window.cerrarTecladoVirtual_orig();
        window.clearInputVirtualActivo && window.clearInputVirtualActivo();
        if (document._tvKeyHandler) {
            document.removeEventListener('keydown', document._tvKeyHandler);
            document._tvKeyHandler = null;
        }
    };

    window.tvEscribir = function (char) {
        tvValor += char;
        display.textContent = tvValor;
        inputReal.value = tvValor;
        inputReal.dispatchEvent(new Event('input', { bubbles: true }));
    };

    window.tvBorrar = function () {
        tvValor = tvValor.slice(0, -1);
        display.textContent = tvValor;
        inputReal.value = tvValor;
        inputReal.dispatchEvent(new Event('input', { bubbles: true }));
    };

    const btnLimpiar = document.getElementById('limpiarBusquedaProductos');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => { tvValor = ''; });
    }
})();
</script>