@extends('layouts.admin')
@section('title', 'Productos | Ollintem Pro')
@section('header-title', 'Gestión de Productos')
@section('header-subtitle', 'Administra el menú y las recetas de los productos')

@push('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0; 
    }
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6 lg:p-8 xl:p-10 max-w-[1400px] mx-auto w-full space-y-5 sm:space-y-8 bg-[#F2F2F2] text-[var(--text-color)]">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-end gap-3 sm:gap-4 animate-fade-in-up" style="animation-delay: 0ms;">
        <div>
            <h1 class="text-xl sm:text-3xl font-black tracking-tight text-[var(--text-color)]">Menú de Productos</h1>
            <p class="text-xs sm:text-sm font-medium text-[var(--text-muted)] mt-1">Gestiona los productos del restaurante</p>
        </div>
        <button type="button" onclick="abrirModalCrear()" class="group w-full sm:w-auto flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 outline-none">
            <i class="fas fa-plus text-xs transition-transform duration-300 group-hover:rotate-90"></i>
            <span>Agregar Producto</span>
        </button>
    </div>

    {{-- Estadísticas con acentos en azul --}}
    <div class="grid grid-cols-1 min-[420px]:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-5 animate-fade-in-up" style="animation-delay: 150ms;">
        <div class="bg-white rounded-[18px] sm:rounded-[20px] p-4 sm:p-6 shadow-sm border border-blue-100 flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md hover:border-blue-300">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <span class="text-[10px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest">Total Productos</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 border border-blue-100">
                    <i class="fas fa-utensils text-xs sm:text-sm"></i>
                </div>
            </div>
            <span class="text-2xl sm:text-4xl font-black text-slate-800 tracking-tight" id="stat-total">0</span>
        </div>

        <div class="bg-white rounded-[18px] sm:rounded-[20px] p-4 sm:p-6 shadow-sm border border-emerald-100 flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md hover:border-emerald-300">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <span class="text-[10px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest">Disponibles</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0 border border-emerald-100">
                    <i class="fas fa-check text-xs sm:text-sm"></i>
                </div>
            </div>
            <span class="text-2xl sm:text-4xl font-black text-slate-800 tracking-tight" id="stat-disponibles">0</span>
        </div>

        <div class="bg-white rounded-[18px] sm:rounded-[20px] p-4 sm:p-6 shadow-sm border border-purple-100 flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md hover:border-purple-300 col-span-1 min-[420px]:col-span-2 md:col-span-1">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <span class="text-[10px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest">Categorías</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0 border border-purple-100">
                    <i class="fas fa-tags text-xs sm:text-sm"></i>
                </div>
            </div>
            <span class="text-2xl sm:text-4xl font-black text-slate-800 tracking-tight" id="stat-categorias">0</span>
        </div>
    </div>

    {{-- BUSCADOR --}}
    <div class="relative mb-4 animate-fade-in-up" style="animation-delay: 300ms;">
        <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
        <input type="text" id="buscadorProductosAdmin"
               placeholder="Buscar producto por nombre..."
               autocomplete="off"
               class="w-full pl-11 pr-11 py-3.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
        <button type="button" id="limpiarBusquedaAdmin"
                class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full text-slate-400 hover:text-slate-600 transition-colors"
                title="Limpiar búsqueda">
            <i class="fas fa-xmark text-sm"></i>
        </button>
    </div>

    {{-- Contenedor de productos por categoría --}}
    <div class="bg-white rounded-[1.5rem] sm:rounded-[2rem] p-3 sm:p-6 shadow-sm border border-slate-200 min-h-[420px] animate-fade-in-up" style="animation-delay: 450ms;">
        <div id="categorias-container" class="space-y-6"
             data-permiso-editar="{{ auth()->user()->tienePermiso('Productos', 'editar') ? 'true' : 'false' }}"
             data-permiso-eliminar="{{ auth()->user()->tienePermiso('Productos', 'eliminar') ? 'true' : 'false' }}"
             data-permiso-gestionar="{{ auth()->user()->tienePermiso('Productos', 'mostrar') ? 'true' : 'false' }}">
        </div>
    </div>

</div>

@include('admin.productos.modal-crear')
@include('admin.productos.modal-editar')
@include('admin.productos.modal-eliminar')

<script>
    const RUTA_PRODUCTOS     = "{{ route('admin.productos.api.productos') }}";
    const RUTA_ESTADISTICAS = "{{ route('admin.productos.api.estadisticas') }}";
    const RUTA_STORE         = "{{ route('admin.productos.api.store') }}";
    const RUTA_API_BASE     = "{{ url('/productos/api/') }}/";
 
    let estadoGlobal = { productos: {}, productosMap: {}, editandoId: null };
    let tienePermisoEditar = false, tienePermisoEliminar = false, tienePermisoGestionar = false;
    let categoriasDisponibles = [], insumosDisponibles = [];
 
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('categorias-container');
        if (container) {
            tienePermisoEditar    = container.dataset.permisoEditar    === 'true';
            tienePermisoEliminar  = container.dataset.permisoEliminar  === 'true';
            tienePermisoGestionar = container.dataset.permisoGestionar === 'true';
        }
        categoriasDisponibles = {!! Illuminate\Support\Js::from($categorias->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre])) !!};
        insumosDisponibles    = @json($insumos);
 
        cargarProductos();
        cargarEstadisticas();
        setInterval(cargarEstadisticas, 10000);
    });
 
    function cargarProductos() {
        const scrollY = window.scrollY;

        fetch(RUTA_PRODUCTOS)
            .then(r => r.json())
            .then(data => {
                estadoGlobal.productos    = data;
                estadoGlobal.productosMap = {};
                Object.keys(data).forEach(cat => {
                    data[cat].forEach(p => { estadoGlobal.productosMap[p.id] = p; });
                });
                renderizarProductos();
                requestAnimationFrame(() => window.scrollTo(0, scrollY));
            })
            .catch(e => console.error('Error cargando productos:', e));
    }
 
    function cargarEstadisticas() {
        fetch(RUTA_ESTADISTICAS)
            .then(r => r.json())
            .then(data => {
                const t = document.getElementById('stat-total');
                const d = document.getElementById('stat-disponibles');
                const c = document.getElementById('stat-categorias');
                if (t) t.textContent = data.total;
                if (d) d.textContent = data.disponibles;
                if (c) c.textContent = data.categorias;
            })
            .catch(e => console.error('Error cargando estadísticas:', e));
    }
 
    let filtroProductos = '';

    function normalizarTexto(texto) {
        return (texto || '').toString().toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function renderizarProductos() {
        const container = document.getElementById('categorias-container');
        container.innerHTML = '';
        if (Object.keys(estadoGlobal.productos).length === 0) {
            container.innerHTML = '<p class="text-center text-slate-400 py-12 font-bold text-sm px-4">No hay productos registrados aún.</p>';
            return;
        }

        const buscado = normalizarTexto(filtroProductos).trim();
        let encontrados = 0;

        Object.keys(estadoGlobal.productos).forEach(catNombre => {
            const coincideCategoria = buscado !== '' && normalizarTexto(catNombre).includes(buscado);

            const productos = buscado === '' || coincideCategoria
                ? estadoGlobal.productos[catNombre]
                : estadoGlobal.productos[catNombre].filter(p => normalizarTexto(p.nombre).includes(buscado));

            if (productos.length === 0) return;
            encontrados += productos.length;
            const gridId = 'grid-' + catNombre.toLowerCase().replace(/[^a-z0-9]/g, '-');    
            const seccion   = document.createElement('div');
            seccion.className = 'mb-6 sm:mb-8 bg-slate-50/50 rounded-2xl p-3 sm:p-5 border border-slate-200/80';
            seccion.innerHTML = `
                <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-5 border-b border-slate-200 pb-3 sm:pb-4 px-2">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="${obtenerIconoCategoria(catNombre)} text-xs sm:text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm sm:text-base font-black text-slate-800 tracking-tight uppercase truncate">${catNombre}</h2>
                        <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">${productos.length} Producto${productos.length !== 1 ? 's' : ''}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 min-[480px]:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4" id="${gridId}"></div>
            `;
            container.appendChild(seccion);
            const grid = seccion.querySelector(`#${gridId}`);
            productos.forEach(p => grid.appendChild(crearCardProducto(p)));
        });

        if (encontrados === 0) {
            container.innerHTML = `
                <div class="text-center py-12 px-4">
                    <i class="fas fa-magnifying-glass text-4xl text-slate-300 mb-3"></i>
                    <p class="font-bold text-slate-700">Sin resultados para "${filtroProductos}"</p>
                    <p class="text-sm text-slate-400 mt-1">Prueba con otras letras.</p>
                </div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const buscador = document.getElementById('buscadorProductosAdmin');
        const btnLimpiar = document.getElementById('limpiarBusquedaAdmin');
        if (!buscador) return;

        const buscar = () => {
            filtroProductos = buscador.value || '';
            if (btnLimpiar) btnLimpiar.classList.toggle('hidden', filtroProductos === '');
            renderizarProductos();
        };

        buscador.addEventListener('input', buscar);

        let vigilante = null;
        buscador.addEventListener('focus', () => {
            let previo = buscador.value;
            vigilante = setInterval(() => {
                if (buscador.value !== previo) { previo = buscador.value; buscar(); }
            }, 250);
        });
        buscador.addEventListener('blur', () => clearInterval(vigilante));

        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => {
                buscador.value = '';
                buscar();
                buscador.focus();
            });
        }
    });
 
    function crearCardProducto(producto) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm hover:border-blue-300 hover:shadow-md transition-all group flex flex-col relative';
 
        const mods = producto.modificadores?.length
            ? `<p class="text-[10px] text-slate-400 mt-1.5 truncate font-semibold"><i class="fas fa-list-ul mr-1 opacity-70"></i> ${producto.modificadores.map(m => m.nombre).join(', ')}</p>`
            : '';

        const esPorPeso = !!producto.se_vende_por_peso;
        const badgePorPeso = esPorPeso
            ? `<span class="text-[8px] font-black text-orange-600 bg-orange-50 border border-orange-200 px-1.5 py-0.5 rounded-md uppercase tracking-widest inline-flex items-center gap-1 mt-1.5"><i class="fas fa-weight-hanging"></i> Por peso</span>`
            : '';

        const precioMostrado = esPorPeso
            ? `$${parseFloat(producto.precio_por_100g ?? 0).toFixed(2)} <span class="text-[10px] sm:text-[11px] font-bold text-slate-400">/100g</span>`
            : `$${parseFloat(producto.precio).toFixed(2)}`;
 
        const botonesHTML = [
            tienePermisoEditar   ? `<button class="w-9 h-9 sm:w-8 sm:h-8 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 flex items-center justify-center transition active:scale-95 shadow-sm" onclick="editarProducto(${producto.id})" title="Editar"><i class="fas fa-pen text-[11px]"></i></button>` : '',
            tienePermisoEliminar ? `<button class="w-9 h-9 sm:w-8 sm:h-8 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-600 hover:text-white flex items-center justify-center transition active:scale-95 shadow-sm" onclick="eliminarProducto(${producto.id})" title="Eliminar"><i class="fas fa-trash text-[11px]"></i></button>` : '',
        ].join('');
 
        const toggleHTML = tienePermisoEditar
            ? `<button class="w-9 h-5 rounded-full transition-colors duration-200 relative shrink-0 ${producto.esta_disponible ? 'bg-emerald-500' : 'bg-slate-300'}" onclick="toggleDisponibilidad(this, ${producto.id})" title="Cambiar disponibilidad"><div class="w-4 h-4 bg-white rounded-full shadow-sm absolute top-0.5 transition-transform duration-200 ${producto.esta_disponible ? 'translate-x-[18px]' : 'translate-x-0.5'}"></div></button>`
            : `<div class="w-9 h-5 rounded-full relative shrink-0 ${producto.esta_disponible ? 'bg-emerald-500' : 'bg-slate-300'} opacity-50 cursor-not-allowed" title="Sin permisos"><div class="w-4 h-4 bg-white rounded-full shadow-sm absolute top-0.5 ${producto.esta_disponible ? 'translate-x-[18px]' : 'translate-x-0.5'}"></div></div>`;
 
        card.innerHTML = `
            <div class="flex justify-between items-start mb-3 sm:mb-4 gap-2">
                <div class="flex items-start gap-2.5 sm:gap-3 overflow-hidden min-w-0">
                    <div class="overflow-hidden min-w-0">
                        <h3 class="text-sm sm:text-[15px] font-bold text-slate-800 tracking-tight truncate">${producto.nombre}</h3>
                        <p class="text-[11px] sm:text-[12px] text-slate-500 mt-1 line-clamp-2">${producto.descripcion ?? 'Sin descripción'}</p>
                        ${mods}
                        ${badgePorPeso}
                    </div>
                </div>
                <div class="flex items-center gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity shrink-0">${botonesHTML}</div>
            </div>
            <div class="flex flex-wrap justify-between items-center gap-2 mt-auto pt-3 sm:pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    ${toggleHTML}
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest texto-estado">${producto.esta_disponible ? 'Disponible' : 'Agotado'}</span>
                </div>
                <span class="text-sm sm:text-[16px] font-black text-slate-800 tracking-tight">${precioMostrado}</span>
            </div>
        `;
        return card;
    }
 
    function obtenerIconoCategoria(nombre) {
        const n = nombre.toLowerCase();
        if (n.includes('pizza'))                             return 'fas fa-pizza-slice';
        if (n.includes('pasta'))                             return 'fas fa-utensils';
        if (n.includes('bebida') || n.includes('cocteleria'))  return 'fas fa-glass-water';
        if (n.includes('postre'))                            return 'fas fa-cake-slice';
        if (n.includes('ensalada') || n.includes('verdura'))   return 'fas fa-leaf';
        if (n.includes('carne') || n.includes('parrillada'))   return 'fas fa-drumstick-bite';
        if (n.includes('marisco') || n.includes('pescado'))    return 'fas fa-fish';
        if (n.includes('sopa'))                              return 'fas fa-bowl-food';
        if (n.includes('abarrote'))                          return 'fas fa-box-open';
        return 'fas fa-concierge-bell';
    }
 
    function eliminarProducto(id) {
        if (!tienePermisoEliminar) { mostrarNotificacion('Sin permisos para eliminar', 'error'); return; }
        const producto = estadoGlobal.productosMap[id];
        if (producto) abrirModalEliminar(id, producto.nombre);
    }
 
    function toggleDisponibilidad(btn, id) {
        if (!tienePermisoEditar) { mostrarNotificacion('Sin autorización', 'error'); return; }
        const circulo    = btn.querySelector('div');
        const estaActivo = btn.classList.contains('bg-emerald-500');
        const textoEstado = btn.nextElementSibling;
        _setToggleEstado(btn, circulo, textoEstado, !estaActivo);
        fetch(RUTA_API_BASE + id + '/toggle-disponibilidad', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
        })
        .then(() => cargarEstadisticas())
        .catch(() => {
            _setToggleEstado(btn, circulo, textoEstado, estaActivo);
            mostrarNotificacion('Error al cambiar disponibilidad', 'error');
        });
    }
 
    function limpiarIngredientesContainer(tipo) {
        document.getElementById(`ingredientes-container-${tipo}`).innerHTML = '';
    }
 
    function agregarIngrediente(tipo = 'crear', ingrediente = {}) {
        const container = document.getElementById(`ingredientes-container-${tipo}`);
        if (container) container.appendChild(crearFilaIngrediente(ingrediente));
    }
 
    function eliminarIngredienteRow(button) { button.closest('.ingrediente-row').remove(); }
 
    function crearFilaIngrediente(ingrediente = {}) {
        const row = document.createElement('div');
        row.className = 'flex flex-col md:grid md:grid-cols-12 gap-3 items-stretch md:items-end p-4 md:p-0 bg-slate-50 md:bg-transparent rounded-2xl border border-slate-200 md:border-0 ingrediente-row relative mb-3 md:mb-0';
        
        const insumoValue   = ingrediente.insumo_id    ?? ingrediente.id ?? '';
        const cantidadValue = ingrediente.cantidad     ?? ingrediente.pivot?.cantidad_usada ?? '';
        const unidadValue   = ingrediente.unidad_medida ?? '';

        const options = insumosDisponibles.map(ins => {
            const sel = ins.id == insumoValue ? 'selected' : '';
            const stockSeguro = (ins.stock_actual !== undefined && ins.stock_actual !== null) ? ins.stock_actual : 0;
            return `<option value="${ins.id}" data-unidad="${ins.unidad_medida ?? ''}" data-stock="${stockSeguro}" ${sel}>${ins.nombre}</option>`;
        }).join('');

        row.innerHTML = `
            <div class="md:col-span-6">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ingrediente</label>
                <select name="insumos[]" class="w-full bg-white border border-slate-200 rounded-xl p-3.5 mt-1 text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition" onchange="sincronizarInsumo(this)" required>
                    <option value="">Seleccionar...</option>${options}
                </select>
            </div>
            <div class="grid grid-cols-12 gap-2 items-end md:col-span-6">
                <div class="col-span-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cantidad</label>
                    <input type="number" name="cantidades[]" step="0.001" min="0.001" value="${cantidadValue}" class="w-full bg-white border border-slate-200 rounded-xl p-3.5 mt-1 text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition" placeholder="0.000" required>
                </div>
                <div class="col-span-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center block">Uni</label>
                    <input type="text" value="${unidadValue}" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3.5 mt-1 text-slate-600 text-center font-bold" disabled>
                </div>
                <div class="col-span-3 flex flex-col items-center justify-end h-full">
                    <button type="button" class="w-full md:w-10 h-11 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition flex items-center justify-center active:scale-95 shadow-sm" onclick="eliminarIngredienteRow(this)">
                        <i class="fas fa-trash-alt text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="text-[10px] text-slate-500 font-bold mt-1 px-1 item-stock-label"></div>
        `;

        const select = row.querySelector('select[name="insumos[]"]');
        if (insumoValue) { select.value = insumoValue; sincronizarInsumo(select); }
        return row;
    }
 
    function sincronizarInsumo(select) {
        const opt = select.options[select.selectedIndex];
        const row = select.closest('.ingrediente-row');
        if (!row) return;

        const unidadInput = row.querySelector('input[disabled]');
        const stockLabel  = row.querySelector('.item-stock-label');

        if (opt && opt.value) {
            if (unidadInput) unidadInput.value = opt.dataset.unidad ?? '';
            
            const stockValor = opt.dataset.stock;
            if (stockValor !== undefined && stockValor !== 'undefined' && stockValor !== '') {
                stockLabel.innerHTML = `<span class="bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-md inline-block font-bold border border-blue-100">Stock: ${stockValor}</span>`;
            } else {
                stockLabel.innerHTML = '';
            }
        } else {
            if (unidadInput) unidadInput.value = '';
            if (stockLabel) stockLabel.innerHTML = '';
        }
    }
 
    function llenarIngredientesEdicion(producto) {
        limpiarIngredientesContainer('editar');
        if (producto.insumos?.length) {
            producto.insumos.forEach(ins => agregarIngrediente('editar', {
                insumo_id:    ins.id,
                cantidad:      ins.pivot?.cantidad_usada ?? '',
                unidad_medida: ins.unidad_medida        ?? '',
                stock_actual:  ins.stock_actual          ?? ''
            }));
        } else {
            agregarIngrediente('editar');
        }
    }
 
    function obtenerCategoriaIdPorNombre(nombre) {
        if (!nombre) return null;
        return categoriasDisponibles.find(c => c.nombre.toLowerCase() === nombre.toLowerCase())?.id ?? null;
    }
 
    function ejecutarPeticion(url, data, boton, textoBoton, cerrarModalFn) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept':       'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => { if (!r.ok) return r.json().then(e => { throw new Error(e.message || 'Error'); }); return r.json(); })
        .then(res => { cerrarModalFn(); cargarProductos(); cargarEstadisticas(); mostrarNotificacion(res.message || 'Operación exitosa', 'success'); })
        .catch(e  => mostrarNotificacion(e.message || 'Error en el servidor', 'error'))
        .finally(() => { boton.textContent = textoBoton; boton.disabled = false; });
    }
 
    function _abrirModal(modalId, panelId) {
        const modal = document.getElementById(modalId);
        const panel = document.getElementById(panelId);
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.add('opacity-100'); panel.classList.add('opacity-100', 'translate-y-0'); }, 10);
    }
 
    function _cerrarModal(modalId, panelId) {
        const modal = document.getElementById(modalId);
        const panel = document.getElementById(panelId);
        modal.classList.remove('opacity-100');
        panel.classList.remove('opacity-100', 'translate-y-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
 
    function _setToggleEstado(btn, circulo, texto, activo) {
        btn.classList.toggle('bg-emerald-500', activo);
        btn.classList.toggle('bg-slate-300', !activo);
        circulo.classList.toggle('translate-x-[18px]', activo);
        circulo.classList.toggle('translate-x-0.5', !activo);
        if (texto) texto.textContent = activo ? 'DISPONIBLE' : 'AGOTADO';
    }
 
    function _serializarFormulario(formId) {
        const data = {};
        new FormData(document.getElementById(formId)).forEach((value, key) => {
            if (key.endsWith('[]')) {
                const name = key.slice(0, -2);
                if (!data[name]) data[name] = [];
                data[name].push(value);
            } else { data[key] = value; }
        });
        return data;
    }

    // ===== NOTIFICACIÓN TOAST =====
    let contadorToast = 0;

    function mostrarNotificacion(mensaje, tipo) {
        contadorToast++;
        const id = `toast-ajax-${contadorToast}`;
        const esExito = tipo === 'success';

        let contenedor = document.getElementById('toast-ajax-container');
        if (!contenedor) {
            contenedor = document.createElement('div');
            contenedor.id = 'toast-ajax-container';
            contenedor.className = 'fixed top-4 left-4 right-4 sm:left-auto sm:top-6 sm:right-6 z-[200] flex flex-col gap-3 sm:gap-4 items-stretch sm:items-end';
            document.body.appendChild(contenedor);
        }

        const colorBarra   = esExito ? 'from-emerald-400 to-cyan-400' : 'from-rose-400 to-red-500';
        const colorIcono   = esExito ? 'border-emerald-200 bg-emerald-50 text-emerald-600 shadow-sm' : 'border-rose-200 bg-rose-50 text-rose-600 shadow-sm';
        const colorTitulo  = esExito ? 'text-emerald-600' : 'text-rose-600';
        const icono        = esExito ? 'fa-check' : 'fa-exclamation';
        const titulo       = esExito ? 'Operación Exitosa' : 'Atención';

        const toast = document.createElement('div');
        toast.id = id;
        toast.className = 'relative overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-xl p-4 flex gap-3.5 items-start w-full sm:w-[320px] transition-all duration-300 transform translate-x-0 opacity-100';
        toast.innerHTML = `
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r ${colorBarra}"></div>
            <div class="flex items-center justify-center w-8 h-8 rounded-full border ${colorIcono} flex-shrink-0 mt-1">
                <i class="fas ${icono} text-[11px]"></i>
            </div>
            <div class="flex-1 pr-3 min-w-0">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] ${colorTitulo} mb-1">${titulo}</p>
                <p class="text-[13px] font-bold text-slate-800 leading-tight break-words">${mensaje}</p>
            </div>
            <button onclick="cerrarToastAjax('${id}')" class="absolute top-3.5 right-3.5 text-slate-400 hover:text-slate-600 transition-colors outline-none">
                <i class="fas fa-times text-[10px]"></i>
            </button>
            <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r ${colorBarra} animate-shrink"></div>
        `;

        contenedor.appendChild(toast);
        setTimeout(() => cerrarToastAjax(id), 3000);
    }

    function cerrarToastAjax(id) {
        const toast = document.getElementById(id);
        if (toast) {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }
    }
</script>
@endsection