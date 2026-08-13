@extends('layouts.admin')

@section('title', 'Dashboard Financiero | Ollintem Pro')
@section('header-title', 'Dashboard Financiero')
@section('header-subtitle', 'Visión analítica de operaciones')

@section('content')
<div class="p-4 sm:p-8 lg:p-10 xl:p-12 max-w-[1800px] mx-auto w-full space-y-8 flex-1 flex flex-col overflow-x-hidden bg-transparent">
    
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-6">
        
        <!-- Tarjeta 1: Ingresos Brutos -->
        <div class="bg-[#04243b]/60 backdrop-blur-xl border border-[#8fc1f0]/15 shadow-2xl shadow-black/30 rounded-[1.2rem] sm:rounded-[1.5rem] p-4 sm:p-7 flex flex-col justify-between h-32 sm:h-44 group transition-all duration-300 hover:bg-[#04243b]/80">
            <div class="flex justify-between items-start">
                <h3 class="text-[8px] sm:text-[10px] font-bold text-[#8fc1f0] uppercase tracking-[0.1em] sm:tracking-[0.15em] leading-tight">Ingresos Brutos</h3>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#38b6ff]/20 bg-white/5 flex items-center justify-center shrink-0 group-hover:border-[#f97316] transition-all duration-300">
                    <i class="fas fa-wallet text-[#93c5fd] text-xs sm:text-sm group-hover:text-[#f97316] group-hover:scale-110 transition-all duration-300"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-[2.5rem] leading-none font-black text-white tracking-tighter truncate tabular-nums"><span class="text-white/70 font-bold">$</span>{{ number_format($stats['ventas_dia'] ?? 0, 2) }}</p>
                <p class="text-[9px] sm:text-[11px] font-bold text-[#fb923c] mt-1.5 sm:mt-3 flex items-center gap-1 sm:gap-1.5 opacity-90 tracking-wide">
                    <i class="fas fa-arrow-trend-up"></i> +4.2% vs ayer
                </p>
            </div>
        </div>

        <!-- Tarjeta 2: Volumen de Órdenes -->
        <div class="bg-[#04243b]/60 backdrop-blur-xl border border-[#8fc1f0]/15 shadow-2xl shadow-black/30 rounded-[1.2rem] sm:rounded-[1.5rem] p-4 sm:p-7 flex flex-col justify-between h-32 sm:h-44 group transition-all duration-300 hover:bg-[#04243b]/80">
            <div class="flex justify-between items-start">
                <h3 class="text-[8px] sm:text-[10px] font-bold text-[#8fc1f0] uppercase tracking-[0.1em] sm:tracking-[0.15em] leading-tight">Volumen de Órdenes</h3>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#38b6ff]/20 bg-white/5 flex items-center justify-center shrink-0 group-hover:border-[#38b6ff] transition-all duration-300">
                    <i class="fas fa-receipt text-[#93c5fd] text-xs sm:text-sm group-hover:text-[#38b6ff] group-hover:scale-110 transition-all duration-300"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-[2.5rem] leading-none font-black text-[#e6f0fa] tracking-tighter truncate tabular-nums">{{ $stats['ordenes_dia'] ?? 0 }}</p>
                <p class="text-[8px] sm:text-[10px] font-semibold text-[#a8cdf0] opacity-85 mt-1.5 sm:mt-3 uppercase tracking-wide">Transacciones</p>
            </div>
        </div>

        <!-- Tarjeta 3: Ticket Promedio -->
        <div class="bg-[#04243b]/60 backdrop-blur-xl border border-[#8fc1f0]/15 shadow-2xl shadow-black/30 rounded-[1.2rem] sm:rounded-[1.5rem] p-4 sm:p-7 flex flex-col justify-between h-32 sm:h-44 group transition-all duration-300 hover:bg-[#04243b]/80">
            <div class="flex justify-between items-start">
                <h3 class="text-[8px] sm:text-[10px] font-bold text-[#8fc1f0] uppercase tracking-[0.1em] sm:tracking-[0.15em] leading-tight">Ticket Promedio</h3>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#38b6ff]/20 bg-white/5 flex items-center justify-center shrink-0 group-hover:border-[#f97316] transition-all duration-300">
                    <i class="fas fa-tag text-[#93c5fd] text-xs sm:text-sm group-hover:text-[#f97316] group-hover:scale-110 transition-all duration-300"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-[2.5rem] leading-none font-black text-white tracking-tighter truncate tabular-nums"><span class="text-white/70 font-bold">$</span>{{ number_format($stats['ticket_promedio'] ?? 0, 2) }}</p>
                <p class="text-[8px] sm:text-[10px] font-semibold text-[#a8cdf0] opacity-85 mt-1.5 sm:mt-3 uppercase tracking-wide">Por Comensal</p>
            </div>
        </div>

        <!-- Tarjeta 4: Afluencia Total -->
        <div class="bg-[#04243b]/60 backdrop-blur-xl border border-[#8fc1f0]/15 shadow-2xl shadow-black/30 rounded-[1.2rem] sm:rounded-[1.5rem] p-4 sm:p-7 flex flex-col justify-between h-32 sm:h-44 group transition-all duration-300 hover:bg-[#04243b]/80">
            <div class="flex justify-between items-start">
                <h3 class="text-[8px] sm:text-[10px] font-bold text-[#8fc1f0] uppercase tracking-[0.1em] sm:tracking-[0.15em] leading-tight">Afluencia Total</h3>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#38b6ff]/20 bg-white/5 flex items-center justify-center shrink-0 group-hover:border-[#f97316] transition-all duration-300">
                    <i class="fas fa-user-friends text-[#93c5fd] text-xs sm:text-sm group-hover:text-[#f97316] group-hover:scale-110 transition-all duration-300"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-[2.5rem] leading-none font-black text-[#e6f0fa] tracking-tighter truncate tabular-nums">{{ number_format($stats['clientes'] ?? 0, 0) }}</p>
                <p class="text-[8px] sm:text-[10px] font-semibold text-[#a8cdf0] opacity-85 mt-1.5 sm:mt-3 uppercase tracking-wide">Registros</p>
            </div>
        </div>
    </div>

    <!-- Gráfica -->
    <div class="bg-[#04243b]/60 backdrop-blur-xl border border-[#8fc1f0]/15 shadow-2xl shadow-black/30 rounded-[1.5rem] sm:rounded-[2rem] p-4 sm:p-8 lg:p-10 w-full flex-1 flex flex-col min-h-[380px] sm:min-h-[450px]">
        <div class="flex justify-between items-start mb-5 sm:mb-8">
            <div>
                <h2 class="text-base sm:text-xl font-black tracking-tight text-white">Análisis de Flujo</h2>
                <p class="text-[9px] sm:text-xs text-[#a8cdf0] font-semibold mt-1.5">Métricas de rendimiento a lo largo de la jornada</p>
            </div>
        </div>
        
        <div class="w-full relative flex-1 min-h-[230px] sm:min-h-[300px]">
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
        const textColor = '#6fa3d8';
        const gridColor = 'rgba(111, 163, 216, 0.12)';
        const tooltipBg = '#04243b'; 
        const tooltipText = '#ffffff'; 
        const pointBg = '#0a4670'; 

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = textColor; 

        const ctx = document.getElementById('salesChart').getContext('2d');
        
        const orangeGlow = ctx.createLinearGradient(0, 0, 0, 400);
        orangeGlow.addColorStop(0, 'rgba(249, 115, 22, 0.25)'); 
        orangeGlow.addColorStop(1, 'rgba(249, 115, 22, 0)');

        if (myChart) {
            myChart.destroy();
        }

        const chartLabels = {!! json_encode($chart['labels'] ?? ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00']) !!};
        const salesValues = {!! json_encode($chart['sales'] ?? [0, 0, 0, 0, 0, 0, 0, 0]) !!};
        const transactionValues = {!! json_encode($chart['transactions'] ?? [0, 0, 0, 0, 0, 0, 0, 0]) !!};

        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Ingresos Brutos',
                    data: salesValues,
                    borderColor: '#f97316', 
                    backgroundColor: orangeGlow,
                    borderWidth: 3,
                    pointBackgroundColor: pointBg, 
                    pointBorderColor: '#f97316',
                    pointBorderWidth: 2.5,
                    pointRadius: window.innerWidth < 768 ? 2 : 4,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.45 
                }, {
                    label: 'Transacciones',
                    data: transactionValues,
                    borderColor: '#38b6ff', 
                    borderWidth: 2.5,
                    borderDash: [5, 5], 
                    pointBackgroundColor: pointBg,
                    pointBorderColor: '#38b6ff', 
                    pointBorderWidth: 2,
                    pointRadius: 0, 
                    pointHoverRadius: 6,
                    tension: 0.45
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
                            font: { size: window.innerWidth < 768 ? 10 : 12, weight: '700' }, 
                            color: '#ffffff',
                            padding: window.innerWidth < 768 ? 15 : 25 
                        } 
                    },
                    tooltip: { 
                        backgroundColor: tooltipBg, 
                        titleColor: tooltipText, 
                        bodyColor: tooltipText, 
                        borderColor: gridColor, 
                        borderWidth: 1, 
                        padding: 12, 
                        cornerRadius: 12, 
                        titleFont: { size: 12, weight: '600' }, 
                        bodyFont: { size: 13, weight: 'bold' }, 
                        displayColors: true, 
                        boxPadding: 6, 
                        usePointStyle: true 
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        border: { display: false }, 
                        ticks: { font: { weight: '600', size: 10 }, padding: 8 } 
                    },
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor, borderDash: [6, 6] }, 
                        border: { display: false }, 
                        ticks: { font: { weight: '600', size: 10 }, padding: window.innerWidth < 768 ? 5 : 15 } 
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