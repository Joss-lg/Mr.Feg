{{-- Estilos para manejo de teclado virtual en PC y Scrollbar personalizada --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalCrear {
            align-items: flex-start !important;
            padding-top: 10px !important;
        }
        
        body.teclado-virtual-abierto #createContainer {
            max-height: 45dvh !important;
            transform: translateY(0) scale(0.95) !important;
        }
    }
    .unique-scrollbar::-webkit-scrollbar { width: 6px; }
    .unique-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
</style>

<div id="modalCrear" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    
    <div id="createContainer" class="bg-white border border-slate-200 w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-500 scale-95 opacity-0 flex flex-col max-h-[95dvh] sm:max-h-[92dvh]">
        
        <div class="p-6 sm:p-8 pb-4 shrink-0 flex justify-between items-center border-b border-slate-100">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shrink-0">
                    <i class="fas fa-layer-group text-lg sm:text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight uppercase m-0 leading-tight truncate">Nuevo Artículo</h3>
                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] mt-1">Añadir al Inventario</p>
                </div>
            </div>
            
            <button type="button" onclick="closeCreateModal()" class="group w-10 h-10 rounded-xl flex items-center justify-center bg-slate-50 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all outline-none shrink-0 border border-slate-200">
                <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
            </button>
        </div>
        
        <form id="formCrearInsumo" action="{{ route('admin.inventario.store') }}" method="POST" class="flex flex-col flex-1 min-h-0">
            @csrf

            <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-5 overflow-y-auto flex-1 overscroll-contain unique-scrollbar">

                {{-- Campo Nombre --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-tag opacity-40"></i> Nombre del Artículo
                    </label>
                    <input type="text" name="nombre" required data-teclado="texto" autocomplete="off"
                        class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all placeholder:text-slate-400 shadow-sm"
                        placeholder="Ej: Harina">
                </div>
                
                {{-- Dropdown Personalizado: Categoría --}}
                <div class="space-y-2 relative" id="cajaCategoria" data-required-dropdown>
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-folder opacity-40"></i> Categoría
                    </label>

                    <input type="hidden" name="categoria_id" id="val_Categoria" value="">

                    <button type="button" onclick="window.toggleCustomMenu('menu_Categoria')" id="btn_Categoria"
                        class="flex items-center justify-between w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 outline-none hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 shadow-sm transition-all">
                        <span id="text_Categoria" class="text-slate-400 truncate">Selecciona una categoría</span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                    </button>

                    <div id="menu_Categoria" class="absolute left-0 right-0 bg-white border border-slate-200 rounded-2xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto unique-scrollbar">
                        @foreach($categorias as $categoria)
                            <button type="button" onclick="window.selectCustomOption('val_Categoria', 'text_Categoria', 'menu_Categoria', '{{ $categoria->id }}', '{{ addslashes($categoria->nombre) }}')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                                {{ $categoria->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>
                
                {{-- Dropdown Personalizado: Unidad de Medida --}}
                <div class="space-y-2 relative" id="cajaUnidad">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                        <i class="fas fa-scale-balanced opacity-40"></i> Unidad de Medida
                    </label>

                    <input type="hidden" name="unidad_medida" id="val_Unidad" value="g">

                    <button type="button" onclick="window.toggleCustomMenu('menu_Unidad')" id="btn_Unidad"
                        class="flex items-center justify-between w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 outline-none hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 shadow-sm transition-all">
                        <span id="text_Unidad" class="text-slate-800 truncate">Gramos (g)</span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                    </button>

                    <div id="menu_Unidad" class="absolute left-0 right-0 bg-white border border-slate-200 rounded-2xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto unique-scrollbar">
                        <button type="button" onclick="window.selectCustomOption('val_Unidad', 'text_Unidad', 'menu_Unidad', 'g', 'Gramos (g)')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Gramos (g)</button>
                        <button type="button" onclick="window.selectCustomOption('val_Unidad', 'text_Unidad', 'menu_Unidad', 'ml', 'Mililitros (ml)')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Mililitros (ml)</button>
                        <button type="button" onclick="window.selectCustomOption('val_Unidad', 'text_Unidad', 'menu_Unidad', 'pz', 'Piezas (pz)')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">Piezas (pz)</button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Campo Precio Compra --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                            <i class="fas fa-dollar-sign opacity-40"></i> Precio Compra
                        </label>
                        <input type="text" name="precio_compra" data-teclado="numerico" data-teclado-decimales="true" autocomplete="off"
                            class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-black text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all placeholder:text-slate-400 shadow-sm"
                            placeholder="0.00">
                    </div>
                    {{-- Campo Stock Mínimo --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                            <i class="fas fa-bell opacity-40"></i> Stock Mínimo
                        </label>
                        <input type="text" name="stock_minimo" required data-teclado="numerico" autocomplete="off"
                            class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all placeholder:text-slate-400 shadow-sm"
                            placeholder="0">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 px-6 sm:px-8 py-6 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 h-12 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-800 hover:bg-slate-50 transition-all outline-none">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-[1.5] h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-600/20 transition-all active:scale-95 outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // =====================================================================
    // DROPDOWNS PERSONALIZADOS (reemplazo de <select> nativo)
    // Definidas aquí mismo, protegidas con "typeof === 'undefined'" para
    // que no truenen si este archivo y modal-movimiento.blade.php cargan
    // en la misma página (ambos definen las mismas funciones globales).
    // =====================================================================
    if (typeof window.toggleCustomMenu === 'undefined') {
        window.toggleCustomMenu = function (menuId) {
            // Cierra cualquier otro menú abierto antes de abrir este
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

    // Validación del dropdown obligatorio (Categoría) antes de enviar el form
    const formCrearInsumo = document.getElementById('formCrearInsumo');
    if (formCrearInsumo) {
        formCrearInsumo.addEventListener('submit', function (e) {
            const cajasRequeridas = formCrearInsumo.querySelectorAll('[data-required-dropdown]');
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
                alert('Por favor selecciona una categoría antes de guardar.');
            }
        });
    }
</script>