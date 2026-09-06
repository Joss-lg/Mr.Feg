(function () {
    const config = window.ComandaConfig || {};

    window.ajustarPersonas = function () {
        const input = document.getElementById('personasInput');
        const modal = document.getElementById('modalPersonas');
        if (input) input.value = numeroPersonas;
        if (modal) modal.classList.remove('hidden');
    };

    window.guardarPersonas = function () {
        const input = document.getElementById('personasInput');
        if (!input) return;

        const v = parseInt(input.value, 10);
        if (isNaN(v) || v <= 0) { mostrarError('Inválido'); return; }

        numeroPersonas = v;
        const txtPers = document.getElementById('txtPersonas');
        if (txtPers) txtPers.innerText = v;
        cerrarModal('modalPersonas');

        fetch(config.rutas.comandaPersonas, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': config.csrfToken
            },
            body: JSON.stringify({ personas: v })
        })
        .then(res => res.json())
        .then(data => { if (!data.success) mostrarError('No se pudo guardar el número de personas.'); })
        .catch(() => mostrarError('Error de red al guardar personas.'));
    };

    window.agregarNota = function () {
        if (!itemActivo) { mostrarError('Selecciona platillo'); return; }
        const textarea = document.getElementById('notaTextarea');
        const modal = document.getElementById('modalNota');
        const tituloProducto = document.getElementById('notaModalProducto');

        const nombreProducto = itemActivo.querySelector('.nombre-platillo')?.innerText || 'Producto';
        if (tituloProducto) tituloProducto.innerText = nombreProducto;

        if (textarea) textarea.value = '';
        if (modal) modal.classList.remove('hidden');
    };

    window.aplicarDescuento = function () {
        const input = document.getElementById('descuentoInput');
        const modal = document.getElementById('modalDescuento');
        if (input) input.value = descuentoPorcentaje;
        if (modal) modal.classList.remove('hidden');
    };

    window.guardarDescuento = function () {
        const input = document.getElementById('descuentoInput');
        if (!input) return;
        const v = parseFloat(input.value);
        if (isNaN(v) || v < 0 || v > 100) { mostrarError('Inválido'); return; }
        descuentoPorcentaje = v;
        if (typeof window.actualizarTotales === 'function') window.actualizarTotales();
        cerrarModal('modalDescuento');
    };

    window.insertarNotaCaracter = function (c) { const t = document.getElementById('notaTextarea'); if (t) { t.value += c; t.focus(); } };
    window.insertarNotaEspacio = function () { const t = document.getElementById('notaTextarea'); if (t) { t.value += ' '; t.focus(); } };
    window.borrarNotaCaracter = function () { const t = document.getElementById('notaTextarea'); if (t) { t.value = t.value.slice(0, -1); t.focus(); } };
    window.limpiarNota = function () { const t = document.getElementById('notaTextarea'); if (t) { t.value = ''; t.focus(); } };

    // ---------------------------------------------------------------
    // GESTIÓN DE PROPINA
    // ---------------------------------------------------------------
    window.abrirModalPropina = function () {
        const input = document.getElementById('propinaInput');
        const modal = document.getElementById('modalPropina');
        if (input) input.value = window.propinaGlobal > 0 ? window.propinaGlobal.toFixed(2) : '';
        if (modal) modal.classList.remove('hidden');
        if (input) setTimeout(() => input.focus(), 150);
    };

    window.calcularPropinaPorcentaje = function (porcentaje) {
        let total = parseFloat(window.totalComandaSinPropina) || 0;
        const input = document.getElementById('propinaInput');
        if (total > 0) {
            if (input) input.value = (total * (porcentaje / 100)).toFixed(2);
        } else {
            alert("Primero debes agregar productos para calcular un porcentaje.");
        }
    };

    window.guardarPropina = function () {
        const input = document.getElementById('propinaInput');
        const montoPropina = input ? (parseFloat(input.value) || 0) : 0;
        if (montoPropina < 0) { alert("La propina no puede ser un valor negativo."); return; }
        window.propinaGlobal = montoPropina;
        if (typeof window.actualizarTotales === 'function') window.actualizarTotales();
        cerrarModal('modalPropina');
    };

    window.imprimirPrecuenta = function () {
        const url = config.rutas && config.rutas.comandaPrecuenta;
        if (!url) { mostrarError('No se encontró la ruta de la pre-cuenta.'); return; }

        const previo = document.getElementById('modal-precuenta-preview');
        if (previo) previo.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modal-precuenta-preview';
        overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.8);backdrop-filter:blur(4px);padding:16px;';
        overlay.innerHTML = `
            <div style="background:#fff;border-radius:24px;width:100%;max-width:480px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,0.4);">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                    <span style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:2px;color:#111;">
                        <i class="fas fa-receipt" style="color:#3b82f6;margin-right:6px;"></i> Pre-Cuenta
                    </span>
                    <button id="btn-cerrar-precuenta" style="width:32px;height:32px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;font-size:14px;color:#6b7280;">✕</button>
                </div>
                <div style="flex:1;overflow-y:auto;padding:12px;background:#f3f4f6;">
                    <iframe id="iframe-precuenta" src="${url}" style="width:100%;min-height:60vh;border:none;border-radius:12px;background:#fff;"></iframe>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:16px;border-top:1px solid #e5e7eb;">
                    <button id="btn-cerrar-precuenta2" style="padding:14px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:16px;font-weight:900;font-size:11px;text-transform:uppercase;letter-spacing:2px;cursor:pointer;color:#111;">Cerrar</button>
                    <button id="btn-imprimir-precuenta" style="padding:14px;background:linear-gradient(to right,#2563eb,#3b82f6);color:#fff;border:none;border-radius:16px;font-weight:900;font-size:11px;text-transform:uppercase;letter-spacing:2px;cursor:pointer;">
                        <i class="fas fa-print" style="margin-right:6px;"></i> Imprimir
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);
        const cerrar = () => overlay.remove();
        document.getElementById('btn-cerrar-precuenta').onclick  = cerrar;
        document.getElementById('btn-cerrar-precuenta2').onclick = cerrar;
        document.getElementById('btn-imprimir-precuenta').onclick = () => {
            try { document.getElementById('iframe-precuenta').contentWindow.print(); } catch(e) { window.open(url, '_blank'); }
        };
    };

    // ===============================================================
    // MODAL DE MÉTODO DE PAGO DOMICILIO — completamente autocontenido
    // Captura el método (efectivo/tarjeta/transferencia) + datos extra,
    // guarda en la orden y envía a Caja como pago pendiente.
    // NO cobra ni libera la mesa — eso lo hace Caja.
    // ===============================================================
    (function crearModalPagoDomicilio() {
        if (document.getElementById('_envio_modal_pago')) return;

        const el = document.createElement('div');
        el.id = '_envio_modal_pago';
        el.style.cssText = 'display:none;position:fixed;inset:0;z-index:10500;align-items:center;justify-content:center;background:rgba(0,0,0,0.75);backdrop-filter:blur(6px);padding:16px;';
        el.innerHTML = `
            <div style="background:#fff;border-radius:28px;width:100%;max-width:400px;max-height:92vh;overflow-y:auto;box-shadow:0 30px 80px rgba(0,0,0,0.4);display:flex;flex-direction:column;">

                <!-- Cabecera -->
                <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:12px;background:linear-gradient(135deg,#d97706,#f59e0b);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-motorcycle" style="color:#fff;font-size:14px;"></i>
                        </div>
                        <div>
                            <p style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:2px;color:#6b7280;margin:0;">Pedido a Domicilio</p>
                            <p style="font-size:16px;font-weight:900;color:#111827;margin:0;">¿Cómo pagará el cliente?</p>
                        </div>
                    </div>
                    <button id="_ep_cerrar" style="width:32px;height:32px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;font-size:14px;color:#6b7280;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>

                <!-- Total -->
                <div style="margin:16px 24px 0;background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:14px;text-align:center;">
                    <p style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:#94a3b8;margin:0 0 2px;">Total del pedido</p>
                    <p id="_ep_total" style="font-size:28px;font-weight:900;color:#111827;margin:0;">$0.00</p>
                </div>

                <!-- PASO 1: Selección de método -->
                <div id="_ep_paso1" style="padding:20px 24px;">
                    <p style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:2px;color:#6b7280;margin:0 0 12px;">Selecciona cómo pagará</p>
                    <div style="display:flex;flex-direction:column;gap:10px;">

                        <button data-metodo="efectivo" class="_ep_btn_metodo"
                            style="padding:16px 20px;border-radius:18px;border:2px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;gap:14px;text-align:left;transition:all .15s;">
                            <div style="width:40px;height:40px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-money-bill-wave" style="font-size:18px;color:#16a34a;"></i>
                            </div>
                            <div>
                                <p style="font-size:14px;font-weight:900;color:#111827;margin:0;">Efectivo</p>
                                <p style="font-size:11px;color:#6b7280;margin:0;">Pago en mano al entregar</p>
                            </div>
                        </button>

                        <button data-metodo="tarjeta" class="_ep_btn_metodo"
                            style="padding:16px 20px;border-radius:18px;border:2px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;gap:14px;text-align:left;transition:all .15s;">
                            <div style="width:40px;height:40px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-credit-card" style="font-size:18px;color:#2563eb;"></i>
                            </div>
                            <div>
                                <p style="font-size:14px;font-weight:900;color:#111827;margin:0;">Tarjeta</p>
                                <p style="font-size:11px;color:#6b7280;margin:0;">Cargo a crédito o débito</p>
                            </div>
                        </button>

                        <button data-metodo="transferencia" class="_ep_btn_metodo"
                            style="padding:16px 20px;border-radius:18px;border:2px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;gap:14px;text-align:left;transition:all .15s;">
                            <div style="width:40px;height:40px;border-radius:12px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-exchange-alt" style="font-size:18px;color:#7c3aed;"></i>
                            </div>
                            <div>
                                <p style="font-size:14px;font-weight:900;color:#111827;margin:0;">Transferencia</p>
                                <p style="font-size:11px;color:#6b7280;margin:0;">SPEI / transferencia bancaria</p>
                            </div>
                        </button>

                    </div>
                </div>

                <!-- PASO 2A: Efectivo — cuánto paga y cambio -->
                <div id="_ep_paso_efectivo" style="display:none;padding:0 24px 20px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                        <button id="_ep_volver_efectivo" style="width:28px;height:28px;border-radius:50%;border:1.5px solid #e2e8f0;background:#f8fafc;cursor:pointer;color:#64748b;font-size:13px;">←</button>
                        <p style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:2px;color:#6b7280;margin:0;">Efectivo</p>
                    </div>

                    <label style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#6b7280;display:block;margin-bottom:6px;">¿Con cuánto paga el cliente?</label>
                    <input id="_ep_ef_monto" type="number" min="0" step="0.01" placeholder="0.00"
                        style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:16px;font-size:22px;font-weight:900;color:#111827;outline:none;box-sizing:border-box;text-align:center;"
                        inputmode="decimal">

                    <div id="_ep_ef_cambio_box" style="display:none;margin-top:12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:14px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:11px;font-weight:700;color:#15803d;">Cambio a devolver</span>
                        <span id="_ep_ef_cambio" style="font-size:20px;font-weight:900;color:#16a34a;">$0.00</span>
                    </div>

                    <div id="_ep_ef_falta_box" style="display:none;margin-top:12px;background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;padding:14px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:11px;font-weight:700;color:#c2410c;">Falta para completar</span>
                        <span id="_ep_ef_falta" style="font-size:20px;font-weight:900;color:#ea580c;">$0.00</span>
                    </div>

                    <button id="_ep_ef_confirmar"
                        style="width:100%;margin-top:16px;padding:15px;border-radius:18px;background:linear-gradient(135deg,#16a34a,#22c55e);border:none;color:#fff;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:1px;cursor:pointer;">
                        <i class="fas fa-check" style="margin-right:6px;"></i> Registrar Pago en Efectivo
                    </button>
                </div>

                <!-- PASO 2B: Tarjeta — folio/referencia -->
                <div id="_ep_paso_tarjeta" style="display:none;padding:0 24px 20px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                        <button id="_ep_volver_tarjeta" style="width:28px;height:28px;border-radius:50%;border:1.5px solid #e2e8f0;background:#f8fafc;cursor:pointer;color:#64748b;font-size:13px;">←</button>
                        <p style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:2px;color:#6b7280;margin:0;">Tarjeta</p>
                    </div>

                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:14px;margin-bottom:16px;text-align:center;">
                        <p style="font-size:11px;font-weight:700;color:#1d4ed8;margin:0;">El pago se confirmará en Caja</p>
                        <p style="font-size:10px;color:#3b82f6;margin:4px 0 0;">Ingresa el folio si ya tienes el comprobante</p>
                    </div>

                    <label style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#6b7280;display:block;margin-bottom:6px;">Folio / Referencia de la operación (opcional)</label>
                    <input id="_ep_ta_folio" type="text" placeholder="Ej. 1234, últimos 4 dígitos..."
                        style="width:100%;padding:12px 16px;border:2px solid #e2e8f0;border-radius:14px;font-size:14px;font-weight:700;color:#111827;outline:none;box-sizing:border-box;">

                    <button id="_ep_ta_confirmar"
                        style="width:100%;margin-top:16px;padding:15px;border-radius:18px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);border:none;color:#fff;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:1px;cursor:pointer;">
                        <i class="fas fa-check" style="margin-right:6px;"></i> Registrar — Pago con Tarjeta
                    </button>
                </div>

                <!-- PASO 2C: Transferencia — folio -->
                <div id="_ep_paso_transferencia" style="display:none;padding:0 24px 20px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                        <button id="_ep_volver_transferencia" style="width:28px;height:28px;border-radius:50%;border:1.5px solid #e2e8f0;background:#f8fafc;cursor:pointer;color:#64748b;font-size:13px;">←</button>
                        <p style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:2px;color:#6b7280;margin:0;">Transferencia</p>
                    </div>

                    <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:14px;padding:14px;margin-bottom:16px;text-align:center;">
                        <p style="font-size:11px;font-weight:700;color:#6d28d9;margin:0;">El pago se confirmará en Caja</p>
                        <p style="font-size:10px;color:#7c3aed;margin:4px 0 0;">Pide al cliente el folio o número de referencia</p>
                    </div>

                    <label style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#6b7280;display:block;margin-bottom:6px;">Folio / Número de transferencia (opcional)</label>
                    <input id="_ep_tr_folio" type="text" placeholder="Ej. SPEI-20240906-1234..."
                        style="width:100%;padding:12px 16px;border:2px solid #e2e8f0;border-radius:14px;font-size:14px;font-weight:700;color:#111827;outline:none;box-sizing:border-box;">

                    <button id="_ep_tr_confirmar"
                        style="width:100%;margin-top:16px;padding:15px;border-radius:18px;background:linear-gradient(135deg,#6d28d9,#7c3aed);border:none;color:#fff;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:1px;cursor:pointer;">
                        <i class="fas fa-check" style="margin-right:6px;"></i> Registrar — Transferencia
                    </button>
                </div>

                <!-- Cancelar siempre visible -->
                <div style="padding:0 24px 20px;">
                    <button id="_ep_cancelar"
                        style="width:100%;padding:11px;border-radius:16px;border:1.5px solid #e2e8f0;background:#f8fafc;color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:1px;cursor:pointer;">
                        Cancelar
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(el);

        // --- Variables internas ---
        let _total   = 0;
        let _mesaId  = null;
        let _ordenId = null;

        // --- Helpers de pasos ---
        const _irPaso = (paso) => {
            ['_ep_paso1','_ep_paso_efectivo','_ep_paso_tarjeta','_ep_paso_transferencia']
                .forEach(id => { const e = document.getElementById(id); if (e) e.style.display = 'none'; });
            const dest = document.getElementById(paso);
            if (dest) dest.style.display = 'block';
        };

        // --- Abrir modal ---
        window.abrirModalPagoDomicilio = function(total, mesaId, ordenId) {
            _total   = parseFloat(total) || 0;
            _mesaId  = mesaId;
            _ordenId = ordenId;

            document.getElementById('_ep_total').textContent = '$' + _total.toFixed(2);

            // Reset inputs
            ['_ep_ef_monto','_ep_ta_folio','_ep_tr_folio'].forEach(id => {
                const inp = document.getElementById(id);
                if (inp) inp.value = '';
            });
            const cb = document.getElementById('_ep_ef_cambio_box');
            const fb = document.getElementById('_ep_ef_falta_box');
            if (cb) cb.style.display = 'none';
            if (fb) fb.style.display = 'none';

            _irPaso('_ep_paso1');
            el.style.display = 'flex';

            // Enfocar después del render
            setTimeout(() => {}, 50);
        };

        window.abrirModalPagoDelivery = window.abrirModalPagoDomicilio;

        // --- Cerrar ---
        function _cerrar() { el.style.display = 'none'; }
        document.getElementById('_ep_cerrar').onclick   = _cerrar;
        document.getElementById('_ep_cancelar').onclick  = _cerrar;

        // --- Selección de método → ir a paso correspondiente ---
        el.querySelectorAll('._ep_btn_metodo').forEach(btn => {
            btn.addEventListener('click', function() {
                const m = this.dataset.metodo;
                _irPaso('_ep_paso_' + m);
                if (m === 'efectivo') {
                    setTimeout(() => document.getElementById('_ep_ef_monto')?.focus(), 100);
                }
            });
        });

        // --- Volver al paso 1 ---
        ['_ep_volver_efectivo','_ep_volver_tarjeta','_ep_volver_transferencia'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', () => _irPaso('_ep_paso1'));
        });

        // --- Efectivo: calcular cambio en tiempo real ---
        document.getElementById('_ep_ef_monto')?.addEventListener('input', function() {
            const pagado   = parseFloat(this.value) || 0;
            const cambio   = pagado - _total;
            const cb       = document.getElementById('_ep_ef_cambio_box');
            const fb       = document.getElementById('_ep_ef_falta_box');
            const cambioEl = document.getElementById('_ep_ef_cambio');
            const faltaEl  = document.getElementById('_ep_ef_falta');

            if (pagado <= 0) {
                if (cb) cb.style.display = 'none';
                if (fb) fb.style.display = 'none';
            } else if (cambio >= 0) {
                if (cb) { cb.style.display = 'flex'; cambioEl.textContent = '$' + cambio.toFixed(2); }
                if (fb) fb.style.display = 'none';
            } else {
                if (fb) { fb.style.display = 'flex'; faltaEl.textContent = '$' + Math.abs(cambio).toFixed(2); }
                if (cb) cb.style.display = 'none';
            }
        });

        // --- Confirmar Efectivo ---
        document.getElementById('_ep_ef_confirmar')?.addEventListener('click', function() {
            const monto = parseFloat(document.getElementById('_ep_ef_monto')?.value) || 0;
            if (monto <= 0) {
                mostrarError('Ingresa el monto que paga el cliente.');
                document.getElementById('_ep_ef_monto')?.focus();
                return;
            }
            const cambio = monto - _total;
            const ref = cambio >= 0 ? `Paga $${monto.toFixed(2)} — Cambio $${cambio.toFixed(2)}` : null;
            _indicarMetodo('efectivo', ref);
        });

        // --- Confirmar Tarjeta ---
        document.getElementById('_ep_ta_confirmar')?.addEventListener('click', function() {
            const folio = document.getElementById('_ep_ta_folio')?.value?.trim() || null;
            _indicarMetodo('tarjeta', folio);
        });

        // --- Confirmar Transferencia ---
        document.getElementById('_ep_tr_confirmar')?.addEventListener('click', function() {
            const folio = document.getElementById('_ep_tr_folio')?.value?.trim() || null;
            _indicarMetodo('transferencia', folio);
        });

        // --- Guardar método en la orden y redirigir a dashboard ---
        function _indicarMetodo(metodo, referencia) {
            const cfg  = window.ComandaConfig || {};
            const url  = cfg.rutas && cfg.rutas.deliveryIndicarPago;
            const csrf = cfg.csrfToken;

            if (!url) {
                // Si no hay ruta configurada (compatibilidad), solo redirigir
                _cerrar();
                mostrarExito('Método registrado. El pedido pasó a Caja.');
                setTimeout(() => window.location.href = (cfg.rutas && cfg.rutas.dashboard) || '/', 1200);
                return;
            }

            // Deshabilitar botones mientras procesa
            el.querySelectorAll('button').forEach(b => b.disabled = true);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    mesa_id:    _mesaId,
                    orden_id:   _ordenId || null,
                    metodo:     metodo,
                    referencia: referencia || null,
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    _cerrar();
                    mostrarExito('Pedido registrado. Pasará a Caja como pago pendiente.');
                    setTimeout(() => window.location.href = (cfg.rutas && cfg.rutas.dashboard) || '/', 1500);
                } else {
                    mostrarError(data.message || 'No se pudo registrar el método.');
                    el.querySelectorAll('button').forEach(b => b.disabled = false);
                }
            })
            .catch(() => {
                // En caso de error de red, igual redirigir para no bloquear al mesero
                _cerrar();
                mostrarExito('Pedido enviado a Caja.');
                setTimeout(() => window.location.href = (cfg.rutas && cfg.rutas.dashboard) || '/', 1200);
            });
        }
    })();

    // ---------------------------------------------------------------
    // ENVÍO A COCINA
    // ---------------------------------------------------------------
    window.enviarACocina = function () {
        const itemsHTML = document.querySelectorAll('.ticket-item');
        if (itemsHTML.length === 0) { mostrarError("¡Agrega platillos!"); return; }

        const cfg       = window.ComandaConfig || {};
        const urlParams = new URLSearchParams(window.location.search);

        // Detección de domicilio: cualquiera de las tres fuentes válidas
        const esDomicilio =
            window.tipoPedidoActual === 'domicilio' ||
            urlParams.get('tipo_pedido') === 'domicilio' ||
            (cfg.mesa && String(cfg.mesa.numero || '').toUpperCase().startsWith('DOM')) ||
            (cfg.delivery && cfg.delivery.esDelivery);

        // --- VALIDACIÓN PARA DOMICILIO ---
        if (esDomicilio) {
            const esExpress = Boolean(window.nombreClienteTemporal);
            if (!esExpress) {
                if (!window.clienteSeleccionadoId || window.clienteSeleccionadoId === 'null') {
                    mostrarError("¡Debes seleccionar un cliente para el pedido a domicilio!");
                    if (typeof window.abrirModalDelivery === 'function') window.abrirModalDelivery();
                    return;
                }
                if (!window.direccionSeleccionadaId || window.direccionSeleccionadaId === 'null') {
                    mostrarError("¡Falta la dirección! Selecciona a dónde enviar el pedido.");
                    if (typeof window.abrirModalDelivery === 'function') window.abrirModalDelivery();
                    return;
                }
            }
        }

        // --- VALIDACIÓN PARA LLEVAR ---
        const esLlevar = window.tipoPedidoActual === 'llevar' || urlParams.get('tipo_pedido') === 'llevar';
        if (esLlevar) {
            if (!window.clienteSeleccionadoId && !window.nombreClienteTemporal) {
                mostrarError("¡Ingresa un nombre para identificar este pedido para llevar!");
                if (typeof window.abrirModal === 'function') window.abrirModal('modalParaLlevar');
                return;
            }
        }

        const platillosData = [];
        itemsHTML.forEach(item => {
            const nomEl = item.querySelector('.nombre-platillo');
            const nombreCompleto = nomEl ? nomEl.innerText.trim() : 'Producto';
            const modsElementos  = item.querySelectorAll('.nota-texto-real');
            const notasManuales  = [];
            modsElementos.forEach(m => notasManuales.push(m.innerText.replace('•', '').trim()));

            let varianteInfo = null;
            const matchParentesis = nombreCompleto.match(/\(([^)]+)\)/);
            if (matchParentesis && matchParentesis[1]) varianteInfo = matchParentesis[1].trim();

            platillosData.push({
                id:           parseInt(item.dataset.productoId, 10),
                nombre:       nombreCompleto,
                cantidad:     parseInt(item.dataset.cantidad, 10),
                precio:       parseFloat(item.dataset.precio),
                variante_info: varianteInfo,
                notas:        notasManuales.length > 0 ? notasManuales.join(', ') : null,
                modificadores: notasManuales,
                gramaje:      item.dataset.gramaje === 'sin-gramaje' ? null : item.dataset.gramaje,
                tiempo:       item.dataset.tiempo
            });
        });

        const btn = document.getElementById('btn-enviar');
        if (btn) { btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; btn.disabled = true; }

        const txtTotalEl  = document.getElementById('txtTotal');
        const totalParseado = txtTotalEl ? parseFloat(txtTotalEl.innerText.replace('$', '')) : 0;

        fetch(cfg.rutas.comandaEnviar, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': cfg.csrfToken
            },
            body: JSON.stringify({
                mesa_id:            (typeof mesaDestinoSeleccionada !== 'undefined' ? mesaDestinoSeleccionada : null) || (cfg.mesa && cfg.mesa.id) || 1,
                platillos:          platillosData,
                total:              totalParseado,
                personas:           (typeof numeroPersonas !== 'undefined' ? numeroPersonas : 1),
                descuento_porcentaje: (typeof descuentoPorcentaje !== 'undefined' ? descuentoPorcentaje : 0),
                nota_general:       (typeof notaGeneral !== 'undefined' ? notaGeneral : ''),
                propina:            window.propinaGlobal || 0,
                tipo_pedido:        window.tipoPedidoActual || urlParams.get('tipo_pedido') || 'comedor',
                cliente_id:         window.clienteSeleccionadoId || null,
                direccion_id:       window.direccionSeleccionadaId || null,
                nombre_temporal:    window.nombreClienteTemporal ||
                    (document.getElementById('lbl-tipo-pedido-actual') &&
                     document.getElementById('lbl-tipo-pedido-actual').innerText !== 'Ingresar Nombre' &&
                     document.getElementById('lbl-tipo-pedido-actual').innerText !== 'Para Llevar'
                     ? document.getElementById('lbl-tipo-pedido-actual').innerText.trim()
                     : null)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof platillosEnviadosDB !== 'undefined') {
                    platillosData.forEach(p => platillosEnviadosDB.push({
                        nombre: p.nombre, cantidad: p.cantidad, precio: p.precio, estado: 'enviado'
                    }));
                }

                if (btn) { btn.innerHTML = '<i class="fas fa-paper-plane text-sm"></i><span>Enviar Orden</span>'; btn.disabled = false; }

                if (esDomicilio) {
                    // Leer total ANTES de limpiar el ticket (limpiarTicket resetea txtTotalComanda a $0.00)
                    const totalEl    = document.getElementById('txtTotalComanda') || document.getElementById('txtTotal');
                    const totalFinal = totalEl
                        ? (parseFloat(totalEl.innerText.replace(/[$,\s]/g, '')) || totalParseado)
                        : totalParseado;

                    if (typeof window.limpiarTicket === 'function') window.limpiarTicket();

                    // Abrir modal de pago (siempre disponible — lo creamos nosotros mismos)
                    window.abrirModalPagoDomicilio(totalFinal, cfg.mesa && cfg.mesa.id, data.orden_id);
                } else {
                    if (typeof window.limpiarTicket === 'function') window.limpiarTicket();
                    mostrarExito("¡Enviado a cocina!");
                    setTimeout(() => window.location.href = (cfg.rutas && cfg.rutas.dashboard) || '/', 1000);
                }
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            mostrarError(error.message);
            if (btn) { btn.innerHTML = '<i class="fas fa-paper-plane text-sm"></i><span>Enviar Orden</span>'; btn.disabled = false; }
        });
    };
})();