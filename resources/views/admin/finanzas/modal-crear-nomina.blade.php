{{-- Estilos para manejo de teclado virtual en PC --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalCrearNomina {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        body.teclado-virtual-abierto #createNominaContainer {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important; 
        }
    }
</style>

<div id="modalCrearNomina" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-opacity duration-300">
    
    <div id="createNominaContainer" class="bg-slate-50 border border-slate-200 w-full max-w-md rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[92dvh]">

        {{-- CABECERA MODAL --}}
        <div class="px-6 pt-6 pb-4 flex justify-between items-start flex-shrink-0">
            <div>
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-2 tracking-tight">
                    Pago de Nómina <i class="fas fa-users text-blue-600 text-base"></i>
                </h3>
                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">Registrar pago a empleado</p>
            </div>
            <button type="button" onclick="closeModal('modalCrearNomina', 'createNominaContainer')" class="text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all duration-300 w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:border-rose-200 hover:bg-rose-50 outline-none flex-shrink-0 shadow-sm active:scale-95">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.pagos-nomina.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            <div class="px-6 pb-4 space-y-4 overflow-y-auto flex-1 overscroll-contain scrollbar-thin" style="-webkit-overflow-scrolling: touch;">

                {{-- Empleado Dropdown Personalizado --}}
                <div class="relative">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Empleado</label>
                    
                    <input type="hidden" name="user_id" id="empleado_id_input" required>
                    
                    <button type="button" onclick="toggleDropdown('empleadoMenu', event)" 
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user text-slate-400 text-sm"></i>
                            <span id="empleadoSelected" class="text-slate-400">Seleccionar empleado...</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </button>
                    
                    <div id="empleadoMenu" class="absolute w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-2 max-h-60 overflow-y-auto">
                        @foreach($empleados ?? [] as $empleado)
                            <button type="button" 
                                onclick="selectEmpleado('{{ $empleado->id }}', '{{ $empleado->nombre }}', '{{ $empleado->sueldo_base ?? 0 }}')" 
                                class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                                {{ $empleado->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Período --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Período</label>
                    <div class="relative group">
                        <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-blue-600 transition-colors"></i>
                        <input type="text" name="periodo" required data-teclado="texto"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 text-sm font-semibold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder:text-slate-400 shadow-sm"
                            placeholder="Ej: 1-15 Mayo 2026">
                    </div>
                </div>

                {{-- Grid de Sueldos y Bonos --}}
                <div class="grid grid-cols-3 gap-2 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                    <div class="space-y-1.5 min-w-0">
                        <label class="text-[8px] font-black text-slate-500 uppercase tracking-widest text-center block">Sueldo Base</label>
                        <input type="text" name="sueldo_base" required id="sueldoBase" data-teclado="numerico" data-teclado-decimales="true"
                            class="w-full h-10 bg-slate-50 border border-slate-100 rounded-lg text-xs font-bold text-slate-800 text-center outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-400" placeholder="0.00" oninput="calcularMonto()">
                    </div>
                    <div class="space-y-1.5 min-w-0">
                        <label class="text-[8px] font-black text-emerald-500 uppercase tracking-widest text-center block">+ Bonos</label>
                        <input type="text" name="bonos" value="0" data-teclado="numerico" data-teclado-decimales="true"
                            class="w-full h-10 bg-slate-50 border border-emerald-100 rounded-lg text-xs font-bold text-emerald-600 text-center outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder:text-emerald-300" placeholder="0.00" oninput="calcularMonto()">
                    </div>
                    <div class="space-y-1.5 min-w-0">
                        <label class="text-[8px] font-black text-rose-500 uppercase tracking-widest text-center block">- Descuentos</label>
                        <input type="text" name="deducciones" value="0" data-teclado="numerico" data-teclado-decimales="true"
                            class="w-full h-10 bg-slate-50 border border-rose-100 rounded-lg text-xs font-bold text-rose-600 text-center outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-500/20 transition-all placeholder:text-rose-300" placeholder="0.00" oninput="calcularMonto()">
                    </div>
                </div>

                <input type="hidden" name="estado" value="pagado">

                {{-- Método de Pago --}}
                <div class="relative">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Método de Pago</label>
                    <input type="hidden" name="metodo_pago" id="pago_metodo_input" required>
                    <button type="button" onclick="toggleDropdown('pagoMetodoMenu', event)" 
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-credit-card text-slate-400 text-sm"></i>
                            <span id="pagoMetodoSelected" class="text-slate-400">Seleccionar método...</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </button>
                    <div id="pagoMetodoMenu" class="absolute w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-2">
                        @foreach(['Efectivo', 'Tarjeta', 'Transferencia'] as $metodo)
                            <button type="button" onclick="selectPagoOption('pagoMetodoSelected', 'pago_metodo_input', '{{ $metodo }}', '{{ $metodo }}')" 
                                class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                                {{ $metodo }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tarjeta de Monto Neto --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-center justify-between gap-3 mt-2 shadow-sm">
                    <div class="min-w-0">
                        <label class="text-[9px] font-black text-blue-600 uppercase tracking-widest ml-1">Monto Neto a Pagar</label>
                    </div>
                    <div class="text-2xl font-black text-blue-600 tracking-tight tabular-nums">$ <span id="montoNeto">0.00</span></div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-5 border-t border-slate-200/60 bg-slate-100/50 flex items-center justify-between flex-shrink-0" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
                <button type="button" onclick="closeModal('modalCrearNomina', 'createNominaContainer')" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors outline-none active:scale-95">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 outline-none">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectEmpleado(id, nombre, sueldo) {
        document.getElementById('empleadoSelected').innerText = nombre;
        document.getElementById('empleadoSelected').classList.remove('text-slate-400');
        document.getElementById('empleadoSelected').classList.add('text-slate-800');
        document.getElementById('empleado_id_input').value = id;
        
        const sueldoInput = document.getElementById('sueldoBase');
        sueldoInput.value = parseFloat(sueldo).toFixed(2);
        
        document.getElementById('empleadoMenu').classList.add('hidden');
        calcularMonto();
    }

    function selectPagoOption(labelId, inputId, valor, texto) {
        document.getElementById(labelId).innerText = texto;
        document.getElementById(labelId).classList.remove('text-slate-400');
        document.getElementById(labelId).classList.add('text-slate-800');
        document.getElementById(inputId).value = valor;
        document.getElementById('pagoMetodoMenu').classList.add('hidden');
    }

    function calcularMonto() {
        const base = parseFloat(document.getElementById('sueldoBase').value) || 0;
        const bonos = parseFloat(document.querySelector('input[name="bonos"]').value) || 0;
        const deducciones = parseFloat(document.querySelector('input[name="deducciones"]').value) || 0;
        const neto = base + bonos - deducciones;
        document.getElementById('montoNeto').innerText = Math.max(0, neto).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
</script>