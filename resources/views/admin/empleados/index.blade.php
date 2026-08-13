@extends('layouts.admin')

@section('title', 'Empleados | Ollintem Pro')
@section('header-title', 'Gestión de Personal')
@section('header-subtitle', 'Administra roles y permisos del equipo')

@section('content')
<div class="p-3 sm:p-6 md:p-8 lg:p-10 xl:p-12 max-w-[1800px] mx-auto w-full space-y-5 md:space-y-8 flex-1 flex flex-col bg-transparent">
    
    {{-- CABECERA DE LA SECCIÓN --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 relative z-10">
        <div>
            <h1 class="text-2xl md:text-4xl font-black text-[#fff7ed] tracking-tight">Empleados</h1>
        </div>

        @if(auth()->user()->tienePermiso('empleados.crear'))
            <div class="relative group w-full sm:w-auto">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-[#ff7a22] to-[#ff7a22] rounded-xl blur opacity-30 group-hover:opacity-60 transition duration-500 pointer-events-none"></div>
                <button type="button" onclick="abrirModalCrear()" class="relative flex items-center justify-center gap-2.5 bg-gradient-to-b from-[#ff7a22] to-[#ff7a22] hover:to-[#ff7a22] text-white px-6 py-3.5 rounded-xl text-sm font-bold transition-all duration-300 outline-none w-full sm:w-auto shadow-lg shadow-[#ff7a22]/20 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] border border-white/10">
                    <i class="fas fa-plus"></i> 
                    <span>Agregar Empleado</span>
                </button>
            </div>
        @endif
    </div>

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
                'bgIcono' => 'bg-[#ff7a22]/20 text-[#ff7a22] border border-[#ff7a22]/30 shadow-inner',
                'barra' => 'bg-[#ff7a22]',
            ],
            [
                'titulo' => 'Capitanes', 'valor' => $totalCapitan, 'icono' => 'clipboard-list',
                'bgIcono' => 'bg-[#38b6ff]/20 text-[#38b6ff] border border-[#38b6ff]/30 shadow-inner',
                'barra' => 'bg-[#38b6ff]',
            ],
            [
                'titulo' => 'Meseros', 'valor' => $totalMesero, 'icono' => 'concierge-bell',
                'bgIcono' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 shadow-inner',
                'barra' => 'bg-emerald-500',
            ],
            [
                'titulo' => 'Cocineros', 'valor' => $totalCocinero, 'icono' => 'fire',
                'bgIcono' => 'bg-[#f97316]/20 text-[#f97316] border border-[#f97316]/30 shadow-inner',
                'barra' => 'bg-[#f97316]',
            ],
            [
                'titulo' => 'Cajeros', 'valor' => $totalCajero, 'icono' => 'cash-register',
                'bgIcono' => 'bg-purple-500/20 text-purple-400 border border-purple-500/30 shadow-inner',
                'barra' => 'bg-purple-500',
            ],
        ];
    @endphp

    {{-- GRID DE ESTADÍSTICAS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-5 relative z-10">
        @foreach($tarjetasStats as $stat)
        <div class="bg-[#0d3f66]/70 backdrop-blur-xl border border-[#8fc1f0]/20 shadow-[0_8px_30px_rgb(0,0,0,0.2)] rounded-xl md:rounded-[1.5rem] p-4 md:p-6 flex flex-col justify-between h-24 md:h-36 relative group overflow-hidden transition-all duration-300 hover:bg-[#0d3f66]/85 hover:border-[#8fc1f0]/30 hover:-translate-y-1 last:col-span-2 sm:last:col-span-1">
            
            <span class="absolute top-0 left-0 right-0 h-[3px] {{ $stat['barra'] }} opacity-70 group-hover:opacity-100 transition-opacity"></span>

            <div class="flex justify-between items-start w-full relative z-10">
                <h3 class="text-[9px] md:text-[10px] font-bold text-blue-200/80 uppercase tracking-widest truncate pr-2 mt-1">{{ $stat['titulo'] }}</h3>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-[0.5rem] md:rounded-xl {{ $stat['bgIcono'] }} flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110">
                    <i class="fas fa-{{ $stat['icono'] }} text-[10px] md:text-sm"></i>
                </div>
            </div>
            <p class="text-2xl md:text-[2.5rem] leading-none font-black text-white relative z-10 mt-2 md:mt-0 tabular-nums">{{ $stat['valor'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- CONTENEDOR PRINCIPAL DE EMPLEADOS --}}
    <div class="bg-gradient-to-br from-[#041d36]/70 to-[#021121]/80 backdrop-blur-2xl border border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.2)] rounded-2xl md:rounded-[2rem] p-4 sm:p-6 md:p-8 w-full flex-1 flex flex-col relative z-20">
        
        {{-- Cabecera de Tabla y Buscador --}}
        <div class="mb-4 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 relative z-30 pb-4 md:pb-6 border-b border-white/10">
            <div>
                <h2 class="text-lg md:text-2xl font-black text-white tracking-tight">Lista de Empleados</h2>
                <p class="text-[10px] md:text-xs font-medium text-blue-200/70 mt-1">{{ count($empleados ?? []) }} registrados en el sistema</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-none sm:w-72 group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-blue-200/50 text-sm group-focus-within:text-[#38b6ff] transition-colors"></i>
                    
                    <input type="text" id="buscadorEmpleados" data-teclado="texto" placeholder="Buscar empleado..." 
                        class="w-full h-11 bg-[#021121]/50 border border-white/10 rounded-full pl-11 pr-4 text-xs font-semibold text-white placeholder-blue-200/50 focus:border-[#38b6ff] focus:ring-1 focus:ring-[#38b6ff]/50 outline-none transition-all shadow-inner">
                </div>
                
                <a href="{{ request()->has('ver_inactivos') ? route('admin.empleados.index') : route('admin.empleados.index', ['ver_inactivos' => 1]) }}" 
                    title="{{ request()->has('ver_inactivos') ? 'Ver solo activos' : 'Ver inactivos' }}"
                    class="w-11 h-11 flex-shrink-0 flex items-center justify-center rounded-full border border-white/10 bg-white/5 hover:bg-white/10 hover:border-white/20 transition-all hover:-translate-y-0.5 {{ request()->has('ver_inactivos') ? 'text-rose-400' : 'text-blue-200/70' }} shadow-inner">
                    <i class="fas fa-{{ request()->has('ver_inactivos') ? 'eye-slash' : 'eye' }} text-sm"></i> 
                </a>
            </div>
        </div>

        {{-- ===================== VISTA MÓVIL ===================== --}}
        <div id="listaEmpleadosMovil" class="flex flex-col gap-3 sm:hidden relative z-20">
            @forelse($empleados ?? [] as $empleado)
                @php
                    $rolStr = mb_strtolower($empleado->rol?->nombre ?? '', 'UTF-8');
                    $colorClass = 'bg-white/10 text-blue-100 border border-white/20';
                    $dotClass = 'bg-blue-300';
                    
                    if (str_contains($rolStr, 'admin')) { $colorClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20'; $dotClass = 'bg-rose-500'; }
                    elseif (str_contains($rolStr, 'cajero')) { $colorClass = 'bg-purple-500/10 text-purple-400 border border-purple-500/20'; $dotClass = 'bg-purple-500'; }
                    elseif (str_contains($rolStr, 'mesero')) { $colorClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'; $dotClass = 'bg-emerald-500'; }
                    elseif (str_contains($rolStr, 'capitan') || str_contains($rolStr, 'capitán')) { $colorClass = 'bg-[#38b6ff]/10 text-[#38b6ff] border border-[#38b6ff]/20'; $dotClass = 'bg-[#38b6ff]'; }
                    elseif (str_contains($rolStr, 'cocinero')) { $colorClass = 'bg-[#f97316]/10 text-[#f97316] border border-[#f97316]/20'; $dotClass = 'bg-[#f97316]'; }
                @endphp
                <div class="fila-empleado-movil border border-white/10 rounded-2xl p-4 bg-white/[0.02] shadow-inner transition-all {{ !$empleado->esta_activo ? 'opacity-40 grayscale' : 'hover:bg-white/[0.05]' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff7a22] to-[#c2410c] border border-[#ff7a22]/20 flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-inner">
                                {{ strtoupper(substr($empleado->nombre, 0, 1)) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="nombre-empleado-txt font-bold text-sm text-white truncate">{{ $empleado->nombre }}</span>
                                <span class="text-[10px] font-medium text-blue-200/60 mt-0.5">EMP-{{ str_pad($empleado->id, 3, '0', STR_PAD_LEFT) }} · PIN {{ $empleado->codigo_empleado ?? '----' }}</span>
                            </div>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $colorClass }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                            {{ $empleado->rol?->nombre ?? 'Sin rol' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-white/10">
                        <a href="{{ route('admin.empleados.permisos', $empleado->id) }}" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-gradient-to-b from-[#ff7a22] to-[#ff7a22] border border-[#ff7a22]/20 text-[10px] font-bold uppercase text-white transition-all shadow-sm">
                            <i class="fas fa-shield-alt text-[#ff7a22] text-[11px]"></i> Permisos
                        </a>

                        @if($empleado->esta_activo)
                            <button type="button" title="Editar" onclick="window.ejecutarEditar(this)"
                                class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center border border-[#ff7a22]/30 bg-[#ff7a22]/10 text-[#ff7a22] hover:bg-[#ff7a22]/20 active:scale-95 transition-all shadow-inner"
                                data-id="{{ $empleado->id }}">
                                <i class="fas fa-pen text-[12px] pointer-events-none"></i>
                            </button>
                            <button type="button" onclick="abrirConfirmacionEliminar('form-delete-movil-{{ $empleado->id }}', false)" title="Desactivar"
                                class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center border border-black/30 bg-black/10 text-white/80 hover:bg-black/20 active:scale-95 transition-all shadow-inner">
                                <i class="fas fa-user-slash text-[12px] pointer-events-none"></i>
                            </button>
                            <form id="form-delete-movil-{{ $empleado->id }}" action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        @else
                            <form action="{{ route('admin.empleados.reactivar', $empleado->id) }}" method="POST" class="shrink-0">
                                @csrf @method('PATCH')
                                <button type="submit" title="Reactivar" class="h-10 w-10 rounded-xl flex items-center justify-center border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 active:scale-95 transition-all shadow-inner">
                                    <i class="fas fa-user-check text-[12px] pointer-events-none"></i>
                                </button>
                            </form>
                            <button type="button" onclick="abrirConfirmacionEliminar('form-delete-movil-{{ $empleado->id }}', true)" title="Eliminar Permanentemente"
                                class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center border border-rose-500/30 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 active:scale-95 transition-all shadow-inner">
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
                    <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-4">
                        <i class="fas fa-users-slash text-xl text-blue-200/50"></i>
                    </div>
                    <span class="text-sm font-bold text-blue-200/60">No hay empleados registrados</span>
                </div>
            @endforelse
        </div>

        {{-- ===================== VISTA ESCRITORIO ===================== --}}
        <div class="hidden sm:block w-full overflow-x-auto relative z-20 pb-4">
            <table class="w-full min-w-[700px] text-left border-collapse table-fixed">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="w-[30%] pb-4 px-4 text-[10px] font-bold text-blue-200/80 uppercase tracking-widest text-left">Nombre</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-bold text-blue-200/80 uppercase tracking-widest text-center">PIN</th>
                        <th class="w-[20%] pb-4 px-4 text-[10px] font-bold text-blue-200/80 uppercase tracking-widest text-center">Rol</th>
                        <th class="w-[20%] pb-4 px-4 text-[10px] font-bold text-blue-200/80 uppercase tracking-widest text-center">Permisos</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-bold text-blue-200/80 uppercase tracking-widest text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaEmpleados" class="divide-y divide-white/5">
                    @forelse($empleados ?? [] as $empleado)
                    
                    @php
                        $rolStr = mb_strtolower($empleado->rol?->nombre ?? '', 'UTF-8');
                        $colorClass = 'bg-white/10 text-blue-100 border border-white/20';
                        $dotClass = 'bg-blue-300';
                        
                        if (str_contains($rolStr, 'admin')) { $colorClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20'; $dotClass = 'bg-rose-500'; }
                        elseif (str_contains($rolStr, 'cajero')) { $colorClass = 'bg-purple-500/10 text-purple-400 border border-purple-500/20'; $dotClass = 'bg-purple-500'; }
                        elseif (str_contains($rolStr, 'mesero')) { $colorClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'; $dotClass = 'bg-emerald-500'; }
                        elseif (str_contains($rolStr, 'capitan') || str_contains($rolStr, 'capitán')) { $colorClass = 'bg-[#38b6ff]/10 text-[#38b6ff] border border-[#38b6ff]/20'; $dotClass = 'bg-[#38b6ff]'; }
                        elseif (str_contains($rolStr, 'cocinero')) { $colorClass = 'bg-[#f97316]/10 text-[#f97316] border border-[#f97316]/20'; $dotClass = 'bg-[#f97316]'; }
                    @endphp

                    <tr class="fila-empleado group hover:bg-white/[0.04] transition-all duration-200 {{ !$empleado->esta_activo ? 'opacity-40 grayscale' : '' }}">
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff7a22] to-[#c2410c] border border-[#ff7a22]/20 flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-inner">
                                    {{ strtoupper(substr($empleado->nombre, 0, 1)) }}
                                </div>
                                <div class="flex flex-col min-w-0 truncate">
                                    <span class="nombre-empleado-txt font-bold text-sm text-white truncate">{{ $empleado->nombre }}</span>
                                    <span class="text-[10px] font-medium text-blue-200/60 mt-0.5">ID: EMP-{{ str_pad($empleado->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 align-middle text-center">
                            <span class="inline-block font-black text-xs text-white tracking-[0.2em] bg-black/30 border border-white/5 px-3 py-1.5 rounded-lg shadow-inner">{{ $empleado->codigo_empleado ?? '----' }}</span>
                        </td>
                        
                        <td class="py-4 px-4 align-middle text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $empleado->rol?->nombre ?? 'Sin rol' }}
                            </span>
                        </td>
                        
                        <td class="py-4 px-4 align-middle text-center">
                            <a href="{{ route('admin.empleados.permisos', $empleado->id) }}" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-b from-[#ff7a22] to-[#ff7a22] border border-[#ff7a22]/20 text-[10px] font-bold uppercase text-white transition-all shadow-sm">
                                <i class="fas fa-shield-alt text-[#ff7a22] text-[10px]"></i> Configurar
                            </a>
                        </td>
                        
                        <td class="py-4 px-4 align-middle text-center">
                            <div class="flex items-center justify-center gap-2 relative z-30">
                                @if($empleado->esta_activo)
                                    <button type="button" title="Editar" onclick="window.ejecutarEditar(this)"
                                        class="h-9 w-9 rounded-xl flex items-center justify-center border border-[#ff7a22]/30 bg-[#ff7a22]/10 text-[#ff7a22] hover:bg-[#ff7a22]/20 transition-all shadow-inner outline-none flex-shrink-0"
                                        data-id="{{ $empleado->id }}">
                                        <i class="fas fa-pen text-[12px] pointer-events-none"></i>
                                    </button>
                                    
                                    <form id="form-delete-{{ $empleado->id }}" action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="abrirConfirmacionEliminar('form-delete-{{ $empleado->id }}', false)" title="Desactivar" 
                                            class="h-9 w-9 rounded-xl flex items-center justify-center border border-black/30 bg-black/10 text-white/80 hover:bg-black/20 transition-all shadow-inner outline-none flex-shrink-0">
                                            <i class="fas fa-user-slash text-[12px] pointer-events-none"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.empleados.reactivar', $empleado->id) }}" method="POST" class="m-0 p-0">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Reactivar" class="h-9 w-9 rounded-xl flex items-center justify-center border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-all shadow-inner outline-none flex-shrink-0">
                                            <i class="fas fa-user-check text-[12px] pointer-events-none"></i>
                                        </button>
                                    </form>
                                    
                                    <form id="form-delete-{{ $empleado->id }}" action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="abrirConfirmacionEliminar('form-delete-{{ $empleado->id }}', true)" title="Eliminar" 
                                            class="h-9 w-9 rounded-xl flex items-center justify-center border border-rose-500/30 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 transition-all shadow-inner outline-none flex-shrink-0">
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
                                <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-4">
                                    <i class="fas fa-users-slash text-xl text-blue-200/50"></i>
                                </div>
                                <span class="text-sm font-bold text-blue-200/60">No hay empleados registrados</span>
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
<div id="modal-confirmacion-eliminar" class="fixed inset-0 z-[999] hidden flex items-center justify-center bg-[#021121]/80 backdrop-blur-md px-4 transition-all duration-300">
    <div class="relative bg-gradient-to-br from-[#041d36] to-[#021121] border border-white/10 rounded-[2rem] p-6 sm:p-8 max-w-sm w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200 overflow-hidden text-center">
        
        <div id="glow-modal" class="absolute -top-24 -left-24 w-48 h-48 rounded-full blur-3xl pointer-events-none transition-colors duration-300"></div>

        <div class="flex justify-center mb-5 relative z-10">
            <div id="wrapper-icono" class="flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl border bg-white/5 shadow-inner transition-colors duration-300">
                <i id="icono-modal" class="fas text-2xl sm:text-3xl transition-colors duration-300"></i>
            </div>
        </div>
        
        <h2 id="titulo-confirmacion" class="text-lg sm:text-xl font-black text-white mb-2 tracking-tight relative z-10">¿Desactivar Empleado?</h2>
        <p id="texto-confirmacion" class="text-blue-200/70 text-sm mb-6 font-medium relative z-10">
            El empleado no podrá acceder al sistema, pero sus registros se mantendrán.
        </p>
        
        <div class="flex gap-3 relative z-10">
            <button type="button" onclick="cerrarConfirmacionEliminar()" class="flex-1 py-3.5 sm:py-4 px-4 bg-white/5 hover:bg-white/10 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl border border-white/10 transition-all outline-none active:scale-[0.98]">
                Cancelar
            </button>
            <button type="button" id="btn-ejecutar-eliminar" class="flex-1 py-3.5 sm:py-4 px-4 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all outline-none active:scale-[0.98] border shadow-inner">
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
            glow.className = "absolute -top-24 -left-24 w-48 h-48 bg-rose-500/20 rounded-full blur-3xl pointer-events-none";
            wrapperIcono.className = "flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl border border-rose-500/30 bg-rose-500/10 shadow-inner";
            icono.className = "fas fa-trash-alt text-rose-400 text-2xl sm:text-3xl";
            btnConfirmar.className = "flex-1 py-3.5 sm:py-4 px-4 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all outline-none active:scale-[0.98] border border-rose-500/30 bg-rose-500/20 hover:bg-rose-500/30 shadow-inner";
        } else {
            titulo.innerText = '¿Desactivar Empleado?';
            texto.innerText = 'El empleado no podrá acceder al sistema, pero sus datos se mantendrán.';
            glow.className = "absolute -top-24 -left-24 w-48 h-48 bg-black/20 rounded-full blur-3xl pointer-events-none";
            wrapperIcono.className = "flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl border border-black/30 bg-black/10 shadow-inner";
            icono.className = "fas fa-user-slash text-white/80 text-2xl sm:text-3xl";
            btnConfirmar.className = "flex-1 py-3.5 sm:py-4 px-4 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all outline-none active:scale-[0.98] border border-black/30 bg-black/20 hover:bg-black/30 shadow-inner";
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