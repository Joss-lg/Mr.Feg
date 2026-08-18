@extends('layouts.admin')

@section('title', 'Dashboard Financiero | Ollintem Pro')
@section('header-title', 'Dashboard Financiero')
@section('header-subtitle', 'Visión analítica de operaciones')

@push('styles')
<style>
    /* Fondo general del sistema en gris extra claro */
    body, html, #app, main, .wrapper, .main-content {
        background-color: #F2F2F2 !important; 
    }
    
    /* Textos del header superior en oscuro */
    header, header h1, header h2, header p, header span, .header-title, .header-subtitle {
        color: #0f172a !important; 
    }

    /* TAMAÑOS DEL TÍTULO */
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
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0; /* Oculto al inicio para que la animación lo muestre */
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-8 lg:p-10 xl:p-12 max-w-[1800px] mx-auto w-full space-y-8 flex-1 flex flex-col overflow-x-hidden min-h-screen">
    
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
        
        <!-- Tarjeta 1: Ingresos Brutos -->
        <div class="group bg-white shadow-sm border border-gray-100/80 rounded-[1.5rem] p-5 sm:p-7 flex flex-col justify-between h-36 sm:h-44 transition-all duration-300 hover:shadow-lg hover:shadow-blue-900/5 hover:border-blue-200 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 0ms;">
            <div class="flex justify-between items-start">
                <h3 class="text-[9px] sm:text-[11px] font-bold text-gray-500 uppercase tracking-[0.15em] leading-tight group-hover:text-blue-600 transition-colors">Ingresos Brutos</h3>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl border border-blue-100 bg-blue-50 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-wallet text-blue-600 text-sm sm:text-base"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-3xl sm:text-[2.75rem] leading-none font-black text-gray-900 tracking-tighter truncate tabular-nums"><span class="text-gray-900 font-bold">$</span>{{ number_format($stats['ventas_dia'] ?? 0, 2) }}</p>
                <p class="text-[10px] sm:text-xs font-bold text-emerald-500 mt-2 sm:mt-3 flex items-center gap-1 sm:gap-1.5 opacity-90 tracking-wide">
                    <i class="fas fa-arrow-trend-up"></i> +4.2% vs ayer
                </p>
            </div>
        </div>

        <!-- Tarjeta 2: Volumen de Órdenes -->
        <div class="group bg-white shadow-sm border border-gray-100/80 rounded-[1.5rem] p-5 sm:p-7 flex flex-col justify-between h-36 sm:h-44 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-900/5 hover:border-cyan-200 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="flex justify-between items-start">
                <h3 class="text-[9px] sm:text-[11px] font-bold text-gray-500 uppercase tracking-[0.15em] leading-tight group-hover:text-cyan-600 transition-colors">Volumen de Órdenes</h3>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl border border-cyan-100 bg-cyan-50 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-receipt text-cyan-500 text-sm sm:text-base"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-3xl sm:text-[2.75rem] leading-none font-black text-gray-900 tracking-tighter truncate tabular-nums">{{ $stats['ordenes_dia'] ?? 0 }}</p>
                <p class="text-[9px] sm:text-[11px] font-semibold text-gray-400 mt-2 sm:mt-3 uppercase tracking-widest">Transacciones</p>
            </div>
        </div>

        <!-- Tarjeta 3: Ticket Promedio -->
        <div class="group bg-white shadow-sm border border-gray-100/80 rounded-[1.5rem] p-5 sm:p-7 flex flex-col justify-between h-36 sm:h-44 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-900/5 hover:border-indigo-200 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 200ms;">
            <div class="flex justify-between items-start">
                <h3 class="text-[9px] sm:text-[11px] font-bold text-gray-500 uppercase tracking-[0.15em] leading-tight group-hover:text-indigo-600 transition-colors">Ticket Promedio</h3>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl border border-indigo-100 bg-indigo-50 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-tag text-indigo-500 text-sm sm:text-base"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-3xl sm:text-[2.75rem] leading-none font-black text-gray-900 tracking-tighter truncate tabular-nums"><span class="text-gray-900 font-bold">$</span>{{ number_format($stats['ticket_promedio'] ?? 0, 2) }}</p>
                <p class="text-[9px] sm:text-[11px] font-semibold text-gray-400 mt-2 sm:mt-3 uppercase tracking-widest">Por Comensal</p>
            </div>
        </div>

        <!-- Tarjeta 4: Afluencia Total -->
        <div class="group bg-white shadow-sm border border-gray-100/80 rounded-[1.5rem] p-5 sm:p-7 flex flex-col justify-between h-36 sm:h-44 transition-all duration-300 hover:shadow-lg hover:shadow-sky-900/5 hover:border-sky-200 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 300ms;">
            <div class="flex justify-between items-start">
                <h3 class="text-[9px] sm:text-[11px] font-bold text-gray-500 uppercase tracking-[0.15em] leading-tight group-hover:text-sky-600 transition-colors">Afluencia Total</h3>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl border border-sky-100 bg-sky-50 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-friends text-sky-500 text-sm sm:text-base"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-3xl sm:text-[2.75rem] leading-none font-black text-gray-900 tracking-tighter truncate tabular-nums">{{ number_format($stats['clientes'] ?? 0, 0) }}</p>
                <p class="text-[9px] sm:text-[11px] font-semibold text-gray-400 mt-2 sm:mt-3 uppercase tracking-widest">Registros</p>
            </div>
        </div>
    </div>

    <!-- Gráfica -->
    <div class="bg-white shadow-sm border border-gray-100/80 rounded-[2rem] p-6 sm:p-8 lg:p-10 w-full flex-1 flex flex-col min-h-[380px] sm:min-h-[450px] animate-fade-in-up" style="animation-delay: 400ms;">
        <div class="flex justify-between items-start mb-6 sm:mb-10">
            <div>
                <h2 class="text-lg sm:text-2xl font-black tracking-tight text-gray-900 flex items-center gap-2">
                    Análisis de Flujo
                    <!-- Animación de pulso (bolita viva) -->
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                </h2>
                <p class="text-[10px] sm:text-sm text-gray-500 font-medium mt-1.5">Métricas de rendimiento a lo largo de la jornada</p>
            </div>
        </div>
        
        <div class="w-full relative flex-1 min-h-[250px] sm:min-h-[300px]">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let myChart; 

    function initChart() {
        const textColor = '#475569'; 
        const gridColor = '#f1f5f9'; 
        const tooltipBg = '#ffffff'; 
        const tooltipText = '#0f172a'; 
        const pointBg = '#ffffff'; 

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = textColor; 

        const ctx = document.getElementById('salesChart').getContext('2d');

        if (myChart) {
            myChart.destroy();
        }

        const chartLabels = {!! json_encode($chart['labels'] ?? ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00']) !!};
        const salesValues = {!! json_encode($chart['sales'] ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) !!};
        const transactionValues = {!! json_encode($chart['transactions'] ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) !!};

        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Ingresos Brutos',
                    data: salesValues,
                    // Se cambió a azul profundo para hacer match con los detalles
                    borderColor: '#2563eb', 
                    backgroundColor: 'transparent',
                    borderWidth: 2.5,
                    pointBackgroundColor: pointBg, 
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false, 
                    tension: 0.4 
                }, {
                    label: 'Transacciones',
                    data: transactionValues,
                    // Se cambió a un celeste/cyan para la segunda línea
                    borderColor: '#0ea5e9', 
                    backgroundColor: 'transparent',
                    borderWidth: 2.5,
                    borderDash: [5, 5],
                    pointBackgroundColor: pointBg,
                    pointBorderColor: '#0ea5e9', 
                    pointBorderWidth: 2,
                    pointRadius: 0, 
                    pointHoverRadius: 6,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { 
                        position: 'top', 
                        align: 'end', 
                        labels: { 
                            usePointStyle: true, 
                            boxWidth: 8, 
                            font: { size: window.innerWidth < 768 ? 11 : 13, weight: '700' }, 
                            color: '#334155',
                            padding: window.innerWidth < 768 ? 15 : 25 
                        } 
                    },
                    tooltip: { 
                        backgroundColor: tooltipBg, 
                        titleColor: tooltipText, 
                        bodyColor: tooltipText, 
                        borderColor: '#e2e8f0', 
                        borderWidth: 1, 
                        padding: 12, 
                        cornerRadius: 12, 
                        titleFont: { size: 13, weight: '700' }, 
                        bodyFont: { size: 14, weight: '600' }, 
                        displayColors: true, 
                        boxPadding: 6, 
                        usePointStyle: true,
                        boxHeight: 8,
                        boxWidth: 8
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        border: { display: false }, 
                        ticks: { font: { weight: '600', size: 11 }, padding: 10, color: '#94a3b8' } 
                    },
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor, drawBorder: false }, 
                        border: { display: false }, 
                        ticks: { font: { weight: '600', size: 11 }, padding: 15, color: '#94a3b8' } 
                    }
                }
            }
        });
    }

    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (myChart) initChart();
        }, 250);
    });

    document.addEventListener('DOMContentLoaded', () => {
        initChart();
    });
</script>
@endpush