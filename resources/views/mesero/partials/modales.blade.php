<style>
    @media (prefers-reduced-motion: no-preference) {
        .modal-overlay:not(.hidden) { animation: modalFadeIn .18s ease-out; }
        .modal-sheet { animation: sheetSlideUp .22s cubic-bezier(.25,.8,.35,1); }
        @media (min-width: 640px) {
            .modal-sheet { animation: sheetPopIn .18s cubic-bezier(.25,.8,.35,1); }
        }
    }
    @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes sheetSlideUp { from { transform: translateY(16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes sheetPopIn { from { transform: scale(.97); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

{{-- ==========================================
     1. MODAL NIP CAPITÁN (Teclado Numérico Virtual)
     ========================================== --}}
<div id="modalNip" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-600 shadow-md shadow-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-lock text-white text-xs"></i>
                </span>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">NIP Administrador</h2>
            </div>
            <button type="button" onclick="cerrarModal('modalNip')" class="w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>
        
        <input type="password" id="nipInput"
               maxlength="6" autocomplete="off"
               data-solo-numeros="true"
               data-teclado-virtual="true"
               class="w-full min-h-[64px] rounded-xl border border-slate-200 bg-white shadow-sm p-4 text-2xl sm:text-xl font-black text-center text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 tracking-[0.3em]"
               placeholder="••••">
               
        {{-- Teclado Numérico --}}
        <div class="grid grid-cols-3 gap-1.5 mt-4">
            @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                <button type="button" onclick="escribirNumVirtual('nipInput', '{{ $key }}')" class="min-h-[44px] rounded-xl bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 active:scale-90 text-slate-800 text-lg font-black shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="escribirNumVirtual('nipInput', '0')" class="col-span-2 min-h-[44px] rounded-xl bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 active:scale-90 text-slate-800 text-lg font-black shadow-sm transition-all duration-100">0</button>
            <button type="button" onclick="borrarNumVirtual('nipInput')" class="min-h-[44px] rounded-xl bg-rose-50 border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white active:scale-90 text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>

        <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModal('modalNip')" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 active:scale-95 text-xs font-black uppercase tracking-widest transition-all duration-150">Cancelar</button>
            <button type="button" onclick="confirmarNipCapitan()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest active:scale-95 shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all duration-150">Aceptar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     2. MODAL CAPITÁN (Selección destino)
     ========================================== --}}
<div id="modalCapitan" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    @php $mesasAbiertas = $mesasAbiertas ?? collect(); @endphp
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-indigo-600 shadow-md shadow-indigo-500/20 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-white text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[9px] uppercase tracking-widest text-slate-500 font-bold">Autorización</p>
                    <h2 class="text-xl font-black text-slate-800 leading-tight">Selecciona mesero destino</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalCapitan')" class="flex-shrink-0 w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div id="capitanMeserosContainer" class="grid gap-2 max-h-[45vh] sm:max-h-[300px] overflow-y-auto hide-scroll pb-2"></div>
        <div class="mt-5 flex justify-end">
            <button type="button" onclick="cerrarModal('modalCapitan')" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl bg-white border border-slate-200 text-xs font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 active:scale-95 transition-all duration-150">Cancelar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     3. MODAL NOTAS (Teclado Nativo del teléfono)
     ========================================== --}}
<div id="modalNota" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[94vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-slate-50 border border-slate-200 p-4 sm:p-6 pb-[calc(1rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-4 sm:mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-600 shadow-md shadow-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-pen text-white text-xs"></i>
                </span>
                <div>
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">Instrucción Especial</h2>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">
                        Para: <span id="notaModalProducto" class="text-blue-600 font-black">-</span>
                    </p>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalNota')" class="w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>

        <textarea id="notaTextarea" rows="4"
            readonly
            inputmode="none"
            onclick="abrirTecladoNota()"
            class="w-full rounded-xl border border-slate-200 bg-white shadow-sm p-4 text-sm font-medium text-slate-800 outline-none resize-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 mb-4 cursor-pointer"
            placeholder="Toca aquí para escribir...">
        </textarea>

        <div class="flex items-center gap-2 mb-4 overflow-x-auto hide-scroll pb-1">
            @foreach(['Sin cebolla', 'Salsa aparte', 'Bien cocido', 'Término medio', 'Para llevar', 'Sin picante'] as $notaRapida)
                <button type="button" onclick="agregarTextoRapidoNota('{{ $notaRapida }}')"
                    class="shrink-0 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-500 hover:text-white active:scale-90 select-none text-[11px] font-bold shadow-sm transition-all duration-150">
                    {{ $notaRapida }}
                </button>
            @endforeach
        </div>

        <div class="mt-2 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="limpiarNota()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 active:scale-95 select-none text-[11px] sm:text-xs font-black uppercase tracking-widest transition-all duration-150">Limpiar</button>
            <button type="button" onclick="cerrarModal('modalNota')" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 active:scale-95 text-xs font-black uppercase tracking-widest transition-all duration-150">Cancelar</button>
            <button type="button" onclick="guardarNota()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest active:scale-95 shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all duration-150">Confirmar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     5. MODAL PERSONAS (Teclado Numérico Virtual)
     ========================================== --}}
<div id="modalPersonas" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-600 shadow-md shadow-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-users text-white text-xs"></i>
                </span>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">Personas en Mesa</h2>
            </div>
            <button type="button" onclick="cerrarModal('modalPersonas')" class="w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>
        
        <input id="personasInput" type="text" data-solo-numeros="true" data-teclado-virtual="true"
               maxlength="3" inputmode="numeric" pattern="[0-9]*" autocomplete="off"
               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)"
               class="w-full min-h-[64px] rounded-xl border border-slate-200 bg-white shadow-sm p-4 text-2xl sm:text-xl font-black text-center text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">

        {{-- Teclado Numérico --}}
        <div class="grid grid-cols-3 gap-1.5 mt-4">
            @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                <button type="button" onclick="escribirNumVirtual('personasInput', '{{ $key }}')" class="min-h-[44px] rounded-xl bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 active:scale-90 text-slate-800 text-lg font-black shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="escribirNumVirtual('personasInput', '0')" class="col-span-2 min-h-[44px] rounded-xl bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 active:scale-90 text-slate-800 text-lg font-black shadow-sm transition-all duration-100">0</button>
            <button type="button" onclick="borrarNumVirtual('personasInput')" class="min-h-[44px] rounded-xl bg-rose-50 border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white active:scale-90 text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>

        <div class="mt-6">
            <button type="button" onclick="guardarPersonas()" class="w-full min-h-[48px] rounded-xl bg-blue-600 text-white text-sm font-black uppercase tracking-widest active:scale-95 shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all duration-150">Guardar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     6. MODAL GRAMAJE (Teclado Numérico Virtual propio)
     ========================================== --}}
