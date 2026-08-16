{{-- resources/views/admin/promociones/modal-crear.blade.php --}}
<style>
    /* Ajuste para que el modal no sea tapado por el teclado */
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalCrear { align-items: flex-start !important; padding-top: 10px !important; }
        body.teclado-virtual-abierto #createContainer { max-height: 45dvh !important; }
    }
    .unique-scrollbar::-webkit-scrollbar { width: 6px; }
    .unique-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    .date-input-icon::-webkit-calendar-picker-indicator { opacity: 0; position: absolute; right: 0; width: 2.5rem; height: 100%; cursor: pointer; }
</style>

<div id="modalCrear" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    {{-- Capa trasera para cerrar --}}
    <div class="fixed inset-0 bg-transparent" onclick="window.closeModal('modalCrear', 'createContainer')"></div>
    
    <div id="createContainer" class="relative bg-white border border-slate-200 w-full max-w-2xl mx-auto rounded-[2rem] shadow-2xl scale-95 opacity-0 transition-all duration-300 flex flex-col overflow-hidden max-h-[95vh] sm:max-h-[92vh]">

        {{-- Encabezado del Modal --}}
        <div class="flex items-center justify-between p-6 sm:p-8 pb-4 sm:pb-5 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 border border-blue-100">
                    <i class="fas fa-tags text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight leading-tight truncate">Nueva Promoción</h2>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Configurar oferta especial</p>
                </div>
            </div>
            <button type="button" onclick="window.closeModal('modalCrear', 'createContainer')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-100 hover:rotate-90 active:scale-95 transition-all duration-300 outline-none shrink-0">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Formulario --}}
        <form id="formCrearPromocion" onsubmit="window.guardarPromocion(event)" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            {{-- La promoción siempre se crea activa por defecto --}}
            <input type="hidden" name="esta_activa" value="1">

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-6 overflow-y-auto flex-1 overscroll-contain unique-scrollbar">

                {{-- Nombre de Promoción --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-tag opacity-40"></i> Nombre de la Promoción
                    </label>
                    <input type="text" name="nombre" required data-teclado="texto" autocomplete="off"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" 
                        placeholder="Ej: Combo Familiar">
                </div>

                {{-- Fila: Tipo (Dropdown Custom) y Valor --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    {{-- Dropdown Personalizado: Tipo de Promoción Crear --}}
                    <div class="space-y-2 relative" id="cajaTipoPromoCrear">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-percent opacity-40"></i> Tipo de Promoción
                        </label>
                        
                        <input type="hidden" name="tipo_promocion" id="tipo_promocion_crear" value="porcentaje" required>

                        <button type="button" onclick="window.toggleTipoPromoMenu(event, 'crear')" id="tipoPromoBtnCrear" 
                            class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                            <span id="tipoPromoSelectedCrear" class="text-slate-800 truncate">Porcentaje (%)</span>
                            <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                        </button>
                        
                        <div id="tipoPromoMenuCrear" class="absolute left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto">
                            <button type="button" onclick="window.selectTipoPromo('porcentaje', 'Porcentaje (%)', 'crear')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Porcentaje (%)</button>
                            <button type="button" onclick="window.selectTipoPromo('descuento_fijo', 'Descuento fijo ($)', 'crear')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Descuento fijo ($)</button>
                            <button type="button" onclick="window.selectTipoPromo('dos_por_uno', '2 x 1', 'crear')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">2 x 1</button>
                            <button type="button" onclick="window.selectTipoPromo('combo', 'Combo', 'crear')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Combo</button>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-hashtag opacity-40"></i> Valor / Cantidad
                        </label>
                        <input type="text" name="valor_descuento" required pattern="[0-9]*\.?[0-9]*" data-teclado="numerico" data-teclado-decimales="true" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" 
                            placeholder="Ej: 15.00">
                    </div>
                </div>

                {{-- Fila: Vigencia de Fechas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-calendar-plus opacity-40"></i> Inicio Vigencia
                        </label>
                        <div class="relative">
                            <input type="date" name="fecha_inicio" id="crear_fecha_inicio" required value="{{ date('Y-m-d') }}" 
                                class="date-input-icon w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-10 text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                            <i onclick="window.abrirCalendario('crear_fecha_inicio')" class="fas fa-calendar-days absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer text-sm z-10"></i>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-calendar-check opacity-40"></i> Fin Vigencia
                        </label>
                        <div class="relative">
                            <input type="date" name="fecha_fin" id="crear_fecha_fin" required value="{{ date('Y-m-d', strtotime('+1 month')) }}" 
                                class="date-input-icon w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-10 text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                            <i onclick="window.abrirCalendario('crear_fecha_fin')" class="fas fa-calendar-days absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer text-sm z-10"></i>
                        </div>
                    </div>
                </div>

                {{-- Días de la Semana --}}
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-clock opacity-40"></i> Días de Aplicación Semanal
                    </label>
                    <div class="grid grid-cols-4 sm:flex gap-2 flex-wrap">
                        @php
                            $mapeoDias = [['L' => 1], ['M' => 2], ['X' => 3], ['J' => 4], ['V' => 5], ['S' => 6], ['D' => 7]];
                        @endphp
                        @foreach($mapeoDias as $diaData)
                            @php $letra = key($diaData); $num = $diaData[$letra]; @endphp
                            <label class="flex-1 min-w-[55px] flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 bg-white shadow-sm cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all select-none group/day relative">
                                <input type="checkbox" name="dias_semana[]" value="{{ $num }}" checked class="peer sr-only">
                                <div class="w-5 h-5 rounded-lg border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-600 flex items-center justify-center transition-all mb-1.5">
                                    <i class="fas fa-check text-[10px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                                <span class="text-slate-400 font-black text-[11px] uppercase tracking-wider peer-checked:text-blue-600 transition-colors">{{ $letra }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Selección de Productos --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-box-open opacity-40"></i> Productos Vinculados
                    </label>
                    <div class="max-h-52 overflow-y-auto border border-slate-200 rounded-2xl p-2 bg-white shadow-sm unique-scrollbar divide-y divide-slate-100">
                        @foreach($productos as $producto)
                            <label class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors select-none group/prod">
                                <input type="checkbox" name="productos[]" value="{{ $producto->id }}" class="w-5 h-5 rounded-md border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer accent-blue-600 bg-slate-50 shrink-0">
                                <div class="flex-1">
                                    <p class="text-[13px] font-bold text-slate-700 group-hover/prod:text-blue-600 transition-colors leading-snug flex items-center gap-2">
                                        {{ $producto->nombre }}
                                        @if($producto->se_vende_por_peso)
                                            <span class="text-[8px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 border border-orange-200 px-1.5 py-0.5 rounded-md">Por peso</span>
                                        @endif
                                    </p>
                                    @if($producto->se_vende_por_peso)
                                        <p class="text-slate-500 text-[11px] font-medium mt-0.5">${{ number_format($producto->precio_por_100g ?? 0, 2) }} MXN /100g</p>
                                    @else
                                        <p class="text-slate-500 text-[11px] font-medium mt-0.5">${{ number_format($producto->precio, 2) }} MXN</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Botones Footer --}}
            <div class="flex items-center gap-4 px-6 sm:px-8 py-6 border-t border-slate-200 shrink-0 bg-slate-50">
                <button type="button" onclick="window.closeModal('modalCrear', 'createContainer')"
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all outline-none">
                    Cancelar
                </button>
                <button type="submit" id="btn-guardar-promocion"
                    class="flex-[1.5] h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all active:scale-95 outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Guardar Promoción
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof window.abrirCalendario === 'undefined') {
        window.abrirCalendario = function(inputId) {
            const input = document.getElementById(inputId);
            if (input && typeof input.showPicker === 'function') {
                input.showPicker();
            } else if (input) {
                input.focus();
            }
        };
    }
</script>