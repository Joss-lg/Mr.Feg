@extends('layouts.admin')

@section('title', 'Caja | Ollintem Pro')

@section('content')
@php
    // $mesasLibres ahora llega desde CajaController: ya NO se puede calcular
    // aquí porque $mesas viene filtrada y solo trae las mesas con cuenta
    // abierta, así que este conteo siempre daría 0.
    $mesasLibres = $mesasLibres ?? 0;
@endphp

<div id="toastContainer" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-8 sm:bottom-8 z-[9999] flex flex-col gap-3 items-center sm:items-end" aria-live="polite" aria-atomic="true"></div>

{{-- Contenedor principal con el fondo actualizado --}}
<div class="px-4 py-6 sm:p-8 lg:p-10 w-full max-w-[1800px] mx-auto space-y-6 sm:space-y-8 relative z-10 min-h-screen bg-[#F2F2F2] font-sans transition-colors duration-300">

    {{-- ALERTAS DE SESIÓN --}}
    @if(session('error'))
        <div class="px-5 py-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl shadow-sm text-xs font-bold flex items-center gap-3 animate-fade-in-up">
            <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl shadow-sm text-xs font-bold flex items-center gap-3 animate-fade-in-up">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ENCABEZADO PREMIUM --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 0ms;">
        <div class="space-y-2 sm:space-y-3 max-w-2xl w-full">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 sm:px-4 py-1.5 sm:py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse shrink-0"></span>
                Panel Financiero · Turno {{ $cajaActiva->turno ?? 'N/A' }}
            </div>
            <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight drop-shadow-sm">Panel de Caja</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 tracking-wide">Consulta el estado de las mesas y el corte de tu turno en tiempo real.</p>
        </div>

        {{-- BARRA DE ESTADÍSTICAS --}}
        <div class="bg-white rounded-[2rem] p-5 sm:p-6 border border-slate-200 shadow-sm w-full xl:w-auto flex items-center justify-between sm:justify-end gap-4 sm:gap-8 sm:px-8">
            <div class="text-center sm:text-right flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Mesas Activas</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-800 leading-none mt-1" id="mesas-activas-display">{{ $mesasActivas ?? 0 }}</p>
            </div>
            <div class="w-px h-8 sm:h-12 bg-slate-200"></div>
            <div class="text-center sm:text-left flex-1 sm:flex-none">
                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">Mesas Libres</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-500 leading-none mt-1" id="mesas-libres-display">{{ $mesasLibres }}</p>
            </div>
        </div>
    </div>

    {{-- GRID DE MESAS --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 150ms;" id="mesas-container">
        @include('admin.caja.partials.mesas')
    </div>
</div>

<script>
    window.mostrarToast = function(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        const typeClasses = type === 'success' ? 'border-l-4 border-emerald-500' : 'border-l-4 border-rose-500';

        toast.className = `w-full sm:min-w-[300px] sm:w-auto p-4 rounded-2xl bg-white border border-slate-200 shadow-xl flex items-center gap-3 opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-5 transition-all duration-300 ${typeClasses}`;
        toast.innerHTML = `<div><strong class="block text-sm font-black text-slate-800">${type === 'success' ? 'Éxito' : 'Error'}</strong><span class="text-xs font-medium text-slate-500">${message}</span></div>`;

        container.appendChild(toast);

        setTimeout(() => { toast.classList.remove('opacity-0', 'translate-y-3', 'sm:translate-x-5'); }, 50);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-3', 'sm:translate-x-5');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    document.addEventListener('DOMContentLoaded', function () {
        // --- FILTROS POR ESTADO ---
        // Se usa delegación en el contenedor (y no querySelectorAll una sola
        // vez) porque las tarjetas se reemplazan cada 5s con el auto-refresco:
        // las referencias capturadas al cargar apuntarían a elementos que ya
        // no existen y el filtro dejaría de funcionar tras el primer refresco.
        const botonesFiltro = document.querySelectorAll('[data-filter]');
        let filtroActivo = 'all';

        const aplicarFiltro = () => {
            document.querySelectorAll('[data-mesa-status]').forEach(card => {
                const coincide = filtroActivo === 'all' || card.dataset.mesaStatus === filtroActivo;
                card.style.display = coincide ? 'flex' : 'none';
                card.style.opacity = coincide ? '1' : '0';
            });
        };

        botonesFiltro.forEach(boton => {
            boton.addEventListener('click', () => {
                botonesFiltro.forEach(b => b.classList.remove('filter-button--active'));
                boton.classList.add('filter-button--active');
                filtroActivo = boton.dataset.filter;
                aplicarFiltro();
            });
        });

        // ---------------------------------------------------------------
        // AUTO-REFRESCO (mismo patrón que Cocina)
        // Consulta un endpoint que devuelve solo las tarjetas y los
        // contadores. Antes se descargaba la página entera cada 4s y se
        // recortaba con DOMParser: mucho más pesado y, si fallaba, el error
        // se tragaba en silencio.
        // ---------------------------------------------------------------
        const URL_API_MESAS = @json(route('admin.caja.api.mesas'));
        let refrescando = false;

        async function actualizarMesas() {
            // Evita que se encimen dos consultas si el servidor va lento.
            if (refrescando || document.hidden) return;
            refrescando = true;

            try {
                const res = await fetch(URL_API_MESAS, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });

                if (!res.ok) {
                    console.error('apiMesas falló:', res.status);
                    return;
                }

                const data = await res.json();

                // La caja se cerró desde otra terminal: recargamos para que
                // aparezca la pantalla de apertura en vez de dejar tarjetas
                // de mesas que ya no se pueden cobrar.
                if (data && data.caja_cerrada) {
                    window.location.reload();
                    return;
                }

                if (!data || !data.success) {
                    console.error('apiMesas respondió sin success:', data);
                    return;
                }

                const contenedor = document.getElementById('mesas-container');
                if (contenedor && contenedor.innerHTML.trim() !== data.html.trim()) {
                    contenedor.innerHTML = data.html;
                    aplicarFiltro(); // reaplicar el filtro a las tarjetas nuevas
                }

                const setTexto = (id, valor) => {
                    const el = document.getElementById(id);
                    if (el && el.innerText !== String(valor)) el.innerText = valor;
                };
                setTexto('total-abierto-display', data.totalAbierto);
                setTexto('mesas-activas-display', data.mesasActivas);
                setTexto('mesas-libres-display', data.mesasLibres);

            } catch (err) {
                console.error('Error actualizando mesas de caja:', err);
            } finally {
                refrescando = false;
            }
        }

        setInterval(actualizarMesas, 5000);

        // Al volver a la pestaña se actualiza de inmediato, sin esperar los 5s.
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) actualizarMesas();
        });
    });
</script>
@endsection