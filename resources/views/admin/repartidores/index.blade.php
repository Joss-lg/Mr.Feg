@extends('layouts.admin')

@section('title', 'Módulo de Repartidores')

@section('content')
<div class="px-3 sm:px-6 lg:px-8 py-6 w-full max-w-7xl mx-auto space-y-6">

    {{-- Título de la sección --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl sm:text-3xl font-black text-[var(--text-main)] tracking-tight">
            Control de Repartos a Domicilio
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ========================================================= --}}
        {{-- COLUMNA 1: PENDIENTES DE ASIGNAR (IMAGEN 1)                --}}
        {{-- ========================================================= --}}
        <div class="space-y-4">
            <h2 class="text-lg font-black text-[var(--text-main)] flex items-center gap-2">
                <i class="fas fa-box-open text-orange-500"></i> Pedidos Listos para Enviar ({{ count($ordenesPendientes) }})
            </h2>

            @forelse($ordenesPendientes as $orden)
                <div class="bg-[var(--bg-panel)] border border-[var(--border-color)] border-l-4 border-l-orange-500 rounded-2xl p-5 shadow-sm space-y-4">
                    
                    {{-- Cabecera Ticket --}}
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-black text-lg text-[var(--text-main)]">
                                Ticket #{{ $orden->id }}
                            </h3>
{{-- Nombre del cliente (Soporta registrado, temporal o general) --}}
<p class="font-bold text-orange-500 text-sm">
    @if($orden->cliente)
        {{ $orden->cliente->nombre }}
    @elseif(!empty($orden->nombre_temporal))
        {{ $orden->nombre_temporal }} (Temporal)
    @else
        Cliente General
    @endif
</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-red-500/10 text-red-500 text-xs font-black">
                            Espera: {{ round(\Carbon\Carbon::parse($orden->created_at)->diffInMinutes(now())) }} min
                        </span>
                    </div>

{{-- Dirección y Contacto --}}
<div class="bg-[var(--bg-base)] p-3.5 rounded-xl border border-[var(--border-color)] space-y-1.5 text-xs text-[var(--text-muted)]">
    <p class="flex items-start gap-2">
        <i class="fas fa-map-marker-alt mt-0.5 text-orange-500"></i>
        <span class="text-[var(--text-main)]">
            @if($orden->direccion)
                <strong>{{ $orden->direccion->calle ?? 'Sin calle' }}</strong><br>
                @if(!empty($orden->direccion->manzana)) Mz: {{ $orden->direccion->manzana }} @endif
                @if(!empty($orden->direccion->lote)) | Lt: {{ $orden->direccion->lote }} @endif<br>
                Col: {{ $orden->direccion->colonia ?? '-' }}<br>
                <em class="text-[var(--text-muted)]">Ref: {{ $orden->direccion->referencia ?? 'Ninguna' }}</em>
            @else
                <strong>Sin dirección registrada (Pedido Rápido / Para Llevar)</strong>
            @endif
        </span>
    </p>
    <p class="flex items-center gap-2 pt-1 font-bold text-[var(--text-main)]">
        <i class="fas fa-phone text-[var(--text-muted)]"></i> 
        {{ $orden->cliente->telefono ?? 'S/N' }}
    </p>
