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
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500/15 to-blue-500/5 border border-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-lock text-blue-500 text-xs"></i>
                </span>
                <h2 class="text-base sm:text-lg font-bold text-[var(--text-main)] tracking-tight">NIP Administrador</h2>
            </div>
            <button type="button" onclick="cerrarModal('modalNip')" class="text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>
        
        <input type="password" id="nipInput"
               maxlength="6" autocomplete="off"
               data-solo-numeros="true"
               data-teclado-virtual="true"
               class="w-full min-h-[64px] rounded-xl border border-[var(--border-color)] bg-[var(--input-bg)] shadow-inner p-4 text-2xl sm:text-xl font-black text-center text-[var(--text-main)] outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 tracking-[0.3em]"
               placeholder="••••">
               
        {{-- Teclado Numérico --}}
        <div class="grid grid-cols-3 gap-1.5 mt-4">
            @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                <button type="button" onclick="escribirNumVirtual('nipInput', '{{ $key }}')" class="min-h-[44px] rounded-lg bg-[var(--input-bg)] border border-[var(--border-color)] hover:border-blue-500/30 hover:bg-[var(--hover-bg)] active:scale-90 text-[var(--text-main)] text-lg font-bold shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="escribirNumVirtual('nipInput', '0')" class="col-span-2 min-h-[44px] rounded-lg bg-[var(--input-bg)] border border-[var(--border-color)] hover:border-blue-500/30 hover:bg-[var(--hover-bg)] active:scale-90 text-[var(--text-main)] text-lg font-bold shadow-sm transition-all duration-100">0</button>
            <button type="button" onclick="borrarNumVirtual('nipInput')" class="min-h-[44px] rounded-lg bg-red-500/10 border border-red-500/15 text-red-500 hover:bg-red-500 hover:text-white active:scale-90 text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>

        <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModal('modalNip')" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl border border-[var(--border-color)] text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--hover-bg)] active:scale-95 text-xs font-bold transition-all duration-150">Cancelar</button>
            <button type="button" onclick="confirmarNipCapitan()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-gradient-to-b from-blue-500 to-blue-600 text-white text-xs font-bold active:scale-95 shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-150">Aceptar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     2. MODAL CAPITÁN (Selección destino)
     ========================================== --}}
<div id="modalCapitan" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    @php $mesasAbiertas = $mesasAbiertas ?? collect(); @endphp
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-lg bg-gradient-to-br from-indigo-500/15 to-indigo-500/5 border border-indigo-500/20 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-indigo-500 text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-widest text-indigo-500 font-bold">Autorización</p>
                    <h2 class="text-lg sm:text-xl font-semibold text-[var(--text-main)] leading-tight">Selecciona mesero destino</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalCapitan')" class="flex-shrink-0 text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div id="capitanMeserosContainer" class="grid gap-2 max-h-[45vh] sm:max-h-[300px] overflow-y-auto hide-scroll pb-2"></div>
        <div class="mt-5 flex justify-end">
            <button type="button" onclick="cerrarModal('modalCapitan')" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl border border-[var(--border-color)] text-xs font-medium text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--hover-bg)] active:scale-95 transition-all duration-150">Cancelar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     3. MODAL NOTAS (Teclado Nativo del teléfono)
     ========================================== --}}
<div id="modalNota" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[94vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-[var(--bg-panel)] border border-[var(--border-color)] p-4 sm:p-6 pb-[calc(1rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-4 sm:mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500/15 to-blue-500/5 border border-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-pen text-blue-500 text-xs"></i>
                </span>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[var(--text-main)] tracking-tight">Instrucción Especial</h2>
                    <p class="text-[11px] text-[var(--text-muted)] font-semibold">
                        Para: <span id="notaModalProducto" class="text-blue-500 font-bold">-</span>
                    </p>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalNota')" class="text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>

        <textarea id="notaTextarea" rows="4"
            readonly
            inputmode="none"
            onclick="abrirTecladoNota()"
            class="w-full rounded-2xl border border-[var(--border-color)] bg-[var(--input-bg)] shadow-inner p-4 text-sm font-medium text-[var(--text-main)] outline-none resize-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 mb-4 cursor-pointer"
            placeholder="Toca aquí para escribir...">
        </textarea>

        <div class="flex items-center gap-2 mb-4 overflow-x-auto hide-scroll pb-1">
            @foreach(['Sin cebolla', 'Salsa aparte', 'Bien cocido', 'Término medio', 'Para llevar', 'Sin picante'] as $notaRapida)
                <button type="button" onclick="agregarTextoRapidoNota('{{ $notaRapida }}')"
                    class="shrink-0 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-500 hover:bg-blue-500 hover:text-white active:scale-90 select-none text-[11px] font-bold shadow-sm transition-all duration-150">
                    {{ $notaRapida }}
                </button>
            @endforeach
        </div>

        <div class="mt-2 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="limpiarNota()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-[var(--text-muted)]/10 border border-[var(--border-color)] text-[var(--text-muted)] hover:bg-[var(--text-muted)] hover:text-white active:scale-95 select-none text-[11px] sm:text-xs font-bold transition-all duration-150">Limpiar</button>
            <button type="button" onclick="cerrarModal('modalNota')" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl border border-[var(--border-color)] text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--hover-bg)] active:scale-95 text-xs font-bold transition-all duration-150">Cancelar</button>
            <button type="button" onclick="guardarNota()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-gradient-to-b from-[var(--text-main)] to-[var(--text-main)] text-[var(--bg-base)] text-xs font-bold active:scale-95 shadow-md hover:shadow-lg transition-all duration-150 hover:opacity-90">Confirmar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     5. MODAL PERSONAS (Teclado Numérico Virtual)
     ========================================== --}}