<div id="modalGramaje" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[94vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-orange-500 shadow-md shadow-orange-500/20 flex items-center justify-center">
                    <i class="fas fa-weight-scale text-white text-xs"></i>
                </span>
                <h2 id="modalGramajeTitulo" class="text-xl font-black text-slate-800 truncate">Gramaje</h2>
            </div>
            <button type="button" onclick="cerrarModalGramaje()" class="flex-shrink-0 w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>

        <div class="flex items-center gap-3 mb-2">
            <input id="gramajeInput" type="text"
                   inputmode="decimal" autocomplete="off"
                   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                   class="flex-1 min-w-0 rounded-xl border border-slate-200 bg-white shadow-sm p-4 text-2xl font-black text-slate-800 text-center outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20" placeholder="0">
            <span class="shrink-0 text-slate-500 font-black text-lg">g</span>
        </div>

        <p id="gramajePrecioPreview" class="text-center text-sm font-black text-orange-500 mb-4 h-5"></p>

        <div id="botonesRapidosGramaje" class="grid grid-cols-5 gap-1.5 mb-4">
            @foreach([['100','100g'],['250','250g'],['500','500g'],['700','700g'],['1000','1kg']] as [$valor, $etiqueta])
                <button type="button" onclick="seleccionarGramajeRapido({{ $valor }})" class="min-h-[40px] py-2 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 hover:bg-orange-500 hover:text-white active:scale-90 select-none text-[11px] font-bold shadow-sm transition-all duration-150">{{ $etiqueta }}</button>
            @endforeach
        </div>

        <div id="tecladoGramaje" class="grid grid-cols-3 gap-2">
            @foreach(['1','2','3','4','5','6','7','8','9','.','0'] as $key)
                <button type="button" onclick="anadirNumeroGramaje('{{ $key }}')" class="min-h-[48px] py-3 rounded-xl bg-white border border-slate-200 hover:border-orange-300 hover:bg-orange-50 active:scale-90 text-slate-800 text-lg font-black shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="borrarNumeroGramaje()" class="min-h-[48px] py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white active:scale-90 select-none text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>
        
        <div class="mt-6">
            <button type="button" onclick="guardarGramajeDelItem()" class="w-full min-h-[48px] rounded-xl bg-orange-500 text-white text-sm font-black uppercase tracking-widest active:scale-95 shadow-md shadow-orange-500/20 hover:bg-orange-600 transition-all duration-150">Confirmar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     7. MODAL TIPO DE TRASPASO
     ========================================== --}}
<div id="modalTipoTraspaso" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-indigo-600 shadow-md shadow-indigo-500/20 flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-white text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[9px] uppercase tracking-widest text-slate-500 font-bold">Traspaso</p>
                    <h2 id="tituloTipoTraspaso" class="text-xl font-black text-slate-800 leading-tight">¿Qué deseas traspasar?</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalTipoTraspaso')" class="flex-shrink-0 w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="flex flex-col gap-3">
            <button type="button" onclick="elegirTraspasoProducto()" class="w-full flex items-center gap-3 text-left rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm hover:border-blue-300 hover:shadow-md active:scale-[0.98] transition-all duration-150">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center">
                    <i class="fas fa-utensils text-blue-600 text-xs"></i>
                </span>
                <span>
                    <p class="text-sm font-bold text-slate-800">Producto Individual</p>
                    <p class="text-[12px] text-slate-500 mt-0.5">Elige uno o varios platillos específicos</p>
                </span>
            </button>
            <button type="button" onclick="elegirTraspasoCompleto()" class="w-full flex items-center gap-3 text-left rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm hover:border-blue-300 hover:shadow-md active:scale-[0.98] transition-all duration-150">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-blue-600 text-xs"></i>
                </span>
                <span>
                    <p class="text-sm font-bold text-slate-800">Pedido Completo</p>
                    <p class="text-[12px] text-slate-500 mt-0.5">Envía toda la orden a la mesa destino</p>
                </span>
            </button>
        </div>
    </div>
</div>

{{-- ==========================================
     8. MODAL SELECCIÓN DE PRODUCTOS
     ========================================== --}}
<div id="modalSeleccionProductos" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-indigo-600 shadow-md shadow-indigo-500/20 flex items-center justify-center">
                    <i class="fas fa-list-check text-white text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[9px] uppercase tracking-widest text-slate-500 font-bold">Traspaso</p>
                    <h2 class="text-lg font-black text-slate-800 leading-tight">Selecciona productos</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalSeleccionProductos')" class="flex-shrink-0 w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div id="listaProductosTraspaso" class="grid gap-2 max-h-[45vh] sm:max-h-[320px] overflow-y-auto hide-scroll pb-2"></div>
        <div class="mt-5 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModal('modalSeleccionProductos')" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl bg-white border border-slate-200 text-xs font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 active:scale-95 transition-all duration-150">Cancelar</button>
            <button type="button" onclick="confirmarTraspasoProductos()" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest active:scale-95 shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all duration-150">Traspasar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     9. MODAL SELECCIÓN DE COMBO
     ========================================== --}}
<div id="modalSeleccionCombo" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-blue-600 shadow-md shadow-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-tags text-white text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[9px] uppercase tracking-widest text-slate-500 font-bold">Combo</p>
                    <h2 id="tituloSeleccionCombo" class="text-lg font-black text-slate-800 leading-tight">Selecciona los productos</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalSeleccionCombo')" class="flex-shrink-0 w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>
        <p class="text-[12px] text-slate-500 mb-3">Marca cuáles productos del ticket quieres que cuenten para este combo. Solo puedes marcar los que ya están agregados.</p>
        <div id="listaSeleccionCombo" class="grid gap-2 max-h-[40vh] sm:max-h-[320px] overflow-y-auto hide-scroll pb-2"></div>
        <div class="mt-5 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModal('modalSeleccionCombo')" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl bg-white border border-slate-200 text-xs font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 active:scale-95 transition-all duration-150">Cancelar</button>
            <button type="button" onclick="confirmarSeleccionCombo()" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest active:scale-95 shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all duration-150">Aplicar combo</button>
        </div>
    </div>
</div>

{{-- ==========================================
     10. MODAL PROMOCIONES
     ========================================== --}}
<div id="modalPromociones" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <!-- Contenido de promociones -->
    </div>
</div>

{{-- ==========================================
     11. MODAL NIP CANCELACIÓN DE PRODUCTO
     (Independiente del modalNip de Capitán/Traspaso)
     ========================================== --}}
<div id="modalNipCancelacion" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-rose-500 shadow-md shadow-rose-500/20 flex items-center justify-center">
                    <i class="fas fa-ban text-white text-xs"></i>
                </span>
                <div>
                    <p class="text-[9px] uppercase tracking-widest text-rose-500 font-bold">Autorización</p>
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">NIP para Cancelar</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModalCancelacion()" class="w-9 h-9 -m-1 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times text-lg"></i></button>
        </div>

        <p class="text-[12px] text-slate-500 mb-3">Ingresa el NIP del Administrador para autorizar la cancelación de este producto.</p>

        <input type="password" id="nipCancelacionInput" data-solo-numeros="true" data-teclado-virtual="true"
               maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="off"
               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
               class="w-full min-h-[64px] rounded-xl border border-slate-200 bg-white shadow-sm p-4 text-2xl sm:text-xl font-black text-center text-slate-800 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 transition-all duration-200 tracking-[0.3em]"
               placeholder="••••">

        {{-- Teclado Numérico (reutiliza escribirNumVirtual/borrarNumVirtual, ya son genéricas por ID) --}}
        <div class="grid grid-cols-3 gap-1.5 mt-4">
            @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                <button type="button" onclick="escribirNumVirtual('nipCancelacionInput', '{{ $key }}')" class="min-h-[44px] rounded-xl bg-white border border-slate-200 hover:border-rose-300 hover:bg-rose-50 active:scale-90 text-slate-800 text-lg font-black shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="escribirNumVirtual('nipCancelacionInput', '0')" class="col-span-2 min-h-[44px] rounded-xl bg-white border border-slate-200 hover:border-rose-300 hover:bg-rose-50 active:scale-90 text-slate-800 text-lg font-black shadow-sm transition-all duration-100">0</button>
            <button type="button" onclick="borrarNumVirtual('nipCancelacionInput')" class="min-h-[44px] rounded-xl bg-rose-50 border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white active:scale-90 text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>

        <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModalCancelacion()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 active:scale-95 text-xs font-black uppercase tracking-widest transition-all duration-150">Cancelar</button>
            <button type="button" id="btnConfirmarNipCancelacion" onclick="confirmarNipCancelacion()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-rose-600 text-white text-xs font-black uppercase tracking-widest active:scale-95 shadow-md shadow-rose-500/20 hover:bg-rose-700 transition-all duration-150">Autorizar Cancelación</button>
        </div>
    </div>
