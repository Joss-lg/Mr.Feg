@extends('layouts.admin')

@section('title', 'Finanzas | Ollintem Pro')

@section('header-title', 'Finanzas y Contabilidad')
@section('header-subtitle', 'Gestión de ingresos, egresos y balance general')

@push('styles')
<style>
    body, html, #app, main, .wrapper, .main-content {
        background-color: #f8fafc !important; 
    }
    
    header, header h1, header h2, header p, header span, .header-title, .header-subtitle {
        color: #0f172a !important; 
    }

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
<div class="p-3 sm:p-6 lg:p-8 xl:p-12 max-w-[1800px] mx-auto w-full space-y-5 sm:space-y-8 flex-1 flex flex-col transition-all duration-300 relative z-10">

    {{-- CABECERA Y BOTONES --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end gap-3 sm:gap-6 mb-2 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-1 w-full sm:w-auto">
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Finanzas</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500">Control centralizado de ingresos y egresos | <span class="text-slate-700 font-bold capitalize">{{ now()->locale('es')->translatedFormat('F Y') }}</span></p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 w-full xl:w-auto">
            <a href="{{ route('admin.finanzas.corte.mensual') }}" class="w-full sm:w-auto bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-5 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] shadow-sm hover:shadow-md hover:shadow-blue-500/10">
                <i class="fas fa-calendar-check"></i> Corte Mensual
            </a>

            <a href="{{ route('admin.finanzas.meseros') }}" class="w-full sm:w-auto bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-5 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] shadow-sm hover:shadow-md hover:shadow-blue-500/10">
                <i class="fas fa-user-tie"></i> Meseros
            </a>

            @if(auth()->user()->tienePermiso('finanzas.mostrar'))
                <a href="{{ route('admin.finanzas.exportar') }}" class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 px-5 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] shadow-sm hover:shadow-md">
                    <i class="fas fa-download text-slate-400"></i> Exportar CSV
                </a>
            @endif

            @if(auth()->user()->tienePermiso('finanzas.editar'))
                <button onclick="openModal('modalCrearNomina', 'createNominaContainer')" class="w-full sm:w-auto bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 px-5 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] shadow-sm hover:shadow-md hover:shadow-purple-500/10">
                    <i class="fas fa-users"></i> Pagar Nómina
                </button>
            @endif

            @if(auth()->user()->tienePermiso('finanzas.crear'))
                <button onclick="openModal('modalCrearGasto', 'createGastoContainer')" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 px-6 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 active:translate-y-0 active:scale-[0.98] group">
                    <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i> Nuevo Gasto
                </button>
            @endif
        </div>
    </div>

    {{-- TARJETAS DE INDICADORES (KPIs) --}}
    <div class="grid grid-cols-1 min-[420px]:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5">
        {{-- Ingresos --}}
        <div class="relative overflow-hidden bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-4 md:p-6 group hover:border-emerald-200 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-emerald-900/5 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 100ms;">
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] bg-emerald-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            
            <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] group-hover:scale-110 transition-all duration-300">
                <i class="fas fa-arrow-up text-5xl sm:text-6xl text-emerald-500"></i>
            </div>
            <div class="flex items-center justify-between mb-3 sm:mb-4 relative z-10 mt-1">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                    <i class="fas fa-arrow-up text-sm sm:text-lg"></i>
                </div>
                <span class="px-2 sm:px-2.5 py-1 bg-emerald-50 border border-emerald-100 rounded-full text-[8px] sm:text-[10px] font-black text-emerald-700 uppercase tracking-widest">Este Mes</span>
            </div>
            <div class="relative z-10">
                <p class="text-emerald-600 text-[9px] sm:text-[11px] font-bold uppercase tracking-widest mb-1 group-hover:text-emerald-700 transition-colors">Ingresos</p>
                <h3 class="text-xl sm:text-[2.75rem] leading-none font-black text-slate-900 break-all tabular-nums">$ {{ number_format($ingresosMes, 2) }}</h3>
                <p class="text-[9px] sm:text-[11px] font-medium text-slate-400 mt-2">Ventas registradas</p>
            </div>
        </div>

        {{-- Egresos --}}
        <div class="relative overflow-hidden bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-4 md:p-6 group hover:border-rose-200 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-rose-900/5 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 200ms;">
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] bg-rose-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            
            <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] group-hover:scale-110 transition-all duration-300">
                <i class="fas fa-arrow-down text-5xl sm:text-6xl text-rose-500"></i>
            </div>
            <div class="flex items-center justify-between mb-3 sm:mb-4 relative z-10 mt-1">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100">
                    <i class="fas fa-arrow-down text-sm sm:text-lg"></i>
                </div>
                <span class="px-2 sm:px-2.5 py-1 bg-rose-50 border border-rose-100 rounded-full text-[8px] sm:text-[10px] font-black text-rose-700 uppercase tracking-widest">Este Mes</span>
            </div>
            <div class="relative z-10">
                <p class="text-rose-600 text-[9px] sm:text-[11px] font-bold uppercase tracking-widest mb-1 group-hover:text-rose-700 transition-colors">Egresos</p>
                <h3 class="text-xl sm:text-[2.75rem] leading-none font-black text-slate-900 break-all tabular-nums">$ {{ number_format($egresosMes, 2) }}</h3>
                <p class="text-[9px] sm:text-[11px] font-medium text-slate-400 mt-2">Gastos registrados</p>
            </div>
        </div>

        {{-- Balance --}}
        <div class="relative overflow-hidden bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-4 md:p-6 group hover:border-blue-200 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-blue-900/5 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 300ms;">
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] bg-blue-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            
            <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] group-hover:scale-110 transition-all duration-300">
                <i class="fas fa-wallet text-5xl sm:text-6xl text-blue-500"></i>
            </div>
            <div class="flex items-center justify-between mb-3 sm:mb-4 relative z-10 mt-1">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                    <i class="fas fa-chart-line text-sm sm:text-lg"></i>
                </div>
                <span class="px-2 sm:px-2.5 py-1 bg-blue-50 border border-blue-100 rounded-full text-[8px] sm:text-[10px] font-black text-blue-700 uppercase tracking-widest">Balance</span>
            </div>
            <div class="relative z-10">
                <p class="text-blue-600 text-[9px] sm:text-[11px] font-bold uppercase tracking-widest mb-1 group-hover:text-blue-700 transition-colors">Neto</p>
                <h3 class="text-xl sm:text-[2.75rem] leading-none font-black break-all tabular-nums {{ $balanceNeto >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                    $ {{ number_format($balanceNeto, 2) }}
                </h3>
                <p class="text-[9px] sm:text-[11px] font-medium mt-2 {{ $balanceNeto >= 0 ? 'text-slate-400' : 'text-rose-400' }}">
                    {{ $balanceNeto >= 0 ? 'Superávit registrado' : 'Déficit registrado' }}
                </p>
            </div>
        </div>

        {{-- Pendiente Nómina --}}
        <div class="relative overflow-hidden bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-4 md:p-6 group hover:border-purple-200 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-purple-900/5 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 400ms;">
            <span class="absolute top-0 left-0 right-0 h-[3px] md:h-[4px] bg-purple-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            
            <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] group-hover:scale-110 transition-all duration-300">
                <i class="fas fa-clock text-5xl sm:text-6xl text-purple-500"></i>
            </div>
            <div class="flex items-center justify-between mb-3 sm:mb-4 relative z-10 mt-1">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 border border-purple-100">
                    <i class="fas fa-clock text-sm sm:text-lg"></i>
                </div>
                <span class="px-2 sm:px-2.5 py-1 bg-purple-50 border border-purple-100 rounded-full text-[8px] sm:text-[10px] font-black text-purple-700 uppercase tracking-widest">Este Mes</span>
            </div>
            <div class="relative z-10">
                <p class="text-purple-600 text-[9px] sm:text-[11px] font-bold uppercase tracking-widest mb-1 group-hover:text-purple-700 transition-colors">Nómina</p>
                <h3 class="text-xl sm:text-[2.75rem] leading-none font-black text-slate-900 break-all tabular-nums">$ {{ number_format($nominaPagada, 2) }}</h3>
                <p class="text-[9px] sm:text-[11px] font-medium text-slate-400 mt-2">Pagada a empleados</p>
            </div>
        </div>
    </div>

    {{-- PESTAÑAS (TABS) --}}
    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 p-1.5 bg-slate-100/50 border border-slate-200/60 rounded-2xl w-full sm:w-fit animate-fade-in-up" style="animation-delay: 500ms;">
        <a href="{{ route('admin.finanzas.index', ['tab' => 'todos']) }}"
            class="flex-1 sm:flex-none text-center px-4 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex justify-center items-center gap-2 {{ $tab === 'todos' ? 'bg-white text-blue-600 shadow-sm border border-slate-200/80' : 'text-slate-500 hover:text-blue-600 hover:bg-white/50 border border-transparent' }}">
            <i class="fas fa-list {{ $tab === 'todos' ? 'text-blue-500' : 'text-slate-400' }}"></i> Todos
        </a>
        <a href="{{ route('admin.finanzas.index', ['tab' => 'ingresos']) }}"
            class="flex-1 sm:flex-none text-center px-4 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex justify-center items-center gap-2 {{ $tab === 'ingresos' ? 'bg-white text-emerald-600 shadow-sm border border-slate-200/80' : 'text-slate-500 hover:text-emerald-600 hover:bg-white/50 border border-transparent' }}">
            <i class="fas fa-arrow-up {{ $tab === 'ingresos' ? 'text-emerald-500' : 'text-slate-400' }}"></i> Ingresos
        </a>
        <a href="{{ route('admin.finanzas.index', ['tab' => 'egresos']) }}"
            class="flex-1 sm:flex-none text-center px-4 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex justify-center items-center gap-2 {{ $tab === 'egresos' ? 'bg-white text-rose-600 shadow-sm border border-slate-200/80' : 'text-slate-500 hover:text-rose-600 hover:bg-white/50 border border-transparent' }}">
            <i class="fas fa-arrow-down {{ $tab === 'egresos' ? 'text-rose-500' : 'text-slate-400' }}"></i> Egresos
        </a>
    </div>

    {{-- TABLA DE HISTORIAL --}}
    <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[2rem] shadow-sm p-4 sm:p-6 lg:p-8 w-full flex-1 relative z-20 overflow-hidden animate-fade-in-up" style="animation-delay: 600ms;">
        
        <div class="mb-4 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 md:pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fas fa-exchange-alt text-base sm:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight">Historial de Transacciones</h2>
                    <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">Total: {{ $flujosCaja->total() }} registros</p>
                </div>
            </div>
            
            <div class="relative w-full sm:w-72 group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors duration-300"></i>
                <input type="text" id="buscadorFlujo" data-teclado="texto" placeholder="Buscar concepto..." 
                    class="w-full h-11 bg-slate-50 border border-slate-200 rounded-full pl-11 pr-4 text-xs font-semibold text-slate-700 focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all duration-300 placeholder:text-slate-400 shadow-sm">
            </div>
        </div>

        {{-- ===================== VISTA MÓVIL: TARJETAS (solo < sm) ===================== --}}
        <div class="flex flex-col gap-3 sm:hidden">
            @forelse($flujosCaja as $flujo)
            <div class="fila-flujo-movil border border-slate-100 rounded-2xl p-4 bg-white shadow-sm hover:shadow-md hover:border-blue-100 hover:bg-blue-50/30 transition-all duration-300">
                <div class="flex items-start justify-between gap-2 mb-3">
                    @if($flujo->tipo === 'ingreso')
                        <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-full text-[9px] font-black uppercase tracking-wider flex items-center gap-1.5 w-fit shrink-0">
                            <i class="fas fa-arrow-up text-[9px]"></i> Ingreso
                        </span>
                    @else
                        <span class="px-2.5 py-1 bg-rose-50 border border-rose-100 text-rose-700 rounded-full text-[9px] font-black uppercase tracking-wider flex items-center gap-1.5 w-fit shrink-0">
                            <i class="fas fa-arrow-down text-[9px]"></i> Egreso
                        </span>
                    @endif
                    <span class="text-[10px] font-bold text-slate-400 shrink-0">{{ $flujo->fecha->format('d M, Y') }}</span>
                </div>

                <p class="concepto-celda-movil text-sm font-bold text-slate-800 mb-3">{{ $flujo->concepto }}</p>

                <div class="flex items-center justify-between gap-2 flex-wrap pt-3 border-t border-slate-50">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="px-2 py-1 bg-slate-50 rounded-lg text-[9px] font-bold text-slate-600 uppercase tracking-widest border border-slate-200">
                            {{ $flujo->categoria }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400">
                            <i class="fas fa-credit-card text-[9px] opacity-70"></i> {{ $flujo->metodo_pago }}
                        </span>
                    </div>
                    <span class="font-black text-sm {{ $flujo->tipo === 'ingreso' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $flujo->tipo === 'ingreso' ? '+' : '-' }}${{ number_format($flujo->monto, 2) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="py-12 text-center bg-slate-50 rounded-2xl border border-slate-100">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <i class="fas fa-folder-open text-3xl mb-3 opacity-40"></i>
                    <p class="text-xs font-medium">No hay registros de flujo de caja aún.</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- ===================== VISTA ESCRITORIO: TABLA (solo sm+) ===================== --}}
        <div class="hidden sm:block overflow-x-auto rounded-xl sm:rounded-2xl border border-slate-100 pb-2">
            <table class="w-full min-w-[700px] text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 sm:py-4 px-4 sm:px-5 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha</th>
                        <th class="py-3 sm:py-4 px-4 sm:px-5 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipo</th>
                        <th class="py-3 sm:py-4 px-4 sm:px-5 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Categoría</th>
                        <th class="py-3 sm:py-4 px-4 sm:px-5 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Concepto</th>
                        <th class="py-3 sm:py-4 px-4 sm:px-5 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Método</th>
                        <th class="py-3 sm:py-4 px-4 sm:px-5 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Monto</th>
                    </tr>
                </thead>
                <tbody id="tablaFlujoCaja" class="divide-y divide-slate-50">
                    @forelse($flujosCaja as $flujo)
                    <tr class="fila-flujo hover:bg-blue-50/50 transition-colors duration-300 group cursor-pointer">
                        
                        <td class="py-4 px-4 sm:px-5 text-xs sm:text-sm font-medium text-slate-500 group-hover:text-blue-900 transition-colors">
                            {{ $flujo->fecha->format('d M, Y') }}
                        </td>

                        <td class="py-4 px-4 sm:px-5">
                            @if($flujo->tipo === 'ingreso')
                                <span class="px-2.5 sm:px-3 py-1 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5 w-fit group-hover:bg-white transition-colors">
                                    <i class="fas fa-arrow-up text-[9px] sm:text-[10px]"></i> Ingreso
                                </span>
                            @else
                                <span class="px-2.5 sm:px-3 py-1 bg-rose-50 border border-rose-100 text-rose-700 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5 w-fit group-hover:bg-white transition-colors">
                                    <i class="fas fa-arrow-down text-[9px] sm:text-[10px]"></i> Egreso
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-4 sm:px-5">
                            <span class="px-2.5 sm:px-3 py-1 sm:py-1.5 bg-slate-50 rounded-lg text-[9px] sm:text-[10px] font-bold text-slate-600 uppercase tracking-widest border border-slate-200 group-hover:bg-white group-hover:border-blue-200 group-hover:text-blue-700 transition-colors">
                                {{ $flujo->categoria }}
                            </span>
                        </td>

                        <td class="py-4 px-4 sm:px-5 text-xs sm:text-sm font-bold text-slate-800 concepto-celda group-hover:text-blue-900 transition-colors">
                            {{ $flujo->concepto }}
                        </td>

                        <td class="py-4 px-4 sm:px-5">
                            <div class="flex items-center gap-1.5 sm:gap-2 text-slate-400 group-hover:text-blue-500/80 transition-colors">
                                <i class="fas fa-credit-card text-[10px] sm:text-xs"></i>
                                <span class="text-[11px] sm:text-xs font-semibold">{{ $flujo->metodo_pago }}</span>
                            </div>
                        </td>

                        <td class="py-4 px-4 sm:px-5 text-right font-black text-sm sm:text-base {{ $flujo->tipo === 'ingreso' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $flujo->tipo === 'ingreso' ? '+' : '-' }}${{ number_format($flujo->monto, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 sm:py-16 text-center bg-slate-50 border border-slate-100">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <i class="fas fa-folder-open text-3xl sm:text-4xl mb-3 opacity-40"></i>
                                <p class="text-xs sm:text-sm font-medium">No hay registros de flujo de caja aún.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 sm:mt-6 flex justify-between items-center text-xs sm:text-sm overflow-x-auto">
            {{ $flujosCaja->links() }}
        </div>
    </div>

    {{-- RESUMEN POR CATEGORÍAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 relative z-20 animate-fade-in-up" style="animation-delay: 700ms;">
        
        {{-- Resumen Ingresos --}}
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[2rem] shadow-sm p-4 sm:p-6 lg:p-8 hover:shadow-md transition-shadow duration-300">
            <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4 pb-4 border-b border-slate-50">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <i class="fas fa-chart-pie text-base sm:text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Ingresos por Categoría</h3>
                    <p class="text-[10px] sm:text-xs font-medium text-slate-500 mt-0.5">Distribución del período</p>
                </div>
            </div>

            <div class="space-y-2.5 sm:space-y-3">
                @forelse($categoriasIngresos as $cat)
                <div class="flex items-center justify-between gap-2 p-3 sm:p-4 bg-slate-50 border border-slate-100 rounded-xl sm:rounded-2xl hover:bg-emerald-50/50 hover:border-emerald-100 hover:-translate-y-0.5 hover:shadow-sm transition-all duration-300 group">
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                        <div class="w-1.5 sm:w-2 h-6 sm:h-8 bg-emerald-400 rounded-full shrink-0 group-hover:scale-y-110 transition-transform"></div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-bold text-slate-800 truncate group-hover:text-emerald-700 transition-colors">{{ $cat->categoria }}</p>
                            <p class="text-[10px] sm:text-xs font-medium text-slate-500 mt-0.5">{{ $cat->cantidad }} transacciones</p>
                        </div>
                    </div>
                    <p class="text-sm sm:text-lg font-black text-emerald-600 shrink-0 tabular-nums">$ {{ number_format($cat->total, 2) }}</p>
                </div>
                @empty
                <div class="py-6 sm:py-8 text-center bg-slate-50 rounded-xl sm:rounded-2xl border border-dashed border-slate-200">
                    <p class="text-xs sm:text-sm font-medium text-slate-400">Sin ingresos este período</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Resumen Egresos --}}
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[2rem] shadow-sm p-4 sm:p-6 lg:p-8 hover:shadow-md transition-shadow duration-300">
            <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4 pb-4 border-b border-slate-50">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                    <i class="fas fa-chart-pie text-base sm:text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Egresos por Categoría</h3>
                    <p class="text-[10px] sm:text-xs font-medium text-slate-500 mt-0.5">Distribución del período</p>
                </div>
            </div>

            <div class="space-y-2.5 sm:space-y-3">
                @forelse($categoriasEgresos as $cat)
                <div class="flex items-center justify-between gap-2 p-3 sm:p-4 bg-slate-50 border border-slate-100 rounded-xl sm:rounded-2xl hover:bg-rose-50/50 hover:border-rose-100 hover:-translate-y-0.5 hover:shadow-sm transition-all duration-300 group">
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                        <div class="w-1.5 sm:w-2 h-6 sm:h-8 bg-rose-400 rounded-full shrink-0 group-hover:scale-y-110 transition-transform"></div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-bold text-slate-800 truncate group-hover:text-rose-700 transition-colors">{{ $cat->categoria }}</p>
                            <p class="text-[10px] sm:text-xs font-medium text-slate-500 mt-0.5">{{ $cat->cantidad }} transacciones</p>
                        </div>
                    </div>
                    <p class="text-sm sm:text-lg font-black text-rose-600 shrink-0 tabular-nums">$ {{ number_format($cat->total, 2) }}</p>
                </div>
                @empty
                <div class="py-6 sm:py-8 text-center bg-slate-50 rounded-xl sm:rounded-2xl border border-dashed border-slate-200">
                    <p class="text-xs sm:text-sm font-medium text-slate-400">Sin egresos este período</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

