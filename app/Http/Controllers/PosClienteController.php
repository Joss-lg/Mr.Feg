<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Direccion;
// --- IMPORTAMOS LOS MODELOS DE LEALTAD ---
use App\Models\FidelidadCliente;
use App\Models\NivelFidelidad;
use Illuminate\Support\Collection;

class PosClienteController extends Controller
{
    // 1. Buscar clientes por nombre o teléfono
    public function buscar(Request $request)
    {
        $termino = $request->q;

        if (!$termino) {
            return response()->json([]);
        }

        // Buscamos coincidencias y traemos sus direcciones asociadas
        $clientes = Cliente::with('direcciones')
            ->where('nombre', 'LIKE', "%{$termino}%")
            ->orWhere('apellido', 'LIKE', "%{$termino}%")
            ->orWhere('telefono', 'LIKE', "%{$termino}%")
            ->take(10) 
            ->get();

        // Los niveles se calculan UNA sola vez aquí y se reutilizan para
        // todos los clientes de este resultado (evita repetir la consulta
        // a niveles_fidelidad por cada cliente encontrado).
        $niveles = NivelFidelidad::orderBy('compras_requeridas', 'asc')->get();

        // Iteramos sobre los clientes encontrados para inyectarles sus sellos
        $clientes->map(function ($cliente) use ($niveles) {
            $this->inyectarLealtad($cliente, $niveles);
            return $cliente;
        });

        return response()->json($clientes);
    }

    // 2. Guardar un cliente nuevo desde el modal rápido
    public function guardarClienteExpress(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
        ]);

        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido, 
            'telefono' => $request->telefono,
            'status' => 1, 
        ]);

        // NUEVO: le inyectamos la lealtad directo aquí, igual que en
        // buscar(), en vez de dejar que el frontend tenga que volver a
        // buscar al cliente para conseguir esta información. Un cliente
        // recién creado siempre tiene 0 compras, pero de todas formas debe
        // mostrar la primera meta a alcanzar (ej. "Falta(n) 5 compra(s)
        // para: Papas a la francesa chicas"), no un mensaje vacío.
        $niveles = NivelFidelidad::orderBy('compras_requeridas', 'asc')->get();
        $this->inyectarLealtad($cliente, $niveles);

        // direcciones vacío explícito: el frontend espera este campo para
        // poder armar el radio de "Dirección de Entrega" sin romperse.
        $cliente->setRelation('direcciones', collect());

        return response()->json([
            'success' => true,
            'cliente' => $cliente
        ]);
    }

    // 3. Guardar una dirección nueva para un cliente existente
    public function guardarDireccionExpress(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'calle' => 'required|string|max:255',
        ]);

        $direccion = Direccion::create([
            'cliente_id' => $request->cliente_id,
            'calle' => $request->calle,
            'manzana' => $request->manzana,
            'lote' => $request->lote,
            'colonia' => $request->colonia,
            'referencia' => $request->referencia,
        ]);

        return response()->json([
            'success' => true,
            'direccion' => $direccion
        ]);
    }

    /**
     * Calcula los sellos, el premio disponible (si ya lo alcanzó) y la
     * siguiente meta de lealtad de un cliente, y los inyecta directo como
     * atributos del modelo (para que salgan tal cual al convertirlo a JSON).
     *
     * Extraído de buscar() para poder reutilizarlo también en
     * guardarClienteExpress() sin duplicar esta lógica en dos lugares —
     * que fue exactamente el bug original: guardarClienteExpress() nunca
     * calculaba nada de esto, así que un cliente recién registrado siempre
     * mostraba "No hay metas de lealtad configuradas" hasta que lo volvías
     * a buscar por separado.
     */
   private function inyectarLealtad(Cliente $cliente, Collection $niveles): void
    {
        $fidelidad = FidelidadCliente::where('cliente_id', $cliente->id)->first();
        $sellos = $fidelidad ? (int)$fidelidad->compras_acumuladas : 0;

        $premioDisponible = null;
        $siguienteMeta = null;
        $metaRequerida = 0;

        if ($niveles->count() > 0) {
            foreach ($niveles as $nivel) {
                $req = (int)$nivel->compras_requeridas;

                if ($sellos >= $req) {
                    // Guarda el premio más alto alcanzado que aún no canjea
                    $premioDisponible = $nivel->premio_descripcion;
                } elseif (!$siguienteMeta) {
                    // Primer nivel no alcanzado (próxima meta)
                    $siguienteMeta = $nivel->premio_descripcion;
                    $metaRequerida = $req;
                }
            }

            // Si superó todos los niveles existentes
            if (!$siguienteMeta && $premioDisponible) {
                $siguienteMeta = "¡Nivel máximo alcanzado!";
                $metaRequerida = (int)$niveles->max('compras_requeridas');
            }
        } else {
            $siguienteMeta = "Configurar Niveles";
            $metaRequerida = 0;
        }

        $cliente->lealtad_sellos = $sellos;
        $cliente->lealtad_premio_disponible = $premioDisponible;
        $cliente->lealtad_siguiente_meta = $siguienteMeta;
        $cliente->lealtad_meta_requerida = $metaRequerida;
    }

 public function canjearPremio(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
    ]);

    $fidelidad = FidelidadCliente::where('cliente_id', $request->cliente_id)->first();
    $sellosActuales = $fidelidad ? (int)$fidelidad->compras_acumuladas : 0;

    $nivelAlcanzado = NivelFidelidad::where('compras_requeridas', '<=', $sellosActuales)
        ->orderBy('compras_requeridas', 'desc')
        ->first();

    if (!$nivelAlcanzado) {
        return response()->json([
            'success' => false,
            'message' => "El cliente tiene {$sellosActuales} sellos y no cuenta con premios disponibles."
        ], 400);
    }

    // Guardamos cuántos sellos tenía en total para poder devolvérselos si cancela el canje
    $sellosConsumidos = $sellosActuales;

    // Reseteamos el contador completamente a 0
    $fidelidad->compras_acumuladas = 0;
    $fidelidad->increment('total_canjes_realizados', 1);
    $fidelidad->save();

    return response()->json([
        'success' => true,
        'nuevos_sellos' => 0,
        'premio' => $nivelAlcanzado->premio_descripcion,
        'costo_sellos' => $sellosConsumidos, // Enviamos el total reseteado para el ticket
        'message' => 'Premio canjeado exitosamente.'
    ]);
}

public function revertirCanje(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'sellos_a_revertir' => 'required|integer|min:1',
    ]);

    $fidelidad = FidelidadCliente::where('cliente_id', $request->cliente_id)->first();

    if ($fidelidad) {
        // Restauramos los sellos que tenía antes de presionar canjear
        $fidelidad->compras_acumuladas += (int)$request->sellos_a_revertir;
        if ($fidelidad->total_canjes_realizados > 0) {
            $fidelidad->decrement('total_canjes_realizados', 1);
        }
        $fidelidad->save();
    }

    return response()->json([
        'success' => true,
        'nuevos_sellos' => $fidelidad ? $fidelidad->compras_acumuladas : 0,
        'message' => 'Sellos devueltos exitosamente.'
    ]);
}
 
}