</div>

{{-- ==========================================
     12. MODAL CONFIRMAR CANCELACIÓN DE PRODUCTO
     ========================================== --}}
<div id="modalConfirmarCancelacion" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-slate-50 border border-slate-200 p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-4"></div>

        <div class="flex flex-col items-center text-center gap-3 mb-2">
            <span class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center">
                <i class="fas fa-triangle-exclamation text-rose-500 text-xl"></i>
            </span>
            <div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">¿Cancelar este producto?</h2>
                <p class="text-[12px] text-slate-500 mt-1.5 leading-relaxed">Esta acción no se puede deshacer y ya no se cobrará al cliente.</p>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModalConfirmarCancelacion()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 active:scale-95 text-xs font-black uppercase tracking-widest transition-all duration-150">No, mantener</button>
            <button type="button" onclick="continuarCancelacionConNip()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-rose-600 text-white text-xs font-black uppercase tracking-widest active:scale-95 shadow-md shadow-rose-500/20 hover:bg-rose-700 transition-all duration-150">Sí, cancelar</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     TECLADO VIRTUAL — Modal de Nota / Instrucción
     ════════════════════════════════════════════════ --}}
<div id="teclado-nota-overlay"
     class="hidden fixed inset-0 z-[99999]"
     onclick="if(event.target===this) cerrarTecladoNota()">

    <div class="absolute bottom-0 inset-x-0 bg-slate-50 border-t border-slate-200 shadow-2xl rounded-t-3xl">

        {{-- Display + cerrar --}}
        <div class="flex items-start gap-3 px-4 pt-4 pb-3 border-b border-slate-200">
            <div class="flex-1 bg-white border border-slate-200 rounded-xl shadow-sm px-3 py-2.5 min-h-[48px] max-h-24 overflow-y-auto">
                <span id="tn-display" class="text-sm font-medium text-slate-800 break-words whitespace-pre-wrap"></span><span class="inline-block w-0.5 h-4 bg-blue-500 animate-pulse rounded-full align-middle ml-0.5"></span>
            </div>
            <button type="button" onclick="cerrarTecladoNota()"
                class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-blue-500 flex items-center justify-center shrink-0 active:scale-95 mt-0.5 shadow-sm">
                <i class="fas fa-check text-sm"></i>
            </button>
        </div>

        {{-- Notas rápidas --}}
        <div class="flex gap-2 px-3 pt-2.5 overflow-x-auto hide-scroll pb-1">
            @foreach(['Sin cebolla','Salsa aparte','Bien cocido','Término medio','Para llevar','Sin picante'] as $nota)
                <button type="button" onclick="tnRapida('{{ $nota }}')"
                    class="shrink-0 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-blue-600 text-[11px] font-bold active:scale-90 transition-all">
                    {{ $nota }}
                </button>
            @endforeach
        </div>

        {{-- QWERTY --}}
        <div class="px-2 py-2 space-y-1.5 select-none">
            @php
                $filasNota = [
                    ['Q','W','E','R','T','Y','U','I','O','P'],
                    ['A','S','D','F','G','H','J','K','L'],
                    ['Z','X','C','V','B','N','M'],
                ];
            @endphp
            @foreach($filasNota as $fila)
                <div class="flex justify-center gap-1">
                    @foreach($fila as $letra)
                        <button type="button" onclick="tnEscribir('{{ $letra }}')"
                            class="flex-1 max-w-[38px] h-10 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 active:bg-blue-100 transition-all duration-75">
                            {{ $letra }}
                        </button>
                    @endforeach
                </div>
            @endforeach
            {{-- Fila inferior --}}
            <div class="flex justify-center gap-1 mt-1">
                <button type="button" onclick="tnEscribir(' ')"
                    class="flex-1 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 font-bold text-xs shadow-sm active:scale-95 transition-all duration-75">
                    ESPACIO
                </button>
                <button type="button" onclick="tnEscribir('.')"
                    class="w-12 h-10 rounded-xl bg-white border border-slate-200 text-slate-800 font-black text-sm shadow-sm active:scale-95 transition-all duration-75">
                    .
                </button>
                <button type="button" onclick="tnBorrar()"
                    class="w-14 h-10 rounded-xl bg-rose-50 border border-rose-200 text-rose-500 shadow-sm active:scale-95 flex items-center justify-center transition-all duration-75">
                    <i class="fas fa-delete-left text-base"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===================================================================
     13. CONTENEDOR GLOBAL DE MODALES REPARTIDORES (NUEVO)
     =================================================================== --}}
<div id="modales-repartidores-container">

    <!-- 1. MODAL: TIPO DE PEDIDO -->
    <div id="modalTipoPedido" class="modal-overlay fixed inset-0 z-[60] hidden flex items-center justify-center" aria-modal="true">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4">
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-slate-50 text-left shadow-2xl transition-all w-full max-w-md border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-xl font-black text-slate-800">Tipo de Pedido</h3>
                    <button type="button" onclick="cerrarModal('modalTipoPedido')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Comedor -->
                    <button type="button" onclick="seleccionarTipoPedido('comedor')" class="w-full flex items-center p-4 border border-slate-200 rounded-2xl bg-white shadow-sm hover:border-blue-300 hover:bg-blue-50/50 hover:shadow-md transition-all group">
                        <div class="w-12 h-12 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                            <i class="fas fa-utensils text-xl"></i>
                        </div>
                        <div class="ml-4 text-left">
                            <p class="font-black text-slate-800 text-lg">Comedor / Mesa</p>
                            <p class="text-sm text-slate-500">El cliente consume en el local.</p>
                        </div>
                    </button>
                    <!-- Para Llevar -->
                    <button type="button" onclick="seleccionarTipoPedido('llevar')" class="w-full flex items-center p-4 border border-slate-200 rounded-2xl bg-white shadow-sm hover:border-emerald-300 hover:bg-emerald-50/50 hover:shadow-md transition-all group">
                        <div class="w-12 h-12 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-xl group-hover:scale-110 transition-transform">
                            <i class="fas fa-shopping-bag text-xl"></i>
                        </div>
                        <div class="ml-4 text-left">
                            <p class="font-black text-slate-800 text-lg">Para Llevar (Pick Up)</p>
                            <p class="text-sm text-slate-500">El cliente pasa a recoger su pedido.</p>
                        </div>
                    </button>
                    <!-- A Domicilio -->
                    <button type="button" onclick="abrirModalDelivery()" class="w-full flex items-center p-4 border border-slate-200 rounded-2xl bg-white shadow-sm hover:border-blue-300 hover:bg-blue-50/50 hover:shadow-md transition-all group">
                        <div class="w-12 h-12 flex items-center justify-center bg-blue-50 text-blue-700 rounded-xl group-hover:scale-110 transition-transform">
                            <i class="fas fa-motorcycle text-xl"></i>
                        </div>
                        <div class="ml-4 text-left">
                            <p class="font-black text-slate-800 text-lg">A Domicilio</p>
                            <p class="text-sm text-slate-500">Requiere cliente y dirección de entrega.</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MODAL: BUSCADOR DE CLIENTES / DIRECCIONES -->
    <div id="modalDireccion" class="modal-overlay fixed inset-0 z-[70] hidden flex items-center justify-center" aria-modal="true">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4">
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-slate-50 text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <div class="flex items-center space-x-3 text-blue-600">
                        <i class="fas fa-box text-2xl"></i>
                        <h3 class="text-xl font-black text-slate-800">Dirección de Entrega</h3>
                    </div>
                    <button type="button" onclick="cerrarModal('modalDireccion')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <div class="p-6 min-h-[300px]">
                    <!-- VISTA A: BUSCADOR -->
                    <div id="vista-buscador-clientes">
                        <div class="flex justify-between items-end mb-4">
                            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Seleccionar Cliente *</label>
                            <button type="button" onclick="abrirModalNuevoCliente()" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-4 rounded-xl shadow-md shadow-blue-600/20 text-xs uppercase tracking-widest transition-colors">
                                <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                            </button>
                        </div>

                <!-- Opción de Domicilio Express / Temporal -->
