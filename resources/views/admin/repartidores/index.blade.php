@extends('layouts.admin')

@section('title', 'Módulo de Repartidores | Ollintem Pro')

@section('content')
<style>
    .unique-scrollbar::-webkit-scrollbar { width: 6px; }
    .unique-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
</style>

<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-[1800px] mx-auto space-y-6 sm:space-y-8 relative z-10 font-sans min-h-screen bg-slate-50 text-slate-800 transition-colors duration-300">

    {{-- ENCABEZADO PREMIUM --}}
    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-orange-50 border border-orange-100 text-orange-600 shrink-0 shadow-sm">
                <i class="fas fa-motorcycle text-lg"></i>
            </div>
            <div class="space-y-1">
                <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">
                    Control de Repartos a Domicilio
                </h1>
                <p class="text-xs sm:text-sm font-medium text-slate-500">
                    Administra los pedidos pendientes de envío y supervisa los pedidos que van en camino.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">

        {{-- ========================================================= --}}
        {{-- COLUMNA 1: PENDIENTES DE ASIGNAR --}}
        {{-- ========================================================= --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
                    <i class="fas fa-box-open text-orange-500"></i> Pedidos Listos para Enviar
                </h2>
                <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-[10px] font-black text-orange-600 uppercase tracking-widest border border-orange-100">
                    {{ count($ordenesPendientes) }} Pendientes
                </span>
            </div>

            @forelse($ordenesPendientes as $orden)
                <div class="bg-white border border-slate-200 border-l-4 border-l-orange-500 rounded-[1.5rem] p-5 sm:p-6 shadow-sm space-y-4 hover:border-slate-300 transition-all">
                    
                    {{-- Cabecera Ticket --}}
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-slate-800 tracking-tight">
                                Ticket #{{ $orden->id }}
                            </h3>
                            <p class="font-bold text-orange-600 text-sm mt-0.5">
                                @if($orden->cliente)
                                    {{ $orden->cliente->nombre }}
                                @elseif(!empty($orden->nombre_temporal))
                                    {{ $orden->nombre_temporal }} <span class="text-xs font-semibold text-slate-400">(Temporal)</span>
                                @else
                                    Cliente General
                                @endif
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-xl bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-wider border border-rose-100">
                            Espera: {{ round(\Carbon\Carbon::parse($orden->created_at)->diffInMinutes(now())) }} min
                        </span>
                    </div>

                    {{-- Dirección y Contacto --}}
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2 text-xs text-slate-600 shadow-inner">
                        <p class="flex items-start gap-2.5">
                            <i class="fas fa-map-marker-alt mt-0.5 text-orange-500 shrink-0"></i>
                            <span class="text-slate-800 font-medium leading-relaxed">
                                @if($orden->direccion)
                                    <strong class="text-slate-900 font-bold">{{ $orden->direccion->calle ?? 'Sin calle' }}</strong><br>
                                    @if(!empty($orden->direccion->manzana)) Mz: {{ $orden->direccion->manzana }} @endif
                                    @if(!empty($orden->direccion->lote)) | Lt: {{ $orden->direccion->lote }} @endif
                                    @if(!empty($orden->direccion->manzana) || !empty($orden->direccion->lote)) <br> @endif
                                    Col: {{ $orden->direccion->colonia ?? '-' }}<br>
                                    <em class="text-slate-400">Ref: {{ $orden->direccion->referencia ?? 'Ninguna' }}</em>
                                @else
                                    <strong class="text-slate-900 font-bold">Sin dirección registrada (Pedido Rápido / Para Llevar)</strong>
                                @endif
                            </span>
                        </p>
                        <p class="flex items-center gap-2.5 pt-1 font-bold text-slate-800 border-t border-slate-200/60">
                            <i class="fas fa-phone text-slate-400"></i> 
                            {{ $orden->cliente->telefono ?? 'S/N' }}
                        </p>
                    </div>

                    {{-- Total --}}
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Monto Total</span>
                        <span class="text-emerald-600 font-black text-lg tracking-tight">
                            ${{ number_format($orden->total, 2) }}
                        </span>
                    </div>

                    {{-- Formulario para asignar repartidor --}}
                    <form action="{{ route('admin.repartidores.asignar', $orden->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2.5 pt-3 border-t border-slate-100 form-async">
                        @csrf
                        
                        {{-- Dropdown Personalizado de Repartidor --}}
                        <div class="relative flex-1" id="cajaRepartidor_{{ $orden->id }}">
                            <input type="hidden" name="repartidor_id" id="val_repartidor_{{ $orden->id }}" value="">

                            <button type="button" onclick="window.toggleCustomMenu('menu_repartidor_{{ $orden->id }}')" id="btn_repartidor_{{ $orden->id }}"
                                class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-xs font-bold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all text-left">
                                <span id="text_repartidor_{{ $orden->id }}" class="truncate text-slate-400">Seleccionar Repartidor...</span>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                            </button>
                            
                            {{-- Menú que abre hacia abajo (top-full mt-1) --}}
                            <div id="menu_repartidor_{{ $orden->id }}" class="absolute top-full mt-1 left-0 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden max-h-40 overflow-y-auto unique-scrollbar">
                                <button type="button" onclick="window.selectCustomOption('val_repartidor_{{ $orden->id }}', 'text_repartidor_{{ $orden->id }}', 'menu_repartidor_{{ $orden->id }}', '', 'Seleccionar Repartidor...')" class="w-full px-4 py-2.5 text-left text-xs hover:bg-blue-50 font-bold text-slate-500 transition-colors">
                                    Seleccionar Repartidor...
                                </button>
                                @foreach($repartidores as $rep)
                                    <button type="button" onclick="window.selectCustomOption('val_repartidor_{{ $orden->id }}', 'text_repartidor_{{ $orden->id }}', 'menu_repartidor_{{ $orden->id }}', '{{ $rep->id }}', '{{ addslashes($rep->nombre ?? $rep->name) }}')" class="w-full px-4 py-2.5 text-left text-xs hover:bg-blue-50 font-bold text-slate-700 transition-colors">
                                        {{ $rep->nombre ?? $rep->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="h-12 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 active:scale-95 outline-none shrink-0">
                            <i class="fas fa-paper-plane"></i> Enviar
                        </button>
                    </form>

                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-[2rem] border border-slate-200 shadow-sm">
                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto border border-emerald-100 text-emerald-600 mb-3 shadow-sm">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <p class="font-black text-slate-800 text-sm">No hay pedidos pendientes de reparto</p>
                    <p class="text-xs text-slate-400 mt-1">Los nuevos pedidos listos aparecerán aquí automáticamente.</p>
                </div>
            @endforelse
        </div>

        {{-- ========================================================= --}}
        {{-- COLUMNA 2: EN CAMINO --}}
        {{-- ========================================================= --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
                    <i class="fas fa-motorcycle text-blue-500"></i> Pedidos en Camino
                </h2>
                <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black text-blue-600 uppercase tracking-widest border border-blue-100">
                    {{ count($ordenesEnCamino) }} En Ruta
                </span>
            </div>

            @forelse($ordenesEnCamino as $orden)
                <div class="bg-white border border-slate-200 border-l-4 border-l-blue-500 rounded-[1.5rem] p-5 sm:p-6 shadow-sm space-y-4 hover:border-slate-300 transition-all">
                    
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-slate-800 tracking-tight">
                                Ticket #{{ $orden->id }}
                            </h3>
                            <p class="font-bold text-blue-600 text-sm mt-0.5">
                                {{ $orden->cliente->nombre ?? 'Cliente General' }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-xl bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-wider border border-blue-100">
                            {{ round(\Carbon\Carbon::parse($orden->updated_at)->diffInMinutes(now())) }} min totales
                        </span>
                    </div>

                    {{-- Info del Repartidor Asignado --}}
                    <div class="bg-blue-50 border border-blue-100 p-3.5 rounded-2xl space-y-1 shadow-sm">
                        <p class="text-xs font-black text-blue-700 flex items-center gap-2">
                            <i class="fas fa-user-tie"></i> Repartidor: {{ $orden->repartidor->nombre ?? $orden->repartidor->name ?? 'Asignado' }}
                        </p>
                        <p class="text-[11px] font-medium text-slate-500">
                            Salió a las {{ \Carbon\Carbon::parse($orden->updated_at)->format('h:i A') }}
                        </p>
                    </div>

                    {{-- Dirección breve --}}
                    <div class="text-xs text-slate-600 bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-1 shadow-inner">
                        <strong class="text-slate-900 font-bold block">{{ $orden->direccion->calle ?? 'Sin dirección' }}</strong>
                        <span class="block">Col: {{ $orden->direccion->colonia ?? '-' }}</span>
                        <em class="text-slate-400 block">Ref: {{ $orden->direccion->referencia ?? 'Ninguna' }}</em>
                    </div>

                    {{-- Botón Marcar como Entregado --}}
                    <form action="{{ route('admin.repartidores.entregado', $orden->id) }}" method="POST" class="form-async pt-1">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full h-12 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] uppercase tracking-widest shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-2 active:scale-95 outline-none">
                            <i class="fas fa-check"></i> Marcar como Entregado
                        </button>
                    </form>

                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-[2rem] border border-slate-200 shadow-sm">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto border border-slate-200 text-slate-400 mb-3 shadow-sm">
                        <i class="fas fa-road text-2xl"></i>
                    </div>
                    <p class="font-black text-slate-800 text-sm">No hay repartos en camino en este momento</p>
                    <p class="text-xs text-slate-400 mt-1">Los pedidos despachados se mostrarán en esta sección.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

<!-- Script de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DE DROPDOWNS ---
    window.toggleCustomMenu = function(menuId) {
        // Cierra todos los demás menús antes de abrir este
        document.querySelectorAll('div[id^="menu_repartidor_"]').forEach(menu => {
            if(menu.id !== menuId) menu.classList.add('hidden');
        });
        const menu = document.getElementById(menuId);
        if(menu) menu.classList.toggle('hidden');
    };

    window.selectCustomOption = function(inputId, spanId, menuId, value, text) {
        const input = document.getElementById(inputId);
        const span = document.getElementById(spanId);
        const menu = document.getElementById(menuId);
        
        if (input) input.value = value;
        if (span) {
            span.innerText = text;
            span.classList.remove('text-slate-400');
            span.classList.add('text-slate-800');
        }
        if (menu) menu.classList.add('hidden');
    };

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(e) {
        document.querySelectorAll('div[id^="menu_repartidor_"]').forEach(menu => {
            if (!menu.classList.contains('hidden')) {
                const btnId = menu.id.replace('menu_', 'btn_');
                const btn = document.getElementById(btnId);
                if (btn && !btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            }
        });
    });

    // --- LÓGICA DE ENVÍO ASÍNCRONO CON SWEETALERT2 ---
    document.querySelectorAll('.form-async').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validación manual para el input oculto
            const inputRepartidor = this.querySelector('input[name="repartidor_id"]');
            if (inputRepartidor && inputRepartidor.value.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Falta información',
                    text: 'Por favor, selecciona un repartidor antes de enviar.',
                    confirmButtonColor: '#2563eb', // blue-600
                    confirmButtonText: 'Entendido',
                    customClass: {
                        popup: 'rounded-[2rem] border border-slate-200 shadow-sm',
                        title: 'text-slate-800 font-black',
                        confirmButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-6 py-3 outline-none'
                    }
                });
                return;
            }

            const url = this.action;
            const data = new FormData(this);
            const method = this.method;
            const btnSubmit = this.querySelector('button[type="submit"]');
            
            // Evitar múltiples clics
            if(btnSubmit) btnSubmit.disabled = true;

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
                    location.reload(); 
                } else {
                    // Alerta de error elegante
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.message || 'Ocurrió un error al procesar la solicitud.',
                        confirmButtonColor: '#e11d48', // rose-600
                        confirmButtonText: 'Cerrar',
                        customClass: {
                            popup: 'rounded-[2rem] border border-slate-200 shadow-sm',
                            title: 'text-slate-800 font-black',
                            confirmButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-6 py-3 outline-none'
                        }
                    });
                    if(btnSubmit) btnSubmit.disabled = false;
                }
            } catch (err) {
                console.error(err);
                this.submit(); // Fallback tradicional
            }
        });
    });
});
</script>
@endsection