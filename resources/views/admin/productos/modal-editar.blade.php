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
        {{-- Panel principal en fondo #F2F2F2 --}}
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

            <form id="formulario-editar-alimento" onsubmit="actualizarProducto(event)" class="p-6 sm:p-8 pt-4 bg-[#F2F2F2]">
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
    // ─── Bloqueo/desbloqueo de scroll del fondo ─────────────────────────
    window.bloquearScrollFondo = function () {
        document.body.style.overflow = 'hidden';
    };
    window.desbloquearScrollFondo = function () {
        document.body.style.overflow = '';
    };

    // ─── Cerrar modal editar ─────────────────────────────────────────────────
    function closeModalEditar() {
        _cerrarModal('modal-editar-alimento', 'modal-editar-panel');
        desbloquearScrollFondo();
        const form = document.getElementById('formulario-editar-alimento');
        if (form) form.reset();
    }

    // ─── Envío multipart genérico ────────────────────────────────────────────
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

    // ─── Abrir modal con datos del producto ──────────────────────────────────
    function editarProducto(id) {
        if (!tienePermisoEditar) { mostrarNotificacion('Sin permisos para editar', 'error'); return; }
        const producto = estadoGlobal.productosMap[id];
        if (!producto) return;
        estadoGlobal.editandoId = id;
        document.getElementById('edit-nombre').value           = producto.nombre              ?? '';
        document.getElementById('edit-precio').value           = producto.precio              ?? '';
        document.getElementById('edit-precio_por_100g').value  = producto.precio_por_100g      ?? '';
        document.getElementById('edit-se_vende_por_peso').checked = !!producto.se_vende_por_peso;
        document.getElementById('edit-descripcion').value      = producto.descripcion         ?? '';
        document.getElementById('edit-categoria_nombre').value = producto.categoria?.nombre   ?? '';
        document.getElementById('edit-categoria_id').value     = producto.categoria?.id       ?? '';

        toggleModoVentaPeso('editar');
        llenarIngredientesEdicion(producto);
        bloquearScrollFondo();

        const modalEditar = document.getElementById('modal-editar-alimento');
        if (modalEditar && modalEditar.parentElement !== document.body) {
            document.body.appendChild(modalEditar);
        }

        _abrirModal('modal-editar-alimento', 'modal-editar-panel');
    }

    // ─── Actualizar producto ─────────────────────────────────────────────────
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
        formData.set('_method', 'PUT');

        enviarFormularioConImagen(RUTA_API_BASE + estadoGlobal.editandoId, formData, btn, original, closeModalEditar);
    }
</script>