@if(request('tipo_pedido') === 'domicilio')
    <!-- Opción de Domicilio Express / Temporal -->
    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-xs font-black text-blue-700 mb-2"><i class="fas fa-bolt mr-1"></i> ¿Pedido exprés sin registrar?</p>
        <div class="flex gap-2">
            <input type="text" id="inputDomicilioExpressNombre" placeholder="Nombre del cliente..." class="flex-1 px-3 py-2 text-xs border border-slate-200 rounded-xl bg-white shadow-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            <input type="text" id="inputDomicilioExpressDireccion" placeholder="Dirección corta / Referencia..." class="flex-1 px-3 py-2 text-xs border border-slate-200 rounded-xl bg-white shadow-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            <button type="button" onclick="confirmarDomicilioExpress()" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-3 py-2 rounded-xl text-xs uppercase tracking-widest transition-colors shrink-0">Usar</button>
        </div>
    </div>
@endif
                        <div class="relative mb-4">
                            <i class="fas fa-search absolute left-3 top-3.5 text-slate-400"></i>
                            <input type="text" id="inputBuscarCliente" placeholder="Escribe el nombre o teléfono del cliente..." class="w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-white shadow-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        </div>
                        <div id="resultadosClientes" class="border border-slate-200 rounded-xl max-h-48 overflow-y-auto bg-white shadow-sm">
                            <!-- Aquí se inyectarán los resultados con JS -->
                            <div class="p-4 text-center text-slate-500 text-sm font-medium">Empieza a escribir para buscar...</div>
                        </div>
                    </div>

                    <!-- VISTA B: CLIENTE SELECCIONADO Y DIRECCIONES -->
                    <div id="vista-direcciones-cliente" class="hidden">
                        <div class="bg-emerald-50 p-4 rounded-xl flex justify-between items-center mb-6 border border-emerald-200">
                            <div>
                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-wider mb-1">Cliente Seleccionado</p>
                                <h4 id="lbl-nombre-cliente" class="text-lg font-black text-slate-800">Nombre Cliente</h4>
                                <p id="lbl-tel-cliente" class="text-sm text-slate-500 font-medium">Tel: 00000000</p>
                            </div>
                            <button type="button" onclick="cambiarCliente()" class="bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white font-black py-1.5 px-3 rounded-xl text-sm transition-colors">
                                Cambiar Cliente
                            </button>
                        </div>

                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Dirección de Entrega *</label>
                            <button type="button" onclick="abrirModalNuevaDireccion()" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-1.5 px-3 rounded-xl text-sm transition-colors">
                                + Nueva Dirección
                            </button>
                        </div>
                        
                        <!-- Lista de Radios para Direcciones -->
                        <div id="lista-direcciones-radios" class="space-y-3">
                            <!-- Se inyecta con JS -->
                        </div>
                    </div>
                </div>

                <div class="bg-slate-100/50 border-t border-slate-200/60 px-6 py-4 flex space-x-3" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
                    <button type="button" onclick="cerrarModal('modalDireccion')" class="flex-1 bg-white border border-slate-200 text-slate-500 font-black py-3 px-4 rounded-xl hover:bg-slate-100 text-xs uppercase tracking-widest transition-colors">Cancelar / Volver</button>
                    <button type="button" id="btnConfirmarDelivery" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-4 rounded-xl text-xs uppercase tracking-widest transition-all shadow-md shadow-blue-600/20 opacity-50 cursor-not-allowed" disabled>Confirmar Dirección</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MODAL: REGISTRAR NUEVO CLIENTE -->
    <div id="modalNuevoCliente" class="modal-overlay fixed inset-0 z-[80] hidden flex items-center justify-center" aria-modal="true">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4">
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-slate-50 text-left shadow-2xl w-full max-w-lg border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800">Registrar Nuevo Cliente</h3>
                    <button type="button" onclick="cerrarModal('modalNuevoCliente')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times"></i></button>
                </div>
                <form id="formNuevoCliente" onsubmit="guardarNuevoClienteAjax(event)" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Nombre *</label>
                            <input type="text" id="nc_nombre" required class="w-full h-12 rounded-xl border border-slate-200 bg-white shadow-sm px-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Apellidos *</label>
                            <input type="text" id="nc_apellido" class="w-full h-12 rounded-xl border border-slate-200 bg-white shadow-sm px-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Teléfono *</label>
                        <input type="text" id="nc_telefono" required class="w-full h-12 rounded-xl border border-slate-200 bg-white shadow-sm px-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div class="pt-4 flex space-x-3">
                        <button type="button" onclick="cerrarModal('modalNuevoCliente')" class="flex-1 bg-white border border-slate-200 text-slate-500 font-black py-2 rounded-xl hover:bg-slate-100 text-xs uppercase tracking-widest transition-colors">Cancelar</button>
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black py-2 rounded-xl text-xs uppercase tracking-widest transition-colors">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. MODAL: REGISTRAR NUEVA DIRECCIÓN -->
    <div id="modalNuevaDireccion" class="modal-overlay fixed inset-0 z-[90] hidden flex items-center justify-center" aria-modal="true">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4">
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-slate-50 text-left shadow-2xl w-full max-w-lg border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800">Registrar Nueva Dirección</h3>
                    <button type="button" onclick="cerrarModal('modalNuevaDireccion')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300"><i class="fas fa-times"></i></button>
                </div>
                <form id="formNuevaDireccion" onsubmit="guardarNuevaDireccionAjax(event)" class="p-6 space-y-4">
                    <input type="hidden" id="nd_cliente_id"> 
                    <div>
                        <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Calle *</label>
                        <input type="text" id="nd_calle" required class="w-full h-12 rounded-xl border border-slate-200 bg-white shadow-sm px-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Manzana</label>
                            <input type="text" id="nd_manzana" class="w-full h-12 rounded-xl border border-slate-200 bg-white shadow-sm px-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Lote</label>
                            <input type="text" id="nd_lote" class="w-full h-12 rounded-xl border border-slate-200 bg-white shadow-sm px-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Colonia</label>
                        <input type="text" id="nd_colonia" class="w-full h-12 rounded-xl border border-slate-200 bg-white shadow-sm px-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Referencia</label>
                        <textarea id="nd_referencia" rows="2" class="w-full rounded-xl border border-slate-200 bg-white shadow-sm p-3 text-slate-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                    </div>
                    <div class="pt-4 flex space-x-3">
                        <button type="button" onclick="cerrarModal('modalNuevaDireccion')" class="flex-1 bg-white border border-slate-200 text-slate-500 font-black py-2 rounded-xl hover:bg-slate-100 text-xs uppercase tracking-widest transition-colors">Cancelar</button>
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black py-2 rounded-xl text-xs uppercase tracking-widest transition-colors">Guardar Dirección</button>
                    </div>
                </form>


          <!-- Modal Cliente Para Llevar -->
