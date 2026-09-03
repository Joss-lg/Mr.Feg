<style>
    /* Solo aplicamos el truco de subir el modal en pantallas grandes (computadoras/punto de venta) */
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modal-crear-alimento {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }

        body.teclado-virtual-abierto #modal-crear-panel {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important;
        }
    }
</style>

{{-- MODAL CREAR ALIMENTO --}}
<div id="modal-crear-alimento" class="fixed inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-3 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModalCrear()"></div>

    {{-- Contenedor principal en fondo #F2F2F2 --}}
    <div class="relative bg-[#F2F2F2] border border-slate-300/70 w-full max-w-xl sm:max-w-2xl max-h-[92vh] flex flex-col rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl transform opacity-0 translate-y-8 transition-all duration-300 overflow-hidden" id="modal-crear-panel">

        <div class="p-5 sm:p-8 pb-4 sm:pb-6 border-b border-slate-300/60 flex justify-between items-start flex-shrink-0">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight" id="modal-title">Nuevo Platillo</h2>
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1" id="modal-subtitle">Configuración estética del menú</p>
            </div>
            <button onclick="closeModalCrear()" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all duration-300 rounded-xl bg-white border border-slate-200 hover:border-rose-200 hover:bg-rose-50 outline-none active:scale-95 shrink-0 shadow-sm">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

<form id="formulario-crear-producto" onsubmit="guardarProducto(event)" novalidate class="flex flex-col flex-1 min-h-0 overflow-y-auto p-5 sm:p-8 pt-4">
            <div class="grid grid-cols-2 gap-4 sm:gap-6 flex-1">

                {{-- Nombre --}}
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Nombre del Platillo</label>
                    <input type="text" id="nombre" name="nombre" data-teclado="texto" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-3.5 sm:p-4 text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition text-sm font-semibold shadow-sm" placeholder="Ej: Lasagna de la Casa" required>
                </div>

                {{-- Toggle: Se vende por peso --}}
                <div class="col-span-2">
                    <label class="flex items-center justify-between gap-3 bg-orange-100/50 border border-orange-200/80 rounded-2xl p-3.5 sm:p-4 cursor-pointer select-none shadow-sm">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-weight-hanging text-orange-500 text-sm"></i>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Se vende por peso</span>
                        </span>
                        <span class="relative inline-flex items-center">
                            <input type="checkbox" id="se_vende_por_peso" name="se_vende_por_peso" class="peer sr-only" onchange="toggleModoVentaPeso('crear')">
                            <span class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-orange-500 transition-colors"></span>
                            <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></span>
                        </span>
                    </label>
                </div>

                {{-- Toggle: Tiene Variantes (Tamaños) --}}
                <div class="col-span-2">
                    <label class="flex items-center justify-between gap-3 bg-blue-50/50 border border-blue-200/80 rounded-2xl p-3.5 sm:p-4 cursor-pointer select-none shadow-sm">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-layer-group text-blue-500 text-sm"></i>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Tiene diferentes tamaños/presentaciones</span>
                        </span>
                        <span class="relative inline-flex items-center">
                            <input type="checkbox" id="tiene_variantes" name="tiene_variantes" class="peer sr-only" onchange="toggleVariantes('crear')">
                            <span class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-blue-500 transition-colors"></span>
                            <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></span>
                        </span>
                    </label>
                </div>

                {{-- Sección Dinámica de Variantes (Tamaños + Extras por tamaño) --}}
                <div class="col-span-2 hidden" id="seccion-variantes-crear">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 mb-3">
                        <div>
                            <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest ml-1 block">Tamaños y Precios</label>
                            <p class="text-[9px] text-slate-400 font-bold mt-1 ml-1 tracking-wide uppercase">
                                Agrega tamaños y sus complementos específicos con precio individual
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="agregarFilaVariante('crear')" class="inline-flex items-center justify-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-xl font-bold transition text-[10px] uppercase tracking-widest outline-none">
                                <i class="fas fa-plus"></i> AGREGAR TAMAÑO
                            </button>
                            <button type="button" onclick="document.getElementById('contenedor-variantes-crear').innerHTML=''" class="inline-flex items-center justify-center gap-2 bg-rose-100 hover:bg-rose-200 text-rose-600 px-3 py-2 rounded-xl font-bold transition text-[10px] uppercase tracking-widest outline-none">
                                <i class="fas fa-trash"></i> LIMPIAR
                            </button>
                        </div>
                    </div>
                    <div id="contenedor-variantes-crear" class="space-y-3"></div>
                </div>

                {{-- Toggle: Extras para producto simple (sin tamaños) --}}
                <div class="col-span-2" id="bloque-toggle-modificadores-crear">
                    <label class="flex items-center justify-between gap-3 bg-purple-50/50 border border-purple-200/80 rounded-2xl p-3.5 sm:p-4 cursor-pointer select-none shadow-sm">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-puzzle-piece text-purple-500 text-sm"></i>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Tiene complementos / extras (Papas, etc.)</span>
                        </span>
                        <span class="relative inline-flex items-center">
                            <input type="checkbox" id="tiene_modificadores" name="tiene_modificadores" class="peer sr-only" onchange="toggleModificadores('crear')">
                            <span class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-purple-600 transition-colors"></span>
                            <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></span>
                        </span>
                    </label>
                </div>

                {{-- Sección de Extras para producto simple --}}
                <div class="col-span-2 hidden" id="seccion-modificadores-crear">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 mb-3">
                        <div>
                            <label class="text-[10px] font-black text-purple-600 uppercase tracking-widest ml-1 block">Extras y Precios Generales</label>
                            <p class="text-[9px] text-slate-400 font-bold mt-1 ml-1 tracking-wide uppercase">
                                Aplica a este platillo único
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="agregarFilaModificador('crear')" class="inline-flex items-center justify-center gap-2 bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-2 rounded-xl font-bold transition text-[10px] uppercase tracking-widest outline-none">
                                <i class="fas fa-plus"></i> AGREGAR EXTRA
                            </button>
                            <button type="button" onclick="document.getElementById('contenedor-modificadores-crear').innerHTML=''" class="inline-flex items-center justify-center gap-2 bg-rose-100 hover:bg-rose-200 text-rose-600 px-3 py-2 rounded-xl font-bold transition text-[10px] uppercase tracking-widest outline-none">
                                <i class="fas fa-trash"></i> LIMPIAR
                            </button>
                        </div>
                    </div>
                    <div id="contenedor-modificadores-crear" class="space-y-2"></div>
                </div>

                {{-- Precio fijo --}}
                <div class="col-span-2 sm:col-span-1" id="grupo-precio-fijo-crear">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Precio</label>
                    <input type="text" id="precio" name="precio" pattern="[0-9]*\.?[0-9]*" data-teclado="numerico" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-3.5 sm:p-4 text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition text-sm font-black shadow-sm" placeholder="0.00" required>
                </div>

                {{-- Precio por 100g --}}
                <div class="col-span-2 sm:col-span-1 hidden" id="grupo-precio-peso-crear">
                    <label class="text-[10px] font-black text-orange-600 uppercase tracking-widest ml-1 mb-1.5 block">Precio por cada 100g</label>
                    <div class="flex items-center bg-white border border-orange-300 rounded-xl focus-within:ring-2 focus-within:ring-orange-500/20 focus-within:border-orange-500 transition shadow-sm">
                        <span class="pl-4 pr-1.5 text-slate-400 text-sm font-bold select-none">$</span>
                        <input type="text" id="precio_por_100g" name="precio_por_100g" pattern="[0-9]*\.?[0-9]*" autocomplete="off" data-teclado="numerico" inputmode="none" class="flex-1 min-w-0 bg-transparent p-3.5 sm:p-4 pl-0 text-slate-800 placeholder:text-slate-400 outline-none transition text-sm font-black" placeholder="50.00">
                    </div>
                    <p class="text-[9px] text-slate-400 mt-1 ml-1 font-semibold">Ej: $50 por 100g → 700g = $350</p>
                </div>

                {{-- Categoría HÍBRIDA --}}
                <div class="col-span-2 sm:col-span-1 relative">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Categoría</label>
                    <input type="text" id="categoria_nombre" name="categoria_nombre" list="lista-categorias" data-teclado="texto" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-3.5 sm:p-4 text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition text-sm font-semibold shadow-sm" placeholder="Escribe o selecciona..." autocomplete="off" required>
                    <input type="hidden" id="categoria_id" name="categoria_id">
                    <datalist id="lista-categorias">
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->nombre }}"></option>
                        @endforeach
                    </datalist>
                </div>

                {{-- Descripción del Platillo --}}
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 mb-1.5 block">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="2" data-teclado="texto" inputmode="none" class="w-full bg-white border border-slate-300 rounded-xl p-3.5 sm:p-4 text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition resize-none text-sm font-semibold shadow-sm" placeholder="Describe qué lleva este platillo..."></textarea>
                </div>

                {{-- Ingredientes del Platillo --}}
                <div class="col-span-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                        <div>
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1 block">Ingredientes del Platillo</label>
                            <p class="text-[9px] text-blue-600 font-bold mt-1 ml-1 tracking-wide uppercase">
                                <i class="fas fa-info-circle mr-1"></i> Selecciona los ingredientes y la cantidad.
                            </p>
                        </div>
                        <button type="button" onclick="agregarIngrediente('crear')" class="group inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 py-2.5 rounded-xl font-black transition text-[11px] uppercase tracking-widest shadow-md shadow-blue-500/20 mt-1 sm:mt-0 outline-none">
                            <i class="fas fa-plus text-xs transition-transform duration-300 group-hover:rotate-90"></i> AGREGAR INGREDIENTE
                        </button>
                    </div>

                    <div id="ingredientes-container-crear" class="space-y-3 mt-3"></div>
                </div>

            </div>

            {{-- Botones de Acción inferiores --}}
            <div class="mt-8 pt-4 border-t border-slate-300/60 bg-slate-200/50 -mx-5 sm:-mx-8 -mb-5 sm:-mb-8 p-5 sm:p-6 flex flex-col-reverse sm:flex-row gap-3 flex-shrink-0">
                <button type="button" onclick="closeModalCrear()" class="w-full sm:flex-1 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors py-3 sm:py-3.5 outline-none active:scale-95">CANCELAR</button>
                <button type="submit" class="w-full sm:flex-1 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-black py-3.5 rounded-xl transition shadow-md shadow-blue-500/20 text-xs uppercase tracking-widest outline-none" id="btn-guardar">GUARDAR CAMBIOS</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.contadorModificadoresCrear = 0;
    window.contadorVariantes = window.contadorVariantes || 0;

    window.bloquearScrollFondo = function () {
        document.body.style.overflow = 'hidden';
    };
    window.desbloquearScrollFondo = function () {
        document.body.style.overflow = '';
    };

    window.openModalCrear = window.abrirModalCrear = function() {
        const modal = document.getElementById('modal-crear-alimento');
        const panel = document.getElementById('modal-crear-panel');

        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        if (modal && panel) {
            modal.classList.remove('hidden');
            bloquearScrollFondo();
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-8');
            }, 10);
        }
    };

   window.closeModalCrear = function() {
    if (typeof _cerrarModal === 'function') {
        _cerrarModal('modal-crear-alimento', 'modal-crear-panel');
    } else {
        const modal = document.getElementById('modal-crear-alimento');
        const panel = document.getElementById('modal-crear-panel');
        if (modal && panel) {
            modal.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-8');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }
    desbloquearScrollFondo();
    
    const form = document.getElementById('formulario-crear-producto');
    if (form) form.reset();

    // Limpiar contenedores dinámicos
    const contVar = document.getElementById('contenedor-variantes-crear');
    if (contVar) contVar.innerHTML = '';
    const contMod = document.getElementById('contenedor-modificadores-crear');
    if (contMod) contMod.innerHTML = '';
};
    window.toggleModoVentaPeso = function(tipo) {
        const checkbox  = document.getElementById(tipo === 'crear' ? 'se_vende_por_peso' : 'edit-se_vende_por_peso');
        const grupoFijo  = document.getElementById(tipo === 'crear' ? 'grupo-precio-fijo-crear' : 'grupo-precio-fijo-editar');
        const grupoPeso  = document.getElementById(tipo === 'crear' ? 'grupo-precio-peso-crear' : 'grupo-precio-peso-editar');
        const inputFijo  = document.getElementById(tipo === 'crear' ? 'precio' : 'edit-precio');
        const inputPeso  = document.getElementById(tipo === 'crear' ? 'precio_por_100g' : 'edit-precio_por_100g');

        if (!checkbox) return;
        const esPorPeso = checkbox.checked;

        if (grupoFijo) grupoFijo.classList.toggle('hidden', esPorPeso);
        if (grupoPeso) grupoPeso.classList.toggle('hidden', !esPorPeso);

        if (inputFijo) {
            inputFijo.required = !esPorPeso;
            if (esPorPeso) inputFijo.value = 0;
        }
        if (inputPeso) inputPeso.required = esPorPeso;
    };

    window.enviarFormularioConImagen = function(url, formData, btn, textoOriginal, onSuccess) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(async (response) => {
            const json = await response.json().catch(() => ({}));

            if (!response.ok) {
                const mensaje = json.message || 'Ocurrió un error al guardar.';
                if (typeof mostrarNotificacion === 'function') {
                    mostrarNotificacion(mensaje, 'error');
                } else {
                    alert(mensaje);
                }
                btn.textContent = textoOriginal;
                btn.disabled = false;
                return;
            }

            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion(json.message || 'Guardado correctamente.', 'success');
            }

            if (typeof onSuccess === 'function') onSuccess();

            if (typeof cargarProductos === 'function')    cargarProductos();
            if (typeof cargarEstadisticas === 'function')  cargarEstadisticas();

            btn.textContent = textoOriginal;
            btn.disabled = false;
        })
        .catch((err) => {
            console.error(err);
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion('Error de conexión al guardar.', 'error');
            } else {
                alert('Error de conexión al guardar.');
            }
            btn.textContent = textoOriginal;
            btn.disabled = false;
        });
    };

    window.toggleVariantes = function(tipo) {
    const checkVariantes = document.getElementById(tipo === 'crear' ? 'tiene_variantes' : 'edit-tiene_variantes');
    const checkPeso = document.getElementById(tipo === 'crear' ? 'se_vende_por_peso' : 'edit-se_vende_por_peso');
    
    const seccionVariantes = document.getElementById(tipo === 'crear' ? 'seccion-variantes-crear' : 'seccion-variantes-editar');
    const grupoFijo = document.getElementById(tipo === 'crear' ? 'grupo-precio-fijo-crear' : 'grupo-precio-fijo-editar');
    const inputFijo = document.getElementById(tipo === 'crear' ? 'precio' : 'edit-precio');
    const contenedor = document.getElementById(tipo === 'crear' ? 'contenedor-variantes-crear' : 'contenedor-variantes-editar');
    const bloqueModsGlobal = document.getElementById(tipo === 'crear' ? 'bloque-toggle-modificadores-crear' : 'bloque-toggle-modificadores-editar');
    const checkMods = document.getElementById(tipo === 'crear' ? 'tiene_modificadores' : 'edit-tiene_modificadores');
    const seccionModsGlobal = document.getElementById(tipo === 'crear' ? 'seccion-modificadores-crear' : 'seccion-modificadores-editar');
    const contenedorModsGlobal = document.getElementById(tipo === 'crear' ? 'contenedor-modificadores-crear' : 'contenedor-modificadores-editar');

    if (!checkVariantes || !seccionVariantes) return;
    const tieneVariantes = checkVariantes.checked;

    if (tieneVariantes) {
        if (checkPeso && checkPeso.checked) {
            checkPeso.checked = false;
            window.toggleModoVentaPeso(tipo);
        }
        
        if (grupoFijo) grupoFijo.classList.add('hidden');
        if (bloqueModsGlobal) bloqueModsGlobal.classList.add('hidden');
        
        // Desactivar y limpiar modificadores globales para que no bloqueen con "required"
        if (checkMods) checkMods.checked = false;
        if (seccionModsGlobal) seccionModsGlobal.classList.add('hidden');
        if (contenedorModsGlobal) contenedorModsGlobal.innerHTML = '';

        if (inputFijo) {
            inputFijo.required = false;
            inputFijo.value = 0;
        }
        
        seccionVariantes.classList.remove('hidden');

        if (contenedor && contenedor.children.length === 0) {
            window.agregarFilaVariante(tipo);
        }
    } else {
        seccionVariantes.classList.add('hidden');
        if (contenedor) contenedor.innerHTML = ''; // Limpiar variantes si se apaga el switch
        if (bloqueModsGlobal) bloqueModsGlobal.classList.remove('hidden');
        if (grupoFijo) grupoFijo.classList.remove('hidden');
        if (inputFijo) inputFijo.required = true;
    }
};

    // ─── Variantes con Extras anidados ───────────────────────────────────────
    window.agregarFilaVariante = function(tipo) {
        const contenedor = document.getElementById(tipo === 'crear' ? 'contenedor-variantes-crear' : 'contenedor-variantes-editar');
        if (!contenedor) return;
        const varIndex = window.contadorVariantes++;
        
        const htmlFila = `
            <div class="bg-white p-3.5 border border-slate-200 rounded-2xl shadow-sm space-y-3 fila-variante" id="variante-${varIndex}">
                {{-- Encabezado del tamaño --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs shrink-0">
                        <i class="fas fa-layer-group text-[10px]"></i>
                    </span>
                    <input type="text" name="variantes[${varIndex}][nombre]" class="flex-1 min-w-0 bg-slate-50 border border-slate-200 rounded-xl p-2 text-sm font-bold text-slate-800 outline-none placeholder:text-slate-400 focus:border-blue-400" placeholder="Ej: 500ML, 1L, 6pz..." required>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2 shrink-0">
                        <span class="text-slate-400 font-black text-xs">$</span>
                        <input type="number" step="0.01" name="variantes[${varIndex}][precio]" class="w-20 bg-transparent p-2 text-sm font-black text-slate-800 outline-none placeholder:text-slate-400 text-right" placeholder="0.00">
                    </div>
                    <button type="button" onclick="document.getElementById('variante-${varIndex}').remove()" class="w-8 h-8 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors outline-none shrink-0" title="Eliminar tamaño">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>

                {{-- Extras exclusivos de este tamaño --}}
                <div class="pl-3 border-l-2 border-purple-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-purple-600 uppercase tracking-widest">
                            <i class="fas fa-puzzle-piece mr-1"></i> Complementos / Bases de este tamaño
                        </span>
                        <button type="button" onclick="agregarExtraAVariante(${varIndex})" class="text-[9px] font-black text-purple-700 bg-purple-100 hover:bg-purple-200 px-2 py-1 rounded-lg transition uppercase tracking-wider">
                            + Extra
                        </button>
                    </div>
                    <div id="contenedor-extras-var-${varIndex}" class="space-y-1.5"></div>
                </div>
            </div>
        `;
        
        contenedor.insertAdjacentHTML('beforeend', htmlFila);
    };

   window.agregarExtraAVariante = function(varIndex, datos = null) {
    const contenedor = document.getElementById(`contenedor-extras-var-${varIndex}`);
    if (!contenedor) return;
    const extraIndex = contenedor.children.length; // Índice secuencial (0, 1, 2...)

    const nombre = datos?.nombre ?? '';
    const precio = (datos && datos.precio !== undefined && datos.precio !== null) 
        ? parseFloat(datos.precio).toFixed(2) 
        : '';

    const html = `
        <div class="flex items-center gap-1.5 bg-purple-50/40 p-1.5 border border-purple-100 rounded-xl fila-extra-variante" id="extra-var-${varIndex}-${extraIndex}">
            <input type="text" name="variantes[${varIndex}][extras][${extraIndex}][nombre]" value="${nombre}" placeholder="Ej: AGUA, LECHE..." class="min-w-0 flex-1 bg-white border border-purple-200/60 rounded-lg p-1.5 text-xs font-bold text-slate-800 outline-none placeholder:text-slate-400" required>
            <div class="flex items-center bg-white border border-purple-200/60 rounded-lg px-1.5 shrink-0">
                <span class="text-purple-400 font-black text-xs">+$</span>
                <input type="number" step="0.01" name="variantes[${varIndex}][extras][${extraIndex}][precio]" value="${precio}" placeholder="0.00" class="w-14 bg-transparent p-1.5 text-xs font-black text-slate-800 outline-none placeholder:text-slate-400 text-right">
            </div>
            <button type="button" onclick="this.closest('.fila-extra-variante').remove()" class="w-6 h-6 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors outline-none shrink-0">
                <i class="fas fa-times text-[10px]"></i>
            </button>
        </div>
    `;

    contenedor.insertAdjacentHTML('beforeend', html);
};

    // ─── Modificadores / Extras para productos sin tamaños ───────────────────
