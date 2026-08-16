@extends('layouts.admin')

@section('title', 'Historial de Cajas | Ollintem Pro')

@section('header-title', 'Finanzas y Contabilidad')
@section('header-subtitle', 'Gestión de ingresos, egresos y balance general')

@push('styles')
<style>
    /* 1. Forzamos modo claro y fondo blanco SOLO en esta vista */
    html.dark { background: #f8fafc !important; }
    body { background: #f8fafc !important; background-image: none !important; }

    /* 2. Colores oscuros para textos, con scope al contenedor .historial-caja-view */
    .historial-caja-view .text-zinc-100,
    .historial-caja-view .text-zinc-300,
    .historial-caja-view .text-zinc-400,
    .historial-caja-view .text-white { color: #0f172a !important; }
    .dark .historial-caja-view .text-zinc-100 { color: #0f172a !important; }

    /* 3. Fondos de tarjetas, también con scope */
    .historial-caja-view .bg-white,
    .dark .historial-caja-view .bg-\[\#0c0c0e\] { background-color: #ffffff !important; border: 1px solid #e2e8f0 !important; }
    .historial-caja-view .bg-zinc-50,
    .dark .historial-caja-view .bg-zinc-900\/30 { background-color: #f1f5f9 !important; }

    /* ANIMACIONES */
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

@push('scripts')
<script>
    // Forzamos el modo claro para que los estilos del layout global no afecten este index
    document.documentElement.classList.remove('dark');
</script>
@endpush

@section('content')
<div class="historial-caja-view p-3 sm:p-6 md:p-8 lg:p-10 xl:p-12 max-w-[1800px] mx-auto w-full space-y-5 md:space-y-8 flex-1 flex flex-col bg-transparent">

    {{-- CABECERA --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 relative z-10 animate-fade-in-up" style="animation-delay: 0ms;">
        <div>
            <h1 class="text-2xl md:text-4xl font-black text-slate-900 tracking-tight">Historial de Turnos</h1>
            <p class="text-[10px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Auditoría, cierres y conciliaciones</p>
        </div>
        <div class="inline-flex items-center gap-2 bg-white px-4 py-2.5 rounded-full border border-slate-100 shadow-sm text-[10px] font-black text-slate-500 uppercase tracking-widest">
            <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
            Mostrando {{ $turnos->count() }} turnos
        </div>
    </div>

    {{-- VISTA MÓVIL (Tarjetas limpias) --}}
    <div class="md:hidden space-y-3">
        @forelse($turnos as $turno)
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-1 bg-slate-100 rounded-md border border-slate-200 font-mono text-[11px] font-bold text-slate-500">#{{ $turno->id }}</span>
                    @if($turno->estado === 'abierta')
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">Activa</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200">Cerrada</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">{{ substr($turno->user->nombre ?? 'U', 0, 1) }}</div>
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-900 text-sm">{{ $turno->user->nombre ?? 'N/A' }}</span>
                        <span class="text-[10px] text-slate-500 font-semibold">ID: {{ $turno->user_id }}</span>
                    </div>
                </div>
                <div class="flex justify-between text-xs font-bold text-slate-700 pt-2 border-t border-slate-100">
                    <span>${{ number_format($turno->monto_inicial, 2) }}</span>
                    <span>${{ number_format($turno->monto_final_real ?? 0, 2) }}</span>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-slate-400 font-bold">Sin registros</div>
        @endforelse
    </div>

    {{-- CONTENEDOR PRINCIPAL (Escritorio) --}}
    <div class="hidden md:flex bg-white border border-slate-100 shadow-sm rounded-2xl md:rounded-[2rem] p-4 sm:p-6 md:p-8 w-full flex-1 flex-col relative z-20 animate-fade-in-up" style="animation-delay: 300ms;">

        {{-- Tabla Escritorio --}}
        <div class="w-full overflow-x-auto relative">
            <table class="w-full min-w-[900px] text-left border-collapse table-fixed">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="w-[10%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Caja</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Turno</th>
                        <th class="w-[20%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Empleado</th>
                        <th class="w-[10%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Saldo Inicial</th>
                        <th class="w-[15%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Saldo Final</th>
                        <th class="w-[10%] pb-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($turnos as $turno)
                        <tr class="group hover:bg-blue-50/50 transition-colors duration-300">
                            {{-- ID --}}
                            <td class="py-5 px-4">
                                <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-600">#{{ $turno->id }}</span>
                            </td>

                            {{-- Fecha --}}
                            <td class="py-5 px-4 text-xs font-bold text-slate-700">
                                {{ $turno->created_at->format('d/m/Y') }}
                            </td>

                            {{-- Turno --}}
                            <td class="py-5 px-4">
                                <span class="text-[10px] font-black uppercase tracking-wider {{ $turno->turno === 'Matutino' ? 'text-blue-600' : 'text-amber-600' }}">
                                    {{ $turno->turno }}
                                </span>
                                <p class="text-[9px] text-slate-400">{{ $turno->created_at->format('h:i A') }}</p>
                            </td>

                            {{-- Empleado --}}
                            <td class="py-5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">{{ substr($turno->user->nombre ?? 'U', 0, 1) }}</div>
                                    <span class="text-xs font-bold text-slate-800">{{ $turno->user->nombre ?? 'N/A' }}</span>
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="py-5 px-4 text-center">
                                @if($turno->estado === 'abierta')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">Activa</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 border border-slate-200">Cerrada</span>
                                @endif
                            </td>

                            {{-- Saldos --}}
                            <td class="py-5 px-4 text-right text-xs font-semibold text-slate-600">${{ number_format($turno->monto_inicial, 2) }}</td>
                            <td class="py-5 px-4 text-right font-black text-sm text-slate-900">
                                {{ $turno->estado === 'abierta' ? '--' : '$'.number_format($turno->monto_final_real ?? 0, 2) }}
                            </td>

                            {{-- Acciones --}}
                            <td class="py-5 px-4 text-center">
                                <a href="{{ route('historial.show', $turno->id) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all active:scale-95">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-14 text-center text-xs font-bold text-slate-400">No hay registros disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-6 pt-4 border-t border-slate-100">
            {{ $turnos->links() }}
        </div>
    </div>
</div>
@endsection