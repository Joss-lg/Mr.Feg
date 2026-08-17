{{--
    resources/views/admin/cobrar/modals/cancelar-cuenta.blade.php

    Confirmación para cerrar una cuenta SIN cobrarla (el cliente se fue sin
    pagar, la comanda se levantó por error, cortesía...).

    Pide un motivo obligatorio a propósito: una cuenta cancelada es dinero
    que no entró, y sin explicación no hay forma de auditarla después.

    Solo se incluye si el usuario tiene permiso de eliminar en Caja; la ruta
    lo vuelve a validar en el servidor.
--}}
<div id="modal-cancelar-cuenta" class="hidden fixed inset-0 z-[9998] items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-cerrar-cancelar></div>

    <div class="relative w-full max-w-md bg-white rounded-[2rem] border border-slate-200 shadow-2xl overflow-hidden">

        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-triangle-exclamation text-rose-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">
                        Cancelar cuenta sin cobrar
                    </h3>
                    <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">
                        La mesa quedará libre y esta cuenta se registrará como pérdida.
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <button type="button" data-cerrar-cancelar
                    class="group w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all outline-none shrink-0">
                    <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
                </button>
            </div>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div class="rounded-2xl bg-rose-50 border border-rose-100 px-4 py-3 flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-rose-600">
                    Se dejará de cobrar
                </span>
                <span class="text-xl font-black text-rose-600">
                    ${{ number_format($totalPagar ?? 0, 2) }}
                </span>
            </div>

            <div>
                <label for="motivo-cancelacion" class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1.5">
                    Motivo <span class="text-rose-500">*</span>
                </label>
                <textarea id="motivo-cancelacion" rows="3" maxlength="255"
                    placeholder="Ej: El cliente se retiró sin pagar."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all resize-none placeholder:text-slate-400"></textarea>
                <p id="error-motivo-cancelacion" class="hidden mt-1.5 text-[11px] font-bold text-rose-600"></p>
            </div>
        </div>

        <div class="px-6 pb-6 flex gap-3">
            <button type="button" data-cerrar-cancelar
                class="flex-1 h-12 rounded-xl border border-slate-200 text-slate-500 text-xs font-black uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-all">
                Volver
            </button>
            <button type="button" id="btn-confirmar-cancelar-cuenta"
                class="flex-1 h-12 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-rose-500/20 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100">
                Sí, cancelar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal    = document.getElementById('modal-cancelar-cuenta');
    const abrir    = document.getElementById('btn-abrir-cancelar-cuenta');
    const confirmar= document.getElementById('btn-confirmar-cancelar-cuenta');
    const motivo   = document.getElementById('motivo-cancelacion');
    const error    = document.getElementById('error-motivo-cancelacion');

    if (!modal || !abrir) return;

    const mostrar = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        error.classList.add('hidden');
        motivo.value = '';
        setTimeout(() => motivo.focus(), 50);
    };

    const ocultar = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    abrir.addEventListener('click', mostrar);
    modal.querySelectorAll('[data-cerrar-cancelar]').forEach(el => el.addEventListener('click', ocultar));

    confirmar.addEventListener('click', async () => {
        const texto = (motivo.value || '').trim();

        // Mismo mínimo que valida el servidor, para avisar antes de enviar.
        if (texto.length < 5) {
            error.textContent = 'Explica brevemente qué pasó (mínimo 5 caracteres).';
            error.classList.remove('hidden');
            motivo.focus();
            return;
        }

        confirmar.disabled = true;
        confirmar.textContent = 'Cancelando...';

        try {
            const res = await fetch(@json(route('admin.caja.cuenta.cancelar')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ mesa_id: @json($mesa->id), motivo: texto }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                window.location.href = data.redirect_url;
                return;
            }

            error.textContent = data.message || 'No se pudo cancelar la cuenta.';
            error.classList.remove('hidden');
        } catch (e) {
            console.error('Error al cancelar la cuenta:', e);
            error.textContent = 'Error de conexión. Intenta de nuevo.';
            error.classList.remove('hidden');
        } finally {
            confirmar.disabled = false;
            confirmar.textContent = 'Sí, cancelar';
        }
    });
});
</script>