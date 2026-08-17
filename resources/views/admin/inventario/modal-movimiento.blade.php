<style>
    @media (min-width: 768px) {
        /* Ajuste para teclado virtual */
        body.teclado-virtual-abierto #modalMovimiento {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        body.teclado-virtual-abierto #movimientoContainer {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important;
        }
    }
</style>

<div id="modalMovimiento" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    
    <div class="relative bg-white border border-slate-200 w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-500 scale-95 opacity-0 flex flex-col max-h-[95dvh] sm:max-h-[92dvh]" id="movimientoContainer">
        
        <div class="p-6 sm:p-8 pb-4 sm:pb-5 flex justify-between items-center border-b border-slate-100 shrink-0 gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shrink-0">
                    <i class="fas fa-exchange-alt text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight uppercase m-0 leading-tight truncate" id="movimientoNombreInsumo">Cargando...</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Registrar Movimiento</p>
                </div>
            </div>
            
            <button type="button" onclick="closeModalMovimiento()" class="group w-10 h-10 rounded-xl flex items-center justify-center bg-slate-50 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all outline-none shrink-0 border border-slate-200">
                <i class="fas fa-times text-xs sm:text-sm transition-transform duration-300 group-hover:rotate-90"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.inventario.movimiento') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-50">
            @csrf
            <input type="hidden" name="insumo_id" id="movimientoInsumoId">

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-5 overflow-y-auto flex-1 overscroll-contain">

                {{-- Dropdown Personalizado: Tipo de Movimiento --}}
                <div class="space-y-2 relative" id="cajaTipoMovimiento">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-arrows-alt-v opacity-40"></i> Tipo de Movimiento
                    </label>

                    <input type="hidden" name="tipo" id="val_TipoMovimiento" value="entrada">

                    <button type="button" onclick="window.toggleCustomMenu('menu_TipoMovimiento')" id="btn_TipoMovimiento"
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl px-5 text-sm font-bold text-slate-800 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 shadow-sm transition-all">
                        <span id="text_TipoMovimiento" class="flex items-center gap-2 truncate">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> Entrada (Suma al stock)
                        </span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                    </button>

                    <div id="menu_TipoMovimiento" class="absolute left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto unique-scrollbar">
                        <button type="button" onclick="window.selectCustomOptionTipoMovimiento('entrada', 'bg-emerald-500', 'Entrada (Suma al stock)')"
                            class="w-full px-5 py-3 flex items-center gap-2 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> Entrada (Suma al stock)
                        </button>
                        <button type="button" onclick="window.selectCustomOptionTipoMovimiento('salida', 'bg-rose-500', 'Salida (Resta al stock)')"
                            class="w-full px-5 py-3 flex items-center gap-2 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> Salida (Resta al stock)
                        </button>
                        <button type="button" onclick="window.selectCustomOptionTipoMovimiento('ajuste', 'bg-orange-500', 'Merma / Desperdicio')"
                            class="w-full px-5 py-3 flex items-center gap-2 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span> Merma / Desperdicio
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-hashtag opacity-40"></i> Cantidad a mover
                    </label>
                    <input type="text" name="cantidad" pattern="[0-9]*\.?[0-9]*" required data-teclado="numerico" inputmode="none"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-5 text-sm font-black text-slate-800 focus:border-blue-500 outline-none transition-all shadow-sm placeholder:text-slate-400"
                        placeholder="Ej: 50">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-comment-alt opacity-40"></i> Motivo o Justificación
                    </label>
                    <input type="text" name="motivo" required data-teclado="texto" inputmode="none"
                        class="w-full h-12 bg-white border border-slate-200 rounded-xl px-5 text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all shadow-sm placeholder:text-slate-400"
                        placeholder="Ej: Factura #1234 / Se rompió">
                </div>
            </div>

            <div class="flex items-center gap-4 px-6 sm:px-8 py-6 border-t border-slate-200 shrink-0 bg-slate-50">
                <button type="button" onclick="closeModalMovimiento()" 
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all outline-none">
                    Cancelar
                </button>
                <button type="submit" 
                    class="flex-[1.5] h-12 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/20 active:scale-95 transition-all outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Registrar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // =====================================================================
    // DROPDOWNS PERSONALIZADOS (reemplazo de <select> nativo)
    // Definidas aquí mismo, protegidas con "typeof === 'undefined'" para
    // que no truenen si este archivo y modal-crear.blade.php cargan en la
    // misma página (ambos definen las mismas funciones globales).
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
                textSpan.classList.add('text-slate-800');
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

    // Selector propio del dropdown "Tipo de Movimiento": reconstruye el
    // span con el puntito de color + texto, y guarda el valor real en el
    // input oculto. Es distinto de selectCustomOption porque necesita
    // reconstruir el ícono de color, no solo el texto.
    window.selectCustomOptionTipoMovimiento = function (value, dotClass, label) {
        const hidden = document.getElementById('val_TipoMovimiento');
        const textSpan = document.getElementById('text_TipoMovimiento');
        const menu = document.getElementById('menu_TipoMovimiento');

        if (hidden) hidden.value = value;
        if (textSpan) {
            textSpan.innerHTML = `<span class="w-2 h-2 rounded-full ${dotClass} shrink-0"></span> ${label}`;
        }
        if (menu) menu.classList.add('hidden');
    };

    function openModalMovimiento(id, nombre) {
        const modal = document.getElementById('modalMovimiento');
        const container = document.getElementById('movimientoContainer');
        
        document.getElementById('movimientoInsumoId').value = id;
        document.getElementById('movimientoNombreInsumo').innerText = nombre;

        // Reinicia el dropdown de tipo a su valor por defecto (Entrada) cada vez que se abre
        window.selectCustomOptionTipoMovimiento('entrada', 'bg-emerald-500', 'Entrada (Suma al stock)');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModalMovimiento() {
        const modal = document.getElementById('modalMovimiento');
        const container = document.getElementById('movimientoContainer');
        
        modal.classList.add('opacity-0');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.querySelector('form').reset();
        }, 300);
    }
</script>