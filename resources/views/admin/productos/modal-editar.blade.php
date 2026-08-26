<style>
    /* Ajuste para pantallas grandes */
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modal-editar-wrapper {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        body.teclado-virtual-abierto #modal-editar-panel {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important;
            overflow-y: auto !important;
        }
    }
</style>

{{-- MODAL EDITAR ALIMENTO --}}
<div id="modal-editar-alimento" class="fixed inset-y-0 right-0 left-[74px] sm:left-0 sm:inset-0 z-[100] overflow-y-auto overscroll-contain hidden opacity-0 transition-all duration-300">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm -ml-[74px] sm:ml-0" onclick="closeModalEditar()"></div>
    
    <div id="modal-editar-wrapper" class="flex min-h-screen items-center justify-center p-3 sm:p-4">
        <div class="relative bg-[#F2F2F2] border border-slate-300/70 w-full max-w-2xl rounded-[2rem] shadow-2xl transform opacity-0 translate-y-8 transition-all duration-300 overflow-hidden" id="modal-editar-panel">
            
            <div class="p-6 sm:p-8 pb-4 sm:pb-6 border-b border-slate-300/60 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Editar Platillo</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Actualiza la información y receta del platillo</p>
                </div>
                <button type="button" onclick="closeModalEditar()" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all duration-300 rounded-xl bg-white border border-slate-200 hover:border-rose-200 hover:bg-rose-50 outline-none active:scale-95 shrink-0 shadow-sm">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

           <form id="formulario-editar-alimento" onsubmit="actualizarProducto(event)" novalidate class="p-6 sm:p-8 pt-4 bg-[#F2F2F2]">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    
                    {{-- Nombre --}}
                    <div class="col-span-1 sm:col-span-2">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Nombre del Platillo</label>
                        <input type="text" id="edit-nombre" name="nombre" data-teclado="texto" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-4 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition shadow-sm" required>
                    </div>

                    {{-- Toggle Peso --}}
                    <div class="col-span-1 sm:col-span-2">
                        <label class="flex items-center justify-between gap-3 bg-orange-100/50 border border-orange-200/80 rounded-2xl p-4 cursor-pointer select-none shadow-sm">
                            <span class="flex items-center gap-3">
                                <i class="fas fa-weight-hanging text-orange-500 text-sm"></i>
                                <span class="text-xs sm:text-sm font-bold text-slate-800">Se vende por peso</span>
                            </span>
                            <span class="relative inline-flex items-center">
                                <input type="checkbox" id="edit-se_vende_por_peso" name="se_vende_por_peso" class="peer sr-only" onchange="toggleModoVentaPeso('editar')">
                                <span class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-orange-500 transition-colors"></span>
                                <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></span>
                            </span>
                        </label>
                    </div>

                    {{-- Toggle: Tiene Variantes (Tamaños) Editar --}}
                    <div class="col-span-1 sm:col-span-2">
                        <label class="flex items-center justify-between gap-3 bg-blue-50/50 border border-blue-200/80 rounded-2xl p-4 cursor-pointer select-none shadow-sm">
                            <span class="flex items-center gap-3">
                                <i class="fas fa-layer-group text-blue-500 text-sm"></i>
                                <span class="text-xs sm:text-sm font-bold text-slate-800">Tiene diferentes tamaños/presentaciones</span>
                            </span>
                            <span class="relative inline-flex items-center">
                                <input type="checkbox" id="edit-tiene_variantes" name="tiene_variantes" class="peer sr-only" onchange="toggleVariantes('editar')">
                                <span class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-blue-500 transition-colors"></span>
                                <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></span>
                            </span>
                        </label>
                    </div>

                    {{-- Sección Dinámica de Variantes Editar (Tamaños + Extras por tamaño) --}}
                    <div class="col-span-1 sm:col-span-2 hidden" id="seccion-variantes-editar">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest ml-1 block">Tamaños y Precios</label>
                                <p class="text-[9px] text-slate-400 font-bold mt-0.5 ml-1 tracking-wide uppercase">
                                    Agrega o edita tamaños y sus extras específicos
                                </p>
                            </div>
                            <button type="button" onclick="agregarFilaVariante('editar')" class="inline-flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-xl font-bold transition text-[10px] uppercase tracking-widest outline-none">
                                <i class="fas fa-plus"></i> AGREGAR TAMAÑO
                            </button>
                        </div>
                        <div id="contenedor-variantes-editar" class="space-y-3"></div>
                    </div>

                    {{-- Toggle: Tiene Modificadores/Extras Globales Editar (solo si no tiene variantes) --}}
                    <div class="col-span-1 sm:col-span-2" id="bloque-toggle-modificadores-editar">
                        <label class="flex items-center justify-between gap-3 bg-purple-50/50 border border-purple-200/80 rounded-2xl p-4 cursor-pointer select-none shadow-sm">
                            <span class="flex items-center gap-3">
                                <i class="fas fa-puzzle-piece text-purple-500 text-sm"></i>
                                <span class="text-xs sm:text-sm font-bold text-slate-800">Tiene complementos / extras (Papas, etc.)</span>
                            </span>
                            <span class="relative inline-flex items-center">
                                <input type="checkbox" id="edit-tiene_modificadores" name="tiene_modificadores" class="peer sr-only" onchange="toggleModificadores('editar')">
                                <span class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-purple-600 transition-colors"></span>
                                <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></span>
                            </span>
                        </label>
                    </div>

                    {{-- Sección Dinámica de Modificadores Globales Editar --}}
                    <div class="col-span-1 sm:col-span-2 hidden" id="seccion-modificadores-editar">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="text-[10px] font-black text-purple-600 uppercase tracking-widest ml-1 block">Extras y Precios Generales</label>
                            </div>
                            <button type="button" onclick="agregarFilaModificador('editar')" class="inline-flex items-center gap-2 bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-2 rounded-xl font-bold transition text-[10px] uppercase tracking-widest outline-none">
                                <i class="fas fa-plus"></i> AGREGAR EXTRA
                            </button>
                        </div>
                        <div id="contenedor-modificadores-editar" class="space-y-2"></div>
                    </div>

                    {{-- Precio Fijo --}}
                    <div class="col-span-1" id="grupo-precio-fijo-editar">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Precio</label>
                        <input type="text" id="edit-precio" name="precio" pattern="[0-9]*\.?[0-9]*" data-teclado="numerico" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-4 text-sm font-black text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition shadow-sm">
                    </div>

                    {{-- Precio por Peso --}}
                    <div class="col-span-1 hidden" id="grupo-precio-peso-editar">
                        <label class="text-[10px] font-black text-orange-600 uppercase tracking-widest ml-1 mb-1.5 block">Precio por 100g</label>
                        <input type="text" id="edit-precio_por_100g" name="precio_por_100g" pattern="[0-9]*\.?[0-9]*" data-teclado="numerico" inputmode="none" class="w-full bg-white border border-orange-300 rounded-xl p-4 text-sm font-black text-slate-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition shadow-sm">
                    </div>

                    {{-- Categoría --}}
                    <div class="col-span-1 relative">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Categoría</label>
                        <input type="text" id="edit-categoria_nombre" name="categoria_nombre" list="lista-categorias-editar" data-teclado="texto" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-4 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition shadow-sm" autocomplete="off" required>
                        <input type="hidden" id="edit-categoria_id" name="categoria_id">
                        <datalist id="lista-categorias-editar">
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->nombre }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Descripción --}}
                    <div class="col-span-1 sm:col-span-2">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Descripción</label>
                        <textarea id="edit-descripcion" name="descripcion" rows="3" data-teclado="texto" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-4 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition resize-none shadow-sm"></textarea>
                    </div>

                    {{-- Ingredientes --}}
                    <div class="col-span-1 sm:col-span-2">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Ingredientes de la Receta</label>
                            <button type="button" onclick="agregarIngrediente('editar')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest transition shadow-md shadow-blue-500/20 outline-none">
                                <i class="fas fa-plus"></i> AGREGAR
                            </button>
                        </div>
                        <div id="ingredientes-container-editar" class="space-y-3"></div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mt-8 pt-6 border-t border-slate-300/60 bg-slate-200/50 -mx-6 sm:-mx-8 -mb-6 sm:-mb-8 p-6 sm:p-8 flex gap-4">
                    <button type="button" onclick="closeModalEditar()" class="flex-1 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition py-4 rounded-2xl outline-none active:scale-95">Cancelar</button>
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-600/20 text-xs uppercase tracking-widest active:scale-95 transition outline-none" id="btn-actualizar">ACTUALIZAR PLATILLO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ─── Contadores de filas dinámicas ───────────────────────────────────────
    let contadorModificadores = 0;
    let contadorVariantesEditar = 0;

    // ─── Bloqueo/desbloqueo de scroll del fondo ─────────────────────────
    window.bloquearScrollFondo = function () {
        document.body.style.overflow = 'hidden';
    };
    window.desbloquearScrollFondo = function () {
        document.body.style.overflow = '';
    };

    // ─── Cerrar modal editar ─────────────────────────────────────────────────
    function closeModalEditar() {
        if (typeof _cerrarModal === 'function') {
            _cerrarModal('modal-editar-alimento', 'modal-editar-panel');
        } else {
            const modal = document.getElementById('modal-editar-alimento');
            const panel = document.getElementById('modal-editar-panel');
            if (modal && panel) {
                modal.classList.add('opacity-0');
                panel.classList.add('opacity-0', 'translate-y-8');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }
        }
        desbloquearScrollFondo();
        const form = document.getElementById('formulario-editar-alimento');
        if (form) form.reset();
    }

    // ─── Control de Switches en Editar ───────────────────────────────────────
    function toggleVariantes(modo) {
        const checkId = (modo === 'crear') ? 'tiene_variantes' : 'edit-tiene_variantes';
        const seccionId = (modo === 'crear') ? 'seccion-variantes-crear' : 'seccion-variantes-editar';
        const grupoFijoId = (modo === 'crear') ? 'grupo-precio-fijo-crear' : 'grupo-precio-fijo-editar';
        const inputFijoId = (modo === 'crear') ? 'precio' : 'edit-precio';
        const bloqueModsId = (modo === 'crear') ? 'bloque-toggle-modificadores-crear' : 'bloque-toggle-modificadores-editar';
        const seccionModsId = (modo === 'crear') ? 'seccion-modificadores-crear' : 'seccion-modificadores-editar';

        const check = document.getElementById(checkId);
        const seccion = document.getElementById(seccionId);
        const grupoFijo = document.getElementById(grupoFijoId);
        const inputFijo = document.getElementById(inputFijoId);
        const bloqueMods = document.getElementById(bloqueModsId);
        const seccionMods = document.getElementById(seccionModsId);

        if (!check || !seccion) return;

        if (check.checked) {
            seccion.classList.remove('hidden');
            if (grupoFijo) grupoFijo.classList.add('hidden');
            if (inputFijo) { inputFijo.required = false; inputFijo.value = 0; }
            if (bloqueMods) bloqueMods.classList.add('hidden');
            if (seccionMods) seccionMods.classList.add('hidden');
        } else {
            seccion.classList.add('hidden');
            if (grupoFijo) grupoFijo.classList.remove('hidden');
            if (inputFijo) inputFijo.required = true;
            if (bloqueMods) bloqueMods.classList.remove('hidden');
        }
    }

    function toggleModificadores(modo) {
        const idCheck = (modo === 'crear') ? 'tiene_modificadores' : 'edit-tiene_modificadores';
        const idSeccion = (modo === 'crear') ? 'seccion-modificadores-crear' : 'seccion-modificadores-editar';
        const idCont = (modo === 'crear') ? 'contenedor-modificadores-crear' : 'contenedor-modificadores-editar';

        const check = document.getElementById(idCheck);
        const seccion = document.getElementById(idSeccion);
        if (!check || !seccion) return;

        if (check.checked) {
            seccion.classList.remove('hidden');
            const cont = document.getElementById(idCont);
            if (cont && cont.children.length === 0) {
                agregarFilaModificador(modo);
            }
        } else {
            seccion.classList.add('hidden');
        }
    }

    // ─── Agregar Fila de Variante (Tamaño) con soporte de Extras Anidados ────
    function agregarFilaVariante(modo, datos = null) {
        const contId = (modo === 'crear') ? 'contenedor-variantes-crear' : 'contenedor-variantes-editar';
        const contenedor = document.getElementById(contId);
        if (!contenedor) return;

        const varIndex = (modo === 'crear') ? window.contadorVariantes++ : contadorVariantesEditar++;
        const id = datos?.id ?? '';
        const nombre = datos?.nombre ?? '';
        const precio = (datos && datos.precio !== undefined && datos.precio !== null && parseFloat(datos.precio) > 0) 
            ? parseFloat(datos.precio).toFixed(2) 
            : '';

        const htmlFila = `
            <div class="bg-white p-3.5 border border-slate-200 rounded-2xl shadow-sm space-y-3 fila-variante" id="variante-${modo}-${varIndex}">
                {{-- Encabezado del tamaño --}}
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs shrink-0">
                        <i class="fas fa-layer-group text-[10px]"></i>
                    </span>
                    <input type="hidden" name="variantes[${varIndex}][id]" value="${id}">
                    <input type="text" name="variantes[${varIndex}][nombre]" value="${nombre}" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-2 text-sm font-bold text-slate-800 outline-none placeholder:text-slate-400 focus:border-blue-400" placeholder="Ej: 500ML, 1L, 6pz..." required>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2">
                        <span class="text-slate-400 font-black text-xs">$</span>
                        <input type="number" step="0.01" name="variantes[${varIndex}][precio]" value="${precio}" class="w-28 bg-transparent p-2 text-sm font-black text-slate-800 outline-none placeholder:text-slate-400 text-right" placeholder="0.00 (Opcional)">
                    </div>
                    <button type="button" onclick="document.getElementById('variante-${modo}-${varIndex}').remove()" class="w-8 h-8 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors outline-none shrink-0" title="Eliminar tamaño">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>

                {{-- Extras exclusivos de este tamaño --}}
                <div class="pl-3 border-l-2 border-purple-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-purple-600 uppercase tracking-widest">
                            <i class="fas fa-puzzle-piece mr-1"></i> Complementos / Bases de este tamaño
                        </span>
                        <button type="button" onclick="agregarExtraAVarianteEditar(${varIndex})" class="text-[9px] font-black text-purple-700 bg-purple-100 hover:bg-purple-200 px-2 py-1 rounded-lg transition uppercase tracking-wider">
                            + Extra
                        </button>
                    </div>
                    <div id="contenedor-extras-var-edit-${varIndex}" class="space-y-1.5"></div>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', htmlFila);

        // Si trae extras asociados (modificadores), renderizarlos dentro de esta variante
        const extras = datos?.modificadores || datos?.extras || [];
        if (Array.isArray(extras) && extras.length > 0) {
            extras.forEach(extra => {
                agregarExtraAVarianteEditar(varIndex, extra);
            });
        }
    }

  function agregarExtraAVarianteEditar(varIndex, datos = null) {
    const contenedor = document.getElementById(`contenedor-extras-var-edit-${varIndex}`);
    if (!contenedor) return;
    const extraIndex = contenedor.children.length; // Índice secuencial (0, 1, 2...)

    const id = datos?.id ?? '';
    const nombre = datos?.nombre ?? '';
    const precio = (datos && datos.precio !== undefined && datos.precio !== null) 
        ? parseFloat(datos.precio).toFixed(2) 
        : '';

    const html = `
        <div class="flex items-center gap-2 bg-purple-50/40 p-1.5 border border-purple-100 rounded-xl fila-extra-variante" id="extra-var-edit-${varIndex}-${extraIndex}">
            <input type="hidden" name="variantes[${varIndex}][extras][${extraIndex}][id]" value="${id}">
            <input type="text" name="variantes[${varIndex}][extras][${extraIndex}][nombre]" value="${nombre}" placeholder="Ej: AGUA, LECHE..." class="flex-1 bg-white border border-purple-200/60 rounded-lg p-1.5 text-xs font-bold text-slate-800 outline-none placeholder:text-slate-400" required>
            <div class="flex items-center bg-white border border-purple-200/60 rounded-lg px-2">
                <span class="text-purple-400 font-black text-xs">+$</span>
                <input type="number" step="0.01" name="variantes[${varIndex}][extras][${extraIndex}][precio]" value="${precio}" placeholder="0.00" class="w-16 bg-transparent p-1.5 text-xs font-black text-slate-800 outline-none placeholder:text-slate-400 text-right">
            </div>
            <button type="button" onclick="this.closest('.fila-extra-variante').remove()" class="w-6 h-6 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors outline-none shrink-0">
                <i class="fas fa-times text-[10px]"></i>
            </button>
        </div>
    `;
    contenedor.insertAdjacentHTML('beforeend', html);
}

    // ─── Modificadores Globales (para platillos sin variantes) ────────────────
    function agregarFilaModificador(modo, datos = null) {
        const idCont = (modo === 'crear') ? 'contenedor-modificadores-crear' : 'contenedor-modificadores-editar';
        const contenedor = document.getElementById(idCont);
        if (!contenedor) return;

        const index = contadorModificadores++;
        const id = datos?.id ?? '';
        const nombre = datos?.nombre ?? '';
        const precio = (datos && datos.precio !== undefined && datos.precio !== null) 
            ? parseFloat(datos.precio).toFixed(2) 
            : '';

        const html = `
            <div class="flex items-center gap-2 bg-white p-2 border border-slate-200 rounded-xl shadow-sm fila-modificador" id="modificador-${modo}-${index}">
                <input type="hidden" name="modificadores[${index}][id]" value="${id}">
                <input type="text" name="modificadores[${index}][nombre]" value="${nombre}" placeholder="Nombre del extra (ej. Papas Francesa)" class="flex-1 bg-transparent p-2 text-sm font-semibold text-slate-800 outline-none placeholder:text-slate-400" required>
                <div class="w-px h-6 bg-slate-200"></div>
                <span class="pl-2 text-slate-400 font-bold">+$</span>
                <input type="number" step="0.01" name="modificadores[${index}][precio]" value="${precio}" placeholder="0.00" class="w-24 bg-transparent p-2 text-sm font-black text-slate-800 outline-none placeholder:text-slate-400">
                <button type="button" onclick="document.getElementById('modificador-${modo}-${index}').remove()" class="w-8 h-8 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors outline-none">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    }

    // ─── Rellenar Variantes y Modificadores al Abrir Edición ─────────────────
    function llenarVariantesEdicion(producto) {
        const contenedor = document.getElementById('contenedor-variantes-editar');
        if (!contenedor) return;
        contenedor.innerHTML = '';
        contadorVariantesEditar = 0;
        
        if (producto.tiene_variantes && Array.isArray(producto.variantes) && producto.variantes.length > 0) {
            producto.variantes.forEach(variante => {
                agregarFilaVariante('editar', variante);
            });
        }
    }

    function llenarModificadoresEdicion(producto) {
        const contenedor = document.getElementById('contenedor-modificadores-editar');
        if (!contenedor) return;
        contenedor.innerHTML = '';

        // Solo cargamos modificadores globales (que no tengan variante_id)
        const modsGlobales = Array.isArray(producto.modificadores) 
            ? producto.modificadores.filter(m => !m.variante_id) 
            : [];

        const tieneMods = modsGlobales.length > 0;
        const check = document.getElementById('edit-tiene_modificadores');
        if (check) check.checked = tieneMods;

        if (tieneMods) {
            modsGlobales.forEach(mod => {
                agregarFilaModificador('editar', mod);
            });
        }
        toggleModificadores('editar');
    }

    // ─── Abrir modal con datos del producto ──────────────────────────────────
    function editarProducto(id) {
        if (!tienePermisoEditar) { mostrarNotificacion('Sin permisos para editar', 'error'); return; }
        const producto = estadoGlobal.productosMap[id];
        if (!producto) return;
        
        estadoGlobal.editandoId = id;
        document.getElementById('edit-nombre').value           = producto.nombre          ?? '';
        document.getElementById('edit-precio').value           = producto.precio          ?? '';
        document.getElementById('edit-precio_por_100g').value  = producto.precio_por_100g ?? '';
        document.getElementById('edit-se_vende_por_peso').checked = !!producto.se_vende_por_peso;
        document.getElementById('edit-descripcion').value      = producto.descripcion      ?? '';
        document.getElementById('edit-categoria_nombre').value = producto.categoria?.nombre ?? '';
        document.getElementById('edit-categoria_id').value     = producto.categoria?.id     ?? '';

        // Variantes (Tamaños) y Extras anidados
        document.getElementById('edit-tiene_variantes').checked = !!producto.tiene_variantes;
        llenarVariantesEdicion(producto);
        toggleVariantes('editar');

        // Modificadores globales (si aplica)
        llenarModificadoresEdicion(producto);

        toggleModoVentaPeso('editar');
        llenarIngredientesEdicion(producto);
        bloquearScrollFondo();

        const modalEditar = document.getElementById('modal-editar-alimento');
        if (modalEditar && modalEditar.parentElement !== document.body) {
            document.body.appendChild(modalEditar);
        }

        _abrirModal('modal-editar-alimento', 'modal-editar-panel');
    }

    // ─── Actualizar producto (Envío) ─────────────────────────────────────────
    function actualizarProducto(event) {
        event.preventDefault();
        if (!tienePermisoEditar) { mostrarNotificacion('Sin autorización para editar', 'error'); return; }
        const btn = document.getElementById('btn-actualizar');
        if (!btn || btn.disabled) return;
        const original = btn.textContent;
        btn.textContent = 'ACTUALIZANDO...';
        btn.disabled    = true;

        const catNombre = document.getElementById('edit-categoria_nombre').value;
        const catId     = obtenerCategoriaIdPorNombre(catNombre);
        if (!catId) {
            mostrarNotificacion('Selecciona una categoría válida', 'error');
            btn.textContent = original; btn.disabled = false; return;
        }
        document.getElementById('edit-categoria_id').value = catId;

        const formEl   = document.getElementById('formulario-editar-alimento');
        const formData = new FormData(formEl);
        formData.set('categoria_id', catId);
        formData.set('se_vende_por_peso', document.getElementById('edit-se_vende_por_peso').checked ? '1' : '0');
        formData.set('tiene_variantes', document.getElementById('edit-tiene_variantes').checked ? '1' : '0');
        
        const tieneModsCheck = document.getElementById('edit-tiene_modificadores');
        formData.set('tiene_modificadores', (tieneModsCheck && tieneModsCheck.checked) ? '1' : '0');
        
        formData.set('_method', 'PUT');

        enviarFormularioConImagen(RUTA_API_BASE + estadoGlobal.editandoId, formData, btn, original, closeModalEditar);
    }
</script>