</div>

                    {{-- Total --}}
                    <div class="text-emerald-500 font-black text-base">
                        Total: ${{ number_format($orden->total, 2) }}
                    </div>

                    {{-- Formulario para asignar repartidor --}}
                    <form action="{{ route('admin.repartidores.asignar', $orden->id) }}" method="POST" class="flex gap-2 pt-2 border-t border-[var(--border-color)] form-async">
                        @csrf
                        <select name="repartidor_id" required class="flex-1 bg-[var(--bg-base)] border border-[var(--border-color)] rounded-xl px-3 py-2.5 text-xs font-bold text-[var(--text-main)] focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Seleccionar Repartidor...</option>
                            @foreach($repartidores as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->nombre ?? $rep->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs uppercase tracking-wider shadow-md transition-all flex items-center gap-1.5 shrink-0 active:scale-95">
                            <i class="fas fa-paper-plane"></i> Enviar
                        </button>
                    </form>

                </div>
            @empty
                <div class="text-center py-12 bg-[var(--bg-panel)] rounded-2xl border border-[var(--border-color)]">
                    <i class="fas fa-check-circle text-4xl text-emerald-500 mb-2"></i>
                    <p class="font-bold text-[var(--text-muted)] text-sm">No hay pedidos pendientes de reparto</p>
                </div>
            @endforelse
        </div>

        {{-- ========================================================= --}}
        {{-- COLUMNA 2: EN CAMINO (IMAGEN 2)                          --}}
        {{-- ========================================================= --}}
        <div class="space-y-4">
            <h2 class="text-lg font-black text-[var(--text-main)] flex items-center gap-2">
                <i class="fas fa-motorcycle text-blue-500"></i> En Camino ({{ count($ordenesEnCamino) }})
            </h2>

            @forelse($ordenesEnCamino as $orden)
                <div class="bg-[var(--bg-panel)] border border-[var(--border-color)] border-l-4 border-l-blue-500 rounded-2xl p-5 shadow-sm space-y-4">
                    
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-black text-lg text-[var(--text-main)]">
                                Ticket #{{ $orden->id }}
                            </h3>
                            <p class="font-bold text-blue-500 text-sm">
                                {{ $orden->cliente->nombre ?? 'Cliente General' }}
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-500 text-xs font-black">
                            {{ round(\Carbon\Carbon::parse($orden->updated_at)->diffInMinutes(now())) }} min totales
                        </span>
                    </div>

                    {{-- Info del Repartidor Asignado --}}
                    <div class="bg-blue-500/10 border border-blue-500/20 p-3 rounded-xl space-y-0.5">
                        <p class="text-xs font-black text-blue-500 flex items-center gap-1.5">
                            <i class="fas fa-user-tie"></i> Repartidor: {{ $orden->repartidor->nombre ?? $orden->repartidor->name ?? 'Asignado' }}
                        </p>
                        <p class="text-[11px] text-[var(--text-muted)]">
                            Se fue a las {{ \Carbon\Carbon::parse($orden->updated_at)->format('h:i A') }}
                        </p>
                    </div>

                    {{-- Dirección breve --}}
                    <div class="text-xs text-[var(--text-muted)] bg-[var(--bg-base)] p-3 rounded-xl border border-[var(--border-color)]">
                        <strong class="text-[var(--text-main)]">{{ $orden->direccion->calle ?? '' }}</strong><br>
                        Col: {{ $orden->direccion->colonia ?? '' }}<br>
                        <em class="text-[var(--text-muted)]">Ref: {{ $orden->direccion->referencia ?? '' }}</em>
                    </div>

                    {{-- Botón Marcar como Entregado --}}
                    <form action="{{ route('admin.repartidores.entregado', $orden->id) }}" method="POST" class="form-async">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-black text-xs uppercase tracking-wider shadow-md transition-all flex items-center justify-center gap-2 active:scale-95">
                            <i class="fas fa-check"></i> Marcar como Entregado
                        </button>
                    </form>

                </div>
            @empty
                <div class="text-center py-12 bg-[var(--bg-panel)] rounded-2xl border border-[var(--border-color)]">
                    <i class="fas fa-road text-4xl text-[var(--text-muted)] opacity-40 mb-2"></i>
                    <p class="font-bold text-[var(--text-muted)] text-sm">No hay repartos en camino en este momento</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

{{-- Script opcional para hacer que los botones actúen de manera fluida sin recargar completo (AJAX básico) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-async').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const url = this.action;
            const data = new FormData(this);
            const method = this.method;

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: data
                });

                const result = await response.json();
                if (result.success) {
                    location.reload(); // Recarga para actualizar las listas visuales
                } else {
                    alert(result.message || 'Ocurrió un error');
                }
            } catch (err) {
                console.error(err);
                // Si falla el fetch por alguna razón, ejecutamos envío tradicional
                this.submit();
            }
        });
    });
});
</script>
@endsection