<div id="modalPersonas" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-[var(--text-main)]/10 to-[var(--text-main)]/5 border border-[var(--border-color)] flex items-center justify-center">
                    <i class="fas fa-users text-[var(--text-main)] text-xs"></i>
                </span>
                <h2 class="text-base sm:text-lg font-bold text-[var(--text-main)] tracking-tight">Personas en Mesa</h2>
            </div>
            <button type="button" onclick="cerrarModal('modalPersonas')" class="text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>
        
        <input id="personasInput" type="text" data-solo-numeros="true" data-teclado-virtual="true"
               maxlength="3" inputmode="numeric" pattern="[0-9]*" autocomplete="off"
               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)"
               class="w-full min-h-[64px] rounded-xl border border-[var(--border-color)] bg-[var(--input-bg)] shadow-inner p-4 text-2xl sm:text-xl font-black text-center text-[var(--text-main)] outline-none focus:border-[var(--text-main)] focus:ring-4 focus:ring-[var(--text-main)]/10 transition-all duration-200">

        {{-- Teclado Numérico --}}
        <div class="grid grid-cols-3 gap-1.5 mt-4">
            @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                <button type="button" onclick="escribirNumVirtual('personasInput', '{{ $key }}')" class="min-h-[44px] rounded-lg bg-[var(--input-bg)] border border-[var(--border-color)] hover:border-[var(--text-main)]/30 hover:bg-[var(--hover-bg)] active:scale-90 text-[var(--text-main)] text-lg font-bold shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="escribirNumVirtual('personasInput', '0')" class="col-span-2 min-h-[44px] rounded-lg bg-[var(--input-bg)] border border-[var(--border-color)] hover:border-[var(--text-main)]/30 hover:bg-[var(--hover-bg)] active:scale-90 text-[var(--text-main)] text-lg font-bold shadow-sm transition-all duration-100">0</button>
            <button type="button" onclick="borrarNumVirtual('personasInput')" class="min-h-[44px] rounded-lg bg-red-500/10 border border-red-500/15 text-red-500 hover:bg-red-500 hover:text-white active:scale-90 text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>

        <div class="mt-6">
            <button type="button" onclick="guardarPersonas()" class="w-full min-h-[48px] rounded-xl bg-gradient-to-b from-[var(--text-main)] to-[var(--text-main)] text-[var(--bg-base)] text-sm font-bold active:scale-95 shadow-md hover:shadow-lg transition-all duration-150">Guardar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     6. MODAL GRAMAJE (Teclado Numérico Virtual propio)
     ========================================== --}}
<div id="modalGramaje" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[94vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 shrink-0 rounded-lg bg-gradient-to-br from-orange-500/15 to-orange-500/5 border border-orange-500/20 flex items-center justify-center">
                    <i class="fas fa-weight-scale text-orange-500 text-xs"></i>
                </span>
                <h2 id="modalGramajeTitulo" class="text-base sm:text-lg font-bold text-[var(--text-main)] truncate">Gramaje</h2>
            </div>
            <button type="button" onclick="cerrarModalGramaje()" class="flex-shrink-0 text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>

        <div class="flex items-center gap-3 mb-2">
            <input id="gramajeInput" type="text"
                   inputmode="decimal" autocomplete="off"
                   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                   class="flex-1 min-w-0 rounded-xl border border-[var(--border-color)] bg-[var(--input-bg)] shadow-inner p-4 text-2xl font-black text-[var(--text-main)] text-center outline-none" placeholder="0">
            <span class="shrink-0 text-[var(--text-muted)] font-bold text-lg">g</span>
        </div>

        <p id="gramajePrecioPreview" class="text-center text-sm font-black text-orange-500 mb-4 h-5"></p>

        <div id="botonesRapidosGramaje" class="grid grid-cols-5 gap-1.5 mb-4">
            @foreach([['100','100g'],['250','250g'],['500','500g'],['700','700g'],['1000','1kg']] as [$valor, $etiqueta])
                <button type="button" onclick="seleccionarGramajeRapido({{ $valor }})" class="min-h-[40px] py-2 rounded-lg bg-orange-500/10 border border-orange-500/20 text-orange-500 hover:bg-orange-500 hover:text-white active:scale-90 select-none text-[11px] font-bold shadow-sm transition-all duration-150">{{ $etiqueta }}</button>
            @endforeach
        </div>

        <div id="tecladoGramaje" class="grid grid-cols-3 gap-2">
            @foreach(['1','2','3','4','5','6','7','8','9','.','0'] as $key)
                <button type="button" onclick="anadirNumeroGramaje('{{ $key }}')" class="min-h-[48px] py-3 rounded-xl bg-[var(--input-bg)] border border-[var(--border-color)] hover:border-orange-500/30 hover:bg-[var(--hover-bg)] active:scale-90 text-[var(--text-main)] text-lg font-bold shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="borrarNumeroGramaje()" class="min-h-[48px] py-3 rounded-xl bg-red-500/10 border border-red-500/15 text-red-500 hover:bg-red-500 hover:text-white active:scale-90 select-none text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>
        
        <div class="mt-6">
            <button type="button" onclick="guardarGramajeDelItem()" class="w-full min-h-[48px] rounded-xl bg-gradient-to-b from-orange-500 to-orange-600 text-white text-sm font-bold active:scale-95 shadow-md hover:shadow-lg transition-all duration-150">Confirmar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     7. MODAL TIPO DE TRASPASO
     ========================================== --}}