window.toggleModificadores = function(tipo) {
    const check = document.getElementById(tipo === 'crear' ? 'tiene_modificadores' : 'edit-tiene_modificadores');
    const seccion = document.getElementById(tipo === 'crear' ? 'seccion-modificadores-crear' : 'seccion-modificadores-editar');
    const cont = document.getElementById(tipo === 'crear' ? 'contenedor-modificadores-crear' : 'contenedor-modificadores-editar');
    if (!check || !seccion) return;

    if (check.checked) {
        seccion.classList.remove('hidden');
        if (cont && cont.children.length === 0) {
            window.agregarFilaModificador(tipo);
        }
    } else {
        seccion.classList.add('hidden');
        if (cont) cont.innerHTML = ''; // <-- Limpia los inputs para que no queden campos required ocultos
    }
};

    window.agregarFilaModificador = function(tipo, datos = null) {
        const contId = (tipo === 'crear') ? 'contenedor-modificadores-crear' : 'contenedor-modificadores-editar';
        const contenedor = document.getElementById(contId);
        if (!contenedor) return;

        const index = (tipo === 'crear') ? window.contadorModificadoresCrear++ : (window.contadorModificadores || 0);
        if (tipo !== 'crear') window.contadorModificadores = (window.contadorModificadores || 0) + 1;

        const id = datos?.id ?? '';
        const nombre = datos?.nombre ?? '';
        const precio = datos?.precio ?? '';

        const html = `
            <div class="flex items-center gap-2 bg-white p-2 border border-slate-200 rounded-xl shadow-sm fila-modificador" id="modificador-${tipo}-${index}">
                <input type="hidden" name="modificadores[${index}][id]" value="${id}">
                <input type="text" name="modificadores[${index}][nombre]" value="${nombre}" placeholder="Nombre del extra (ej. Papas Francesa)" class="min-w-0 flex-1 bg-transparent p-2 text-sm font-semibold text-slate-800 outline-none placeholder:text-slate-400" required>
                <div class="w-px h-6 bg-slate-200 shrink-0"></div>
                <span class="pl-2 text-slate-400 font-bold shrink-0">+$</span>
                <input type="number" step="0.01" name="modificadores[${index}][precio]" value="${precio}" placeholder="0.00" class="w-16 bg-transparent p-2 text-sm font-black text-slate-800 outline-none placeholder:text-slate-400 shrink-0">
                <button type="button" onclick="document.getElementById('modificador-${tipo}-${index}').remove()" class="w-8 h-8 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors outline-none shrink-0">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    };

    window.guardarProducto = function(event) {
        event.preventDefault();
        const btn = document.getElementById('btn-guardar');
        if (!btn || btn.disabled) return;
        const original = btn.textContent;
        btn.textContent = 'GUARDANDO...';
        btn.disabled = true;

        const catNombre = document.getElementById('categoria_nombre').value;
        const catId = typeof obtenerCategoriaIdPorNombre === 'function' 
            ? obtenerCategoriaIdPorNombre(catNombre) 
            : document.getElementById('categoria_id').value;

        if (!catId) {
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion('Selecciona una categoría válida', 'error');
            } else {
                alert('Selecciona una categoría válida');
            }
            btn.textContent = original;
            btn.disabled = false;
            return;
        }
        document.getElementById('categoria_id').value = catId;

        const formEl = document.getElementById('formulario-crear-producto');
        const formData = new FormData(formEl);
        formData.set('categoria_id', catId);

        const tienePeso = document.getElementById('se_vende_por_peso').checked;
        const tieneVars = document.getElementById('tiene_variantes').checked;
        const tieneModsCheck = document.getElementById('tiene_modificadores');
        const tieneMods = (tieneModsCheck && tieneModsCheck.checked);

        formData.set('se_vende_por_peso', tienePeso ? '1' : '0');
        formData.set('tiene_variantes', tieneVars ? '1' : '0');
        formData.set('tiene_modificadores', tieneMods ? '1' : '0');

        // --- LIMPIEZA DE CAMPOS APAGADOS PARA EVITAR EL ERROR 422 ---
        if (!tieneMods) {
            for (const key of Array.from(formData.keys())) {
                if (key.startsWith('modificadores[')) {
                    formData.delete(key);
                }
            }
        }

        if (!tieneVars) {
            for (const key of Array.from(formData.keys())) {
                if (key.startsWith('variantes[')) {
                    formData.delete(key);
                }
            }
        }

        window.enviarFormularioConImagen(RUTA_STORE, formData, btn, original, window.closeModalCrear);
    };
</script>