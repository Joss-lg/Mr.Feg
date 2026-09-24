{{-- resources/views/admin/inventario/modal-categorias.blade.php --}}
{{-- Categorías propias del inventario (independientes de las del punto de venta) --}}

<div id="modalCategoriasInv" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm p-3 sm:p-4 transition-all duration-300">

    <div id="categoriasInvContainer" class="bg-white border border-slate-200/80 w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[92dvh]">

        {{-- ENCABEZADO --}}
        <div class="p-6 sm:p-8 pb-4 shrink-0 flex justify-between items-center border-b border-slate-100">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shrink-0">
                    <i class="fas fa-folder-open text-lg sm:text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight uppercase m-0 leading-tight truncate">Categorías</h3>
                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] mt-1">Solo para el inventario</p>
                </div>
            </div>

            <button type="button" onclick="cerrarModalCategoriasInv()" class="group w-10 h-10 rounded-xl flex items-center justify-center bg-[#F2F2F2] text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all outline-none shrink-0 border border-slate-200/80">
                <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
            </button>
        </div>

        <div class="p-6 sm:p-8 pt-4 sm:pt-6 space-y-5 overflow-y-auto flex-1 overscroll-contain">

            {{-- NUEVA CATEGORÍA --}}
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                    <i class="fas fa-plus opacity-40"></i> Nueva categoría
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" id="nuevaCategoriaInv" maxlength="100" data-teclado="texto" autocomplete="off"
                        onkeydown="if (event.key === 'Enter') { event.preventDefault(); crearCategoriaInv(); }"
                        class="flex-1 min-w-0 h-12 bg-[#F2F2F2] border border-slate-200/80 rounded-2xl px-5 text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all placeholder:text-slate-400 shadow-sm"
                        placeholder="Ej: Desechables">
                    <button type="button" id="btnCrearCategoriaInv" onclick="crearCategoriaInv()"
                        class="h-12 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-600/20 transition-all active:scale-95 outline-none shrink-0">
                        Agregar
                    </button>
                </div>
            </div>

            <p id="msgCatInv" class="hidden text-xs font-bold ml-1"></p>

            {{-- LISTA --}}
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                    <i class="fas fa-list opacity-40"></i> Categorías existentes
                </label>

                @forelse($categorias as $cat)
                    <div class="fila-cat-inv flex items-center gap-2">
                        <input type="text" value="{{ $cat->nombre }}" data-original="{{ $cat->nombre }}" maxlength="100" data-teclado="texto" autocomplete="off"
                            class="flex-1 min-w-0 h-11 bg-[#F2F2F2] border border-slate-200/80 rounded-2xl px-4 text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                        <span class="hidden sm:block text-[9px] font-black uppercase tracking-widest text-slate-400 w-16 text-center shrink-0">
                            {{ (int) $cat->insumos_count }} {{ (int) $cat->insumos_count === 1 ? 'artículo' : 'artículos' }}
                        </span>
                        <button type="button" title="Guardar nombre" onclick="guardarCategoriaInv({{ $cat->id }}, this)"
                            class="w-11 h-11 shrink-0 rounded-2xl flex items-center justify-center bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all outline-none">
                            <i class="fas fa-check text-sm"></i>
                        </button>
                        <button type="button" title="Eliminar categoría" onclick="eliminarCategoriaInv({{ $cat->id }}, this)"
                            class="min-w-[2.75rem] h-11 px-3 shrink-0 rounded-2xl flex items-center justify-center bg-rose-50 text-rose-500 border border-rose-100 hover:bg-rose-600 hover:text-white text-[10px] font-black uppercase tracking-widest transition-all outline-none">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                @empty
                    <p class="text-xs font-semibold text-slate-400 ml-1">Aún no hay categorías. Crea la primera arriba.</p>
                @endforelse
            </div>
        </div>

        <div class="flex items-center px-6 sm:px-8 py-6 border-t border-slate-100 shrink-0">
            <button type="button" onclick="cerrarModalCategoriasInv()"
                class="w-full h-12 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-800 hover:bg-[#F2F2F2] transition-all outline-none">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // URLs armadas con route() para no depender de prefijos escritos a mano.
    const urlCrearCatInv      = "{{ route('admin.inventario.categorias.store') }}";
    const urlActualizarCatInv = "{{ route('admin.inventario.categorias.update', ['id' => '__ID__']) }}";
    const urlEliminarCatInv   = "{{ route('admin.inventario.categorias.destroy', ['id' => '__ID__']) }}";
    const csrfCatInv          = "{{ csrf_token() }}";

    function abrirModalCategoriasInv() {
        const modal = document.getElementById('modalCategoriasInv');
        const container = document.getElementById('categoriasInvContainer');
        if (!modal || !container) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function cerrarModalCategoriasInv() {
        const modal = document.getElementById('modalCategoriasInv');
        const container = document.getElementById('categoriasInvContainer');
        if (!modal || !container) return;

        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    function mostrarMsgCatInv(texto, esError) {
        const msg = document.getElementById('msgCatInv');
        if (!msg) return;
        msg.textContent = texto;
        msg.classList.remove('hidden', 'text-rose-500', 'text-slate-500');
        msg.classList.add(esError ? 'text-rose-500' : 'text-slate-500');
    }

    async function peticionCatInv(url, metodo, cuerpo) {
        const res = await fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfCatInv,
            },
            body: cuerpo ? JSON.stringify(cuerpo) : undefined,
        });

        let data = {};
        try { data = await res.json(); } catch (e) { /* respuesta sin JSON */ }

        if (!res.ok) {
            const primerError = data && data.errors ? Object.values(data.errors)[0][0] : null;
            throw new Error(primerError || (data && data.message) || 'No se pudo completar la acción.');
        }
        return data;
    }

    // Recarga la página (para refrescar los desplegables) y vuelve a abrir este modal.
    function recargarConModalCatInv() {
        sessionStorage.setItem('reabrirCatInv', '1');
        location.reload();
    }

    async function crearCategoriaInv() {
        const input = document.getElementById('nuevaCategoriaInv');
        const btn = document.getElementById('btnCrearCategoriaInv');
        const nombre = input.value.trim();

        if (!nombre) { mostrarMsgCatInv('Escribe el nombre de la categoría.', true); return; }

        btn.disabled = true;
        try {
            await peticionCatInv(urlCrearCatInv, 'POST', { nombre });
            recargarConModalCatInv();
        } catch (e) {
            mostrarMsgCatInv(e.message, true);
            btn.disabled = false;
        }
    }

    async function guardarCategoriaInv(id, btn) {
        const input = btn.closest('.fila-cat-inv').querySelector('input');
        const nombre = input.value.trim();

        if (nombre === input.dataset.original) { mostrarMsgCatInv('No hay cambios que guardar en esa categoría.', false); return; }
        if (!nombre) { mostrarMsgCatInv('El nombre no puede quedar vacío.', true); return; }

        btn.disabled = true;
        try {
            await peticionCatInv(urlActualizarCatInv.replace('__ID__', id), 'PUT', { nombre });
            recargarConModalCatInv();
        } catch (e) {
            mostrarMsgCatInv(e.message, true);
            btn.disabled = false;
        }
    }

    // Eliminar pide confirmación con un segundo toque (3 segundos).
    function eliminarCategoriaInv(id, btn) {
        if (btn.dataset.confirmar !== '1') {
            btn.dataset.confirmar = '1';
            btn.dataset.html = btn.innerHTML;
            btn.innerHTML = '¿Seguro?';
            btn.classList.add('bg-rose-600', 'text-white');
            setTimeout(() => {
                if (btn.dataset.confirmar === '1') {
                    btn.dataset.confirmar = '';
                    btn.innerHTML = btn.dataset.html;
                    btn.classList.remove('bg-rose-600', 'text-white');
                }
            }, 3000);
            return;
        }

        btn.disabled = true;
        peticionCatInv(urlEliminarCatInv.replace('__ID__', id), 'DELETE')
            .then(recargarConModalCatInv)
            .catch(e => {
                mostrarMsgCatInv(e.message, true);
                btn.disabled = false;
                btn.dataset.confirmar = '';
                btn.innerHTML = btn.dataset.html;
                btn.classList.remove('bg-rose-600', 'text-white');
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (sessionStorage.getItem('reabrirCatInv') === '1') {
            sessionStorage.removeItem('reabrirCatInv');
            abrirModalCategoriasInv();
        }
    });
</script>