<div id="modalTipoTraspaso" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-lg bg-gradient-to-br from-indigo-500/15 to-indigo-500/5 border border-indigo-500/20 flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-indigo-500 text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-widest text-indigo-500 font-bold">Traspaso</p>
                    <h2 id="tituloTipoTraspaso" class="text-base sm:text-lg font-bold text-[var(--text-main)] leading-tight">¿Qué deseas traspasar?</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalTipoTraspaso')" class="flex-shrink-0 text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="flex flex-col gap-3">
            <button type="button" onclick="elegirTraspasoProducto()" class="w-full flex items-center gap-3 text-left rounded-2xl border border-[var(--border-color)] bg-[var(--bg-base)] px-4 py-4 hover:border-blue-500/40 hover:shadow-md active:scale-[0.98] transition-all duration-150">
                <span class="w-9 h-9 shrink-0 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-utensils text-blue-500 text-xs"></i>
                </span>
                <span>
                    <p class="text-sm font-bold text-[var(--text-main)]">Producto Individual</p>
                    <p class="text-[12px] text-[var(--text-muted)] mt-0.5">Elige uno o varios platillos específicos</p>
                </span>
            </button>
            <button type="button" onclick="elegirTraspasoCompleto()" class="w-full flex items-center gap-3 text-left rounded-2xl border border-[var(--border-color)] bg-[var(--bg-base)] px-4 py-4 hover:border-blue-500/40 hover:shadow-md active:scale-[0.98] transition-all duration-150">
                <span class="w-9 h-9 shrink-0 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-blue-500 text-xs"></i>
                </span>
                <span>
                    <p class="text-sm font-bold text-[var(--text-main)]">Pedido Completo</p>
                    <p class="text-[12px] text-[var(--text-muted)] mt-0.5">Envía toda la orden a la mesa destino</p>
                </span>
            </button>
        </div>
    </div>
</div>

{{-- ==========================================
     8. MODAL SELECCIÓN DE PRODUCTOS
     ========================================== --}}
<div id="modalSeleccionProductos" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-lg bg-gradient-to-br from-indigo-500/15 to-indigo-500/5 border border-indigo-500/20 flex items-center justify-center">
                    <i class="fas fa-list-check text-indigo-500 text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-widest text-indigo-500 font-bold">Traspaso</p>
                    <h2 class="text-lg font-bold text-[var(--text-main)] leading-tight">Selecciona productos</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalSeleccionProductos')" class="flex-shrink-0 text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div id="listaProductosTraspaso" class="grid gap-2 max-h-[45vh] sm:max-h-[320px] overflow-y-auto hide-scroll pb-2"></div>
        <div class="mt-5 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModal('modalSeleccionProductos')" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl border border-[var(--border-color)] text-xs font-medium text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--hover-bg)] active:scale-95 transition-all duration-150">Cancelar</button>
            <button type="button" onclick="confirmarTraspasoProductos()" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl bg-gradient-to-b from-blue-500 to-blue-600 text-white text-xs font-bold active:scale-95 shadow-md transition-all duration-150">Traspasar</button>
        </div>
    </div>
</div>

{{-- ==========================================
     9. MODAL SELECCIÓN DE COMBO
     ========================================== --}}
<div id="modalSeleccionCombo" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-lg bg-gradient-to-br from-blue-500/15 to-blue-500/5 border border-blue-500/20 flex items-center justify-center">
                    <i class="fas fa-tags text-blue-500 text-xs"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-widest text-blue-500 font-bold">Combo</p>
                    <h2 id="tituloSeleccionCombo" class="text-lg font-bold text-[var(--text-main)] leading-tight">Selecciona los productos</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModal('modalSeleccionCombo')" class="flex-shrink-0 text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>
        <p class="text-[12px] text-[var(--text-muted)] mb-3">Marca cuáles productos del ticket quieres que cuenten para este combo. Solo puedes marcar los que ya están agregados.</p>
        <div id="listaSeleccionCombo" class="grid gap-2 max-h-[40vh] sm:max-h-[320px] overflow-y-auto hide-scroll pb-2"></div>
        <div class="mt-5 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModal('modalSeleccionCombo')" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl border border-[var(--border-color)] text-xs font-medium text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--hover-bg)] active:scale-95 transition-all duration-150">Cancelar</button>
            <button type="button" onclick="confirmarSeleccionCombo()" class="w-full sm:w-auto min-h-[44px] px-5 rounded-xl bg-gradient-to-b from-blue-500 to-blue-600 text-white text-xs font-bold active:scale-95 shadow-md transition-all duration-150">Aplicar combo</button>
        </div>
    </div>
</div>

{{-- ==========================================
     10. MODAL PROMOCIONES
     ========================================== --}}
<div id="modalPromociones" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-md max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-3xl bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <!-- Contenido de promociones -->
    </div>
</div>

{{-- ==========================================
     11. MODAL NIP CANCELACIÓN DE PRODUCTO
     (Independiente del modalNip de Capitán/Traspaso)
     ========================================== --}}
