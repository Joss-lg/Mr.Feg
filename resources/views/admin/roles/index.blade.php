@extends('layouts.admin')

@section('title', 'Roles y Puestos | Ollintem Pro')
@section('header-title', 'Roles')
@section('header-subtitle', 'Administra el catálogo de roles')

@push('styles')
<style>
    /* Fondo general del sistema en gris extra claro */
    body, html, #app, main, .wrapper, .main-content {
        background-color: #f8fafc !important; /* slate-50 */
    }
    
    /* Textos del header superior en oscuro */
    header, header h1, header h2, header p, header span, .header-title, .header-subtitle {
        color: #0f172a !important; /* slate-900 */
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
        color: #64748b !important; /* slate-500 */
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

<div class="px-3 sm:px-6 lg:px-8 py-5 sm:py-8 w-full max-w-7xl mx-auto space-y-5 sm:space-y-8 relative z-10">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-6 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="w-full sm:w-auto">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-blue-600 shadow-md shadow-blue-500/20 shrink-0">
                    <i class="fas fa-sitemap text-white text-sm"></i>
                </div>
                <h1 class="text-xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Catálogo de Roles
                </h1>
            </div>
            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1.5 sm:ml-[3.1rem]">
                Administra los roles para el personal del restaurante
            </p>
        </div>

        @if(auth()->user()->tienePermiso('roles.crear'))
            <button type="button"
                    onclick="abrirModalCrear()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 sm:py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] outline-none group">
                <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform duration-300"></i>
                Nuevo Rol
            </button>
        @endif
    </div>

    {{-- CONTENEDOR PRINCIPAL --}}
    <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[2rem] p-3.5 sm:p-8 shadow-sm transition-all duration-300 relative z-20 animate-fade-in-up" style="animation-delay: 150ms;">

        <div class="flex items-center justify-between mb-3.5 sm:mb-6 px-1 sm:px-0">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Roles Activos</h3>
            <span class="hidden sm:inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200">
                <i class="fas fa-layer-group text-[10px]"></i>
                {{ $roles->count() }} {{ $roles->count() == 1 ? 'rol' : 'roles' }}
            </span>
        </div>

        {{-- ===================== VISTA MÓVIL: TARJETAS (solo < sm) ===================== --}}
        <div class="flex flex-col gap-2.5 sm:hidden">
            @forelse($roles as $rol)
            <div class="group relative overflow-hidden border border-slate-100 rounded-2xl p-3.5 bg-white hover:bg-blue-50/50 shadow-sm hover:shadow-md hover:border-blue-100 transition-all duration-300">
                <span class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 {{ $rol->usuarios_count > 0 ? 'opacity-100' : 'opacity-30' }}"></span>
                <div class="flex items-center justify-between gap-3 pl-1.5">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 shrink-0">
                            <i class="fas fa-user-shield text-sm"></i>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-sm text-slate-900 truncate group-hover:text-blue-900 transition-colors">{{ $rol->nombre }}</span>
                            <span class="text-[10px] font-bold text-slate-500 mt-0.5 uppercase tracking-wide group-hover:text-blue-500/80 transition-colors">{{ $rol->usuarios_count }} empleado{{ $rol->usuarios_count == 1 ? '' : 's' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if(auth()->user()->tienePermiso('roles.editar'))
                            <button onclick="abrirModalEditar(this)" data-id="{{ $rol->id }}" data-nombre="{{ $rol->nombre }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-blue-200 hover:bg-blue-500 hover:text-white text-blue-500 active:scale-95 transition-all outline-none shadow-sm hover:shadow-md hover:shadow-blue-500/20"><i class="fas fa-pen text-xs"></i></button>
                        @endif
                        @if(auth()->user()->tienePermiso('roles.eliminar'))
                            <button onclick="abrirModalEliminar(this)" data-id="{{ $rol->id }}" data-nombre="{{ $rol->nombre }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-rose-200 hover:bg-rose-500 hover:text-white text-rose-500 active:scale-95 transition-all outline-none shadow-sm hover:shadow-md hover:shadow-rose-500/20"><i class="fas fa-trash-alt text-xs"></i></button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-14 h-14 flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-100 text-slate-300 mb-3">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-400">No hay roles registrados.</p>
            </div>
            @endforelse
        </div>

        {{-- ===================== VISTA ESCRITORIO: TABLA ===================== --}}
        <div class="hidden sm:block w-full overflow-x-auto pb-2">
            <table class="w-full min-w-[500px] text-left border-collapse table-fixed">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="w-[40%] text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 pl-2">Rol</th>
                        <th class="w-[30%] text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-center">Empleados</th>
                        <th class="w-[30%] text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-right pr-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($roles as $rol)
                    <tr class="group hover:bg-blue-50/50 transition-colors duration-300">
                        <td class="py-4 sm:py-5 pl-2">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-700 font-black text-sm group-hover:text-white group-hover:bg-blue-600 transition-colors duration-300 shrink-0">
                                    <i class="fas fa-user-shield text-xs sm:text-sm"></i>
                                </div>
                                <span class="font-bold text-sm sm:text-base text-slate-900 group-hover:text-blue-900 transition-colors">{{ $rol->nombre }}</span>
                            </div>
                        </td>
                        <td class="py-4 sm:py-5 text-center">
                            <span class="inline-flex items-center justify-center font-black px-3 py-1 rounded-lg text-xs sm:text-sm {{ $rol->usuarios_count > 0 ? 'text-blue-700 bg-blue-100 group-hover:bg-white group-hover:border group-hover:border-blue-100' : 'text-slate-500 bg-slate-100 group-hover:bg-white group-hover:border group-hover:border-slate-200' }} transition-colors">
                                {{ $rol->usuarios_count }}
                            </span>
                        </td>
                        <td class="py-4 sm:py-5 text-right pr-2 sm:pr-4">
                            <div class="flex items-center justify-end gap-2 relative z-30">
                                @if(auth()->user()->tienePermiso('roles.editar'))
                                    <button onclick="abrirModalEditar(this)" data-id="{{ $rol->id }}" data-nombre="{{ $rol->nombre }}" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl bg-white border border-blue-200 text-blue-500 hover:bg-blue-500 hover:text-white hover:shadow-md hover:shadow-blue-500/20 active:scale-95 transition-all duration-300 outline-none flex-shrink-0"><i class="fas fa-pen text-xs"></i></button>
                                @endif
                                @if(auth()->user()->tienePermiso('roles.eliminar'))
                                    {{-- FIX: AGREGADO data-nombre="{{ $rol->nombre }}" --}}
                                    <button onclick="abrirModalEliminar(this)" data-id="{{ $rol->id }}" data-nombre="{{ $rol->nombre }}" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl bg-white border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white hover:shadow-md hover:shadow-rose-500/20 active:scale-95 transition-all duration-300 outline-none flex-shrink-0"><i class="fas fa-trash-alt text-xs"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-12 sm:py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-100 text-slate-300 mb-3">
                                    <i class="fas fa-user-shield text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-400">No hay roles registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('admin.roles.modal-crear')
@include('admin.roles.modal-editar')
@include('admin.roles.modal-eliminar')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modales = ['modalCrearRol', 'modalEditarRol', 'modalEliminarRol'];
        modales.forEach(id => {
            const modalElement = document.getElementById(id);
            if (modalElement) document.body.appendChild(modalElement);
        });
    });
</script>
@endpush