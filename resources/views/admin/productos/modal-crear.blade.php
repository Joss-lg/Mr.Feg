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
<div id="modal-crear-alimento" class="fixed inset-y-0 right-0 left-[74px] sm:left-0 sm:inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-3 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm -ml-[74px] sm:ml-0" onclick="closeModalCrear()"></div>

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

        <form id="formulario-crear-producto" onsubmit="guardarProducto(event)" class="overflow-y-auto overscroll-contain flex-1 p-5 sm:p-8 pt-4 sm:pt-6 bg-[#F2F2F2] flex flex-col">
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
            
            modal.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-8');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        desbloquearScrollFondo();
        const form = document.getElementById('formulario-crear-producto');
        if (form) form.reset();
    };

    function toggleModoVentaPeso(tipo) {
        const checkbox  = document.getElementById(tipo === 'crear' ? 'se_vende_por_peso' : 'edit-se_vende_por_peso');
        const grupoFijo  = document.getElementById(tipo === 'crear' ? 'grupo-precio-fijo-crear' : 'grupo-precio-fijo-editar');
        const grupoPeso  = document.getElementById(tipo === 'crear' ? 'grupo-precio-peso-crear' : 'grupo-precio-peso-editar');
        const inputFijo  = document.getElementById(tipo === 'crear' ? 'precio' : 'edit-precio');
        const inputPeso  = document.getElementById(tipo === 'crear' ? 'precio_por_100g' : 'edit-precio_por_100g');

        const esPorPeso = checkbox.checked;

        grupoFijo.classList.toggle('hidden', esPorPeso);
        grupoPeso.classList.toggle('hidden', !esPorPeso);

        inputFijo.required = !esPorPeso;
        inputPeso.required = esPorPeso;

        if (esPorPeso) { inputFijo.value = 0; }
    }

    function enviarFormularioConImagen(url, formData, btn, textoOriginal, onSuccess) {
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
    }

    function guardarProducto(event) {
        event.preventDefault();
        const btn = document.getElementById('btn-guardar');
        if (!btn || btn.disabled) return;
        const original = btn.textContent;
        btn.textContent = 'GUARDANDO...';
        btn.disabled    = true;

        const catNombre = document.getElementById('categoria_nombre').value;
        const catId     = obtenerCategoriaIdPorNombre(catNombre);
        if (!catId) {
            mostrarNotificacion('Selecciona una categoría válida', 'error');
            btn.textContent = original; btn.disabled = false; return;
        }
        document.getElementById('categoria_id').value = catId;

        const formEl   = document.getElementById('formulario-crear-producto');
        const formData = new FormData(formEl);
        formData.set('categoria_id', catId);
        formData.set('se_vende_por_peso', document.getElementById('se_vende_por_peso').checked ? '1' : '0');

        enviarFormularioConImagen(RUTA_STORE, formData, btn, original, closeModalCrear);
    }
</script>