<div id="modalNipCancelacion" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500/15 to-red-500/5 border border-red-500/20 flex items-center justify-center">
                    <i class="fas fa-ban text-red-500 text-xs"></i>
                </span>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-red-500 font-bold">Autorización</p>
                    <h2 class="text-base sm:text-lg font-bold text-[var(--text-main)] tracking-tight">NIP para Cancelar</h2>
                </div>
            </div>
            <button type="button" onclick="cerrarModalCancelacion()" class="text-[var(--text-muted)] hover:text-[var(--text-main)] w-9 h-9 -m-1 rounded-full hover:bg-[var(--hover-bg)] flex items-center justify-center transition-all duration-200"><i class="fas fa-times text-lg"></i></button>
        </div>

        <p class="text-[12px] text-[var(--text-muted)] mb-3">Ingresa el NIP del Administrador para autorizar la cancelación de este producto.</p>

        <input type="password" id="nipCancelacionInput" data-solo-numeros="true" data-teclado-virtual="true"
               maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="off"
               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
               class="w-full min-h-[64px] rounded-xl border border-[var(--border-color)] bg-[var(--input-bg)] shadow-inner p-4 text-2xl sm:text-xl font-black text-center text-[var(--text-main)] outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all duration-200 tracking-[0.3em]"
               placeholder="••••">

        {{-- Teclado Numérico (reutiliza escribirNumVirtual/borrarNumVirtual, ya son genéricas por ID) --}}
        <div class="grid grid-cols-3 gap-1.5 mt-4">
            @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                <button type="button" onclick="escribirNumVirtual('nipCancelacionInput', '{{ $key }}')" class="min-h-[44px] rounded-lg bg-[var(--input-bg)] border border-[var(--border-color)] hover:border-red-500/30 hover:bg-[var(--hover-bg)] active:scale-90 text-[var(--text-main)] text-lg font-bold shadow-sm transition-all duration-100">{{ $key }}</button>
            @endforeach
            <button type="button" onclick="escribirNumVirtual('nipCancelacionInput', '0')" class="col-span-2 min-h-[44px] rounded-lg bg-[var(--input-bg)] border border-[var(--border-color)] hover:border-red-500/30 hover:bg-[var(--hover-bg)] active:scale-90 text-[var(--text-main)] text-lg font-bold shadow-sm transition-all duration-100">0</button>
            <button type="button" onclick="borrarNumVirtual('nipCancelacionInput')" class="min-h-[44px] rounded-lg bg-red-500/10 border border-red-500/15 text-red-500 hover:bg-red-500 hover:text-white active:scale-90 text-sm font-bold transition-all duration-150"><i class="fas fa-backspace"></i></button>
        </div>

        <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModalCancelacion()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl border border-[var(--border-color)] text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--hover-bg)] active:scale-95 text-xs font-bold transition-all duration-150">Cancelar</button>
            <button type="button" id="btnConfirmarNipCancelacion" onclick="confirmarNipCancelacion()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-gradient-to-b from-red-500 to-red-600 text-white text-xs font-bold active:scale-95 shadow-md shadow-red-500/20 hover:shadow-lg hover:shadow-red-500/25 transition-all duration-150">Autorizar Cancelación</button>
        </div>
    </div>
</div>

{{-- ==========================================
     12. MODAL CONFIRMAR CANCELACIÓN DE PRODUCTO
     ========================================== --}}
<div id="modalConfirmarCancelacion" class="modal-overlay hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="modal-sheet w-full sm:max-w-sm max-h-[92vh] overflow-y-auto hide-scroll rounded-t-[28px] sm:rounded-[24px] bg-[var(--bg-panel)] border border-[var(--border-color)] p-5 sm:p-6 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:pb-6 shadow-2xl ring-1 ring-black/5">
        <div class="sm:hidden w-10 h-1.5 rounded-full bg-[var(--border-color)] mx-auto mb-4"></div>

        <div class="flex flex-col items-center text-center gap-3 mb-2">
            <span class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500/15 to-red-500/5 border border-red-500/20 flex items-center justify-center">
                <i class="fas fa-triangle-exclamation text-red-500 text-xl"></i>
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[var(--text-main)] tracking-tight">¿Cancelar este producto?</h2>
                <p class="text-[12px] text-[var(--text-muted)] mt-1.5 leading-relaxed">Esta acción no se puede deshacer y ya no se cobrará al cliente.</p>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2.5 sm:gap-3">
            <button type="button" onclick="cerrarModalConfirmarCancelacion()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl border border-[var(--border-color)] text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--hover-bg)] active:scale-95 text-xs font-bold transition-all duration-150">No, mantener</button>
            <button type="button" onclick="continuarCancelacionConNip()" class="w-full sm:w-auto min-h-[44px] px-6 rounded-xl bg-gradient-to-b from-red-500 to-red-600 text-white text-xs font-bold active:scale-95 shadow-md shadow-red-500/20 hover:shadow-lg hover:shadow-red-500/25 transition-all duration-150">Sí, cancelar</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     TECLADO VIRTUAL — Modal de Nota / Instrucción
     ════════════════════════════════════════════════ --}}
