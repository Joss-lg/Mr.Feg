{{-- resources/views/admin/promociones/modal-editar.blade.php --}}
<style>
    /* Ajuste para que el modal no sea tapado por el teclado */
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalEditar { align-items: flex-start !important; padding-top: 10px !important; }
        body.teclado-virtual-abierto #modalEditarPromocionContent { max-height: 45dvh !important; }
    }
    .unique-scrollbar::-webkit-scrollbar { width: 6px; }
    .unique-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    .date-input-icon::-webkit-calendar-picker-indicator { opacity: 0; position: absolute; right: 0; width: 2.5rem; height: 100%; cursor: pointer; }
</style>

<div id="modalEditar" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    <div class="fixed inset-0 bg-transparent" onclick="window.closeModal('modalEditar', 'modalEditarPromocionContent')"></div>
    
    <div id="modalEditarPromocionContent" class="relative bg-white border border-slate-200 w-full max-w-2xl mx-auto rounded-[2rem] shadow-2xl scale-95 opacity-0 transition-all duration-300 flex flex-col overflow-hidden max-h-[95vh] sm:max-h-[92vh]">

        {{-- Encabezado del Modal --}}
        <div class="flex items-center justify-between p-6 sm:p-8 pb-4 sm:pb-5 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 border border-blue-100">
                    <i class="fas fa-pen text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight leading-tight truncate">Modificar Promoción</h2>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Actualizar parámetros de oferta</p>
                </div>
            </div>
            <button type="button" onclick="window.closeModal('modalEditar', 'modalEditarPromocionContent')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-100 hover:rotate-90 active:scale-95 transition-all duration-300 outline-none shrink-0">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- El campo esta_activa se envía oculto para mantener su estado --}}
        <input type="checkbox" name="esta_activa" value="1" id="edit_esta_activa" class="hidden" form="formEditarPromocion">

        {{-- Formulario --}}
        <form id="formEditarPromocion" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            @method('PUT')

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-6 overflow-y-auto flex-1 overscroll-contain unique-scrollbar">

                {{-- Nombre de Promoción --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-tag opacity-40"></i> Nombre de la Promoción
                    </label>
                    <input type="text" name="nombre" id="edit_nombre" required data-teclado="texto" autocomplete="off"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" 
                        placeholder="Ej: Jueves de Alitas 2x1">
                </div>

                {{-- Descripción --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-align-left opacity-40"></i> Descripción de la Oferta
                    </label>
                    <textarea name="descripcion" id="edit_descripcion" rows="2" data-teclado="texto" autocomplete="off"
                        class="w-full bg-white border border-slate-200 rounded-xl py-3 px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm resize-none" 
                        placeholder="Breve nota explicativa para los meseros o clientes..."></textarea>
                </div>

                {{-- Fila: Tipo (Dropdown Custom) y Valor --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    {{-- Dropdown Personalizado: Tipo de Promoción --}}
                    <div class="space-y-2 relative" id="cajaTipoPromoEditar">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-percent opacity-40"></i> Tipo de Promoción
                        </label>
                        
                        <input type="hidden" name="tipo_promocion" id="tipo_promocion_editar" value="porcentaje" required>

                        <button type="button" onclick="window.toggleTipoPromoMenu(event, 'editar')" id="tipoPromoBtnEditar" 
                            class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                            <span id="tipoPromoSelectedEditar" class="text-slate-800 truncate">Porcentaje (%)</span>
                            <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                        </button>
                        
                        <div id="tipoPromoMenuEditar" class="absolute left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto">
                            <button type="button" onclick="window.selectTipoPromo('porcentaje', 'Porcentaje (%)', 'editar')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Porcentaje (%)</button>
                            <button type="button" onclick="window.selectTipoPromo('descuento_fijo', 'Descuento fijo ($)', 'editar')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Descuento fijo ($)</button>
                            <button type="button" onclick="window.selectTipoPromo('dos_por_uno', '2 x 1', 'editar')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">2 x 1</button>
                            <button type="button" onclick="window.selectTipoPromo('combo', 'Combo', 'editar')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Combo</button>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-hashtag opacity-40"></i> Valor / Cantidad
                        </label>
                        <input type="text" name="valor_descuento" id="edit_valor_descuento" required pattern="[0-9]*\.?[0-9]*" data-teclado="numerico" data-teclado-decimales="true" autocomplete="off"
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
                            <input type="date" name="fecha_inicio" id="edit_fecha_inicio" required 
                                class="date-input-icon w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-10 text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                            <i onclick="window.abrirCalendario('edit_fecha_inicio')" class="fas fa-calendar-days absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer text-sm z-10"></i>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-calendar-check opacity-40"></i> Fin Vigencia
                        </label>
                        <div class="relative">
                            <input type="date" name="fecha_fin" id="edit_fecha_fin" required 
                                class="date-input-icon w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-10 text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                            <i onclick="window.abrirCalendario('edit_fecha_fin')" class="fas fa-calendar-days absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer text-sm z-10"></i>
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
                            $mapeoDiasEdit = [['L' => 1], ['M' => 2], ['X' => 3], ['J' => 4], ['V' => 5], ['S' => 6], ['D' => 7]];
                        @endphp
                        @foreach($mapeoDiasEdit as $diaData)
                            @php $letra = key($diaData); $num = $diaData[$letra]; @endphp
                            <label class="flex-1 min-w-[55px] flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 bg-white shadow-sm cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all select-none group/day relative">
                                <input type="checkbox" name="dias_semana[]" value="{{ $num }}" id="edit_dia_{{ $num }}" class="edit-dia-checkbox peer sr-only">
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
                                <input type="checkbox" name="productos[]" value="{{ $producto->id }}" id="edit_prod_{{ $producto->id }}" class="edit-prod-checkbox w-5 h-5 rounded-md border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer accent-blue-600 bg-slate-50 shrink-0">
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
                <button type="button" onclick="window.closeModal('modalEditar', 'modalEditarPromocionContent')"
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all outline-none">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-[1.5] h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all active:scale-95 outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Actualizar Promoción
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

    /**
     * Dropdown personalizado "Tipo de Promoción" (usado en modal-editar).
     * toggleTipoPromoMenu: abre/cierra el menú de opciones.
     * selectTipoPromo: guarda el valor real en el input hidden y actualiza el texto visible.
     */
    window.toggleTipoPromoMenu = function(event, contexto) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('tipoPromoMenu' + capitalizar(contexto));
        if (!menu) return;

        // Cierra cualquier otro menú de este mismo tipo que esté abierto
        document.querySelectorAll('[id^="tipoPromoMenu"]').forEach(m => {
            if (m !== menu) m.classList.add('hidden');
        });

        menu.classList.toggle('hidden');
    };

    window.selectTipoPromo = function(valor, etiqueta, contexto) {
        const inputHidden = document.getElementById('tipo_promocion_' + contexto);
        const spanTexto = document.getElementById('tipoPromoSelected' + capitalizar(contexto));
        const menu = document.getElementById('tipoPromoMenu' + capitalizar(contexto));

        if (inputHidden) inputHidden.value = valor;
        if (spanTexto) spanTexto.textContent = etiqueta;
        if (menu) menu.classList.add('hidden');
    };

    function capitalizar(texto) {
        return texto.charAt(0).toUpperCase() + texto.slice(1);
    }

    // Cierra el menú si se hace clic fuera de la caja del dropdown
    window.addEventListener('click', function(e) {
        document.querySelectorAll('[id^="cajaTipoPromo"]').forEach(caja => {
            if (!caja.contains(e.target)) {
                const menu = caja.querySelector('[id^="tipoPromoMenu"]');
                if (menu) menu.classList.add('hidden');
            }
        });
    });
</script>