@extends('layouts.admin')

@section('title', 'Delivery | Ollintem Pro')

@section('content')
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-4xl mx-auto space-y-6 sm:space-y-8 relative z-10 font-sans min-h-screen bg-[#F2F2F2] text-slate-800 transition-colors duration-300">

    {{-- ENCABEZADO PREMIUM --}}
    <div class="flex items-center gap-4 bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm">
        <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-orange-50 border border-orange-100 text-orange-600 shrink-0 shadow-sm">
            <i class="fas fa-motorcycle text-lg"></i>
        </div>
        <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Plataformas de Delivery</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500">
                Estos porcentajes se negocian directamente con cada plataforma. Ajústalos aquí cuando cambie tu contrato.
            </p>
        </div>
    </div>

    {{-- CONTENEDOR DE PLATAFORMAS --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] p-5 sm:p-6 shadow-sm space-y-4">
        @foreach($plataformas as $plataforma)
            <div class="plataforma-card p-4 sm:p-5 rounded-2xl border border-slate-200 bg-slate-50/50 transition-all hover:border-slate-300"
                 data-id="{{ $plataforma->id }}">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    
                    {{-- Nombre e Identificador de Color --}}
                    <div class="flex items-center gap-3 sm:w-48 shrink-0">
                        <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-sm" style="background-color: {{ $plataforma->color }}"></span>
                        <span class="font-black text-sm text-slate-800">{{ $plataforma->nombre }}</span>
                    </div>

                    {{-- Entradas de Porcentajes --}}
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">
                                % Comisión
                            </label>
                            <input type="text" inputmode="decimal" data-teclado="numerico" data-teclado-decimales="true" autocomplete="off"
                                   class="input-comision w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                                   value="{{ number_format($plataforma->comision_porcentaje, 2, '.', '') }}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">
                                % IVA sobre comisión
                            </label>
                            <input type="text" inputmode="decimal" data-teclado="numerico" data-teclado-decimales="true" autocomplete="off"
                                   class="input-iva w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                                   value="{{ number_format($plataforma->iva_comision_porcentaje, 2, '.', '') }}">
                        </div>
                    </div>

                    {{-- Controles de Estado y Guardado --}}
                    <div class="flex items-center justify-between sm:justify-end gap-4 sm:w-auto shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200">
                        {{-- Switch Tipo iOS --}}
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="input-activo sr-only peer" {{ $plataforma->activo ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 peer-checked:after:border-transparent shadow-inner"></div>
                        </label>

                        <button type="button" class="btn-guardar px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all active:scale-95 outline-none flex items-center gap-2">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
                <p class="mensaje-guardado hidden text-[11px] font-bold mt-2 ml-1"></p>
            </div>
        @endforeach

        @if($plataformas->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto border border-slate-200 text-slate-400 mb-3">
                    <i class="fas fa-motorcycle text-2xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-700">No hay plataformas configuradas todavía.</p>
            </div>
        @endif
    </div>

    <p class="text-[11px] text-slate-500 text-center font-medium px-4">
        La comisión se calcula sobre el precio de venta (subtotal + IVA del producto) y se suma al total que paga el cliente en el pedido de delivery.
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.plataforma-card').forEach(card => {
        card.querySelector('.btn-guardar').addEventListener('click', async () => {
            const id = card.dataset.id;
            const mensaje = card.querySelector('.mensaje-guardado');
            const btnGuardar = card.querySelector('.btn-guardar');

            const leerPorcentaje = (selector) => {
                const el = card.querySelector(selector);
                const crudo = (el.value || '').trim().replace(',', '.');
                const num = parseFloat(crudo);
                return isNaN(num) ? null : num;
            };

            const comision = leerPorcentaje('.input-comision');
            const iva = leerPorcentaje('.input-iva');
            const activo = card.querySelector('.input-activo').checked;

            const mostrarError = (texto) => {
                mensaje.textContent = texto;
                mensaje.classList.remove('hidden', 'text-emerald-500');
                mensaje.classList.add('text-rose-500');
                setTimeout(() => mensaje.classList.add('hidden'), 3000);
            };

            if (comision === null || iva === null) {
                mostrarError('Escribe un número válido (ej. 25.5)');
                return;
            }
            if (comision < 0 || comision > 100 || iva < 0 || iva > 100) {
                mostrarError('Los porcentajes deben estar entre 0 y 100');
                return;
            }

            card.querySelector('.input-comision').value = comision.toFixed(2);
            card.querySelector('.input-iva').value = iva.toFixed(2);

            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                // Ruta dinámica con prefijo admin blindada para evitar errores 404
                const res = await fetch("{{ url('admin/delivery') }}/" + id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        comision_porcentaje: comision,
                        iva_comision_porcentaje: iva,
                        activo: activo,
                    }),
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    mensaje.textContent = 'Guardado correctamente';
                    mensaje.classList.remove('hidden', 'text-rose-500');
                    mensaje.classList.add('text-emerald-500');
                } else {
                    mensaje.textContent = data.message || 'Error al guardar';
                    mensaje.classList.remove('hidden', 'text-emerald-500');
                    mensaje.classList.add('text-rose-500');
                }
            } catch (e) {
                mensaje.textContent = 'Error de conexión al guardar';
                mensaje.classList.remove('hidden', 'text-emerald-500');
                mensaje.classList.add('text-rose-500');
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = textoOriginal;
            }

            setTimeout(() => mensaje.classList.add('hidden'), 2500);
        });
    });
});
</script>

{{-- Inclusión del Teclado Virtual --}}
@include('partials.teclado-virtual')
<script src="{{ asset('js/teclado-virtual.js') }}"></script>
@endsection