<div id="teclado-nota-overlay"
     class="hidden fixed inset-0 z-[99999]"
     onclick="if(event.target===this) cerrarTecladoNota()">

    <div class="absolute bottom-0 inset-x-0 bg-[var(--bg-base)] border-t border-[var(--border-color)] shadow-2xl rounded-t-3xl">

        {{-- Display + cerrar --}}
        <div class="flex items-start gap-3 px-4 pt-4 pb-3 border-b border-[var(--border-color)]">
            <div class="flex-1 bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-xl px-3 py-2.5 min-h-[48px] max-h-24 overflow-y-auto">
                <span id="tn-display" class="text-sm font-medium text-[var(--text-main)] break-words whitespace-pre-wrap"></span><span class="inline-block w-0.5 h-4 bg-blue-500 animate-pulse rounded-full align-middle ml-0.5"></span>
            </div>
            <button type="button" onclick="cerrarTecladoNota()"
                class="w-10 h-10 rounded-xl bg-[var(--bg-panel)] border border-[var(--border-color)] text-[var(--text-muted)] flex items-center justify-center shrink-0 active:scale-95 mt-0.5">
                <i class="fas fa-check text-sm text-blue-500"></i>
            </button>
        </div>

        {{-- Notas rápidas --}}
        <div class="flex gap-2 px-3 pt-2.5 overflow-x-auto hide-scroll pb-1">
            @foreach(['Sin cebolla','Salsa aparte','Bien cocido','Término medio','Para llevar','Sin picante'] as $nota)
                <button type="button" onclick="tnRapida('{{ $nota }}')"
                    class="shrink-0 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-500 text-[11px] font-bold active:scale-90 transition-all">
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
                            class="flex-1 max-w-[38px] h-10 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-white font-black text-sm shadow-sm active:scale-95 active:bg-blue-100 dark:active:bg-blue-500/20 transition-all duration-75">
                            {{ $letra }}
                        </button>
                    @endforeach
                </div>
            @endforeach
            {{-- Fila inferior --}}
            <div class="flex justify-center gap-1 mt-1">
                <button type="button" onclick="tnEscribir(' ')"
                    class="flex-1 h-10 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-500 font-bold text-xs shadow-sm active:scale-95 transition-all duration-75">
                    ESPACIO
                </button>
                <button type="button" onclick="tnEscribir('.')"
                    class="w-12 h-10 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-white font-black text-sm shadow-sm active:scale-95 transition-all duration-75">
                    .
                </button>
                <button type="button" onclick="tnBorrar()"
                    class="w-14 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-500 shadow-sm active:scale-95 flex items-center justify-center transition-all duration-75">
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
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-[var(--bg-panel)] text-left shadow-2xl transition-all w-full max-w-md border border-[var(--border-color)]">
                <div class="px-6 py-4 border-b border-[var(--border-color)] bg-[var(--bg-panel)] flex justify-between items-center">
                    <h3 class="text-xl font-black text-[var(--text-main)]">Tipo de Pedido</h3>
                    <button type="button" onclick="cerrarModal('modalTipoPedido')" class="text-[var(--text-muted)] hover:text-[var(--text-main)] transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Comedor -->
                    <button type="button" onclick="seleccionarTipoPedido('comedor')" class="w-full flex items-center p-4 border-2 border-[var(--border-color)] rounded-xl hover:border-blue-500 hover:bg-blue-500/5 transition-all group">
                        <div class="w-12 h-12 flex items-center justify-center bg-blue-500/10 text-blue-500 rounded-full group-hover:scale-110 transition-transform">
                            <i class="fas fa-utensils text-xl"></i>
                        </div>
                        <div class="ml-4 text-left">
                            <p class="font-bold text-[var(--text-main)] text-lg">Comedor / Mesa</p>
                            <p class="text-sm text-[var(--text-muted)]">El cliente consume en el local.</p>
                        </div>
                    </button>
                    <!-- Para Llevar -->
                    <button type="button" onclick="seleccionarTipoPedido('llevar')" class="w-full flex items-center p-4 border-2 border-[var(--border-color)] rounded-xl hover:border-green-500 hover:bg-green-500/5 transition-all group">
                        <div class="w-12 h-12 flex items-center justify-center bg-green-500/10 text-green-500 rounded-full group-hover:scale-110 transition-transform">
                            <i class="fas fa-shopping-bag text-xl"></i>
                        </div>
                        <div class="ml-4 text-left">
                            <p class="font-bold text-[var(--text-main)] text-lg">Para Llevar (Pick Up)</p>
                            <p class="text-sm text-[var(--text-muted)]">El cliente pasa a recoger su pedido.</p>
                        </div>
                    </button>
                    <!-- A Domicilio -->
                    <button type="button" onclick="abrirModalDelivery()" class="w-full flex items-center p-4 border-2 border-[var(--border-color)] rounded-xl hover:border-amber-500 hover:bg-amber-500/5 transition-all group">
                        <div class="w-12 h-12 flex items-center justify-center bg-amber-500/10 text-amber-500 rounded-full group-hover:scale-110 transition-transform">
                            <i class="fas fa-motorcycle text-xl"></i>
                        </div>
                        <div class="ml-4 text-left">
                            <p class="font-bold text-[var(--text-main)] text-lg">A Domicilio</p>
                            <p class="text-sm text-[var(--text-muted)]">Requiere cliente y dirección de entrega.</p>
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
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-[var(--bg-panel)] text-left shadow-2xl transition-all w-full max-w-2xl border border-[var(--border-color)]">
                <div class="px-6 py-4 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--bg-panel)]">
                    <div class="flex items-center space-x-3 text-amber-500">
                        <i class="fas fa-box text-2xl"></i>
                        <h3 class="text-xl font-black text-[var(--text-main)]">Dirección de Entrega</h3>
                    </div>
                    <button type="button" onclick="cerrarModal('modalDireccion')" class="text-[var(--text-muted)] hover:text-[var(--text-main)] transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 min-h-[300px]">
                    <!-- VISTA A: BUSCADOR -->
                    <div id="vista-buscador-clientes">
                        <div class="flex justify-between items-end mb-4">
                            <label class="block text-sm font-bold text-[var(--text-main)]">Seleccionar Cliente *</label>
                            <button type="button" onclick="abrirModalNuevoCliente()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg shadow text-sm transition-colors">
                                <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                            </button>
                        </div>

                <!-- Opción de Domicilio Express / Temporal -->
@if(request('tipo_pedido') === 'domicilio')
    <!-- Opción de Domicilio Express / Temporal -->
    <div class="mt-3 p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl">
        <p class="text-xs font-bold text-amber-600 dark:text-amber-400 mb-2"><i class="fas fa-bolt mr-1"></i> ¿Pedido exprés sin registrar?</p>
        <div class="flex gap-2">
            <input type="text" id="inputDomicilioExpressNombre" placeholder="Nombre del cliente..." class="flex-1 px-3 py-2 text-xs border border-[var(--border-color)] rounded-lg bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-1 focus:ring-amber-500">
            <input type="text" id="inputDomicilioExpressDireccion" placeholder="Dirección corta / Referencia..." class="flex-1 px-3 py-2 text-xs border border-[var(--border-color)] rounded-lg bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-1 focus:ring-amber-500">
            <button type="button" onclick="confirmarDomicilioExpress()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-2 rounded-lg text-xs transition-colors shrink-0">Usar</button>
        </div>
    </div>
