@extends('layouts.admin')

@section('title', 'Privilegios de Acceso | Ollintem Pro')
@section('header-title', 'Privilegios de Acceso')
@section('header-subtitle', 'Administra los permisos del equipo')

@push('styles')
<style>
    /* Fondo general del sistema en gris extra claro */
    body, html, #app, main, .wrapper, .main-content {
        background-color: #F2F2F2 !important; 
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
<div class="p-3 sm:p-6 lg:p-8 xl:p-12 max-w-[1600px] mx-auto w-full space-y-6 sm:space-y-8 flex-1 flex flex-col transition-all duration-300 relative z-10">
    
    <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[2rem] shadow-sm overflow-hidden animate-fade-in-up" style="animation-delay: 0ms;">
        <form action="{{ route('admin.empleados.permisos.update', $empleado->id) }}" method="POST" id="permisosForm">
            @csrf
            
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[640px] sm:min-w-[760px] border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th class="py-4 px-4 sm:py-5 sm:px-6 lg:py-6 lg:px-8 text-[9px] sm:text-[10px] lg:text-[11px] font-black uppercase tracking-widest text-left whitespace-nowrap text-slate-500">Módulos</th>
                            @php $permisosHeader = ['Mostrar', 'Crear', 'Editar', 'Eliminar', 'Gestionar']; @endphp
                            @foreach($permisosHeader as $p)
                            <th class="py-4 px-2 sm:py-5 sm:px-3 lg:py-6 lg:px-4 text-[9px] sm:text-[10px] lg:text-[11px] font-black uppercase tracking-widest text-center whitespace-nowrap text-slate-500">{{ $p }}</th>
                            @endforeach
                            <th class="py-4 px-4 sm:py-5 sm:px-6 lg:py-6 lg:px-8 text-[9px] sm:text-[10px] lg:text-[11px] font-black text-blue-600 uppercase tracking-widest text-center whitespace-nowrap">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                            // Mapeo dinámico de iconos basado en el nombre del módulo (en minúsculas)
                            $iconos = [
                                'dashboard'          => 'fa-th-large',
                                'inventario'         => 'fa-cube',
                                'empleados'          => 'fa-users',
                                'productos'          => 'fa-utensils',
                                'categorías'         => 'fa-layer-group',
                                'categorias'         => 'fa-layer-group',
                                'mesas'              => 'fa-chair',
                                'promociones'        => 'fa-tags',
                                'cocina'             => 'fa-fire-burner',
                                'caja'               => 'fa-cash-register',
                                'finanzas'           => 'fa-chart-line',
                                'roles'              => 'fa-id-badge',
                                'historial de cajas' => 'fa-history'
                            ];
                        @endphp

                        {{-- Iteramos sobre los módulos pasados desde el EmpleadoController --}}
                        @foreach($modulos as $modulo)
                            @php
                                $nombreModulo = strtolower($modulo->nombre);
                                $icono = $iconos[$nombreModulo] ?? 'fa-circle';
                            @endphp

                            <tr class="modulo-row group hover:bg-blue-50/50 transition-all duration-300">
                                <td class="py-3 px-4 sm:py-4 sm:px-6 lg:py-5 lg:px-8">
                                    <div class="flex items-center gap-3 sm:gap-4 lg:gap-5">
                                        <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-11 lg:h-11 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center shrink-0 group-hover:text-blue-600 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all duration-300 text-slate-400">
                                            <i class="fas {{ $icono }} text-xs sm:text-sm lg:text-base"></i>
                                        </div>
                                        <span class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-900 group-hover:text-blue-900 transition-colors whitespace-nowrap">{{ $modulo->nombre }}</span>
                                    </div>
                                </td>

                                @php
                                    // Validamos qué permisos tiene ya guardados este usuario para el módulo actual
                                    $permisoActual = $empleado->permisos->where('modulo_id', $modulo->id)->first();
                                @endphp

                                @foreach(['mostrar', 'crear', 'editar', 'eliminar', 'gestionar'] as $accion)
                                    <td class="py-3 px-2 sm:py-4 sm:px-3 lg:py-5 lg:px-4">
                                        <label class="relative flex items-center justify-center cursor-pointer group/check">
                                            <input type="checkbox" 
                                                name="permisos[{{ $modulo->id }}][{{ $accion }}]" 
                                                value="1"
                                                class="permiso-checkbox peer sr-only"
                                                {{ ($permisoActual && $permisoActual->$accion) ? 'checked' : '' }}>
                                            
                                            {{-- Contenedor con efecto visual moderno --}}
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 lg:w-9 lg:h-9 rounded-xl border-2 transition-all duration-300 ease-out 
                                                flex items-center justify-center
                                                bg-slate-100 border-slate-200 
                                                peer-checked:bg-blue-600 peer-checked:border-blue-600 
                                                peer-checked:shadow-md peer-checked:shadow-blue-500/20
                                                peer-checked:scale-105 
                                                group-hover/check:border-blue-300 group-hover/check:bg-blue-50/50">
                                                
                                                {{-- Icono Check animado --}}
                                                <i class="fas fa-check text-white text-[10px] sm:text-xs font-black opacity-0 scale-50 transition-all duration-300 peer-checked:opacity-100 peer-checked:scale-100"></i>
                                            </div>
                                        </label>
                                    </td>
                                @endforeach

                                <td class="py-3 px-4 sm:py-4 sm:px-6 lg:py-5 lg:px-8 text-center">
                                    <button type="button" class="toggle-row text-[10px] sm:text-xs font-black uppercase tracking-wider text-slate-400 hover:text-blue-600 transition-colors outline-none whitespace-nowrap bg-slate-50 hover:bg-blue-50 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-blue-200">
                                        Todos
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 sm:p-6 lg:p-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 bg-slate-50/50">
                <div class="flex items-center gap-3 order-2 md:order-1">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse shrink-0"></div>
                    <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-slate-500">
                        Seguridad de Acceso Nivel: <span class="text-slate-800">Granular</span>
                    </p>
                </div>
                
                <div class="flex gap-3 w-full md:w-auto order-1 md:order-2">
                    <button type="reset" class="flex-1 md:flex-none px-6 py-3 rounded-xl text-xs font-black uppercase tracking-wider text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-all border border-slate-200">
                        Limpiar
                    </button>
                    <button type="submit" class="flex-1 md:flex-none px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save text-xs"></i>
                        Confirmar Privilegios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-row').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const checkboxes = row.querySelectorAll('.permiso-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            
            this.classList.add('text-blue-600', 'bg-blue-50', 'border-blue-200');
            setTimeout(() => { 
                this.classList.remove('text-blue-600', 'bg-blue-50', 'border-blue-200'); 
            }, 300);
        });
    });
</script>
@endsection