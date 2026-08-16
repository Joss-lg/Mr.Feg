@extends('layouts.admin')

@section('title', 'Productos Vendidos | Ollintem Pro')

@section('content')
<div class="px-4 py-6 sm:p-8 lg:p-10 xl:p-12 max-w-[1800px] mx-auto w-full space-y-6 sm:space-y-8 flex-1 flex flex-col font-sans bg-slate-50 text-slate-800 min-h-screen transition-colors duration-300">

    {{-- Encabezado --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end gap-6 bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm">
        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Reporte de Productos Vendidos</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500">
                Del <span class="font-bold text-slate-800">{{ $fechaInicio }}</span>
                al <span class="font-bold text-slate-800">{{ $fechaFin }}</span>
                @if($areaFiltro !== 'todas')
                    &mdash; Área: <span class="font-bold text-slate-800 uppercase">{{ $areaFiltro }}</span>
                @endif
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
            {{-- Filtro de fechas --}}
            <form method="GET" action="{{ route('admin.corte.index') }}" class="flex w-full sm:w-auto items-center gap-2">
                <input type="hidden" name="area" value="{{ $areaFiltro }}">
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                    class="w-full sm:w-36 h-12 bg-white border border-slate-200 rounded-xl px-4 text-xs font-bold text-slate-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all shadow-sm cursor-pointer">
                <span class="text-slate-400 font-bold">-</span>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
                    class="w-full sm:w-36 h-12 bg-white border border-slate-200 rounded-xl px-4 text-xs font-bold text-slate-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all shadow-sm cursor-pointer">
                <button type="submit" title="Filtrar"
                    class="bg-slate-800 hover:bg-slate-900 text-white w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center transition-all shadow-md active:scale-95 outline-none">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </button>
            </form>

            {{-- PDF --}}
            <a href="{{ route('admin.corte.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin, 'area' => $areaFiltro]) }}"
               class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-7 h-12 rounded-xl text-xs font-black uppercase tracking-[0.15em] transition-all shadow-md shadow-blue-500/20 active:scale-95 flex items-center justify-center gap-2 outline-none">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    {{-- ── BOTONES DE ÁREA ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Botón "Todas" --}}
        <a href="{{ route('admin.corte.index', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin, 'area' => 'todas']) }}"
           class="flex items-center gap-2 px-5 h-11 rounded-xl text-xs font-black uppercase tracking-wider transition-all border
                 {{ $areaFiltro === 'todas'
                     ? 'bg-slate-800 text-white border-transparent shadow-md'
                     : 'bg-white text-slate-500 border-slate-200 hover:border-slate-300 hover:text-slate-800' }}">
            <i class="fas fa-layer-group text-[10px]"></i> Todas
        </a>

        {{-- Botón por cada área disponible --}}
        @foreach($areasDisponibles as $area)
            @php
                $areaSlug  = strtolower($area);
                $activo    = strtolower($areaFiltro) === $areaSlug;
                $icono     = match($areaSlug) {
                    'barra'   => 'fa-glass-martini-alt',
                    'cocina'  => 'fa-utensils',
                    default   => 'fa-tag',
                };
            @endphp
            <a href="{{ route('admin.corte.index', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin, 'area' => $area]) }}"
               class="flex items-center gap-2 px-5 h-11 rounded-xl text-xs font-black uppercase tracking-wider transition-all border
                     {{ $activo
                         ? "bg-blue-600 text-white border-transparent shadow-md shadow-blue-500/20"
                         : "bg-white text-slate-500 border-slate-200 hover:border-blue-300 hover:text-blue-600" }}">
                <i class="fas {{ $icono }} text-[10px]"></i>
                {{ $area }}
            </a>
        @endforeach
    </div>

    {{-- ── TABLA ────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm p-5 sm:p-8 w-full">

        @forelse($ventasPorArea as $area => $productos)
            @php
                $areaSlug  = strtolower($area);
                $icono     = match($areaSlug) { 'barra' => 'fa-glass-martini-alt', 'cocina' => 'fa-utensils', default => 'fa-tag' };
                $totalUnidades = $productos->sum('total_vendido');
            @endphp

            <div class="mb-10 last:mb-0">

                {{-- Cabecera del área --}}
                <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-sm">
                            <i class="fas {{ $icono }} text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">{{ $area }}</h3>
                            <p class="text-[10px] text-slate-400 font-bold tracking-wider mt-0.5">{{ $productos->count() }} productos · {{ $totalUnidades }} unidades vendidas</p>
                        </div>
                    </div>
                    <span class="px-3.5 py-1.5 rounded-xl text-[10px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-widest shadow-sm">
                        {{ $totalUnidades }} Unidades
                    </span>
                </div>
    
                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">#</th>
                                <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Producto</th>
                                <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Vendidos</th>
                                <th class="pb-3 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">% del área</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($productos->sortByDesc('total_vendido') as $i => $item)
                                @php $pct = $totalUnidades > 0 ? round($item->total_vendido / $totalUnidades * 100, 1) : 0; @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 text-xs text-slate-400 font-bold w-10">{{ $loop->iteration }}</td>
                                    <td class="py-3.5 px-4 text-sm font-black text-slate-800">{{ $item->producto }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 bg-slate-100 border border-slate-200 rounded-xl text-xs font-black text-slate-800">
                                            {{ $item->total_vendido }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="w-24 h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200/60">
                                                <div class="h-full rounded-full bg-blue-600" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-500 w-10 text-right">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-200">
                                <td colspan="2" class="pt-4 px-4 text-xs font-black text-slate-400 uppercase tracking-widest">Total</td>
                                <td class="pt-4 px-4 text-center">
                                    <span class="px-3.5 py-1.5 bg-blue-50 border border-blue-100 rounded-xl text-xs font-black text-blue-600">
                                        {{ $totalUnidades }}
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="py-16 text-center flex flex-col items-center gap-3">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-200 text-slate-400 shadow-sm">
                    <i class="fas fa-inbox text-2xl"></i>
                </div>
                <p class="text-sm text-slate-500 font-bold">No hay productos vendidos en las fechas seleccionadas.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection