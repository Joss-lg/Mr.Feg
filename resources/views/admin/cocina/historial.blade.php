@extends('layouts.admin')

@section('title', 'Historial de Comandas | ' . $area)

@section('content')
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-5xl mx-auto space-y-6 bg-[#F2F2F2] min-h-screen">

    {{-- CABECERA --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm animate-fade-in-up" style="animation-delay: 0ms;">
        <div>
            <a href="{{ route('admin.cocina.index') }}"
               class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition-colors flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-[10px]"></i> Volver a {{ $area }}
            </a>
            <h1 class="text-xl sm:text-3xl font-black text-slate-800 tracking-tight mt-1.5">
                Historial de comandas
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1 leading-relaxed">
                Lo que llegó a <span class="font-bold text-slate-700">{{ $area }}</span>
                el {{ $fecha }}.
                Este registro es inmutable: muestra el pedido tal como llegó al momento del envío.
            </p>
        </div>

        {{-- Selector de área --}}
        <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-200 shrink-0">
            <a href="{{ route('admin.cocina.historial', ['area' => 'Cocina']) }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all
                      {{ $areaSeleccionada !== 'Barra' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-slate-800' }}">
                Cocina
            </a>
            <a href="{{ route('admin.cocina.historial', ['area' => 'Barra']) }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all
                      {{ $areaSeleccionada === 'Barra' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-slate-800' }}">
                Barra
            </a>
        </div>
    </div>

    {{-- AVISO cuando no hay nada --}}
    @if($jobs->isEmpty())
        <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm p-8 sm:p-14 text-center flex flex-col items-center justify-center animate-fade-in-up" style="animation-delay: 150ms;">
            <div class="relative mb-6">
                <div class="absolute inset-0 rounded-[1.5rem] bg-blue-100 animate-ping opacity-40"></div>
                <div class="relative w-20 h-20 bg-blue-50 rounded-[1.5rem] flex items-center justify-center border border-blue-100 shadow-sm">
                    <i class="fas fa-clipboard-list text-3xl text-blue-500"></i>
                </div>
            </div>
            <p class="font-black text-slate-800 text-base sm:text-lg">Sin comandas registradas hoy en {{ $area }}</p>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1.5 max-w-sm">
                Aquí aparecerá cada envío a cocina con el detalle exacto de lo que se pidió, en cuanto entre el primer pedido del turno.
            </p>
            <a href="{{ route('admin.cocina.index') }}"
               class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                <i class="fas fa-fire"></i> Ir a Cocina en vivo
            </a>
        </div>

    @else
        {{-- Una tarjeta por LOTE DE ENVIO (cada vez que el mesero presionó "Enviar") --}}
        <div class="space-y-3 animate-fade-in-up" style="animation-delay: 150ms;">
            @foreach($jobs as $lote => $lotejobs)
                @php
                    $primerJob = $lotejobs->first();
                    $orden = $primerJob?->orden;
                    $mesa = optional($orden?->mesa)->numero ?? '—';
                    $mesero = optional($orden?->mesero)->nombre ?? optional($orden?->mesero)->name ?? 'Sin asignar';
                    $hora = $primerJob?->created_at?->format('H:i');
                @endphp

                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">

                    {{-- Encabezado del lote --}}
                    <button type="button"
                        class="btn-lote w-full px-4 py-3.5 flex items-center justify-between gap-3 text-left hover:bg-slate-50 transition-colors">

                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-receipt text-blue-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-black text-slate-800 text-sm">
                                    Mesa {{ $mesa }}
                                    <span class="font-medium text-slate-400 text-xs ml-1">· {{ $mesero }}</span>
                                </p>
                                <p class="text-[11px] text-slate-400 font-medium">
                                    {{ $hora }} hrs
                                    · lote <span class="font-mono">{{ substr($lote, 0, 12) }}</span>
                                </p>
                            </div>
                        </div>

                        <i class="fas fa-chevron-down text-slate-400 text-xs shrink-0 transition-transform icono-lote"></i>
                    </button>

                    {{-- Contenido del ticket (el texto exacto que llegó a cocina) --}}
                    <div class="contenido-lote border-t border-slate-100">
                        @foreach($lotejobs as $job)
                            <div class="px-4 py-3.5 {{ !$loop->first ? 'border-t border-slate-100' : '' }}">

                                {{-- Estado del job --}}
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                        {{ $job->area ?? $area }}
                                    </span>
                                    <span class="text-[10px] font-black uppercase tracking-wide px-2.5 py-1 rounded-lg border
                                        {{ $job->estado === 'impreso' ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-amber-50 border-amber-100 text-amber-600' }}">
                                        {{ $job->estado === 'impreso' ? 'Impreso' : 'Pendiente' }}
                                    </span>
                                </div>

                                {{-- El texto del ticket TAL CUAL llegó: fuente monoespaciada para
                                     respetar el formato de la impresora térmica. Esto es el
                                     respaldo oficial — no se puede editar ni reinterpretar. --}}
                                <pre class="text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 whitespace-pre-wrap overflow-x-auto leading-relaxed">{{ $job->contenido }}</pre>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-[11px] text-slate-400 font-medium leading-snug text-center pb-4">
            El contenido de cada comanda es exactamente lo que llegó al momento del envío.
            Si existe discrepancia entre lo que el cliente dice haber pedido y lo que aparece aquí,
            este registro es el referente oficial.
        </p>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Plegar/desplegar cada lote. El primero arranca abierto.
    document.querySelectorAll('.btn-lote').forEach((btn, i) => {
        const contenido = btn.nextElementSibling;
        const icono = btn.querySelector('.icono-lote');

        // El mas reciente (primero en el DOM) viene abierto.
        if (i !== 0) {
            contenido.classList.add('hidden');
            icono.style.transform = 'rotate(-90deg)';
        }

        btn.addEventListener('click', () => {
            const oculto = contenido.classList.toggle('hidden');
            icono.style.transform = oculto ? 'rotate(-90deg)' : 'rotate(0deg)';
        });
    });
});
</script>
@endsection