<div id="modalParaLlevar" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[60] p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-slate-50 rounded-[24px] shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <h2 class="text-xl font-black text-slate-800"><i class="fas fa-shopping-bag mr-2"></i>Cliente Para Llevar</h2>
            <button type="button" onclick="cerrarModal('modalParaLlevar')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-6">
            <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Nombre del cliente (Temporal o Buscar)</label>
            <input type="text" id="inputClienteLlevar" autocomplete="off" class="w-full h-12 px-4 bg-white border border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-colors" placeholder="Ej. Juan Pérez">
            
            <!-- Contenedor opcional si decides reutilizar la búsqueda aquí -->
            <div id="resultadosLlevar" class="mt-2 max-h-40 overflow-y-auto"></div>
        </div>
        <div class="px-6 py-4 bg-slate-100/50 border-t border-slate-200/60 flex justify-end gap-3" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
            <button type="button" onclick="cerrarModal('modalParaLlevar')" class="px-4 py-2 rounded-xl font-black text-xs uppercase tracking-widest border border-slate-200 bg-white text-slate-500 hover:bg-slate-100 transition">Cancelar</button>
            <button type="button" onclick="confirmarClienteLlevar()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-md shadow-blue-500/20 transition">Confirmar Nombre</button>
        </div>
    </div>
</div>

<script>
let timeoutBuscador = null;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// 1. Forzar que la tarjeta lateral se mantenga como "A Domicilio" si la mesa es de delivery
document.addEventListener('DOMContentLoaded', function() {
    const config = window.ComandaConfig || {};
    const numeroMesa = (config.mesa && config.mesa.numero) ? config.mesa.numero.toString().toUpperCase() : '';
    const urlParams = new URLSearchParams(window.location.search);
    
    if (numeroMesa.startsWith('DOM') || urlParams.get('tipo_pedido') === 'domicilio') {
        const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
        const lblTitulo = document.getElementById('lbl-titulo-tarjeta');
        if (lblTitulo) lblTitulo.textContent = 'A Domicilio';
        if (lblBoton) {
            lblBoton.textContent = 'Seleccionar Cliente';
            lblBoton.className = 'text-[11px] font-black text-blue-600 mt-1 truncate max-w-full px-1';
        }
    }
});

// 2. Renderizar las opciones de direcciones
function renderDireccionesRadios(direcciones) {
    const contenedor = document.getElementById('lista-direcciones-radios');
    if (!contenedor) return;
    
    if (!direcciones || direcciones.length === 0) {
        contenedor.innerHTML = `<div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm font-medium text-center">
            Este cliente no tiene direcciones registradas. Haz clic en "+ Nueva Dirección".
        </div>`;
        return;
    }

    let html = '';
    direcciones.forEach(d => {
        let detalle = `${d.calle || ''}`;
        if(d.manzana) detalle += ` Mz: ${d.manzana}`;
        if(d.lote) detalle += ` Lt: ${d.lote}`;
        
        html += `
        <label class="flex items-start p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
            <div class="flex items-center h-5 mt-1">
                <input type="radio" name="radio_direccion" value="${d.id}" onchange="window.activarBotonConfirmar()" class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-600 focus:ring-2 cursor-pointer">
            </div>
            <div class="ml-3 text-sm flex-1">
                <p class="font-bold text-slate-800">${detalle}</p>
                <p class="text-xs text-slate-500">${d.colonia || ''}</p>
                ${d.referencia ? `<p class="text-xs text-slate-500 mt-1 italic border-l-2 border-blue-600 pl-2">Ref: ${d.referencia}</p>` : ''}
            </div>
        </label>`;
    });
    contenedor.innerHTML = html;
}

// 3. Activar botón confirmar dirección
window.activarBotonConfirmar = function() {
    const selector = document.querySelector('input[name="radio_direccion"]:checked');
    if (selector) {
        window.direccionSeleccionadaId = selector.value;
        const btn = document.getElementById('btnConfirmarDelivery');
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
};

// 4. Al confirmar la dirección, actualizar la tarjeta lateral con el nombre del cliente
document.getElementById('btnConfirmarDelivery')?.addEventListener('click', function() {
    cerrarModal('modalDireccion');
    
    const nombreClienteEl = document.getElementById('lbl-nombre-cliente');
    const nombreCliente = nombreClienteEl ? nombreClienteEl.textContent : 'Cliente Asignado';

    const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
    const lblTitulo = document.getElementById('lbl-titulo-tarjeta');

    if (lblTitulo) lblTitulo.textContent = 'A Domicilio';
    if (lblBoton) {
        lblBoton.textContent = nombreCliente;
        lblBoton.className = 'text-[11px] font-black text-emerald-500 mt-1 truncate max-w-full px-1';
    }

    if (typeof mostrarExito === 'function') {
        mostrarExito("¡Cliente y dirección asignados correctamente!");
    }
});

window.manejarClickTipoPedido = function() {
    const modalDir = document.getElementById('modalDireccion');
    
    if (modalDir) {
        // Quitamos la clase hidden para mostrar el modal de dirección/clientes
        modalDir.classList.remove('hidden');
        
        // Enfocamos el buscador automáticamente si existe
        setTimeout(() => {
            const inputBuscar = document.getElementById('inputBuscarCliente');
            if (inputBuscar) {
                inputBuscar.focus();
            }
        }, 100);
    } else {
        console.error("No se encontró el elemento #modalDireccion en el DOM.");
    }
};

// Función universal para abrir cualquier modal correctamente
window.abrirModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = ''; // Limpiamos estilos en línea para que mande Tailwind
    }
};

// Función específica para abrir el de nuevo cliente y enfocar el campo
window.abrirModalNuevoCliente = function() {
    window.abrirModal('modalNuevoCliente');
    setTimeout(() => {
        const inputNombre = document.getElementById('nc_nombre');
        if (inputNombre) inputNombre.focus();
    }, 100);
};

// Función universal para cerrar cualquier modal limpiamente
window.cerrarModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = ''; // Limpiamos estilos en línea
    }
};

