@extends('layouts.admin')

@section('title', 'Mis Pedidos | Ollintem Pro')

@section('content')
<div class="px-4 py-6 sm:p-8 w-full max-w-2xl mx-auto space-y-6 font-sans min-h-screen bg-slate-50">

    {{-- ENCABEZADO --}}
    <div class="flex items-center gap-4 bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm">
        <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-orange-50 border border-orange-100 text-orange-600 shrink-0">
            <i class="fas fa-motorcycle text-lg"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-slate-800">Mis Pedidos en Ruta</h1>
            <p class="text-xs text-slate-500 font-medium">Hola, {{ auth()->user()->nombre ?? auth()->user()->name }}</p>
        </div>
    </div>

    {{-- PEDIDOS EN CAMINO --}}
    @forelse($ordenesEnCamino as $orden)
        <div class="bg-white border border-slate-200 border-l-4 border-l-blue-500 rounded-[1.5rem] p-5 shadow-sm space-y-4">

            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-black text-lg text-slate-800">Ticket #{{ $orden->id }}</h3>
                    <p class="font-bold text-blue-600 text-sm mt-0.5">
                        {{ $orden->cliente->nombre ?? 'Cliente General' }}
                    </p>
                </div>
                <span class="px-3 py-1 rounded-xl bg-blue-50 text-blue-600 text-[10px] font-black uppercase border border-blue-100">
                    En Ruta
                </span>
            </div>

            {{-- Dirección --}}
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-1">
                <p class="flex items-start gap-2.5">
                    <i class="fas fa-map-marker-alt mt-0.5 text-orange-500 shrink-0"></i>
                    <span class="leading-relaxed">
                        <strong class="text-slate-900 font-bold block">{{ $orden->direccion->calle ?? 'Sin dirección' }}</strong>
                        @if(!empty($orden->direccion->manzana)) Mz: {{ $orden->direccion->manzana }} @endif
                        @if(!empty($orden->direccion->lote)) | Lt: {{ $orden->direccion->lote }} @endif
                        <span class="block">Col: {{ $orden->direccion->colonia ?? '-' }}</span>
                        <em class="text-slate-400">Ref: {{ $orden->direccion->referencia ?? 'Ninguna' }}</em>
                    </span>
                </p>
                <p class="flex items-center gap-2.5 pt-1 font-bold text-slate-800 border-t border-slate-200/60">
                    <i class="fas fa-phone text-slate-400"></i>
                    {{ $orden->cliente->telefono ?? 'S/N' }}
                </p>
            </div>

            {{-- Total --}}
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Monto Total</span>
                <span class="text-emerald-600 font-black text-lg">${{ number_format($orden->total, 2) }}</span>
            </div>

            {{-- Botón ver ticket --}}
            <button type="button"
                onclick="imprimirTicketDirecto('{{ route('admin.repartidores.ticket.orden', $orden->id) }}')"
                class="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-2 active:scale-95">
                <i class="fas fa-print"></i> Ver / Imprimir Ticket
            </button>

        </div>
    @empty
        <div class="text-center py-16 bg-white rounded-[2rem] border border-slate-200 shadow-sm">
            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto border border-slate-200 text-slate-400 mb-3">
                <i class="fas fa-road text-2xl"></i>
            </div>
            <p class="font-black text-slate-800 text-sm">No tienes pedidos en ruta</p>
            <p class="text-xs text-slate-400 mt-1">Cuando te asignen un pedido aparecerá aquí.</p>
        </div>
    @endforelse

</div>

<script>
window.imprimirTicketDirecto = function(url) {
    let printFrame = document.getElementById('frame-impresion-ticket');
    if (!printFrame) {
        printFrame = document.createElement('iframe');
        printFrame.id = 'frame-impresion-ticket';
        printFrame.style.display = 'none';
        document.body.appendChild(printFrame);
    }
    printFrame.src = url;
    printFrame.onload = function() {
        printFrame.contentWindow.focus();
        printFrame.contentWindow.print();
    };
};
</script>
@endsection