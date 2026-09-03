@extends('layouts.admin')

@section('title', 'Listado de Clientes | Ollintem Pro')

@section('content')
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-[1800px] mx-auto space-y-6 sm:space-y-8 relative z-10 min-h-screen bg-slate-50 font-sans transition-colors duration-300">

    {{-- ENCABEZADO PREMIUM --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-2 sm:space-y-3 max-w-2xl w-full">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 sm:px-4 py-1.5 sm:py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 shadow-sm">
                <i class="fas fa-users"></i> Directorio General
            </div>
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight drop-shadow-sm">Listado de Clientes</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 tracking-wide">Consulta, edita y administra los clientes registrados y sus direcciones.</p>
        </div>

        <div class="w-full xl:w-auto mt-2 xl:mt-0">
            @if(auth()->user()->tienePermiso(14, 'crear'))
                <button type="button" onclick="window.openModal('modalCrearCliente', 'clienteModalContainer')" class="group w-full sm:w-auto relative flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-7 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 active:scale-95 outline-none border-0">
                    <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i>
                    Nuevo Cliente
                </button>
            @endif
        </div>
    </div>

    {{-- MENSAJES DE SESIÓN --}}
    @if (session('success'))
        <div class="px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl shadow-sm text-xs font-bold flex items-center gap-3 animate-fade-in-up">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="px-5 py-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl shadow-sm text-xs font-bold flex items-center gap-3 animate-fade-in-up">
            <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- BARRA DE BÚSQUEDA Y ESTADÍSTICAS --}}
    <div class="bg-white rounded-[2rem] p-5 sm:p-6 flex flex-col lg:flex-row justify-between items-center gap-4 sm:gap-6 border border-slate-200 shadow-sm animate-fade-in-up" style="animation-delay: 150ms;">
        <div class="relative w-full lg:max-w-md group">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 transition-colors">
                <i class="fas fa-search text-sm"></i>
            </div>
            <input type="text" id="buscadorClientes" data-teclado="texto" placeholder="Buscar cliente por nombre o teléfono..." 
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm">
        </div>

        <div class="flex items-center justify-between sm:justify-end w-full lg:w-auto gap-4 sm:gap-8 sm:px-4">
            <div class="text-center sm:text-right flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Total Registrados</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-800 leading-none mt-1">{{ $clientes->total() }}</p>
            </div>
            <div class="w-px h-8 sm:h-12 bg-slate-200"></div>
            <div class="text-center sm:text-left flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Activos</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-500 leading-none mt-1">{{ $clientes->where('status', 1)->count() }}</p>
            </div>
        </div>
    </div>

    {{-- CONTENEDOR PRINCIPAL DE LA TABLA --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm p-5 sm:p-8 w-full space-y-6 animate-fade-in-up" style="animation-delay: 300ms;">

        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <h3 class="text-sm sm:text-base font-black text-slate-800 uppercase tracking-tight">Directorio de Clientes</h3>
            <span class="px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-xl text-[9px] sm:text-[10px] font-black text-blue-600 uppercase tracking-widest">
                {{ $clientes->total() }} {{ Str::plural('registro', $clientes->total()) }}
            </span>
        </div>

        @if($clientes->isEmpty())
            <div class="p-8 sm:p-12 md:p-20 text-center flex flex-col items-center justify-center">
                <div class="relative mb-6">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-blue-50 rounded-[2rem] flex items-center justify-center border border-blue-100 shadow-sm relative z-10 group">
                        <i class="fas fa-users text-4xl sm:text-5xl text-blue-500 group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Sin clientes registrados</h2>
                <p class="mt-3 text-xs sm:text-sm text-slate-500 font-medium max-w-md">Aún no tienes clientes en tu directorio. Registra el primero para comenzar.</p>

                @if(auth()->user()->tienePermiso(14, 'crear'))
                    <button type="button" onclick="window.openModal('modalCrearCliente', 'clienteModalContainer')" class="mt-8 sm:mt-10 group w-full sm:w-auto rounded-2xl bg-blue-600 px-8 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 outline-none active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i> Registrar cliente
                    </button>
                @endif
            </div>
        @else
            {{-- VISTA MÓVIL --}}
            <div class="md:hidden space-y-3 mt-4">
                @foreach ($clientes as $cliente)
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-sm text-slate-800 nombre-cliente">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
                            @if($cliente->status == 1)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactivo
                                </span>
                            @endif
                        </div>
                        <div class="text-xs font-semibold text-slate-600 telefono-cliente">{{ $cliente->telefono ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-500">
                            @if($cliente->direcciones->count() > 0)
                                {{ $cliente->direcciones->first()->calle }}@if($cliente->direcciones->first()->colonia), {{ $cliente->direcciones->first()->colonia }}@endif
                            @else
                                <span class="italic">Sin dirección</span>
                            @endif
                        </div>
                        <div class="flex gap-2 pt-2 border-t border-slate-100">
                            @if(auth()->user()->tienePermiso(14, 'editar'))
                                <button type="button" onclick="window.editCliente({{ $cliente->id }})" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 text-[11px] font-bold transition-all active:scale-95">
                                    <i class="fas fa-pen text-xs"></i> Editar
                                </button>
                            @endif
                            @if(auth()->user()->tienePermiso(14, 'eliminar'))
                                <button type="button" onclick="window.openDeleteModalCliente({{ $cliente->id }}, '{{ addslashes($cliente->nombre.' '.$cliente->apellido) }}')" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl border border-rose-200 bg-rose-50 text-rose-500 text-[11px] font-bold transition-all active:scale-95">
                                    <i class="fas fa-trash-alt text-xs"></i> Eliminar
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- VISTA ESCRITORIO --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th scope="col" class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nombre</th>
                            <th scope="col" class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Teléfono</th>
                            <th scope="col" class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Dirección Principal</th>
                            <th scope="col" class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Estatus</th>
                            <th scope="col" class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($clientes as $cliente)
                            <tr class="fila-cliente hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-4 font-black text-sm text-slate-800 whitespace-nowrap">
                                    <span class="nombre-cliente">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
                                </td>
                                <td class="py-4 px-4 text-xs font-semibold text-slate-600 telefono-cliente">
                                    {{ $cliente->telefono ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-xs font-medium text-slate-600">
                                    @if($cliente->direcciones->count() > 0)
                                        <span class="font-bold text-slate-800">{{ $cliente->direcciones->first()->calle }}</span>
                                        @if($cliente->direcciones->first()->colonia)
                                            , {{ $cliente->direcciones->first()->colonia }}
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Sin dirección</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($cliente->status == 1)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Botón de editar protegido --}}
                                        @if(auth()->user()->tienePermiso(14, 'editar'))
                                            <button type="button" onclick="window.editCliente({{ $cliente->id }})" class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 transition-all hover:bg-blue-50 hover:border-blue-100 hover:text-blue-600 outline-none active:scale-95 shadow-sm">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>
                                        @endif

                                        {{-- Botón de eliminar protegido --}}
                                        @if(auth()->user()->tienePermiso(14, 'eliminar'))
                                            <button type="button" onclick="window.openDeleteModalCliente({{ $cliente->id }}, '{{ addslashes($cliente->nombre.' '.$cliente->apellido) }}')" class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 transition-all hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 outline-none active:scale-95 shadow-sm cursor-pointer">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="sinResultadosClientes" class="hidden py-12 text-center text-slate-400 font-bold text-sm">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto border border-slate-200 text-slate-400 mb-3 shadow-sm">
                    <i class="fas fa-search text-2xl"></i>
                </div>
                No se encontraron clientes con ese criterio de búsqueda.
            </div>

            <div class="pt-4 border-t border-slate-100">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Inclusión del Modal de Creación --}}
@include('clientes.create-modal')

{{-- Inclusión del Modal de Edición --}}
@include('clientes.edit-modal')

{{-- Inclusión del Modal de Eliminación --}}
@include('clientes.delete-modal')

<!-- Scripts de control -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Protección de los modales (mover al body para evitar recortes del sidebar) ---
        const modalesCliente = document.querySelectorAll('#modalCrearCliente, #modalEditarCliente, #modalEliminarCliente');
        modalesCliente.forEach(m => {
            if (m && m.parentElement !== document.body) {
                document.body.appendChild(m);
            }
        });

        // NOTA: window.openModal y window.closeModal NO se redefinen aquí.
        // Se usan las versiones genéricas de layouts.admin, que reciben
        // (modalId, containerId). Por eso todas las llamadas de este módulo
        // pasan ambos parámetros: 'modalCrearCliente' y 'clienteModalContainer'.

        // --- Modal de eliminación ---
        window.openDeleteModalCliente = function (id, nombre) {
            const form = document.getElementById('formEliminarCliente');
            const display = document.getElementById('delete_cliente_nombre_display');

            if (form) form.action = `/clientes/${id}`;
            if (display) display.innerText = nombre;

            window.openModal('modalEliminarCliente', 'eliminarClienteModalContainer');
        };

        window.closeDeleteModalCliente = function () {
            window.closeModal('modalEliminarCliente', 'eliminarClienteModalContainer');
        };

        // --- BUSCADOR CON TECLADO VIRTUAL ---
        const buscador = document.getElementById('buscadorClientes');
        const filas = document.querySelectorAll('.fila-cliente');
        const sinResultados = document.getElementById('sinResultadosClientes');

        function filtrarClientes(term) {
            let visibles = 0;
            filas.forEach(fila => {
                const nombre = fila.querySelector('.nombre-cliente')?.textContent.toLowerCase() || '';
                const telefono = fila.querySelector('.telefono-cliente')?.textContent.toLowerCase() || '';
                const coincide = nombre.includes(term) || telefono.includes(term);
                fila.style.display = coincide ? '' : 'none';
                if (coincide) visibles++;
            });
            if (sinResultados) {
                sinResultados.classList.toggle('hidden', visibles !== 0 || term === '');
            }
        }

        if (buscador) {
            buscador.addEventListener('input', function(e) {
                filtrarClientes(e.target.value.toLowerCase().trim());
            });

            buscador.addEventListener('virtualKeyboardInput', function(e) {
                filtrarClientes(e.target.value.toLowerCase().trim());
            });
        }
    });

    // --- MODAL DE EDICIÓN: carga los datos del cliente por AJAX ---
    window.editCliente = function(id) {
        fetch(`/clientes/${id}/edit`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            const cliente = data.cliente;
            const direccion = data.direccion;
            const form = document.getElementById('formEditarCliente');

            form.action = `/clientes/${id}`;

            form.querySelector('#edit_nombre').value = cliente.nombre || '';
            form.querySelector('#edit_apellido').value = cliente.apellido || '';
            form.querySelector('#edit_telefono').value = cliente.telefono || '';

            form.querySelector('#edit_calle').value = direccion ? (direccion.calle || '') : '';
            form.querySelector('#edit_manzana').value = direccion ? (direccion.manzana || '') : '';
            form.querySelector('#edit_lote').value = direccion ? (direccion.lote || '') : '';
            form.querySelector('#edit_colonia').value = direccion ? (direccion.colonia || '') : '';
            form.querySelector('#edit_referencia').value = direccion ? (direccion.referencia || '') : '';

            const statusCheckbox = document.getElementById('edit_status');
            const statusLabel = document.getElementById('editStatusLabel');
            const activo = cliente.status == 1;

            statusCheckbox.checked = activo;
            statusLabel.textContent = activo ? 'Activo' : 'Inactivo';
            statusLabel.classList.toggle('text-emerald-600', activo);
            statusLabel.classList.toggle('text-slate-500', !activo);

            window.openModal('modalEditarCliente', 'editClienteModalContainer');
        })
        .catch(() => {
            alert('No se pudo cargar la información del cliente.');
        });
    };

    const formEditarCliente = document.getElementById('formEditarCliente');
    if (formEditarCliente) {
        formEditarCliente.addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const btn = document.getElementById('btn-actualizar-cliente');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ACTUALIZANDO...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al actualizar.');
                }
            } catch (err) {
                alert('Ocurrió un error inesperado.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }
</script>

{{-- Inclusión del Teclado Virtual --}}
@include('partials.teclado-virtual')
<script src="{{ asset('js/teclado-virtual.js') }}"></script>
@endsection