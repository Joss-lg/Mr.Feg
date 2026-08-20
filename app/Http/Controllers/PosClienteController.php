<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Direccion;
// --- IMPORTAMOS LOS MODELOS DE LEALTAD ---
use App\Models\FidelidadCliente;
use App\Models\NivelFidelidad;

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

        // --- NUEVO: LÓGICA DE LEALTAD OPTIMIZADA ---
        $niveles = NivelFidelidad::orderBy('compras_requeridas', 'asc')->get();

        // Iteramos sobre los clientes encontrados para inyectarles sus sellos
        $clientes->map(function ($cliente) use ($niveles) {
            $fidelidad = FidelidadCliente::where('cliente_id', $cliente->id)->first();
            $sellos = $fidelidad ? $fidelidad->compras_acumuladas : 0;
            
            $premioDisponible = null;
            $siguienteMeta = null;
            $metaRequerida = 0;

            if ($niveles->count() > 0) {
                foreach ($niveles as $nivel) {
                    if ($sellos >= $nivel->compras_requeridas) {
                        $premioDisponible = $nivel->premio_descripcion;
                    }
                    if ($sellos < $nivel->compras_requeridas && !$siguienteMeta) {
                        $siguienteMeta = $nivel->premio_descripcion;
                        $metaRequerida = $nivel->compras_requeridas;
                        break;
                    }
                }
                
                // Si ya completó todo, ponemos un mensaje claro
                if (!$siguienteMeta && $premioDisponible) {
                    $siguienteMeta = "¡Nivel máximo alcanzado!";
                }
            } else {
                $siguienteMeta = "Configurar Niveles";
                $metaRequerida = 0;
            }

            // Inyectamos las variables al objeto cliente
            $cliente->lealtad_sellos = $sellos;
            $cliente->lealtad_premio_disponible = $premioDisponible;
            $cliente->lealtad_siguiente_meta = $siguienteMeta;
            $cliente->lealtad_meta_requerida = $metaRequerida;

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
}