// Función específica para abrir el modal de Nueva Dirección asegurando que el ID del cliente actual se pase al formulario
window.abrirModalNuevaDireccion = function() {
    // Si ya tenemos un cliente seleccionado, nos aseguramos de que su ID esté listo en el input oculto del formulario de dirección
    const clienteIdActual = window.clienteSeleccionadoId;
    if (clienteIdActual) {
        const inputClienteId = document.getElementById('nd_cliente_id');
        if (inputClienteId) inputClienteId.value = clienteIdActual;
    }

    // Usamos nuestra función universal para mostrar el modal limpiamente
    window.abrirModal('modalNuevaDireccion');
    
    // Enfocamos el primer campo (Calle) automáticamente
    setTimeout(() => {
        const inputCalle = document.getElementById('nd_calle');
        if (inputCalle) inputCalle.focus();
    }, 100);
};

// Función global y robusta para el botón "Cambiar Cliente"
window.cambiarCliente = function() {
    window.clienteSeleccionadoId = null;
    window.direccionSeleccionadaId = null;

    // Limpiamos el input de búsqueda previo por si quedó con texto
    const inputBuscar = document.getElementById('inputBuscarCliente');
    if (inputBuscar) inputBuscar.value = '';

    // Vaciamos los resultados anteriores del buscador
    const resultados = document.getElementById('resultadosClientes');
    if (resultados) {
        resultados.innerHTML = '<div class="p-4 text-center text-slate-500 text-sm font-medium">Empieza a escribir para buscar...</div>';
    }

    // Ocultamos la vista del cliente seleccionado y mostramos de nuevo el buscador
    const vistaDirecciones = document.getElementById('vista-direcciones-cliente');
    const vistaBuscador = document.getElementById('vista-buscador-clientes');
    
    if (vistaDirecciones) vistaDirecciones.classList.add('hidden');
    if (vistaBuscador) vistaBuscador.classList.remove('hidden');

    // Desactivamos el botón de confirmar hasta que elija una nueva dirección
    const btnConfirmar = document.getElementById('btnConfirmarDelivery');
    if (btnConfirmar) {
        btnConfirmar.disabled = true;
        btnConfirmar.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Opcional: enfocar el input de búsqueda de nuevo
    if (inputBuscar) {
        setTimeout(() => inputBuscar.focus(), 100);
    }
};

// 5. Detectar al cargar la página si es "Para Llevar"
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tipoParams = urlParams.get('tipo_pedido');

    if (tipoParams === 'llevar') {
        window.tipoPedidoActual = 'llevar';
        const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
        const lblTitulo = document.getElementById('lbl-titulo-tarjeta');
        
        if (lblTitulo) lblTitulo.textContent = 'Para Llevar';
        if (lblBoton) {
            lblBoton.textContent = 'Ingresar Nombre';
            lblBoton.className = 'text-[11px] font-black text-blue-500 mt-1 truncate max-w-full px-1';
        }
    }
});

// 6. Función para abrir el modal correcto al hacer clic en la tarjeta de tipo de pedido
window.abrirModalSegunTipo = function() {
    if (window.tipoPedidoActual === 'domicilio') {
        abrirModalDelivery(); // Tu función actual para abrir el de domicilio
    } else if (window.tipoPedidoActual === 'llevar') {
        abrirModal('modalParaLlevar');
        setTimeout(() => document.getElementById('inputClienteLlevar')?.focus(), 100);
    }
};

// 7. Confirmar el nombre temporal
window.nombreClienteTemporal = null; // Variable global para guardar el nombre suelto

window.confirmarClienteLlevar = function() {
    const input = document.getElementById('inputClienteLlevar');
    const nombre = input.value.trim();

    if(!nombre) {
        mostrarError("Por favor, ingresa un nombre para identificar el pedido.");
        return;
    }

    // Guardamos el nombre en la variable global
    window.nombreClienteTemporal = nombre;
    window.clienteSeleccionadoId = null; // Limpiamos por si había uno registrado antes

    // Actualizamos la tarjeta visualmente
    const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
    if (lblBoton) {
        lblBoton.textContent = window.nombreClienteTemporal;
        lblBoton.className = 'text-[11px] font-black text-emerald-500 mt-1 truncate max-w-full px-1';
    }

    cerrarModal('modalParaLlevar');
};

