{{-- MODAL CONFIRMACIÓN ELIMINAR PLATILLO --}}
<div id="modal-eliminar-alimento" class="fixed inset-y-0 right-0 left-[74px] sm:left-0 sm:inset-0 z-[100] hidden opacity-0 transition-all duration-300 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/20 -ml-[74px] sm:ml-0" onclick="cerrarModalEliminar()"></div>
    
    <div class="relative bg-white border border-slate-200 w-full max-w-md rounded-[2rem] shadow-2xl transform opacity-0 scale-95 transition-all duration-300" id="modal-eliminar-panel">
        <div class="p-8 sm:p-10 text-center">
            {{-- Ícono de advertencia --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center">
                    <i class="fas fa-trash-alt text-3xl text-rose-500"></i>
                </div>
            </div>

            {{-- Título y descripción --}}
            <h3 class="text-2xl font-black text-slate-800 mb-2 tracking-tight">¿Eliminar Platillo?</h3>
            <p class="text-sm text-slate-500 mb-8">
                Estás a punto de eliminar permanentemente el platillo:
                <span class="font-black text-slate-900 block mt-2 break-words" id="nombre-platillo-eliminar">Platillo</span>
            </p>

            {{-- Botones de acción --}}
            <div class="flex flex-col-reverse sm:flex-row gap-3">
                <button type="button" onclick="cerrarModalEliminar()" class="flex-1 px-6 py-4 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-500 font-black rounded-2xl transition text-xs uppercase tracking-widest outline-none">
                    CANCELAR
                </button>
                <button type="button" onclick="confirmarEliminacion()" class="flex-1 px-6 py-4 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-black rounded-2xl transition shadow-lg shadow-rose-500/20 text-xs uppercase tracking-widest outline-none">
                    <i class="fas fa-trash-alt mr-2"></i> SÍ, ELIMINAR
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variable global para almacenar el ID del producto a eliminar
    let idProductoAEliminar = null;

    // ─── Bloqueo/desbloqueo de scroll del fondo ─────────────────────────
    window.bloquearScrollFondo = window.bloquearScrollFondo || function () {
        document.body.style.overflow = 'hidden';
    };
    window.desbloquearScrollFondo = window.desbloquearScrollFondo || function () {
        document.body.style.overflow = '';
    };
 
    // Abrir modal de eliminación
    function abrirModalEliminar(id, nombreProducto) {
        idProductoAEliminar = id;
        document.getElementById('nombre-platillo-eliminar').textContent = nombreProducto;
 
        const modal = document.getElementById('modal-eliminar-alimento');
        const panel = document.getElementById('modal-eliminar-panel');

        // Mover modal al final del body para evitar problemas de posicionamiento
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
 
        modal.classList.remove('hidden');
        modal.classList.add('opacity-100');
        bloquearScrollFondo();
 
        setTimeout(() => {
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }
 
    // Cerrar modal de eliminación
    function cerrarModalEliminar() {
        const modal = document.getElementById('modal-eliminar-alimento');
        const panel = document.getElementById('modal-eliminar-panel');
 
        panel.classList.remove('opacity-100', 'scale-100');
        desbloquearScrollFondo();
 
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('opacity-100');
            idProductoAEliminar = null;
        }, 300);
    }
 
    // Confirmar y ejecutar eliminación
    function confirmarEliminacion() {
        if (!idProductoAEliminar) return;
 
        fetch(RUTA_API_BASE + idProductoAEliminar, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la solicitud');
            return response.json();
        })
        .then(resultado => {
            cerrarModalEliminar();
            if(typeof cargarProductos === 'function') cargarProductos();
            if(typeof cargarEstadisticas === 'function') cargarEstadisticas();
            if(typeof mostrarNotificacion === 'function') mostrarNotificacion(resultado.message, 'success');
        })
        .catch(error => {
            if(typeof mostrarNotificacion === 'function') mostrarNotificacion('Error al eliminar el platillo', 'error');
            console.error(error);
        });
    }
</script>