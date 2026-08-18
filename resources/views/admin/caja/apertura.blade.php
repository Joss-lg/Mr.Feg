@extends('layouts.admin')

@section('content')
<style>
    /* Fondo general del sistema actualizado a #F2F2F2 */
    body, html, #app, main, .wrapper, .main-content {
        background-color: #F2F2F2 !important; 
    }

    /* Solo aplicamos el truco de subir la tarjeta en pantallas grandes (computadoras/punto de venta) */
    @media (min-width: 768px) {
        /* 1. Mandamos la tarjeta a la parte de arriba de la pantalla */
        body.teclado-virtual-abierto #aperturaCajaWrapper {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }

        /* 2. Hacemos que la tarjeta sea más corta para que no choque con el teclado y active el scroll interno */
        body.teclado-virtual-abierto #aperturaCajaCard {
            max-height: calc(100dvh - 340px) !important;
            overflow-y: auto !important;
        }
    }
</style>

<div id="aperturaCajaWrapper" class="flex items-center justify-center min-h-[80vh] bg-[#F2F2F2] px-4 py-8">
    <div id="aperturaCajaCard" class="max-w-md w-full bg-white rounded-[2rem] shadow-sm p-6 sm:p-10 border border-slate-200 transition-all duration-300">

        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex w-14 h-14 sm:w-16 sm:h-16 items-center justify-center bg-blue-50 text-blue-600 border border-blue-100 rounded-2xl mb-4 shadow-sm">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Apertura de Caja</h2>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-2 leading-relaxed">
                Para comenzar a gestionar mesas y registrar cobros, es necesario iniciar un turno operativo.
            </p>
        </div>

        @if(session('error'))
            <div class="px-4 py-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl mb-4 text-xs font-bold flex items-center gap-2.5">
                <i class="fas fa-exclamation-circle text-rose-500"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl mb-4 text-xs font-bold flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-500"></i>
                {{ session('success') }}
            </div>
        @endif

        <form id="formAperturaCaja" action="{{ route('admin.caja.abrir') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Dropdown Personalizado: Turno --}}
            <div class="space-y-2 relative" id="cajaTurno" data-required-dropdown>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Seleccionar Turno</label>

                <input type="hidden" name="turno" id="val_Turno" value="{{ old('turno') }}">

                <button type="button" onclick="window.toggleCustomMenu('menu_Turno')" id="btn_Turno"
                    class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 shadow-sm transition-all @error('turno') border-rose-400 @enderror">
                    <span id="text_Turno" class="truncate {{ old('turno') ? 'text-slate-800 font-bold' : 'text-slate-400' }}">
                        {{ old('turno') ?? 'Elige el turno actual' }}
                    </span>
                    <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                </button>

                <div id="menu_Turno" class="absolute left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-1">
                    <button type="button" onclick="window.selectCustomOption('val_Turno', 'text_Turno', 'menu_Turno', 'Matutino', 'Matutino')" class="w-full px-5 py-3 flex items-center gap-2.5 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">
                        <i class="fas fa-sun text-amber-500 w-4"></i> Matutino
                    </button>
                    <button type="button" onclick="window.selectCustomOption('val_Turno', 'text_Turno', 'menu_Turno', 'Vespertino', 'Vespertino')" class="w-full px-5 py-3 flex items-center gap-2.5 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">
                        <i class="fas fa-moon text-indigo-500 w-4"></i> Vespertino
                    </button>
                </div>

                @error('turno')
                    <p class="text-rose-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="monto_inicial" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Monto Inicial (Fondo de Caja)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-400 text-sm font-bold">$</span>
                    </div>
                    {{-- TECLADO VIRTUAL NUMÉRICO: type=text (no number) para que el teclado personalizado pueda escribir el valor --}}
                    <input type="text" name="monto_inicial" id="monto_inicial" pattern="[0-9]*\.?[0-9]*" required data-teclado="numerico" data-teclado-titulo="Monto Inicial" inputmode="none"
                        value="{{ old('monto_inicial', '0.00') }}"
                        class="w-full h-12 pl-8 pr-4 rounded-xl border border-slate-200 bg-white text-slate-800 font-semibold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all shadow-sm @error('monto_inicial') border-rose-400 @enderror"
                        placeholder="0.00"
                        onfocus="this.select()">
                </div>
                @error('monto_inicial')
                    <p class="text-rose-500 text-xs font-semibold mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full h-12 sm:h-14 flex items-center justify-center gap-2 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 active:scale-[0.98] transition-all outline-none">
                <i class="fas fa-door-open"></i> Iniciar Turno e Ir a Mesas
            </button>
        </form>

        {{-- HISTORIAL DE TURNOS CERRADOS
             Antes esta pantalla era un callejón sin salida: con la caja
             cerrada no había forma de consultar los cortes anteriores sin
             abrir un turno nuevo. --}}
        @if(($turnosCerrados ?? collect())->isNotEmpty())
            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                        Últimos turnos cerrados
                    </h3>
                    <a href="{{ route('historial.index') }}"
                       class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-700">
                        Ver todo
                    </a>
                </div>

                <ul class="space-y-2">
                    @foreach($turnosCerrados as $turno)
                        <li>
                            <a href="{{ route('historial.show', $turno->id) }}"
                               class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl border border-slate-200 bg-white hover:border-blue-200 hover:bg-blue-50/50 transition-all">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">
                                        {{ $turno->updated_at?->format('d/m/Y') }}
                                        <span class="font-medium text-slate-400">
                                            · {{ $turno->user->nombre ?? 'Sin usuario' }}
                                        </span>
                                    </p>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                                        Contado: ${{ number_format($turno->monto_final_real ?? 0, 2) }}
                                    </p>
                                </div>

                                @php $dif = (float) ($turno->diferencia ?? 0); @endphp
                                <span class="shrink-0 text-[10px] font-black uppercase tracking-wide px-2.5 py-1.5 rounded-lg border
                                    {{ abs($dif) < 0.01
                                        ? 'bg-emerald-50 border-emerald-100 text-emerald-600'
                                        : ($dif < 0
                                            ? 'bg-rose-50 border-rose-100 text-rose-600'
                                            : 'bg-amber-50 border-amber-100 text-amber-600') }}">
                                    @if(abs($dif) < 0.01)
                                        Cuadrada
                                    @elseif($dif < 0)
                                        Faltante ${{ number_format(abs($dif), 2) }}
                                    @else
                                        Sobrante ${{ number_format($dif, 2) }}
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</div>