// --- 8. BUSCADOR INTELIGENTE (Detecta si es Domicilio o Llevar) ---
// NOTA: esta es la ÚNICA versión del listener de búsqueda. Antes existía
// una copia previa (más simple, sin soporte "Para Llevar") que quedaba
// registrada en paralelo a esta: al escribir en el buscador se disparaban
// DOS peticiones a /pos/clientes/buscar por cada tecleo, y como esta versión
// se ejecutaba después, terminaba sobreescribiendo el HTML de la otra. El
// problema real era que, al generar el onclick de cada resultado, a esta
// versión "nueva" se le olvidó reenviar los 4 datos de lealtad que el
// backend sí manda (lealtad_sellos, lealtad_premio_disponible,
// lealtad_siguiente_meta, lealtad_meta_requerida) — por eso la tarjeta de
// lealtad siempre caía en "No hay metas configuradas" sin importar la
// respuesta real del servidor. Fix: se agregan esos 4 argumentos al onclick.
document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'inputBuscarCliente') {
        clearTimeout(timeoutBuscador);
        const query = e.target.value.trim();
        const contenedor = document.getElementById('resultadosClientes');
        if (!contenedor) return;

        // Detectamos si estamos en modo "Llevar"
        const esLlevar = (window.tipoPedidoActual === 'llevar') || (new URLSearchParams(window.location.search).get('tipo_pedido') === 'llevar');

        if (query.length < 2) {
            contenedor.innerHTML = `<div class="p-4 text-center text-slate-500 text-sm font-medium">Empieza a escribir para buscar ${esLlevar ? 'o usar un nombre temporal' : ''}...</div>`;
            return;
        }

        timeoutBuscador = setTimeout(() => {
            contenedor.innerHTML = '<div class="p-4 text-center text-blue-600 text-sm font-bold"><i class="fas fa-spinner fa-spin mr-2"></i>Buscando...</div>';
            
            fetch(`/pos/clientes/buscar?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') }
            })
            .then(res => res.json())
            .then(clientes => {
                let html = '';

                // SI ES PARA LLEVAR: Inyectamos el botón de cliente rápido al principio
                if (esLlevar) {
                    html += `
                    <div class="flex justify-between items-center p-3 mb-2 bg-blue-50 border border-blue-300 rounded-xl hover:bg-blue-100 cursor-pointer transition-colors shadow-sm" 
                         onclick="window.confirmarClienteTemporal('${query.replace(/'/g, "\\'")}')">
                        <div>
                            <p class="font-bold text-blue-700 text-sm"><i class="fas fa-walking mr-1"></i> Usar nombre sin registrar</p>
                            <p class="text-xs text-blue-600 font-medium">Nombre: ${query}</p>
                        </div>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm transition-colors">Seleccionar</button>
                    </div>`;
                }

                if (!clientes || clientes.length === 0) {
                    if (!esLlevar) {
                        contenedor.innerHTML = '<div class="p-4 text-center text-slate-500 text-sm font-medium">No se encontraron clientes. Haz clic en "Nuevo Cliente".</div>';
                    } else {
                        contenedor.innerHTML = html; // Si es llevar, muestra solo el botón temporal
                    }
                    return;
                }

                // Renderiza los clientes encontrados en la BD
                clientes.forEach(c => {
                    const jsonDirecciones = encodeURIComponent(JSON.stringify(c.direcciones || []));
                    const nombreCompleto = `${c.nombre} ${c.apellido || ''}`.trim();

                    // FIX: se agregan los 4 argumentos de lealtad al onclick,
                    // igual que los manda el backend (ver PosClienteController@buscar).
                    // Se escapan comillas simples en los textos de premio/meta por
                    // si algún día contienen apóstrofes.
                    const premioSeguro = (c.lealtad_premio_disponible || 'null').toString().replace(/'/g, "\\'");
                    const metaSegura = (c.lealtad_siguiente_meta || 'null').toString().replace(/'/g, "\\'");

                    html += `
                    <div class="flex justify-between items-center p-3 border-b border-slate-100 hover:bg-blue-50/50 cursor-pointer transition-colors" 
                         onclick="window.seleccionarClienteDelivery(${c.id}, '${nombreCompleto.replace(/'/g, "\\'")}', '${c.telefono}', '${jsonDirecciones}', ${c.lealtad_sellos || 0}, '${premioSeguro}', '${metaSegura}', ${c.lealtad_meta_requerida || 0})">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">${nombreCompleto}</p>
                            <p class="text-xs text-slate-500"><i class="fas fa-phone mr-1"></i>${c.telefono}</p>
                        </div>
                        <button type="button" class="bg-white border border-slate-200 text-slate-800 px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm"><i class="fas fa-check text-blue-600"></i></button>
                    </div>`;
                });
                contenedor.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                contenedor.innerHTML = '<div class="p-4 text-center text-red-500 text-sm font-bold">Error de conexión.</div>';
            });
        }, 300);
    }
});

// 9. Función para guardar el cliente temporal (flujo "Para Llevar")
window.confirmarClienteTemporal = function(nombre) {
    window.nombreClienteTemporal = nombre;
    window.clienteSeleccionadoId = null; 
    window.direccionSeleccionadaId = null;

    // Actualizamos la tarjeta en la comanda
    const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
    if (lblBoton) {
        lblBoton.textContent = nombre;
        lblBoton.className = 'text-[11px] font-black text-blue-500 mt-1 truncate max-w-full px-1';
    }

    if (typeof window.cerrarModal === 'function') window.cerrarModal('modalDireccion'); 
    if (typeof mostrarExito === 'function') mostrarExito("Nombre temporal asignado");
};

// NUEVO: abre el buscador de clientes en "modo simple" — solo asigna
// el cliente para lealtad, sin pedir dirección (una mesa física no
// necesita dirección de entrega). Se usa desde el badge "Cliente" que
// ahora aparece en cualquier tipo de mesa, no solo en Para Llevar/Domicilio.
window.abrirSeleccionClienteMesa = function() {
    window.modoAsignarClienteSimple = true;
    window.manejarClickTipoPedido();
};

// SELECCIÓN INTELIGENTE (Lealtad + Domicilio / Llevar / Mesa física)
window.seleccionarClienteDelivery = function(id, nombre, telefono, direccionesEncoded, sellos = 0, premio = null, metaNombre = null, metaReq = 0) {
    window.clienteSeleccionadoId = id;
    window.nombreClienteTemporal = nombre;

 // Guardamos el último estado de lealtad calculado en una variable
    // global. Cualquier otra función que necesite volver a llamar a
    // seleccionarClienteDelivery más adelante (ej. al agregar una nueva
    // dirección) puede leer este valor en vez de dejarlo en blanco por
    // accidente y resetear la tarjeta de lealtad.
    window.__ultimaLealtad = { sellos, premio, metaNombre, metaReq };
 
    // ACTUALIZACIÓN DE LA TARJETA DE LEALTAD
    const tarjetaLealtad = document.getElementById('tarjeta-lealtad');
    const lealtadSellos = document.getElementById('lealtad-sellos');
    const lealtadMensaje = document.getElementById('lealtad-mensaje');
 
    if (tarjetaLealtad) {
        tarjetaLealtad.classList.remove('hidden');
        lealtadSellos.innerHTML = `${sellos} <i class="fas fa-star text-[8px] ml-0.5"></i>`;
 
        if (premio && premio !== 'null') {
            tarjetaLealtad.className = "col-span-2 mt-2 flex flex-col p-3 rounded-[16px] bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 shadow-sm relative overflow-hidden";
            lealtadSellos.className = "bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse";
            lealtadMensaje.innerHTML = `<span class="text-emerald-700 font-bold">¡Premio Disponible!</span><br><span class="text-slate-800">${premio}</span>`;
        } 
        else if (metaNombre && metaNombre !== 'null' && metaNombre !== "Configurar Niveles") {
            tarjetaLealtad.className = "col-span-2 mt-2 flex flex-col p-3 rounded-[16px] bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 shadow-sm relative overflow-hidden";
            lealtadSellos.className = "bg-indigo-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm";
            let faltan = metaReq - sellos;
            lealtadMensaje.innerHTML = `Falta(n) <b class="text-indigo-600">${faltan} compra(s)</b> para:<br><span class="text-slate-800 font-semibold">${metaNombre}</span>`;
        } else {
            tarjetaLealtad.className = "col-span-2 mt-2 flex flex-col p-3 rounded-[16px] bg-gradient-to-br from-slate-100 to-white border border-slate-200 shadow-sm relative overflow-hidden";
            lealtadSellos.className = "bg-slate-400 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm";
            lealtadMensaje.innerHTML = "No hay metas de lealtad configuradas.";
        }
    }
 
    const esLlevar = (window.tipoPedidoActual === 'llevar') || (new URLSearchParams(window.location.search).get('tipo_pedido') === 'llevar');
 
    // NUEVO: "modo simple" cubre dos casos que NO necesitan dirección:
    // 1) Para Llevar (ya existía), y
    // 2) Mesa física normal, activado por abrirSeleccionClienteMesa()
    //    cuando el mesero solo quiere ligar un cliente para lealtad
    //    sin que sea un pedido de domicilio.
    const esModoSimple = esLlevar || window.modoAsignarClienteSimple;
 
    if (esModoSimple) {
        // Mesa virtual "Para Llevar": actualiza su propia etiqueta.
        const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
        if (lblBoton) { 
            lblBoton.textContent = nombre; 
            lblBoton.className = 'text-[11px] font-black text-blue-500 mt-1 truncate max-w-full px-1'; 
        }
 
        // Mesa física: actualiza el badge de cliente asignado.
        const lblClienteMesa = document.getElementById('lbl-cliente-mesa-fisica');
        if (lblClienteMesa) {
            lblClienteMesa.textContent = nombre;
            lblClienteMesa.className = 'block text-sm font-black text-emerald-600 truncate';
        }
 
        window.modoAsignarClienteSimple = false; // se resetea para no quedar pegado a futuras selecciones
 
        if (typeof window.cerrarModal === 'function') window.cerrarModal('modalDireccion'); 
        if (typeof mostrarExito === 'function') mostrarExito("Cliente asignado a la mesa.");
        return; 
    }
 
    // SI ES DOMICILIO: Flujo normal de direcciones
    const inputClienteId = document.getElementById('nd_cliente_id');
    if (inputClienteId) inputClienteId.value = id; 
    
    const lblNombre = document.getElementById('lbl-nombre-cliente');
    const lblTel = document.getElementById('lbl-tel-cliente');
    if (lblNombre) lblNombre.textContent = nombre;
    if (lblTel) lblTel.textContent = 'Tel: ' + telefono;
 
    try {
        const direcciones = JSON.parse(decodeURIComponent(direccionesEncoded));
        renderDireccionesRadios(direcciones);
    } catch(err) { 
        renderDireccionesRadios([]); 
    }
 
    const vistaBuscador = document.getElementById('vista-buscador-clientes');
    const vistaDirecciones = document.getElementById('vista-direcciones-cliente');
    
    if (vistaBuscador) vistaBuscador.classList.add('hidden');
    if (vistaDirecciones) vistaDirecciones.classList.remove('hidden');
 
    const btnConfirmar = document.getElementById('btnConfirmarDelivery');
    if (btnConfirmar) {
        btnConfirmar.disabled = true;
        btnConfirmar.classList.add('opacity-50', 'cursor-not-allowed');
    }
};

window.guardarNuevoClienteAjax = async function(event) {
    event.preventDefault();

    const nombre = document.getElementById('nc_nombre').value.trim();
    const apellido = document.getElementById('nc_apellido').value.trim();
    const telefono = document.getElementById('nc_telefono').value.trim();

    if (!nombre || !telefono) {
        alert('Por favor, completa los campos obligatorios (Nombre y Teléfono).');
        return;
    }

    try {
        const response = await fetch("{{ route('pos.clientes.express') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nombre: nombre,
                apellido: apellido,
                telefono: telefono
            })
        });

        const data = await response.json();

        if (data.success) {
            // 1. Cerramos el modal de nuevo cliente
            cerrarModal('modalNuevoCliente');

            // 2. Limpiamos el formulario
            document.getElementById('formNuevoCliente').reset();

            // 3. NUEVO: pos.clientes.express ahora calcula y devuelve la
            // lealtad directo en la respuesta (mismo cálculo que usa el
            // buscador), así que ya no hace falta una segunda petición
            // para conseguir esos datos — se leen directo de data.cliente.
            const nombreCompleto = `${data.cliente.nombre} ${data.cliente.apellido || ''}`.trim();

            window.seleccionarClienteDelivery(
                data.cliente.id,
                nombreCompleto,
                data.cliente.telefono,
                encodeURIComponent(JSON.stringify(data.cliente.direcciones || [])),
                data.cliente.lealtad_sellos || 0,
                data.cliente.lealtad_premio_disponible || 'null',
                data.cliente.lealtad_siguiente_meta || 'null',
                data.cliente.lealtad_meta_requerida || 0
            );

            if (typeof mostrarExito === 'function') {
                mostrarExito("¡Cliente registrado y guardado con éxito!");
            }
        } else {
            alert(data.message || 'Error al registrar el cliente.');
        }
    } catch (error) {
        console.error('Error de red:', error);
        alert('Ocurrió un error de conexión al guardar el cliente.');
    }
};
window.guardarNuevaDireccionAjax = async function(event) {
    event.preventDefault();

    // Forzamos a que tome el ID del cliente activo en la ventana
    const clienteId = document.getElementById('nd_cliente_id').value || window.clienteSeleccionadoId;
    const calle = document.getElementById('nd_calle').value.trim();
    const manzana = document.getElementById('nd_manzana').value.trim();
    const lote = document.getElementById('nd_lote').value.trim();
    const colonia = document.getElementById('nd_colonia').value.trim();
    const referencia = document.getElementById('nd_referencia').value.trim();

    if (!clienteId || !calle) {
        alert('Error: No se detectó un cliente seleccionado o falta la calle.');
        return;
    }

    try {
        const response = await fetch("{{ route('pos.direcciones.express') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                cliente_id: clienteId,
                calle: calle,
                manzana: manzana,
                lote: lote,
                colonia: colonia,
                referencia: referencia
            })
        });

        const data = await response.json();

        if (data.success) {
            cerrarModal('modalNuevaDireccion');
            document.getElementById('formNuevaDireccion').reset();

            // NUEVO: reutilizamos el último estado de lealtad conocido
            // (guardado por seleccionarClienteDelivery en window.__ultimaLealtad)
            // en vez de omitir esos 4 argumentos. Antes, al agregar una
            // dirección nueva, esta misma llamada reseteaba la tarjeta de
            // lealtad a "No hay metas configuradas" sin importar que ya
            // se hubiera cargado correctamente segundos antes.
            const lealtadPrevia = window.__ultimaLealtad || {};

            // Recargamos las direcciones del cliente para que aparezcan de inmediato en el radio
            window.seleccionarClienteDelivery(
                clienteId, 
                document.getElementById('lbl-nombre-cliente').textContent, 
                document.getElementById('lbl-tel-cliente').textContent.replace('Tel: ', ''), 
                encodeURIComponent(JSON.stringify([data.direccion])),
                lealtadPrevia.sellos || 0,
                lealtadPrevia.premio || 'null',
                lealtadPrevia.metaNombre || 'null',
                lealtadPrevia.metaReq || 0
            );

            if (typeof mostrarExito === 'function') {
                mostrarExito("¡Dirección agregada y guardada con éxito!");
            }
        } else {
            alert(data.message || 'Error al guardar la dirección.');
        }
    } catch (error) {
        console.error('Error de red:', error);
        alert('Ocurrió un error de conexión al guardar la dirección.');
    }
};

window.confirmarDomicilioExpress = function() {
    const nombreInput = document.getElementById('inputDomicilioExpressNombre');
    const direccionInput = document.getElementById('inputDomicilioExpressDireccion');
    
    const nombre = nombreInput ? nombreInput.value.trim() : '';
    const direccion = direccionInput ? direccionInput.value.trim() : '';

    if (!nombre || !direccion) {
        // Reemplazamos el alert tradicional por tu función de notificación personalizada
        if (typeof mostrarError === 'function') {
            mostrarError("Por favor, ingresa tanto el nombre como la dirección temporal.");
        } else if (typeof mostrarAlerta === 'function') {
            mostrarAlerta("Atención", "Por favor, ingresa tanto el nombre como la dirección temporal.");
        } else {
            alert('Por favor, ingresa tanto el nombre como la dirección temporal.'); // Respaldo por si acaso
        }
        return;
    }

    // Guardamos en las variables globales
    window.nombreClienteTemporal = nombre + " (" + direccion + ")";
    window.clienteSeleccionadoId = null;
    window.direccionSeleccionadaId = null;

    // Actualizamos la tarjeta visualmente en la comanda
    const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
    const lblTitulo = document.getElementById('lbl-titulo-tarjeta');

    if (lblTitulo) lblTitulo.textContent = 'A Domicilio (Exprés)';
    if (lblBoton) {
        lblBoton.textContent = window.nombreClienteTemporal;
        lblBoton.className = 'text-[11px] font-black text-blue-600 mt-1 truncate max-w-full px-1';
    }

    // Limpiamos los inputs
    nombreInput.value = '';
    direccionInput.value = '';

    // Cerramos el modal
    if (typeof cerrarModal === 'function') {
        cerrarModal('modalDireccion');
    }

    if (typeof mostrarExito === 'function') {
        mostrarExito("¡Domicilio exprés asignado correctamente!");
    }
};

</script>