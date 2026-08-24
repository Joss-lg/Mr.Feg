@extends('layouts.admin')

@section('title', 'Fidelidad | Ollintem Pro')
@section('header-title', 'Niveles de Fidelidad')
@section('header-subtitle', 'Programa de sellos y premios para clientes')

@section('content')
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-[1800px] mx-auto space-y-6 sm:space-y-8 relative z-10 min-h-screen bg-[#F2F2F2] font-sans transition-colors duration-300">

    {{-- ENCABEZADO PREMIUM --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-2 sm:space-y-3 max-w-2xl w-full">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 sm:px-4 py-1.5 sm:py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 shadow-sm">
                <i class="fas fa-award"></i> Lealtad de Clientes
            </div>
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight drop-shadow-sm">Niveles de Fidelidad</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 tracking-wide">Define las metas de compras y los premios que tus clientes desbloquean al alcanzarlas.</p>
        </div>

        <div class="w-full xl:w-auto mt-2 xl:mt-0">
            @if(auth()->user()->tienePermiso('fidelidad.crear'))
                <button type="button" onclick="window.openModal('modalCrearNivel', 'createNivelContainer')" class="group w-full sm:w-auto relative flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-7 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 active:scale-95 outline-none border-0">
                    <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i>
                    Nuevo Nivel
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
            <input type="text" id="buscadorNiveles" data-teclado="texto" placeholder="Buscar por premio..."
                class="w-full bg-[#F2F2F2] border border-slate-300/80 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm">
        </div>

        <div class="flex items-center justify-between sm:justify-end w-full lg:w-auto gap-4 sm:gap-8 sm:px-4">
            <div class="text-center sm:text-right flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Total Registrados</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-800 leading-none mt-1">{{ $niveles->count() }}</p>
            </div>
            <div class="w-px h-8 sm:h-12 bg-slate-200"></div>
            <div class="text-center sm:text-left flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Activos ahora</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-500 leading-none mt-1">{{ $niveles->where('activo', true)->count() }}</p>
            </div>
        </div>
    </div>

    {{-- TABLERO DE NIVELES --}}
    @if($niveles->isEmpty())
        <div class="bg-white rounded-[2rem] p-8 sm:p-12 md:p-20 text-center border border-slate-200 shadow-sm flex flex-col items-center justify-center animate-fade-in-up" style="animation-delay: 300ms;">
            <div class="relative mb-6">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-blue-50 rounded-[2rem] flex items-center justify-center border border-blue-100 shadow-sm relative z-10 group">
                    <i class="fas fa-award text-4xl sm:text-5xl text-blue-500 group-hover:scale-110 transition-transform duration-300"></i>
                </div>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Sin niveles configurados</h2>
            <p class="mt-3 text-xs sm:text-sm text-slate-500 font-medium max-w-md">Aún no tienes ninguna meta de fidelidad definida. Crea el primer nivel para empezar a premiar a tus clientes frecuentes.</p>

            @if(auth()->user()->tienePermiso('fidelidad.crear'))
                <button type="button" onclick="window.openModal('modalCrearNivel', 'createNivelContainer')" class="mt-8 sm:mt-10 group w-full sm:w-auto rounded-2xl bg-blue-600 px-8 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 outline-none active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i> Comenzar ahora
                </button>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 300ms;">
            @foreach($niveles as $nivel)
                <article class="fila-nivel bg-white border border-slate-200 rounded-[1.5rem] sm:rounded-[2rem] p-5 sm:p-6 relative group transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg flex flex-col overflow-hidden shadow-sm">

                    <div class="absolute inset-x-0 top-0 h-1 {{ $nivel->activo ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : 'bg-slate-200' }}"></div>

                    {{-- HEADER DE LA TARJETA --}}
                    <div class="flex justify-between items-start mb-5 sm:mb-6 pt-2">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $nivel->activo ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-[#F2F2F2] text-slate-400 border border-slate-200' }} transition-all duration-300 shadow-sm">
                            <i class="fas fa-award text-xl"></i>
                        </div>

                        @if(auth()->user()->tienePermiso('fidelidad.editar'))
                            {{-- SWITCH TIPO iOS PREMIUM --}}
                            <label class="relative inline-flex items-center cursor-pointer p-1 -m-1">
                                <input id="toggleNivel{{ $nivel->id }}" type="checkbox" class="sr-only peer" {{ $nivel->activo ? 'checked' : '' }} onchange="window.toggleNivel({{ $nivel->id }})">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[6px] after:left-[6px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 peer-checked:after:border-transparent shadow-inner"></div>
                            </label>
                        @endif
                    </div>

                    {{-- CONTENIDO --}}
                    <div class="mb-5 sm:mb-6 flex-1">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Meta de compras</p>
                        <div class="flex items-end gap-1.5 sm:gap-2 mb-4">
                            <span class="bg-clip-text text-transparent bg-gradient-to-r {{ $nivel->activo ? 'from-blue-600 to-indigo-600' : 'from-slate-400 to-slate-500' }} font-black text-3xl sm:text-4xl tracking-tighter">
                                {{ $nivel->compras_requeridas }}
                            </span>
                            <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 pb-0.5">Sellos</span>
                        </div>

                        <h3 class="nombre-nivel text-base sm:text-lg font-black text-slate-800 tracking-tight leading-tight mb-1">{{ $nivel->premio_descripcion }}</h3>

                        <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-2 tracking-wide leading-relaxed">
                            Compra mínima: <span class="text-slate-700 font-black">${{ number_format($nivel->monto_minimo, 2) }}</span>
                        </p>
                        <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-1 tracking-wide leading-relaxed">
                            Valor del premio: <span class="text-slate-700 font-black">${{ number_format($nivel->valor_premio, 2) }}</span>
                        </p>
                    </div>

                    {{-- FOOTER DE TARJETA --}}
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100 gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest shrink-0 {{ $nivel->activo ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-[#F2F2F2] text-slate-500 border border-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $nivel->activo ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                            {{ $nivel->activo ? 'Activo' : 'Inactivo' }}
                        </span>

                        <div class="flex items-center gap-2 shrink-0">
                            @if(auth()->user()->tienePermiso('fidelidad.editar'))
                                <button type="button" onclick="window.editNivel({{ $nivel->id }})" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F2F2F2] border border-slate-200 text-slate-400 transition-all hover:bg-blue-50 hover:border-blue-100 hover:text-blue-600 outline-none active:scale-95 shadow-sm">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                            @endif

                            @if(auth()->user()->tienePermiso('fidelidad.eliminar'))
                                <button type="button" onclick="window.openDeleteNivelModal({{ $nivel->id }}, '{{ addslashes($nivel->premio_descripcion) }}')" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F2F2F2] border border-slate-200 text-slate-400 transition-all hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 outline-none active:scale-95 shadow-sm">
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

@include('admin.fidelidad.modal-crear')
@include('admin.fidelidad.modal-editar')
@include('admin.fidelidad.modal-eliminar')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- BUSCADOR CON TECLADO VIRTUAL ---
        const buscador = document.getElementById('buscadorNiveles');
        const filas = document.querySelectorAll('.fila-nivel');

        function filtrarNiveles(term) {
            filas.forEach(fila => {
                const nombreElement = fila.querySelector('.nombre-nivel');
                if (nombreElement) {
                    const nombre = nombreElement.textContent.toLowerCase();
                    fila.style.display = nombre.includes(term) ? '' : 'none';
                }
            });
        }

        if (buscador) {
            buscador.addEventListener('input', function(e) {
                filtrarNiveles(e.target.value.toLowerCase().trim());
            });

            buscador.addEventListener('virtualKeyboardInput', function(e) {
                filtrarNiveles(e.target.value.toLowerCase().trim());
            });
        }

        // --- PROTECCIÓN DEL SIDEBAR (Mover modales al body) ---
        const modales = document.querySelectorAll('#modalEditarNivel, #modalCrearNivel, #modalEliminarNivel');
        modales.forEach(m => {
            if (m && m.parentElement !== document.body) {
                document.body.appendChild(m);
            }
        });
    });

    // Funciones específicas para el modal de eliminar
    window.openDeleteNivelModal = function(id, premio) {
        const form = document.getElementById('formEliminarNivel');
        const display = document.getElementById('delete_nivel_nombre_display');

        if (form) form.action = `{{ url('fidelidad') }}/${id}`;
        if (display) display.innerText = premio;

        window.openModal('modalEliminarNivel', 'deleteNivelContainer');
    };

    window.closeDeleteNivelModal = function() {
        window.closeModal('modalEliminarNivel', 'deleteNivelContainer');
    };

    // --- PETICIONES AJAX ---
    window.guardarNivel = function(event) {
        event.preventDefault();

        const form = document.getElementById('formCrearNivel');
        const btn = document.getElementById('btn-guardar-nivel');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GUARDANDO...';

        fetch("{{ route('admin.fidelidad.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                window.closeModal('modalCrearNivel', 'createNivelContainer');
                form.reset();
                window.location.reload();
            } else {
                const primerError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Error al guardar.');
                alert(primerError);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error inesperado.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Guardar Nivel';
        });
    };

    window.toggleNivel = function(id) {
        const checkbox = document.getElementById(`toggleNivel${id}`);

        fetch(`{{ url('fidelidad') }}/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en el servidor');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error(error);
            checkbox.checked = !checkbox.checked;
            alert('No se pudo cambiar el estado del nivel.');
        });
    };

    window.editNivel = function(id) {
        fetch(`{{ url('fidelidad') }}/${id}/editar`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            const nivel = data.nivel;
            const form = document.getElementById('formEditarNivel');

            form.dataset.id = id;
            form.querySelector('[name="compras_requeridas"]').value = nivel.compras_requeridas;
            form.querySelector('[name="monto_minimo"]').value = nivel.monto_minimo;
            form.querySelector('[name="premio_descripcion"]').value = nivel.premio_descripcion;
            form.querySelector('[name="valor_premio"]').value = nivel.valor_premio;

            window.openModal('modalEditarNivel', 'editNivelContainer');
        });
    };

    const formEditarNivel = document.getElementById('formEditarNivel');
    if (formEditarNivel) {
        formEditarNivel.addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.target;
            const id = form.dataset.id;
            const formData = new FormData(form);
            formData.append('_method', 'PUT');
            const btn = form.querySelector('button[type="submit"]');

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GUARDANDO...';

            try {
                const response = await fetch(`{{ url('fidelidad') }}/${id}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    location.reload();
                } else {
                    const primerError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Error al actualizar.');
                    alert(primerError);
                }
            } catch (err) {
                alert('Error al actualizar');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }
</script>

{{-- INCLUSIÓN DEL TECLADO VIRTUAL --}}
@include('partials.teclado-virtual')
<script src="{{ asset('js/teclado-virtual.js') }}"></script>
@endsection