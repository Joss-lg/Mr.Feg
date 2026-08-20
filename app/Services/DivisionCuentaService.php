<?php

namespace App\Services;

use App\Models\Orden;
use App\Models\Transaccion;
use App\Models\DetalleOrden;
use App\Models\FidelidadCliente;
use App\Models\NivelFidelidad;
use Illuminate\Support\Facades\DB;
use Exception;

class DivisionCuentaService
{
    public function procesarPago(array $data)
    {
        return DB::transaction(function () use ($data) {
            $orden = Orden::findOrFail($data['orden_id']);
            
            // 1. Validar saldo
            $pendiente = $this->calcularSaldoPendiente($orden);
            if ($data['monto'] > $pendiente) {
                throw new Exception("El monto excede el saldo pendiente de la mesa.");
            }

            // 2. Crear Transacción
            $transaccion = Transaccion::create([
                'orden_id'      => $orden->id,
                'monto'         => $data['monto'],
                'tipo_division' => $data['tipo_division'],
                'turno_id'      => session('turno_id'),
                'cajero_id'     => auth()->id(),
                'metodo_pago'   => $data['metodo_pago'] ?? 'efectivo',
            ]);

            // 3. Lógica por producto
            if ($data['tipo_division'] === 'por_producto') {
                DetalleOrden::whereIn('id', $data['detalles_ids'])
                    ->update(['transaccion_id' => $transaccion->id]);
            }

            // 4. Marcar como pagada si el saldo llega a cero
            if (($pendiente - $data['monto']) <= 0) {
                $orden->update(['estado' => Orden::ESTADO_PAGADA]);

                // --- PROGRAMA DE FIDELIDAD ---
                // Se suma 1 sello por ORDEN COMPLETA pagada (no por cada pago
                // parcial), y solo si el total de la orden alcanza el mínimo
                // configurado (ej. $150). Así una cuenta dividida en varios
                // pagos chicos sigue sumando 1 sello si el total de la mesa
                // llegó al mínimo, en vez de nunca sumar nada.
                $this->registrarComprasFidelidad($orden);
            }

            return $transaccion;
        });
    }

    public function calcularSaldoPendiente(Orden $orden): float
    {
        $pagado = Transaccion::where('orden_id', $orden->id)->sum('monto');
        return (float) ($orden->total - $pagado);
    }

    /**
     * Suma 1 sello de fidelidad al cliente de la orden, si aplica.
     *
     * Reglas:
     *  - La orden debe tener un cliente_id asignado (pedidos sin cliente,
     *    como una mesa normal del comedor sin registrar, no suman).
     *  - El total de la orden debe ser >= al monto mínimo configurado en
     *    niveles_fidelidad (se usa el mínimo más bajo de la tabla como
     *    umbral, para no hardcodear el "$150" en el código).
     *  - Solo se llama desde el bloque que hace la transición a PAGADA,
     *    para que no sume de más si el método se invocara más de una vez
     *    sobre la misma orden.
     */
    protected function registrarComprasFidelidad(Orden $orden): void
    {
        if (empty($orden->cliente_id)) {
            return;
        }

        $montoMinimo = (float) (NivelFidelidad::min('monto_minimo') ?? 150);

        if ((float) $orden->total < $montoMinimo) {
            return;
        }

        $fidelidad = FidelidadCliente::firstOrCreate(
            ['cliente_id' => $orden->cliente_id],
            ['compras_acumuladas' => 0, 'total_canjes_realizados' => 0]
        );

        $fidelidad->increment('compras_acumuladas');
    }
}