@endif
                        <div class="relative mb-4">
                            <i class="fas fa-search absolute left-3 top-3.5 text-[var(--text-muted)]"></i>
                            <input type="text" id="inputBuscarCliente" placeholder="Escribe el nombre o teléfono del cliente..." class="w-full pl-10 pr-3 py-3 border border-[var(--border-color)] rounded-xl bg-[var(--input-bg)] text-[var(--text-main)] focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all outline-none">
                        </div>
                        <div id="resultadosClientes" class="border border-[var(--border-color)] rounded-xl max-h-48 overflow-y-auto bg-[var(--bg-base)]">
                            <!-- Aquí se inyectarán los resultados con JS -->
                            <div class="p-4 text-center text-[var(--text-muted)] text-sm font-medium">Empieza a escribir para buscar...</div>
                        </div>
                    </div>

                    <!-- VISTA B: CLIENTE SELECCIONADO Y DIRECCIONES -->
                    <div id="vista-direcciones-cliente" class="hidden">
                        <div class="bg-green-500/10 p-4 rounded-xl flex justify-between items-center mb-6 border border-green-500/20">
                            <div>
                                <p class="text-[10px] font-black text-green-500 uppercase tracking-wider mb-1">Cliente Seleccionado</p>
                                <h4 id="lbl-nombre-cliente" class="text-lg font-bold text-[var(--text-main)]">Nombre Cliente</h4>
                                <p id="lbl-tel-cliente" class="text-sm text-[var(--text-muted)] font-medium">Tel: 00000000</p>
                            </div>
                            <button type="button" onclick="cambiarCliente()" class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white font-bold py-1.5 px-3 rounded-lg text-sm transition-colors">
                                Cambiar Cliente
                            </button>
                        </div>

                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-bold text-[var(--text-main)]">Dirección de Entrega *</label>
                            <button type="button" onclick="abrirModalNuevaDireccion()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-1.5 px-3 rounded-lg text-sm transition-colors">
                                + Nueva Dirección
                            </button>
                        </div>
                        
                        <!-- Lista de Radios para Direcciones -->
                        <div id="lista-direcciones-radios" class="space-y-3">
                            <!-- Se inyecta con JS -->
                        </div>
                    </div>
                </div>

                <div class="bg-[var(--bg-panel)] px-6 py-4 flex space-x-3 border-t border-[var(--border-color)]">
                    <button type="button" onclick="cerrarModal('modalDireccion')" class="flex-1 bg-[var(--input-bg)] border border-[var(--border-color)] text-[var(--text-main)] font-bold py-3 px-4 rounded-xl hover:bg-[var(--hover-bg)] transition-colors">Cancelar / Volver</button>
                    <button type="button" id="btnConfirmarDelivery" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl transition-all shadow opacity-50 cursor-not-allowed" disabled>Confirmar Dirección</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MODAL: REGISTRAR NUEVO CLIENTE -->
    <div id="modalNuevoCliente" class="modal-overlay fixed inset-0 z-[80] hidden flex items-center justify-center" aria-modal="true">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4">
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-[var(--bg-panel)] text-left shadow-2xl w-full max-w-lg border border-[var(--border-color)]">
                <div class="px-6 py-4 border-b border-[var(--border-color)] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-[var(--text-main)]">Registrar Nuevo Cliente</h3>
                    <button type="button" onclick="cerrarModal('modalNuevoCliente')" class="text-[var(--text-muted)] hover:text-[var(--text-main)] transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <form id="formNuevoCliente" onsubmit="guardarNuevoClienteAjax(event)" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Nombre *</label>
                            <input type="text" id="nc_nombre" required class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Apellidos *</label>
                            <input type="text" id="nc_apellido" class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Teléfono *</label>
                        <input type="text" id="nc_telefono" required class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="pt-4 flex space-x-3">
                        <button type="button" onclick="cerrarModal('modalNuevoCliente')" class="flex-1 bg-[var(--input-bg)] border border-[var(--border-color)] text-[var(--text-main)] font-bold py-2 rounded-xl hover:bg-[var(--hover-bg)] transition-colors">Cancelar</button>
                        <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 rounded-xl transition-colors">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. MODAL: REGISTRAR NUEVA DIRECCIÓN -->
    <div id="modalNuevaDireccion" class="modal-overlay fixed inset-0 z-[90] hidden flex items-center justify-center" aria-modal="true">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4">
            <div class="modal-sheet relative transform overflow-hidden rounded-[24px] bg-[var(--bg-panel)] text-left shadow-2xl w-full max-w-lg border border-[var(--border-color)]">
                <div class="px-6 py-4 border-b border-[var(--border-color)] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-[var(--text-main)]">Registrar Nueva Dirección</h3>
                    <button type="button" onclick="cerrarModal('modalNuevaDireccion')" class="text-[var(--text-muted)] hover:text-[var(--text-main)] transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <form id="formNuevaDireccion" onsubmit="guardarNuevaDireccionAjax(event)" class="p-6 space-y-4">
                    <input type="hidden" id="nd_cliente_id"> 
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Calle *</label>
                        <input type="text" id="nd_calle" required class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Manzana</label>
                            <input type="text" id="nd_manzana" class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Lote</label>
                            <input type="text" id="nd_lote" class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Colonia</label>
                        <input type="text" id="nd_colonia" class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-muted)] mb-1">Referencia</label>
                        <textarea id="nd_referencia" rows="2" class="w-full rounded-lg border-[var(--border-color)] bg-[var(--input-bg)] text-[var(--text-main)] outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>
                    <div class="pt-4 flex space-x-3">
                        <button type="button" onclick="cerrarModal('modalNuevaDireccion')" class="flex-1 bg-[var(--input-bg)] border border-[var(--border-color)] text-[var(--text-main)] font-bold py-2 rounded-xl hover:bg-[var(--hover-bg)] transition-colors">Cancelar</button>
                        <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 rounded-xl transition-colors">Guardar Dirección</button>
                    </div>
                </form>


          <!-- Modal Cliente Para Llevar -->
