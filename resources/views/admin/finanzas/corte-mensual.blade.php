@extends('layouts.admin')

@section('title', 'Corte Mensual | Finanzas | Ollintem Pro')
@section('header-title', 'Corte Mensual')
@section('header-subtitle', 'Análisis detallado del periodo')

<!-- ESTILOS PARA EL LIGHT MODE PREMIUM Y ANIMACIONES -->
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
<div class="p-3 sm:p-6 lg:p-8 xl:p-12 max-w-[1800px] mx-auto w-full space-y-5 sm:space-y-8 flex-1 flex flex-col transition-all duration-300 relative z-10">

    {{-- CABECERA --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end gap-3 sm:gap-6 mb-2 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-1 w-full sm:w-auto">
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Corte Mensual</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500">
                Desglose diario de ingresos y egresos |
                <span class="text-slate-700 font-bold capitalize">{{ $inicioMes->locale('es')->translatedFormat('F Y') }}</span>
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 w-full xl:w-auto">
            <a href="{{ route('admin.finanzas.index') }}"
                class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 px-5 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]">
                <i class="fas fa-arrow-left text-slate-400"></i> Volver a Finanzas
            </a>

            <a href="{{ route('admin.finanzas.corte.exportar', ['mes' => $mes, 'año' => $año]) }}"
                class="w-full sm:w-auto bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-5 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]">
                <i class="fas fa-file-csv"></i> Descargar Excel
            </a>

            <a href="{{ route('admin.finanzas.corte.pdf', ['mes' => $mes, 'año' => $año]) }}"
                class="w-full sm:w-auto bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-5 py-3.5 sm:py-2.5 rounded-xl text-sm font-bold transition-all outline-none flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>
        </div>
    </div>

    {{-- FILTRO DE MES Y AÑO --}}
    <form method="GET" action="{{ route('admin.finanzas.corte.mensual') }}"
        class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-4 sm:p-5 flex flex-col sm:flex-row items-stretch sm:items-end gap-3 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up" style="animation-delay: 100ms;">
        <div class="flex-1 sm:max-w-[220px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Mes</label>
            <select name="mes" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 transition-all cursor-pointer">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" @selected($m == $mes)>
                        {{ ucfirst(\Carbon\Carbon::create(null, $m, 1)->locale('es')->translatedFormat('F')) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 sm:max-w-[160px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Año</label>
            <select name="año" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 transition-all cursor-pointer">
                @foreach($añosDisponibles as $a)
                    <option value="{{ $a }}" @selected($a == $año)>{{ $a }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-all shadow-md shadow-blue-500/20 flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]">
            <i class="fas fa-filter"></i> Filtrar
        </button>
    </form>

    {{-- TARJETAS DE TOTALES DEL MES --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-5">
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-emerald-900/5 hover:border-emerald-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 200ms;">
            <span class="absolute top-0 left-0 right-0 h-[4px] bg-emerald-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 group-hover:text-emerald-600 transition-colors">Ingresos</p>
            <p class="text-xl sm:text-3xl font-black text-slate-900 mt-1 truncate tabular-nums">${{ number_format($totales->ingresos, 2) }}</p>
        </div>
        
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-rose-900/5 hover:border-rose-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 250ms;">
            <span class="absolute top-0 left-0 right-0 h-[4px] bg-rose-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 group-hover:text-rose-600 transition-colors">Gastos</p>
            <p class="text-xl sm:text-3xl font-black text-slate-900 mt-1 truncate tabular-nums">${{ number_format($totales->gastos, 2) }}</p>
        </div>
        
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-purple-900/5 hover:border-purple-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 300ms;">
            <span class="absolute top-0 left-0 right-0 h-[4px] bg-purple-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 group-hover:text-purple-600 transition-colors">Nómina</p>
            <p class="text-xl sm:text-3xl font-black text-slate-900 mt-1 truncate tabular-nums">${{ number_format($totales->nomina, 2) }}</p>
        </div>
        
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-orange-900/5 hover:border-orange-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 350ms;">
            <span class="absolute top-0 left-0 right-0 h-[4px] bg-orange-400 opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 group-hover:text-orange-600 transition-colors">Total Egresos</p>
            <p class="text-xl sm:text-3xl font-black text-slate-900 mt-1 truncate tabular-nums">${{ number_format($totales->egresos, 2) }}</p>
        </div>
        
        <div class="col-span-2 lg:col-span-1 bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 shadow-sm hover:shadow-lg hover:shadow-blue-900/5 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 400ms;">
            <span class="absolute top-0 left-0 right-0 h-[4px] {{ $totales->balance >= 0 ? 'bg-emerald-400' : 'bg-rose-400' }} opacity-80 group-hover:opacity-100 transition-opacity"></span>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 group-hover:text-slate-600 transition-colors">Balance del Mes</p>
            <p class="text-xl sm:text-3xl font-black {{ $totales->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1 truncate tabular-nums">
                ${{ number_format($totales->balance, 2) }}
            </p>
        </div>
    </div>

    {{-- DESGLOSE POR CATEGORÍA DEL MES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up" style="animation-delay: 500ms;">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-50">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-arrow-up text-sm"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Ingresos por Categoría</h3>
            </div>
            <div class="space-y-1.5">
                @forelse($categoriasIngresos as $categoria => $info)
                    <div class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors group">
                        <span class="text-sm font-semibold text-slate-600 group-hover:text-emerald-700 transition-colors">
                            {{ $categoria ?: 'Sin categoría' }} 
                            <span class="text-xs font-medium text-slate-400 ml-1">({{ $info->cantidad }})</span>
                        </span>
                        <span class="font-black text-emerald-600 tabular-nums">${{ number_format($info->total, 2) }}</span>
                    </div>
                @empty
                    <div class="py-4 text-center">
                        <p class="text-sm text-slate-400 font-medium">Sin ingresos este mes.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[1.5rem] p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up" style="animation-delay: 600ms;">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-50">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-100">
                    <i class="fas fa-arrow-down text-sm"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Egresos por Categoría</h3>
            </div>
            <div class="space-y-1.5">
                @forelse($categoriasEgresos as $categoria => $info)
                    <div class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors group">
                        <span class="text-sm font-semibold text-slate-600 group-hover:text-rose-700 transition-colors">
                            {{ $categoria ?: 'Sin categoría' }} 
                            <span class="text-xs font-medium text-slate-400 ml-1">({{ $info->cantidad }})</span>
                        </span>
                        <span class="font-black text-rose-500 tabular-nums">${{ number_format($info->total, 2) }}</span>
                    </div>
                @empty
                    <div class="py-4 text-center">
                        <p class="text-sm text-slate-400 font-medium">Sin egresos este mes.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TABLA DÍA POR DÍA --}}
    <div class="bg-white border border-slate-100 rounded-2xl sm:rounded-[2rem] overflow-hidden shadow-sm hover:shadow-md transition-shadow animate-fade-in-up" style="animation-delay: 700ms;">
        <div class="overflow-x-auto pb-2">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Día</th>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Ingresos</th>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Gastos</th>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Nómina</th>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Egresos</th>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Balance</th>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acumulado</th>
                        <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($dias as $dia)
                        <tr class="group transition-colors duration-300 {{ !$dia->tiene_movimientos ? 'opacity-50 grayscale' : 'hover:bg-blue-50/50 cursor-pointer' }}">
                            
                            <td class="py-4 px-5 text-sm font-bold text-slate-700 capitalize group-hover:text-blue-700 transition-colors">
                                {{ $dia->fecha->translatedFormat('D d') }}
                            </td>
                            
                            <td class="py-4 px-5 text-right text-sm tabular-nums {{ $dia->ingresos > 0 ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' }}">
                                ${{ number_format($dia->ingresos, 2) }}
                            </td>
                            
                            <td class="py-4 px-5 text-right text-sm tabular-nums {{ $dia->gastos > 0 ? 'text-rose-500 font-bold' : 'text-slate-400 font-medium' }}">
                                ${{ number_format($dia->gastos, 2) }}
                            </td>
                            
                            <td class="py-4 px-5 text-right text-sm tabular-nums {{ $dia->nomina > 0 ? 'text-purple-600 font-bold' : 'text-slate-400 font-medium' }}">
                                ${{ number_format($dia->nomina, 2) }}
                            </td>
                            
                            <td class="py-4 px-5 text-right text-sm tabular-nums {{ $dia->egresos > 0 ? 'text-orange-500 font-bold' : 'text-slate-400 font-medium' }}">
                                ${{ number_format($dia->egresos, 2) }}
                            </td>
                            
                            <td class="py-4 px-5 text-right text-sm font-black tabular-nums {{ $dia->balance > 0 ? 'text-emerald-600' : ($dia->balance < 0 ? 'text-rose-600' : 'text-slate-400 font-medium') }}">
                                ${{ number_format($dia->balance, 2) }}
                            </td>
                            
                            <td class="py-4 px-5 text-right text-sm font-bold text-slate-500 group-hover:text-blue-600 transition-colors tabular-nums">
                                ${{ number_format($dia->balance_acumulado, 2) }}
                            </td>
                            
                            <td class="py-4 px-5 text-center">
                                @if($dia->tiene_movimientos)
                                    <button type="button"
                                        onclick="document.getElementById('detalle-{{ $dia->fecha->format('Ymd') }}').classList.toggle('hidden')"
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[10px] font-bold uppercase text-slate-600 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all duration-300 shadow-sm">
                                        <i class="fas fa-eye mr-1 text-blue-500"></i> Ver ({{ $dia->movimientos->count() }})
                                    </button>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>

                        @if($dia->tiene_movimientos)
                            <tr id="detalle-{{ $dia->fecha->format('Ymd') }}" class="hidden bg-slate-50/50">
                                <td colspan="8" class="p-4 sm:p-6 border-b border-slate-100">
                                    <div class="space-y-2 max-w-5xl mx-auto">
                                        @foreach($dia->movimientos as $mov)
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-sm bg-white border border-slate-200 rounded-xl px-4 py-3 hover:border-blue-200 hover:shadow-sm hover:shadow-blue-500/5 transition-all duration-300">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <span class="shrink-0 font-mono text-xs font-semibold text-slate-400 bg-slate-50 px-2 py-1 rounded-md">{{ $mov->fecha->format('H:i') }}</span>
                                                    <span class="shrink-0 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $mov->tipo === 'ingreso' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                                        {{ $mov->getTipoLegible() }}
                                                    </span>
                                                    <span class="shrink-0 text-xs font-bold text-slate-500 px-2">{{ $mov->categoria }}</span>
                                                    <span class="truncate text-slate-800 font-medium">{{ $mov->concepto }}</span>
                                                </div>
                                                <div class="flex items-center gap-4 shrink-0 mt-2 sm:mt-0 ml-14 sm:ml-0 border-t sm:border-t-0 border-slate-100 pt-2 sm:pt-0">
                                                    <span class="text-xs font-semibold text-slate-400 flex items-center gap-1.5"><i class="fas fa-credit-card opacity-50"></i> {{ $mov->metodo_pago }}</span>
                                                    <span class="font-black text-base {{ $mov->tipo === 'ingreso' ? 'text-emerald-600' : 'text-rose-500' }} tabular-nums">
                                                        {{ $mov->getSimboloTipo() }}${{ number_format($mov->monto, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-200 font-black text-slate-800">
                        <td class="py-4 px-5 text-sm uppercase tracking-widest text-slate-500">TOTALES</td>
                        <td class="py-4 px-5 text-right text-emerald-600 tabular-nums">${{ number_format($totales->ingresos, 2) }}</td>
                        <td class="py-4 px-5 text-right text-rose-500 tabular-nums">${{ number_format($totales->gastos, 2) }}</td>
                        <td class="py-4 px-5 text-right text-purple-600 tabular-nums">${{ number_format($totales->nomina, 2) }}</td>
                        <td class="py-4 px-5 text-right text-orange-500 tabular-nums">${{ number_format($totales->egresos, 2) }}</td>
                        <td class="py-4 px-5 text-right tabular-nums {{ $totales->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">${{ number_format($totales->balance, 2) }}</td>
                        <td class="py-4 px-5"></td>
                        <td class="py-4 px-5"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection