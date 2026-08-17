@extends('layouts.admin')

@section('title', 'Inventario | Ollintem Pro')

@section('content')
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-[1800px] mx-auto space-y-6 sm:space-y-8 relative z-10 min-h-screen bg-slate-50 font-sans transition-colors duration-300">

    @php
        $listaInsumos = collect($insumos ?? []);
        $totalInsumos = $listaInsumos->count();
        $criticosInsumos = $listaInsumos->filter(function ($item) {
            $minimo = $item->stock_minimo > 0 ? $item->stock_minimo : 1;
            return (($item->stock_actual / $minimo) * 100) < 50;
        })->count();
    @endphp

    {{-- ENCABEZADO PREMIUM --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-2 sm:space-y-3 max-w-2xl w-full">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 sm:px-4 py-1.5 sm:py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 shadow-sm">
                <i class="fas fa-boxes"></i> Control de Insumos
            </div>
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight drop-shadow-sm">Inventario del Restaurante</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 tracking-wide">Controla existencias, stock mínimo y movimientos de tus insumos.</p>
        </div>

        <div class="w-full xl:w-auto mt-2 xl:mt-0 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @if(auth()->user()->tienePermiso('inventario.mostrar'))
                <a href="{{ route('admin.inventario.exportar_pdf_bajo_stock') }}"
                    class="w-full sm:w-auto flex justify-center items-center gap-2 rounded-2xl bg-rose-600 px-6 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-rose-700 shadow-md shadow-rose-500/20 active:scale-95 outline-none border-0">
                    <i class="fas fa-file-pdf"></i> Reporte Bajo Stock
                </a>
            @endif

            <a href="{{ route('admin.corte.index') }}"
                class="w-full sm:w-auto flex justify-center items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-emerald-700 shadow-md shadow-emerald-500/20 active:scale-95 outline-none border-0">
                <i class="fas fa-receipt"></i> Productos Vendidos
            </a>

            @if(auth()->user()->tienePermiso('inventario.crear'))
                <button type="button" onclick="openModalCrear()" class="group w-full sm:w-auto relative flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-7 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 active:scale-95 outline-none border-0">
                    <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i>
                    Agregar Producto
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
            <input type="text" id="buscadorInventario" data-teclado="texto" placeholder="Buscar ingrediente..."
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm">
        </div>

        <div class="flex items-center justify-between sm:justify-end w-full lg:w-auto gap-4 sm:gap-8 sm:px-4">
            <div class="text-center sm:text-right flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Total Registrados</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-800 leading-none mt-1">{{ $totalInsumos }}</p>
            </div>
            <div class="w-px h-8 sm:h-12 bg-slate-200"></div>
            <div class="text-center sm:text-left flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Stock Crítico</p>
                <p class="text-2xl sm:text-3xl font-black text-rose-500 leading-none mt-1">{{ $criticosInsumos }}</p>
            </div>
        </div>
    </div>

    {{-- CONTENEDOR PRINCIPAL --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm p-5 sm:p-8 w-full space-y-6 animate-fade-in-up" style="animation-delay: 300ms;">

        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 border border-blue-100 text-blue-600 shrink-0">
                    <i class="fas fa-boxes text-sm"></i>
                </div>
                <h3 class="text-sm sm:text-base font-black text-slate-800 uppercase tracking-tight">Existencias</h3>
            </div>
            <span class="px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-xl text-[9px] sm:text-[10px] font-black text-blue-600 uppercase tracking-widest">
                {{ $totalInsumos }} {{ Str::plural('registro', $totalInsumos) }}
            </span>
        </div>

        @if($totalInsumos === 0)
            <div class="p-8 sm:p-12 md:p-20 text-center flex flex-col items-center justify-center">
                <div class="relative mb-6">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-blue-50 rounded-[2rem] flex items-center justify-center border border-blue-100 shadow-sm relative z-10 group">
                        <i class="fas fa-boxes text-4xl sm:text-5xl text-blue-500 group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Sin productos registrados</h2>
                <p class="mt-3 text-xs sm:text-sm text-slate-500 font-medium max-w-md">Aún no tienes insumos en tu inventario. Registra el primero para comenzar a controlar tu stock.</p>

                @if(auth()->user()->tienePermiso('inventario.crear'))
                    <button type="button" onclick="openModalCrear()" class="mt-8 sm:mt-10 group w-full sm:w-auto rounded-2xl bg-blue-600 px-8 py-4 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 shadow-md shadow-blue-500/20 outline-none active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-plus transition-transform duration-300 group-hover:rotate-90"></i> Agregar producto
                    </button>
                @endif
            </div>
        @else
            {{-- ================= VISTA PARA ESCRITORIO (>=1024px) ================= --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Código</th>
                            <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Producto / Ingrediente</th>
                            <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Cantidad</th>
                            <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Unidad</th>
                            <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Stock Mínimo</th>
                            <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                            <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaInventario" class="divide-y divide-slate-100">
                        @forelse($insumos ?? [] as $item)
                        <tr class="fila-articulo hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-4 text-xs font-mono font-bold text-slate-400">
                                {{ $item->codigo ?? 'S/N' }}
                            </td>
                            <td class="py-4 px-4 text-sm font-black text-slate-800 nombre-celda">
                                {{ $item->nombre }}
                            </td>
                            <td class="py-4 px-4 text-sm font-black text-slate-800">
                                {{ number_format($item->stock_actual, 2) }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                    {{ $item->unidad_medida }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm font-bold text-slate-500">
                                {{ number_format($item->stock_minimo, 2) }}
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $minimo = $item->stock_minimo > 0 ? $item->stock_minimo : 1;
                                    $porcentaje = ($item->stock_actual / $minimo) * 100;

                                    if($porcentaje >= 150) {
                                        $estadoClase = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                                        $puntoClase = 'bg-emerald-500';
                                        $textoEstado = 'Óptimo';
                                    } elseif($porcentaje > 100) {
                                        $estadoClase = 'bg-blue-50 text-blue-600 border border-blue-100';
                                        $puntoClase = 'bg-blue-500';
                                        $textoEstado = 'Bien';
                                    } elseif($porcentaje >= 50) {
                                        $estadoClase = 'bg-amber-50 text-amber-600 border border-amber-100';
                                        $puntoClase = 'bg-amber-500';
                                        $textoEstado = 'Regular';
                                    } else {
                                        $estadoClase = 'bg-rose-50 text-rose-600 border border-rose-100';
                                        $puntoClase = 'bg-rose-500';
                                        $textoEstado = 'Crítico';
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $estadoClase }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $puntoClase }} {{ $porcentaje < 50 ? 'animate-pulse' : '' }}"></span>
                                    {{ $textoEstado }} ({{ round($porcentaje) }}%)
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(auth()->user()->tienePermiso('inventario.editar'))
                                        <button type="button" title="Ajustar Stock"
                                            onclick="openModalMovimiento('{{ $item->id }}', '{{ $item->nombre }}')"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 transition-all hover:bg-emerald-50 hover:border-emerald-100 hover:text-emerald-600 outline-none active:scale-95 shadow-sm">
                                            <i class="fas fa-exchange-alt text-xs"></i>
                                        </button>
                                        <button type="button" title="Editar Detalles"
                                            onclick="abrirModalEspecifico('modalEditar-{{ $item->id }}')"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 transition-all hover:bg-blue-50 hover:border-blue-100 hover:text-blue-600 outline-none active:scale-95 shadow-sm">
                                            <i class="fas fa-cog text-xs"></i>
                                        </button>
                                    @endif
                                    @if(auth()->user()->tienePermiso('inventario.eliminar'))
                                        <button type="button" title="Dar de baja"
                                            onclick="confirmarEliminacion('{{ $item->id }}', '{{ $item->nombre }}')"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 transition-all hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 outline-none active:scale-95 shadow-sm">
                                            <i class="far fa-trash-alt text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @include('admin.inventario.modal-editar', ['item' => $item])
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 font-bold text-sm">No hay productos registrados en el inventario.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= VISTA DE TARJETAS (móvil y táctil, <1024px) ================= --}}
            <div class="block lg:hidden space-y-4">
                @forelse($insumos ?? [] as $item)
                    @php
                        $minimo = $item->stock_minimo > 0 ? $item->stock_minimo : 1;
                        $porcentaje = ($item->stock_actual / $minimo) * 100;

                        if($porcentaje >= 150) {
                            $estadoClase = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                            $puntoClase = 'bg-emerald-500';
                            $textoEstado = 'Óptimo';
                        } elseif($porcentaje > 100) {
                            $estadoClase = 'bg-blue-50 text-blue-600 border border-blue-100';
                            $puntoClase = 'bg-blue-500';
                            $textoEstado = 'Bien';
                        } elseif($porcentaje >= 50) {
                            $estadoClase = 'bg-amber-50 text-amber-600 border border-amber-100';
                            $puntoClase = 'bg-amber-500';
                            $textoEstado = 'Regular';
                        } else {
                            $estadoClase = 'bg-rose-50 text-rose-600 border border-rose-100';
                            $puntoClase = 'bg-rose-500';
                            $textoEstado = 'Crítico';
                        }
                    @endphp

                    <div class="fila-articulo bg-white border border-slate-200 rounded-[1.5rem] p-5 space-y-3 transition-all hover:border-blue-200 hover:shadow-lg shadow-sm">

                        {{-- Fila Superior: Código y Estado --}}
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="font-mono font-bold text-slate-400">Cod: {{ $item->codigo ?? 'S/N' }}</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-black uppercase tracking-wider text-[10px] {{ $estadoClase }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $puntoClase }} {{ $porcentaje < 50 ? 'animate-pulse' : '' }}"></span>
                                {{ $textoEstado }} ({{ round($porcentaje) }}%)
                            </span>
                        </div>

                        {{-- Nombre del producto --}}
                        <h3 class="text-base font-black text-slate-800 nombre-celda leading-tight uppercase tracking-tight">
                            {{ $item->nombre }}
                        </h3>

                        {{-- Cuadrícula de Stocks --}}
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-xs">
                            <div>
                                <span class="block text-[9px] uppercase font-black tracking-[0.15em] text-slate-400 mb-0.5">Stock Actual</span>
                                <span class="font-black text-slate-800 text-sm">
                                    {{ number_format($item->stock_actual, 2) }}
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider ml-0.5">{{ $item->unidad_medida }}</span>
                                </span>
                            </div>
                            <div>
                                <span class="block text-[9px] uppercase font-black tracking-[0.15em] text-slate-400 mb-0.5">Mínimo Req.</span>
                                <span class="font-bold text-slate-500 text-sm">
                                    {{ number_format($item->stock_minimo, 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                            @if(auth()->user()->tienePermiso('inventario.editar'))
                                <button type="button"
                                    onclick="openModalMovimiento('{{ $item->id }}', '{{ $item->nombre }}')"
                                    class="flex-1 h-11 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-wider rounded-xl flex items-center justify-center gap-1.5 transition-colors">
                                    <i class="fas fa-exchange-alt"></i> Ajustar
                                </button>
                                <button type="button"
                                    onclick="abrirModalEspecifico('modalEditar-{{ $item->id }}')"
                                    class="flex-1 h-11 bg-blue-50 hover:bg-blue-100 border border-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-wider rounded-xl flex items-center justify-center gap-1.5 transition-colors">
                                    <i class="fas fa-cog"></i> Editar
                                </button>
                            @endif

                            @if(auth()->user()->tienePermiso('inventario.eliminar'))
                                <button type="button"
                                    onclick="confirmarEliminacion('{{ $item->id }}', '{{ $item->nombre }}')"
                                    class="w-11 h-11 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 rounded-xl flex items-center justify-center transition-colors shrink-0">
                                    <i class="far fa-trash-alt text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if(auth()->user()->tienePermiso('inventario.editar'))
                        @include('admin.inventario.modal-editar', ['item' => $item])
                    @endif

                @empty
                    <div class="py-8 text-center text-slate-400 font-bold text-sm">No hay productos registrados en el inventario.</div>
                @endforelse
            </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- APÉNDICE DE MODALES AL BODY (Arreglo del Sidebar en Móviles) ---
        // Toma dinámicamente todos los modales existentes y los pasa a la raíz del body
        const modales = document.querySelectorAll('#modalCrear, #modalEliminar, #modalMovimiento, [id^="modalEditar-"], [id^="modalMovimiento-"]');
        modales.forEach(modal => {
            if(modal) {
                document.body.appendChild(modal);
            }
        });

        // --- BUSCADOR EN TIEMPO REAL CON INTEGRACIÓN DE TECLADO VIRTUAL ---
        const buscador = document.getElementById('buscadorInventario');
        const filas = document.querySelectorAll('.fila-articulo');
        
        function filtrarInventario(term) {
            filas.forEach(fila => {
                const nombre = fila.querySelector('.nombre-celda').textContent.toLowerCase();
                fila.style.display = nombre.includes(term) ? '' : 'none';
            });
        }

        if (buscador) {
            // Escucha tanto el input normal (teclado físico) como los eventos custom (teclado virtual)
            buscador.addEventListener('input', function(e) {
                filtrarInventario(e.target.value.toLowerCase().trim());
            });
            
            buscador.addEventListener('virtualKeyboardInput', function(e) {
                filtrarInventario(e.target.value.toLowerCase().trim());
            });
        }
    });

    // --- MODAL AGREGAR (NUEVO INSUMO) ---
    function openModalCrear() {
        const modal = document.getElementById('modalCrear');
        const container = document.getElementById('createContainer');
        if (!modal || !container) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // El nombre de esta función coincide con tu disparador onclick
    function closeCreateModal() {
        const modal = document.getElementById('modalCrear');
        const container = document.getElementById('createContainer');
        if(container) {
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    // --- MODAL EDITAR DINÁMICO ---
    function abrirModalEspecifico(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        const container = modal.querySelector('div[id^="modalContainer-"]');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            if(container) {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    }

    function cerrarModalEspecifico(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const container = modal.querySelector('div[id^="modalContainer-"]');
        
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        if(container) {
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
        }
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // --- MODAL ELIMINAR ---
    function confirmarEliminacion(id, nombre) {
        const modal = document.getElementById('modalEliminar');
        const container = document.getElementById('deleteContainer');
        const form = document.getElementById('formEliminar');
        const displayNombre = document.getElementById('delete_nombre_display');

        if (!modal || !container) return;
        if(displayNombre) displayNombre.innerText = nombre;
        if(form) form.action = `/admin/inventario/${id}`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('modalEliminar');
        const container = document.getElementById('deleteContainer');
        if(container) {
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }
</script>

@include('admin.inventario.modal-crear')
@include('admin.inventario.modal-eliminar')
@include('admin.inventario.modal-movimiento')

{{-- AQUÍ INCLUIMOS EL COMPONENTE DEL TECLADO VIRTUAL --}}
@include('partials.teclado-virtual')
@endsection