<div id="modalParaLlevar" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[60] p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-[var(--card-color)] rounded-xl shadow-2xl max-w-md w-full border border-[var(--border-color)] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--bg-color)]/50">
            <h2 class="text-xl font-bold text-[var(--text-color)]"><i class="fas fa-shopping-bag mr-2"></i>Cliente Para Llevar</h2>
            <button type="button" onclick="cerrarModal('modalParaLlevar')" class="text-[var(--text-muted)] hover:text-red-500 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-6">
            <label class="block text-sm font-semibold text-[var(--text-color)] mb-2">Nombre del cliente (Temporal o Buscar)</label>
            <input type="text" id="inputClienteLlevar" autocomplete="off" class="w-full px-4 py-3 bg-[var(--input-bg)] border border-[var(--border-color)] rounded-lg focus:ring-2 focus:ring-blue-500 text-[var(--text-color)] shadow-inner transition-colors" placeholder="Ej. Juan Pérez">
            
            <!-- Contenedor opcional si decides reutilizar la búsqueda aquí -->
            <div id="resultadosLlevar" class="mt-2 max-h-40 overflow-y-auto"></div>
        </div>
        <div class="px-6 py-4 bg-[var(--bg-color)]/50 border-t border-[var(--border-color)] flex justify-end gap-3">
            <button type="button" onclick="cerrarModal('modalParaLlevar')" class="px-4 py-2 rounded-lg font-semibold border border-[var(--border-color)] text-[var(--text-color)] hover:opacity-80 transition">Cancelar</button>
            <button type="button" onclick="confirmarClienteLlevar()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-sm transition">Confirmar Nombre</button>
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
            lblBoton.className = 'text-[11px] font-black text-amber-500 mt-1 truncate max-w-full px-1';
        }
    }
});

