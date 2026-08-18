@extends('layouts.admin')

@section('title', 'Empleados | Ollintem Pro')
@section('header-title', 'Gestión de Empleados')
@section('header-subtitle', 'Administra empleados y sus permisos')

@push('styles')
<style>
    /* Fondo general del sistema en gris extra claro */
    body, html, #app, main, .wrapper, .main-content {
        background-color: #F2F2F2 !important; 
    }
    
    /* Textos del header superior en oscuro */
    header, header h1, header h2, header p, header span, .header-title, .header-subtitle {
        color: #0f172a !important; 
    }

    /* Aumentamos tamaños del header */
    header h1, .header-title {
        font-size: 2.2rem !important; 
        line-height: 1.2 !important;
        font-weight: 800 !important; 
    }
    header p, header span, .header-subtitle {
        font-size: 1rem !important; 
        margin-top: 0.25rem !important;
        font-weight: 500 !important;
        color: #64748b !important; 
    }

    /* ANIMACIONES DE ENTRADA */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0; 
    }
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6 md:p-8 lg:p-10 xl:p-12 max-w-[1800px] mx-auto w-full space-y-5 md:space-y-8 flex-1 flex flex-col bg-transparent">
    
    {{-- CABECERA DE LA SECCIÓN --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 relative z-10 animate-fade-in-up" style="animation-delay: 0ms;">
        <div>
            <h1 class="text-2xl md:text-4xl font-black text-slate-900 tracking-tight">Empleados</h1>
        </div>

        @if(auth()->user()->tienePermiso('empleados.crear'))
            <div class="relative group w-full sm:w-auto">
                <button type="button" onclick="openModal('modalCrearEmpleado', 'modalCrearContent')" 
                    class="relative flex items-center justify-center gap-2.5 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl text-sm font-bold transition-all duration-300 outline-none w-full sm:w-auto shadow-md shadow-blue-500/20 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 active:translate-y-0 active:scale-[0.98] group">
                    <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i> 
                    <span>Agregar Empleado</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Las alertas de session('success') / session('error') ya las muestra
         layouts.admin de forma global (el toast que aparece arriba a la
         derecha), así que no se repiten aquí para evitar el mensaje duplicado. --}}


    {{-- LÓGICA DE ESTADÍSTICAS --}}
    @php
        $totalAdmin = 0; $totalCapitan = 0; $totalMesero = 0; $totalCocinero = 0; $totalCajero = 0;
        
        foreach($empleados ?? [] as $emp) {
            $rolStr = mb_strtolower($emp->rol?->nombre ?? '', 'UTF-8');
            
            if(str_contains($rolStr, 'admin')) $totalAdmin++;
            elseif(str_contains($rolStr, 'capitan') || str_contains($rolStr, 'capitán')) $totalCapitan++;
            elseif(str_contains($rolStr, 'mesero')) $totalMesero++;
            elseif(str_contains($rolStr, 'cocinero')) $totalCocinero++;
            elseif(str_contains($rolStr, 'cajero')) $totalCajero++;
        }

        $tarjetasStats = [
            [
                'titulo' => 'Administradores', 'valor' => $totalAdmin, 'icono' => 'user-shield',
                'bgIcono' => 'bg-rose-50 text-rose-500',
                'barra' => 'bg-rose-400',
            ],
            [
                'titulo' => 'Capitanes', 'valor' => $totalCapitan, 'icono' => 'clipboard-list',
                'bgIcono' => 'bg-blue-50 text-blue-500',
                'barra' => 'bg-blue-400',
            ],
            [
                'titulo' => 'Meseros', 'valor' => $totalMesero, 'icono' => 'concierge-bell',
                'bgIcono' => 'bg-emerald-50 text-emerald-500',
                'barra' => 'bg-emerald-400',
            ],
            [
                'titulo' => 'Cocineros', 'valor' => $totalCocinero, 'icono' => 'fire',
                'bgIcono' => 'bg-orange-50 text-orange-500',
                'barra' => 'bg-orange-400',
            ],
            [
                'titulo' => 'Cajeros', 'valor' => $totalCajero, 'icono' => 'cash-register',
                'bgIcono' => 'bg-purple-50 text-purple-500',
                'barra' => 'bg-purple-400',
            ],
        ];
    @endphp

   {{-- GRID DE ESTADÍSTICAS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-5 relative z-10">
        @foreach($tarjetasStats as $stat)
        <!-- Tarjeta individual -->
        <div class="bg-white border border-slate-100 shadow-sm rounded-xl md:rounded-[1.5rem] p-4 md:p-6 flex flex-col justify-between h-24 md:h-36 relative group overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-blue-900/5 hover:border-blue-200 hover:-translate-y-1 animate-fade-in-up last:col-span-2 sm:last:col-span-1" style="animation-delay: {{ ($loop->index * 100) + 100 }}ms;">
            
            <!-- LÍNEA SUPERIOR -->
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] {{ $stat['barra'] }} opacity-80 group-hover:opacity-100 transition-opacity"></span>

            <div class="flex justify-between items-start w-full relative z-10 mt-1">
                <h3 class="text-[9px] md:text-[11px] font-bold text-slate-400 uppercase tracking-widest truncate pr-2 mt-2 group-hover:text-slate-600 transition-colors">{{ $stat['titulo'] }}</h3>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-[0.5rem] md:rounded-xl {{ $stat['bgIcono'] }} flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110">
                    <i class="fas fa-{{ $stat['icono'] }} text-[10px] md:text-sm"></i>
                </div>
            </div>
            <p class="text-2xl md:text-[2.75rem] leading-none font-black text-slate-900 relative z-10 mt-2 md:mt-0 tabular-nums">{{ $stat['valor'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- CONTENEDOR PRINCIPAL DE EMPLEADOS --}}
    <div class="bg-white border border-slate-100 shadow-sm rounded-2xl md:rounded-[2rem] p-4 sm:p-6 md:p-8 w-full flex-1 flex flex-col relative z-20 animate-fade-in-up" style="animation-delay: 500ms;">
        
        {{-- Cabecera de Tabla y Buscador --}}
        <div class="mb-4 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 relative z-30 pb-4 md:pb-6 border-b border-slate-100">
            <div>
                <h2 class="text-lg md:text-2xl font-black text-slate-900 tracking-tight">Lista de Empleados</h2>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-1">{{ count($empleados ?? []) }} registrados en el sistema</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-none sm:w-72 group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-blue-500 transition-colors duration-300"></i>
                    
                    <input type="text" id="buscadorEmpleados" data-teclado="texto" placeholder="Buscar empleado..." 
                        class="w-full h-11 bg-slate-50 border border-slate-200 rounded-full pl-11 pr-4 text-xs font-semibold text-slate-700 placeholder-slate-400 focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all duration-300">
                </div>
                
                <a href="{{ request()->has('ver_inactivos') ? route('admin.empleados.index') : route('admin.empleados.index', ['ver_inactivos' => 1]) }}" 
                    title="{{ request()->has('ver_inactivos') ? 'Ver solo activos' : 'Ver inactivos' }}"
                    class="w-11 h-11 flex-shrink-0 flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-all hover:-translate-y-0.5 {{ request()->has('ver_inactivos') ? 'text-rose-500 hover:text-rose-600 hover:bg-rose-50' : 'text-slate-500' }}">
                    <i class="fas fa-{{ request()->has('ver_inactivos') ? 'eye-slash' : 'eye' }} text-sm"></i> 
                </a>
            </div>
        </div>

        {{-- ===================== VISTA MÓVIL ===================== --}}
        <div id="listaEmpleadosMovil" class="flex flex-col gap-3 sm:hidden relative z-20">
            @forelse($empleados ?? [] as $empleado)
                @php
                    $rolStr = mb_strtolower($empleado->rol?->nombre ?? '', 'UTF-8');
                    $colorClass = 'bg-slate-50 text-slate-600 border border-slate-200';
                    $dotClass = 'bg-slate-400';
                    
                    if (str_contains($rolStr, 'admin')) { $colorClass = 'bg-rose-50 text-rose-600 border border-rose-100'; $dotClass = 'bg-rose-500'; }
                    elseif (str_contains($rolStr, 'cajero')) { $colorClass = 'bg-purple-50 text-purple-600 border border-purple-100'; $dotClass = 'bg-purple-500'; }
                    elseif (str_contains($rolStr, 'mesero')) { $colorClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100'; $dotClass = 'bg-emerald-500'; }
                    elseif (str_contains($rolStr, 'capitan') || str_contains($rolStr, 'capitán')) { $colorClass = 'bg-blue-50 text-blue-600 border border-blue-100'; $dotClass = 'bg-blue-500'; }
                    elseif (str_contains($rolStr, 'cocinero')) { $colorClass = 'bg-orange-50 text-orange-600 border border-orange-100'; $dotClass = 'bg-orange-500'; }
                @endphp
                <div class="fila-empleado-movil border border-slate-100 rounded-2xl p-4 bg-white shadow-sm transition-all duration-300 {{ !$empleado->esta_activo ? 'opacity-50 grayscale' : 'hover:shadow-md hover:border-blue-100 hover:bg-blue-50/30' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 text-blue-700 font-black text-sm flex items-center justify-center flex-shrink-0">
                                {{ strtoupper(substr($empleado->nombre, 0, 1)) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="nombre-empleado-txt font-bold text-sm text-slate-900 truncate">{{ $empleado->nombre }}</span>
                                <span class="text-[10px] font-medium text-slate-500 mt-0.5">EMP-{{ str_pad($empleado->id, 3, '0', STR_PAD_LEFT) }} · PIN {{ $empleado->codigo_empleado ?? '----' }}</span>
                            </div>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $colorClass }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                            {{ $empleado->rol?->nombre ?? 'Sin rol' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.empleados.permisos', $empleado->id) }}" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase text-slate-700 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all">
                            <i class="fas fa-shield-alt text-blue-500 text-[11px]"></i> Permisos
                        </a>

                        @if($empleado->esta_activo)
                            <button type="button" title="Editar" onclick="window.ejecutarEditar(this)"
                                class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center border border-blue-200 bg-white text-blue-500 hover:bg-blue-500 hover:text-white hover:shadow-md hover:shadow-blue-500/20 active:scale-95 transition-all"
                                data-id="{{ $empleado->id }}"
                                data-nombre="{{ $empleado->nombre }}"
                                data-codigo="{{ $empleado->codigo_empleado }}"
                                data-rol-id="{{ $empleado->rol_id }}"
                                data-rol-nombre="{{ $empleado->rol?->nombre }}"
                                data-acceso="{{ $empleado->puede_acceder_pos }}">
                                <i class="fas fa-pen text-[12px] pointer-events-none"></i>
                            </button>
                            <button type="button" onclick="abrirConfirmacionEliminar('form-delete-movil-{{ $empleado->id }}', false)" title="Desactivar"
                                class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center border border-amber-200 bg-white text-amber-500 hover:bg-amber-500 hover:text-white hover:shadow-md hover:shadow-amber-500/20 active:scale-95 transition-all">
                                <i class="fas fa-user-slash text-[12px] pointer-events-none"></i>
                            </button>
                            <form id="form-delete-movil-{{ $empleado->id }}" action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        @else
                            <form action="{{ route('admin.empleados.reactivar', $empleado->id) }}" method="POST" class="shrink-0">
                                @csrf @method('PATCH')
                                <button type="submit" title="Reactivar" class="h-10 w-10 rounded-xl flex items-center justify-center border border-emerald-200 bg-white text-emerald-500 hover:bg-emerald-500 hover:text-white hover:shadow-md hover:shadow-emerald-500/20 active:scale-95 transition-all">
                                    <i class="fas fa-user-check text-[12px] pointer-events-none"></i>
                                </button>
                            </form>
                            <button type="button" onclick="abrirConfirmacionEliminar('form-delete-movil-{{ $empleado->id }}', true)" title="Eliminar Permanentemente"
                                class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center border border-rose-200 bg-white text-rose-500 hover:bg-rose-500 hover:text-white hover:shadow-md hover:shadow-rose-500/20 active:scale-95 transition-all">
                                <i class="fas fa-trash-alt text-[12px] pointer-events-none"></i>
                            </button>
                            <form id="form-delete-movil-{{ $empleado->id }}" action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-14">
                    <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mb-4">
                        <i class="fas fa-users-slash text-xl text-slate-300"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-400">No hay empleados registrados</span>
                </div>
            @endforelse
        </div>

        {{-- ===================== VISTA ESCRITORIO ===================== --}}
        <div class="hidden sm:block w-full overflow-x-auto relative z-20 pb-4">
            <table class="w-full min-w-[700px] text-left border-collapse table-fixed">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="w-[30%] pb-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-left">Nombre</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">PIN</th>
                        <th class="w-[20%] pb-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Rol</th>
                        <th class="w-[20%] pb-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Permisos</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaEmpleados" class="divide-y divide-slate-50">
                    @forelse($empleados ?? [] as $empleado)
                    
                    @php
                        $rolStr = mb_strtolower($empleado->rol?->nombre ?? '', 'UTF-8');
                        $colorClass = 'bg-slate-50 text-slate-600 border border-slate-200';
                        $dotClass = 'bg-slate-400';
                        
                        if (str_contains($rolStr, 'admin')) { $colorClass = 'bg-rose-50 text-rose-600 border border-rose-100'; $dotClass = 'bg-rose-500'; }
                        elseif (str_contains($rolStr, 'cajero')) { $colorClass = 'bg-purple-50 text-purple-600 border border-purple-100'; $dotClass = 'bg-purple-500'; }
                        elseif (str_contains($rolStr, 'mesero')) { $colorClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100'; $dotClass = 'bg-emerald-500'; }
                        elseif (str_contains($rolStr, 'capitan') || str_contains($rolStr, 'capitán')) { $colorClass = 'bg-blue-50 text-blue-600 border border-blue-100'; $dotClass = 'bg-blue-500'; }
                        elseif (str_contains($rolStr, 'cocinero')) { $colorClass = 'bg-orange-50 text-orange-600 border border-orange-100'; $dotClass = 'bg-orange-500'; }
                    @endphp

                    <tr class="fila-empleado group hover:bg-blue-50/50 transition-all duration-300 {{ !$empleado->esta_activo ? 'opacity-50 grayscale' : '' }}">
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 text-blue-700 font-black text-sm flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                    {{ strtoupper(substr($empleado->nombre, 0, 1)) }}
                                </div>
                                <div class="flex flex-col min-w-0 truncate">
                                    <span class="nombre-empleado-txt font-bold text-sm text-slate-900 truncate group-hover:text-blue-900 transition-colors">{{ $empleado->nombre }}</span>
                                    <span class="text-[10px] font-medium text-slate-500 mt-0.5 group-hover:text-blue-500/80 transition-colors">ID: EMP-{{ str_pad($empleado->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 align-middle text-center">
                            <span class="inline-block font-black text-xs text-slate-700 tracking-[0.2em] bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg group-hover:border-blue-200 transition-colors">{{ $empleado->codigo_empleado ?? '----' }}</span>
                        </td>
                        
                        <td class="py-4 px-4 align-middle text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $empleado->rol?->nombre ?? 'Sin rol' }}
                            </span>
                        </td>
                        
                        <td class="py-4 px-4 align-middle text-center">
                            <a href="{{ route('admin.empleados.permisos', $empleado->id) }}" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[10px] font-bold uppercase text-slate-700 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 hover:shadow-sm hover:shadow-blue-500/10 transition-all duration-300">
                                <i class="fas fa-shield-alt text-blue-500 text-[10px]"></i> Configurar
                            </a>
                        </td>
                        
                        <td class="py-4 px-4 align-middle text-center">
                            <div class="flex items-center justify-center gap-2 relative z-30">
                                @if($empleado->esta_activo)
                                    <button type="button" title="Editar" onclick="window.ejecutarEditar(this)"
                                        class="h-9 w-9 rounded-xl flex items-center justify-center border border-blue-200 bg-white text-blue-500 hover:bg-blue-500 hover:text-white hover:shadow-md hover:shadow-blue-500/20 transition-all duration-300 outline-none flex-shrink-0"
                                        data-id="{{ $empleado->id }}"
                                        data-nombre="{{ $empleado->nombre }}"
                                        data-codigo="{{ $empleado->codigo_empleado }}"
                                        data-rol-id="{{ $empleado->rol_id }}"
                                        data-rol-nombre="{{ $empleado->rol?->nombre }}"
                                        data-acceso="{{ $empleado->puede_acceder_pos }}">
                                        <i class="fas fa-pen text-[12px] pointer-events-none"></i>
                                    </button>
                                    
                                    <form id="form-delete-{{ $empleado->id }}" action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="abrirConfirmacionEliminar('form-delete-{{ $empleado->id }}', false)" title="Desactivar" 
                                            class="h-9 w-9 rounded-xl flex items-center justify-center border border-amber-200 bg-white text-amber-500 hover:bg-amber-500 hover:text-white hover:shadow-md hover:shadow-amber-500/20 transition-all duration-300 outline-none flex-shrink-0">
                                            <i class="fas fa-user-slash text-[12px] pointer-events-none"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.empleados.reactivar', $empleado->id) }}" method="POST" class="m-0 p-0">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Reactivar" class="h-9 w-9 rounded-xl flex items-center justify-center border border-emerald-200 bg-white text-emerald-500 hover:bg-emerald-500 hover:text-white hover:shadow-md hover:shadow-emerald-500/20 transition-all duration-300 outline-none flex-shrink-0">
                                            <i class="fas fa-user-check text-[12px] pointer-events-none"></i>
                                        </button>
                                    </form>
                                    
                                    <form id="form-delete-{{ $empleado->id }}" action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="abrirConfirmacionEliminar('form-delete-{{ $empleado->id }}', true)" title="Eliminar" 
                                            class="h-9 w-9 rounded-xl flex items-center justify-center border border-rose-200 bg-white text-rose-500 hover:bg-rose-500 hover:text-white hover:shadow-md hover:shadow-rose-500/20 transition-all duration-300 outline-none flex-shrink-0">
                                            <i class="fas fa-trash-alt text-[12px] pointer-events-none"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-users-slash text-xl text-slate-300"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-400">No hay empleados registrados</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODALES INCLUIDOS --}}
@include('admin.empleados.modal-editar')
@include('admin.empleados.modal-crear')

{{-- MODAL DE CONFIRMACIÓN --}}
<div id="modal-confirmacion-eliminar" class="fixed inset-0 z-[999] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm px-4 transition-all duration-300">
    <div class="relative bg-white border border-slate-200 rounded-[2rem] p-6 sm:p-8 max-w-sm w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200 overflow-hidden text-center">
        
        <div id="glow-modal" class="absolute -top-24 -left-24 w-48 h-48 rounded-full blur-3xl pointer-events-none transition-colors duration-300"></div>

        <div class="flex justify-center mb-5 relative z-10">
            <div id="wrapper-icono" class="flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl border bg-slate-50 transition-colors duration-300">
                <i id="icono-modal" class="fas text-2xl sm:text-3xl transition-colors duration-300"></i>
            </div>
        </div>
        
        <h2 id="titulo-confirmacion" class="text-lg sm:text-xl font-black text-slate-900 mb-2 tracking-tight relative z-10">¿Desactivar Empleado?</h2>
        <p id="texto-confirmacion" class="text-slate-500 text-sm mb-6 font-medium relative z-10">
            El empleado no podrá acceder al sistema, pero sus registros se mantendrán.
        </p>
        
        <div class="flex gap-3 relative z-10">
            <button type="button" onclick="cerrarConfirmacionEliminar()" class="flex-1 py-3.5 sm:py-4 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all outline-none active:scale-[0.98]">
                Cancelar
            </button>
            <button type="button" id="btn-ejecutar-eliminar" class="flex-1 py-3.5 sm:py-4 px-4 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all outline-none active:scale-[0.98]">
                Confirmar
            </button>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    let formularioEliminarSeleccionado = null;

    window.abrirConfirmacionEliminar = function(formId, esPermanente) {
        formularioEliminarSeleccionado = formId;
        const modal = document.getElementById('modal-confirmacion-eliminar');
        const titulo = document.getElementById('titulo-confirmacion');
        const texto = document.getElementById('texto-confirmacion');
        const glow = document.getElementById('glow-modal');
        const wrapperIcono = document.getElementById('wrapper-icono');
        const icono = document.getElementById('icono-modal');
        const btnConfirmar = document.getElementById('btn-ejecutar-eliminar');

        if (esPermanente) {
            titulo.innerText = '¿Eliminar Permanentemente?';
            texto.innerText = 'Esta acción no se puede deshacer. Se borrarán todos los datos.';
            glow.className = "absolute -top-24 -left-24 w-48 h-48 bg-rose-50 rounded-full blur-3xl pointer-events-none";
            wrapperIcono.className = "flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl border border-rose-100 bg-rose-50";
            icono.className = "fas fa-trash-alt text-rose-500 text-2xl sm:text-3xl";
            btnConfirmar.className = "flex-1 py-3.5 sm:py-4 px-4 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all outline-none active:scale-[0.98] bg-rose-600 hover:bg-rose-700";
        } else {
            titulo.innerText = '¿Desactivar Empleado?';
            texto.innerText = 'El empleado no podrá acceder al sistema, pero sus datos se mantendrán.';
            glow.className = "absolute -top-24 -left-24 w-48 h-48 bg-amber-50 rounded-full blur-3xl pointer-events-none";
            wrapperIcono.className = "flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl border border-amber-100 bg-amber-50";
            icono.className = "fas fa-user-slash text-amber-500 text-2xl sm:text-3xl";
            btnConfirmar.className = "flex-1 py-3.5 sm:py-4 px-4 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all outline-none active:scale-[0.98] bg-amber-500 hover:bg-amber-600";
        }

        modal.classList.remove('hidden');
    };

    window.cerrarConfirmacionEliminar = function() {
        formularioEliminarSeleccionado = null;
        const modal = document.getElementById('modal-confirmacion-eliminar');
        modal.classList.add('hidden');
    };

    document.getElementById('btn-ejecutar-eliminar').addEventListener('click', function() {
        if (formularioEliminarSeleccionado) {
            document.getElementById(formularioEliminarSeleccionado).submit();
        }
    });
</script>
@endsection