</div>

@if(auth()->user()->tienePermiso('finanzas.crear'))
    @include('admin.finanzas.modal-crear-gasto')
@endif

@if(auth()->user()->tienePermiso('finanzas.editar'))
    @include('admin.finanzas.modal-crear-nomina')
@endif
@endsection

@include('partials.teclado-virtual')
<script src="{{ asset('js/teclado-virtual.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- MOVER MODALES AL BODY ---
        const modales = ['modalCrearGasto', 'modalCrearNomina']; 
        modales.forEach(id => {
            const modalElement = document.getElementById(id);
            if (modalElement) document.body.appendChild(modalElement);
        });

        // --- BUSCADOR ---
        const buscador = document.getElementById('buscadorFlujo');
        const filas = document.querySelectorAll('.fila-flujo, .fila-flujo-movil');
        if (buscador) {
            buscador.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                filas.forEach(fila => {
                    const celda = fila.querySelector('.concepto-celda') || fila.querySelector('.concepto-celda-movil');
                    if (celda) fila.classList.toggle('hidden', !celda.textContent.toLowerCase().includes(term));
                });
            });
        }
    });

    // --- LÓGICA DE NÓMINA (Calculadora) ---
    function actualizarSueldo() {
        const select = document.getElementById('empleadoSelect');
        const inputSueldo = document.getElementById('sueldoBase');
        if (!select || !inputSueldo) return;
        const opcion = select.options[select.selectedIndex];
        inputSueldo.value = (parseFloat(opcion.dataset.sueldo) || 0).toFixed(2);
        calcularMonto();
    }

    function calcularMonto() {
        const sueldo = parseFloat(document.getElementById('sueldoBase')?.value || 0);
        const bonos = parseFloat(document.querySelector('input[name="bonos"]')?.value || 0);
        const deduc = parseFloat(document.querySelector('input[name="deducciones"]')?.value || 0);
        const neto = sueldo + bonos - deduc;
        const span = document.getElementById('montoNeto');
        if (span) span.textContent = neto.toLocaleString('en-US', { minimumFractionDigits: 2 });
    }
</script>