// 2. Buscador en tiempo real optimizado
document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'inputBuscarCliente') {
        clearTimeout(timeoutBuscador);
        const query = e.target.value.trim();
        const contenedor = document.getElementById('resultadosClientes');

        if (!contenedor) return;

        if (query.length < 2) {
            contenedor.innerHTML = '<div class="p-4 text-center text-[var(--text-muted)] text-sm font-medium">Empieza a escribir para buscar...</div>';
            return;
        }

        timeoutBuscador = setTimeout(() => {
            contenedor.innerHTML = '<div class="p-4 text-center text-amber-500 text-sm font-bold"><i class="fas fa-spinner fa-spin mr-2"></i>Buscando cliente...</div>';
            
            fetch(`/pos/clientes/buscar?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json())
            .then(clientes => {
                if (!clientes || clientes.length === 0) {
                    contenedor.innerHTML = '<div class="p-4 text-center text-[var(--text-muted)] text-sm font-medium">No se encontraron clientes con esos datos.</div>';
                    return;
                }

                let html = '';
                clientes.forEach(c => {
                    const jsonDirecciones = encodeURIComponent(JSON.stringify(c.direcciones || []));
                    const nombreCompleto = `${c.nombre} ${c.apellido || ''}`.trim();
                    
                    html += `
                    <div class="flex justify-between items-center p-3 border-b border-[var(--border-color)] hover:bg-[var(--hover-bg)] cursor-pointer transition-colors" 
                         onclick="window.seleccionarClienteDelivery(${c.id}, '${nombreCompleto.replace(/'/g, "\\'")}', '${c.telefono}', '${jsonDirecciones}')">
                        <div>
                            <p class="font-bold text-[var(--text-main)] text-sm">${nombreCompleto}</p>
                            <p class="text-xs text-[var(--text-muted)]"><i class="fas fa-phone mr-1"></i>${c.telefono}</p>
                        </div>
                        <button type="button" class="bg-[var(--bg-base)] border border-[var(--border-color)] text-[var(--text-main)] px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm"><i class="fas fa-check text-blue-500"></i></button>
                    </div>`;
                });
                contenedor.innerHTML = html;
            })
            .catch(error => {
                console.error('Error buscando cliente:', error);
                contenedor.innerHTML = '<div class="p-4 text-center text-red-500 text-sm font-bold">Error de conexión al buscar.</div>';
            });
        }, 300);
    }
});

// 3. Función global de selección de cliente para avanzar a direcciones
window.seleccionarClienteDelivery = function(id, nombre, telefono, direccionesEncoded) {
    window.clienteSeleccionadoId = id;
    
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
        console.error("Error al decodificar direcciones:", err);
        renderDireccionesRadios([]);
    }

    // Cambiamos de vista dentro del modal (ocultar buscador, mostrar direcciones)
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

// 4. Renderizar las opciones de direcciones
function renderDireccionesRadios(direcciones) {
    const contenedor = document.getElementById('lista-direcciones-radios');
    if (!contenedor) return;
    
    if (!direcciones || direcciones.length === 0) {
        contenedor.innerHTML = `<div class="p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl text-orange-500 text-sm font-medium text-center">
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
        <label class="flex items-start p-3 border border-[var(--border-color)] rounded-xl cursor-pointer hover:bg-[var(--hover-bg)] transition-colors">
            <div class="flex items-center h-5 mt-1">
                <input type="radio" name="radio_direccion" value="${d.id}" onchange="window.activarBotonConfirmar()" class="w-4 h-4 text-amber-500 bg-[var(--input-bg)] border-[var(--border-color)] focus:ring-amber-500 focus:ring-2 cursor-pointer">
            </div>
            <div class="ml-3 text-sm flex-1">
                <p class="font-bold text-[var(--text-main)]">${detalle}</p>
                <p class="text-xs text-[var(--text-muted)]">${d.colonia || ''}</p>
                ${d.referencia ? `<p class="text-xs text-[var(--text-muted)] mt-1 italic border-l-2 border-amber-500 pl-2">Ref: ${d.referencia}</p>` : ''}
            </div>
        </label>`;
    });
    contenedor.innerHTML = html;
}

// 5. Activar botón confirmar dirección
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

// 6. Al confirmar la dirección, actualizar la tarjeta lateral con el nombre del cliente
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
        resultados.innerHTML = '<div class="p-4 text-center text-[var(--text-muted)] text-sm font-medium">Empieza a escribir para buscar...</div>';
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

// 1. Detectar al cargar la página si es "Para Llevar"
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

// 2. Función para abrir el modal correcto al hacer clic en la tarjeta de tipo de pedido
window.abrirModalSegunTipo = function() {
    if (window.tipoPedidoActual === 'domicilio') {
        abrirModalDelivery(); // Tu función actual para abrir el de domicilio
    } else if (window.tipoPedidoActual === 'llevar') {
        abrirModal('modalParaLlevar');
        setTimeout(() => document.getElementById('inputClienteLlevar')?.focus(), 100);
    }
};

// 3. Confirmar el nombre temporal
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

// --- 1. BUSCADOR INTELIGENTE (Detecta si es Domicilio o Llevar) ---
document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'inputBuscarCliente') {
        clearTimeout(timeoutBuscador);
        const query = e.target.value.trim();
        const contenedor = document.getElementById('resultadosClientes');
        if (!contenedor) return;

        // Detectamos si estamos en modo "Llevar"
        const esLlevar = (window.tipoPedidoActual === 'llevar') || (new URLSearchParams(window.location.search).get('tipo_pedido') === 'llevar');

        if (query.length < 2) {
            contenedor.innerHTML = `<div class="p-4 text-center text-[var(--text-muted)] text-sm font-medium">Empieza a escribir para buscar ${esLlevar ? 'o usar un nombre temporal' : ''}...</div>`;
            return;
        }

        timeoutBuscador = setTimeout(() => {
            contenedor.innerHTML = '<div class="p-4 text-center text-amber-500 text-sm font-bold"><i class="fas fa-spinner fa-spin mr-2"></i>Buscando...</div>';
            
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
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors">Seleccionar</button>
                    </div>`;
                }

                if (!clientes || clientes.length === 0) {
                    if (!esLlevar) {
                        contenedor.innerHTML = '<div class="p-4 text-center text-[var(--text-muted)] text-sm font-medium">No se encontraron clientes. Haz clic en "Nuevo Cliente".</div>';
                    } else {
                        contenedor.innerHTML = html; // Si es llevar, muestra solo el botón temporal
                    }
                    return;
                }

                // Renderiza los clientes encontrados en la BD
                clientes.forEach(c => {
                    const jsonDirecciones = encodeURIComponent(JSON.stringify(c.direcciones || []));
                    const nombreCompleto = `${c.nombre} ${c.apellido || ''}`.trim();
                    
                    html += `
                    <div class="flex justify-between items-center p-3 border-b border-[var(--border-color)] hover:bg-[var(--hover-bg)] cursor-pointer transition-colors" 
                         onclick="window.seleccionarClienteDelivery(${c.id}, '${nombreCompleto.replace(/'/g, "\\'")}', '${c.telefono}', '${jsonDirecciones}')">
                        <div>
                            <p class="font-bold text-[var(--text-main)] text-sm">${nombreCompleto}</p>
                            <p class="text-xs text-[var(--text-muted)]"><i class="fas fa-phone mr-1"></i>${c.telefono}</p>
                        </div>
                        <button type="button" class="bg-[var(--bg-base)] border border-[var(--border-color)] text-[var(--text-main)] px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm"><i class="fas fa-check text-amber-500"></i></button>
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

// --- 2. FUNCIÓN PARA GUARDAR EL CLIENTE TEMPORAL ---
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

// --- 3. SELECCIÓN INTELIGENTE (Salta la dirección si es "Para Llevar") ---
window.seleccionarClienteDelivery = function(id, nombre, telefono, direccionesEncoded) {
    window.clienteSeleccionadoId = id;
    window.nombreClienteTemporal = nombre; 

    const esLlevar = (window.tipoPedidoActual === 'llevar') || (new URLSearchParams(window.location.search).get('tipo_pedido') === 'llevar');

    // SI ES PARA LLEVAR: Asignamos el nombre, saltamos las direcciones y cerramos
    if (esLlevar) {
        const lblBoton = document.getElementById('lbl-tipo-pedido-actual');
        if (lblBoton) {
            lblBoton.textContent = nombre;
            lblBoton.className = 'text-[11px] font-black text-blue-500 mt-1 truncate max-w-full px-1';
        }
        if (typeof window.cerrarModal === 'function') window.cerrarModal('modalDireccion'); 
        if (typeof mostrarExito === 'function') mostrarExito("Cliente asignado para llevar.");
        return; // Cortamos la ejecución aquí
    }

    // SI ES DOMICILIO: Sigue su flujo normal hacia las direcciones
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

            // 3. Opcional: Seleccionamos automáticamente al cliente recién creado en la vista de delivery
            const nombreCompleto = `${data.cliente.nombre} ${data.cliente.apellido || ''}`.trim();
            window.seleccionarClienteDelivery(data.cliente.id, nombreCompleto, data.cliente.telefono, encodeURIComponent(JSON.stringify([])));

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

            // Recargamos las direcciones del cliente para que aparezcan de inmediato en el radio
            window.seleccionarClienteDelivery(
                clienteId, 
                document.getElementById('lbl-nombre-cliente').textContent, 
                document.getElementById('lbl-tel-cliente').textContent.replace('Tel: ', ''), 
                encodeURIComponent(JSON.stringify([data.direccion]))
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
        lblBoton.className = 'text-[11px] font-black text-amber-500 mt-1 truncate max-w-full px-1';
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