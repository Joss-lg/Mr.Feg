{{-- Estilos para manejo de teclado virtual --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto .modal-editar-insumo { align-items: flex-start !important; padding-top: 15px !important; }
        body.teclado-virtual-abierto .modal-editar-insumo-container { transform: translateY(0) scale(0.98) !important; max-height: calc(100dvh - 340px) !important; overflow-y: auto !important; }
    }
    .unique-scrollbar::-webkit-scrollbar { width: 6px; }
    .unique-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
</style>

<div id="modalEditar-{{ $item->id }}" class="modal-editar-insumo hidden fixed inset-0 z-[9999] items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">
    {{-- Capa para cerrar al hacer clic fuera --}}
    <div class="fixed inset-0 bg-transparent" onclick="cerrarModalEspecifico('modalEditar-{{ $item->id }}')"></div>

    <div id="modalContainer-{{ $item->id }}" class="modal-editar-insumo-container relative bg-white border border-slate-200/80 rounded-[2rem] w-full max-w-md shadow-2xl scale-95 opacity-0 transition-all duration-300 overflow-hidden flex flex-col max-h-[92dvh]">

        {{-- Encabezado --}}
        <div class="flex items-center gap-3 sm:gap-4 p-6 sm:p-8 pb-4 sm:pb-5 shrink-0 border-b border-slate-100">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0 bg-blue-50 text-blue-600 border border-blue-100">
                <i class="fas fa-pen"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight truncate">Editar Insumo</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 truncate">{{ strtoupper($item->nombre) }}</p>
            </div>
            <button type="button" onclick="cerrarModalEspecifico('modalEditar-{{ $item->id }}')"
                class="group ml-auto w-10 h-10 flex items-center justify-center rounded-xl bg-[#F2F2F2] text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 border border-slate-200/80 transition-all outline-none shrink-0">
                <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
            </button>
        </div>

        <form action="{{ route('admin.inventario.update', $item->id) }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-white">
            @csrf
            @method('PUT')

            <div class="p-6 sm:p-8 space-y-5 overflow-y-auto flex-1 overscroll-contain unique-scrollbar">

                {{-- Nombre --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-tag opacity-40"></i> Nombre del Artículo
                    </label>
                    <input type="text" name="nombre" value="{{ $item->nombre }}" required data-teclado="texto" autocomplete="off"
                        class="w-full h-12 bg-[#F2F2F2] border border-slate-200/80 rounded-2xl px-5 text-sm font-semibold text-slate-800 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all shadow-sm">
                </div>

                {{-- Categoría (Dropdown Personalizado) --}}
                <div class="space-y-2 relative">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-folder opacity-40"></i> Categoría
                    </label>
                    <input type="hidden" name="categoria_id" id="val_Categoria{{ $item->id }}" value="{{ $item->categoria_id }}">

                    <button type="button" onclick="window.toggleCustomMenu('menu_Categoria{{ $item->id }}')" id="btn_Categoria{{ $item->id }}"
                        class="w-full h-12 bg-[#F2F2F2] border border-slate-200/80 rounded-2xl px-5 text-sm font-bold text-slate-800 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all flex items-center justify-between shadow-sm">
                        <span id="text_Categoria{{ $item->id }}" class="truncate">{{ $item->categoria ? $item->categoria->nombre : 'Sin categoría' }}</span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                    </button>

                    <div id="menu_Categoria{{ $item->id }}" class="absolute left-0 right-0 bg-white border border-slate-200/80 rounded-2xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto unique-scrollbar">
                        <button type="button" onclick="window.selectCustomOption('val_Categoria{{ $item->id }}', 'text_Categoria{{ $item->id }}', 'menu_Categoria{{ $item->id }}', '', 'Sin categoría')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">Sin categoría</button>
                        @foreach($categorias ?? [] as $cat)
                            <button type="button" onclick="window.selectCustomOption('val_Categoria{{ $item->id }}', 'text_Categoria{{ $item->id }}', 'menu_Categoria{{ $item->id }}', '{{ $cat->id }}', '{{ addslashes($cat->nombre) }}')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">
                                {{ $cat->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Unidad de Medida (Dropdown Personalizado) --}}
                <div class="space-y-2 relative">
                    <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                        <i class="fas fa-scale-balanced opacity-40"></i> Unidad de Medida
                    </label>
                    <input type="hidden" name="unidad_medida" id="val_Unidad{{ $item->id }}" value="{{ $item->unidad_medida }}">

                    @php
                        $unidades = ['g' => 'Gramos (g)', 'ml' => 'Mililitros (ml)', 'pz' => 'Piezas (pz)'];
                    @endphp

                    <button type="button" onclick="window.toggleCustomMenu('menu_Unidad{{ $item->id }}')" id="btn_Unidad{{ $item->id }}"
                        class="w-full h-12 bg-[#F2F2F2] border border-slate-200/80 rounded-2xl px-5 text-sm font-bold text-slate-800 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all flex items-center justify-between shadow-sm">
                        <span id="text_Unidad{{ $item->id }}" class="truncate">{{ $unidades[$item->unidad_medida] ?? 'Seleccionar' }}</span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 ml-2"></i>
                    </button>

                    <div id="menu_Unidad{{ $item->id }}" class="absolute left-0 right-0 bg-white border border-slate-200/80 rounded-2xl shadow-xl z-[110] py-2 hidden mt-1 max-h-40 overflow-y-auto unique-scrollbar">
                        @foreach($unidades as $key => $label)
                            <button type="button" onclick="window.selectCustomOption('val_Unidad{{ $item->id }}', 'text_Unidad{{ $item->id }}', 'menu_Unidad{{ $item->id }}', '{{ $key }}', '{{ $label }}')" class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 transition-colors">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Grid: Stock --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-box opacity-40"></i> Stock Actual
                        </label>
                        <div class="h-12 bg-[#F2F2F2] border border-slate-200/80 rounded-2xl px-4 flex items-center text-sm font-black text-blue-600 opacity-90">
                            {{ number_format($item->stock_actual, 2) }}
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                            <i class="fas fa-bell opacity-40"></i> Stock Mínimo
                        </label>
                        <input type="text" name="stock_minimo" value="{{ $item->stock_minimo }}" required data-teclado="numerico" autocomplete="off"
                            class="w-full h-12 bg-[#F2F2F2] border border-slate-200/80 rounded-2xl px-4 text-sm font-black text-slate-800 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Botones Footer --}}
            <div class="flex items-center justify-between px-6 sm:px-8 py-6 border-t border-slate-100 gap-4 shrink-0 bg-white">
                <button type="button" onclick="cerrarModalEspecifico('modalEditar-{{ $item->id }}')"
                    class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 hover:bg-[#F2F2F2] transition-colors outline-none px-4 py-3 rounded-2xl">
                    Cancelar
                </button>
                <button type="submit"
                    class="h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl px-8 shadow-md shadow-emerald-500/20 active:scale-95 transition-all outline-none">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script de soporte para Dropdowns (definido una sola vez, sin importar cuántos ítems se rendericen) --}}
<script>
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
</script>