<script>
    // =====================================================================
    // DROPDOWN PERSONALIZADO: Turno (reemplazo de <select> nativo)
    // Definidas aquí mismo, protegidas con "typeof === 'undefined'" por si
    // ya existen (otra vista/modal en la misma página las definió antes).
    // =====================================================================
    if (typeof window.toggleCustomMenu === 'undefined') {
        window.toggleCustomMenu = function (menuId) {
            document.querySelectorAll('[id^="menu_"]').forEach(menu => {
                if (menu.id !== menuId) menu.classList.add('hidden');
            });
            const menu = document.getElementById(menuId);
            if (menu) menu.classList.toggle('hidden');
        };
    }

    if (typeof window.selectCustomOption === 'undefined') {
        window.selectCustomOption = function (hiddenInputId, textSpanId, menuId, value, label) {
            const hiddenInput = document.getElementById(hiddenInputId);
            const textSpan = document.getElementById(textSpanId);
            const menu = document.getElementById(menuId);

            if (hiddenInput) hiddenInput.value = value;
            if (textSpan) {
                textSpan.textContent = label;
                textSpan.classList.remove('text-slate-400');
                textSpan.classList.add('text-slate-800', 'font-bold');
            }
            if (menu) menu.classList.add('hidden');
        };
    }

    if (!window.__customDropdownOutsideClickBound) {
        window.__customDropdownOutsideClickBound = true;
        document.addEventListener('click', function (e) {
            document.querySelectorAll('[id^="menu_"]').forEach(menu => {
                const btnId = 'btn_' + menu.id.replace('menu_', '');
                const btn = document.getElementById(btnId);
                if (!menu.contains(e.target) && (!btn || !btn.contains(e.target))) {
                    menu.classList.add('hidden');
                }
            });
        });
    }

    // Validación del dropdown obligatorio (Turno) antes de enviar el form
    const formAperturaCaja = document.getElementById('formAperturaCaja');
    if (formAperturaCaja) {
        formAperturaCaja.addEventListener('submit', function (e) {
            const cajasRequeridas = formAperturaCaja.querySelectorAll('[data-required-dropdown]');
            let valido = true;

            cajasRequeridas.forEach(caja => {
                const hidden = caja.querySelector('input[type="hidden"]');
                const boton = caja.querySelector('button[id^="btn_"]');
                if (hidden && !hidden.value) {
                    valido = false;
                    if (boton) boton.classList.add('ring-4', 'ring-rose-500/20', 'border-rose-400');
                }
            });

            if (!valido) {
                e.preventDefault();
                alert('Por favor selecciona el turno antes de continuar.');
            }
        });
    }

    // Nos aseguramos de que el teclado virtual detecte el campo numérico de esta vista.
    // Si tu layout ya llama a esto globalmente, esta llamada es redundante pero inofensiva.
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TecladoVirtual !== 'undefined') {
            TecladoVirtual.attachAll();
        }
    });
</script>
@endsection