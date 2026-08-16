@extends('layouts.admin')

@section('title', 'Promociones | Ollintem Pro')
@section('header-title', 'Promociones')
@section('header-subtitle', 'Marketing, ofertas y descuentos')

@push('styles')

@section('content')
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-[1800px] mx-auto space-y-6 sm:space-y-8 relative z-10 min-h-screen bg-slate-50 font-sans transition-colors duration-300">
    
    {{-- ENCABEZADO PREMIUM --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-2 sm:space-y-3 max-w-2xl w-full">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 sm:px-4 py-1.5 sm:py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 shadow-sm">
                <i class="fas fa-tags"></i> Marketing y Ofertas
            </div>
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight drop-shadow-sm">Promociones y Descuentos</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 tracking-wide">Controla las ofertas activas, paquetes especiales y descuentos para tus clientes.</p>
        </div>

        <div class="w-full xl:w-auto mt-2 xl:mt-0">
            @if(auth()->user()->tienePermiso('promociones.crear'))
                <button type="button" onclick="window.openModal('modalCrear', 'createContainer')" class="group w-full sm:w-auto relative flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-7 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 active:scale-95 outline-none border-0">
                    <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i>
                    Nueva Promo
                </button>
            @endif
        </div>
    </div>

    {{-- BARRA DE BÚSQUEDA Y ESTADÍSTICAS --}}
    <div class="bg-white rounded-[2rem] p-5 sm:p-6 flex flex-col lg:flex-row justify-between items-center gap-4 sm:gap-6 border border-slate-200 shadow-sm animate-fade-in-up" style="animation-delay: 150ms;">
        <div class="relative w-full lg:max-w-md group">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 transition-colors">
                <i class="fas fa-search text-sm"></i>
            </div>
            <input type="text" id="buscadorPromociones" data-teclado="texto" placeholder="Buscar promoción..." 
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm">
        </div>

        <div class="flex items-center justify-between sm:justify-end w-full lg:w-auto gap-4 sm:gap-8 sm:px-4">
            <div class="text-center sm:text-right flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Total Registradas</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-800 leading-none mt-1">{{ $promociones->count() }}</p>
            </div>
            <div class="w-px h-8 sm:h-12 bg-slate-200"></div>
            <div class="text-center sm:text-left flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Activas ahora</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-500 leading-none mt-1">{{ $promociones->where('esta_activa', true)->count() }}</p>
            </div>
        </div>
    </div>

    {{-- TABLERO DE PROMOCIONES --}}
    @if($promociones->isEmpty())
        <div class="bg-white rounded-[2rem] p-8 sm:p-12 md:p-20 text-center border border-slate-200 shadow-sm flex flex-col items-center justify-center animate-fade-in-up" style="animation-delay: 300ms;">
            <div class="relative mb-6">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-blue-50 rounded-[2rem] flex items-center justify-center border border-blue-100 shadow-sm relative z-10 group">
                    <i class="fas fa-ticket-alt text-4xl sm:text-5xl text-blue-500 group-hover:scale-110 transition-transform duration-300"></i>
                </div>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Sin promociones activas</h2>
            <p class="mt-3 text-xs sm:text-sm text-slate-500 font-medium max-w-md">No tienes ninguna oferta configurada en el sistema. Crea una nueva promo para atraer más clientes.</p>
            
            @if(auth()->user()->tienePermiso('promociones.crear'))
                <button type="button" onclick="window.openModal('modalCrear', 'createContainer')" class="mt-8 sm:mt-10 group w-full sm:w-auto rounded-2xl bg-blue-600 px-8 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 outline-none active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i> Comenzar ahora
                </button>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 300ms;">
            @foreach($promociones as $promo)
                <article class="fila-promocion bg-white border border-slate-200 rounded-[1.5rem] sm:rounded-[2rem] p-5 sm:p-6 relative group transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg flex flex-col overflow-hidden shadow-sm">
                    
                    <div class="absolute inset-x-0 top-0 h-1 {{ $promo->esta_activa ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : 'bg-slate-200' }}"></div>

                    {{-- HEADER DE LA TARJETA --}}
                    <div class="flex justify-between items-start mb-5 sm:mb-6 pt-2">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $promo->esta_activa ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-50 text-slate-400 border border-slate-200' }} transition-all duration-300 shadow-sm">
                            <i class="fas fa-ticket-alt text-xl"></i>
                        </div>
                        
                        @if(auth()->user()->tienePermiso('promociones.editar'))
                            {{-- SWITCH TIPO iOS PREMIUM --}}
                            <label class="relative inline-flex items-center cursor-pointer p-1 -m-1">
                                <input id="togglePromo{{ $promo->id }}" type="checkbox" class="sr-only peer" {{ $promo->esta_activa ? 'checked' : '' }} onchange="window.togglePromo({{ $promo->id }})">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[6px] after:left-[6px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 peer-checked:after:border-transparent shadow-inner"></div>
                            </label>
                        @endif
                    </div>

                    {{-- CONTENIDO --}}
                    <div class="mb-5 sm:mb-6 flex-1">
                        <h3 class="nombre-promocion text-lg sm:text-xl font-black text-slate-800 tracking-tight leading-tight mb-1">{{ $promo->nombre }}</h3>
                        
                        @if($promo->descripcion)
                            <p class="text-[11px] sm:text-xs font-semibold text-slate-500 line-clamp-2 mt-1 tracking-wide leading-relaxed">{{ $promo->descripcion }}</p>
                        @endif
                        
                        <div class="mt-4 flex items-end gap-1.5 sm:gap-2">
                            <span class="bg-clip-text text-transparent bg-gradient-to-r {{ $promo->esta_activa ? 'from-blue-600 to-indigo-600' : 'from-slate-400 to-slate-500' }} font-black text-3xl sm:text-4xl tracking-tighter">
                                @if($promo->tipo_promocion === 'dos_por_uno')
                                    2x1
                                @elseif($promo->tipo_promocion === 'combo')
                                    Combo
                                @elseif($promo->tipo_promocion === 'porcentaje')
                                    {{ (int)$promo->valor_descuento }}%
                                @else
                                    ${{ number_format($promo->valor_descuento, 2) }}
                                @endif
                            </span>
                            <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 pb-0.5">Beneficio</span>
                        </div>
                    </div>

                    {{-- DÍAS DE LA SEMANA --}}
                    <div class="mb-5 sm:mb-6">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 sm:mb-3 ml-1">Días aplicables</p>
                        <div class="flex flex-wrap justify-between gap-1.5 sm:gap-1">
                            @php
                                $dias = $promo->dias_semana;
                                if (is_string($dias)) {
                                    $decode = json_decode($dias, true);
                                    if (is_string($decode)) {
                                        $decode = json_decode($decode, true);
                                    }
                                    $dias = $decode;
                                }
                                $dias = is_array($dias) ? $dias : [];
                            @endphp
                            @foreach(['L','M','X','J','V','S','D'] as $i => $d)
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-black transition-colors shrink-0 {{ in_array($i+1, $dias) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                                    {{ $d }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- FOOTER DE TARJETA --}}
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100 gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest shrink-0 {{ $promo->esta_activa ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-500 border border-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $promo->esta_activa ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                            {{ $promo->esta_activa ? 'Activa' : 'Inactiva' }}
                        </span>
                        
                        <div class="flex items-center gap-2 shrink-0">
                            @if(auth()->user()->tienePermiso('promociones.editar'))
                                <button type="button" onclick="window.editPromo({{ $promo->id }})" class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 transition-all hover:bg-blue-50 hover:border-blue-100 hover:text-blue-600 outline-none active:scale-95 shadow-sm">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                            @endif

                            @if(auth()->user()->tienePermiso('promociones.eliminar'))
                                <button type="button" onclick="window.openDeleteModal({{ $promo->id }}, '{{ addslashes($promo->nombre) }}')" class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 transition-all hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 outline-none active:scale-95 shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>

@include('admin.promociones.modal-crear')
@include('admin.promociones.modal-editar')
@include('admin.promociones.modal-eliminar')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- BUSCADOR CON TECLADO VIRTUAL ---
        const buscador = document.getElementById('buscadorPromociones');
        const filas = document.querySelectorAll('.fila-promocion');
        
        function filtrarPromociones(term) {
            filas.forEach(fila => {
                const nombreElement = fila.querySelector('.nombre-promocion');
                if(nombreElement) {
                    const nombre = nombreElement.textContent.toLowerCase();
                    fila.style.display = nombre.includes(term) ? '' : 'none';
                }
            });
        }

        if (buscador) {
            buscador.addEventListener('input', function(e) {
                filtrarPromociones(e.target.value.toLowerCase().trim());
            });
            
            buscador.addEventListener('virtualKeyboardInput', function(e) {
                filtrarPromociones(e.target.value.toLowerCase().trim());
            });
        }

        // --- PROTECCIÓN DEL SIDEBAR (Mover modales al body) ---
        // OJO: usamos ids EXACTOS (#modalEditar), no el selector de prefijo [id^="modalEditar"],
        // porque ese prefijo también atrapaba a "modalEditarPromocionContent" (el contenedor
        // interno) y lo movía por separado al <body>, dejando el modal vacío y la tarjeta
        // suelta fuera del overlay.
        const modales = document.querySelectorAll('#modalEditar, #modalCrear, #modalEliminar');
        modales.forEach(m => {
            if(m && m.parentElement !== document.body) {
                document.body.appendChild(m);
            }
        });
    });

    {{-- NOTA: window.openModal y window.closeModal YA NO se redefinen aquí.
         Se usan las versiones genéricas de layouts.admin, que reciben
         (modalId, containerId). Por eso todas las llamadas abajo pasan
         ambos parámetros. --}}

    // Funciones específicas para el modal de eliminar
    window.openDeleteModal = function(id, nombre) {
        const form = document.getElementById('formEliminar');
        const display = document.getElementById('delete_nombre_display');
        
        if(form) form.action = `/promociones/${id}`;
        if(display) display.innerText = nombre;
        
        // TODO: confirma el id real del contenedor interno en modal-eliminar.blade.php
        // (el div con las clases scale-95/opacity-0) y reemplázalo aquí.
        window.openModal('modalEliminar', 'deleteContainer');
    };

    window.closeDeleteModal = function() {
        window.closeModal('modalEliminar', 'deleteContainer');
    };

    // --- PETICIONES AJAX ---
    window.guardarPromocion = function(event) {
        event.preventDefault();

        const form = document.getElementById('formCrearPromocion');
        const btn = document.getElementById('btn-guardar-promocion');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GUARDANDO...';

        fetch("{{ route('admin.promociones.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.closeModal('modalCrear', 'createContainer');
                form.reset();
                const spanTipoCrear = document.getElementById('tipoPromoSelectedCrear');
                if (spanTipoCrear) spanTipoCrear.textContent = 'Porcentaje (%)';
                window.location.reload();
            } else {
                alert(data.message || 'Error al guardar.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error inesperado.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Guardar Promoción';
        });
    };

    window.togglePromo = function(id) {
        const checkbox = document.getElementById(`togglePromo${id}`);
        
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('toggle_status', '1');
        formData.append('esta_activa', checkbox.checked ? '1' : '0');

       fetch(`/promociones/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en el servidor');
            return response.json();
        })
        .then(data => {
            if(data.success){
                window.location.reload();
            }
        })
        .catch(error => {
            console.error(error);
            checkbox.checked = !checkbox.checked;
            alert('No se pudo cambiar el estado de la promoción.');
        });
    };

    window.editPromo = function(id) {
        fetch(`/promociones/${id}/edit`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            const promo = data.promocion;
            const form = document.getElementById('formEditarPromocion');
            
            form.action = `/promociones/${id}`;
            form.querySelector('[name="nombre"]').value = promo.nombre;
            form.querySelector('[name="descripcion"]').value = promo.descripcion || '';
            form.querySelector('[name="tipo_promocion"]').value = promo.tipo_promocion;
            form.querySelector('[name="valor_descuento"]').value = promo.valor_descuento;
            form.querySelector('[name="fecha_inicio"]').value = promo.fecha_inicio;
            form.querySelector('[name="fecha_fin"]').value = promo.fecha_fin;
            
            const toggle = document.getElementById('edit_esta_activa');
            if(toggle) toggle.checked = (promo.esta_activa == 1);
            
            window.openModal('modalEditar', 'modalEditarPromocionContent');
        });
    };

    const formEditar = document.getElementById('formEditarPromocion');
    if (formEditar) {
        formEditar.addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GUARDANDO...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert(data.message);
            } catch (err) { 
                alert('Error al actualizar'); 
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }
</script>

{{-- AQUÍ INCLUIMOS EL COMPONENTE DEL TECLADO VIRTUAL --}}
@include('partials.teclado-virtual')
<script src="{{ asset('js/teclado-virtual.js') }}"></script>
@endsection