<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Direccion;

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
            ->take(10) // Limitamos a 10 resultados para no saturar la vista
            ->get();

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
            'apellido' => $request->apellido, // Puede ser nulo
            'telefono' => $request->telefono,
            'status' => 1, // Activo por defecto
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