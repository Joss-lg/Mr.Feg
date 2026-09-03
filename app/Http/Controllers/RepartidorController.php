<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Orden, User};
class RepartidorController extends Controller
{
    /**
     * Muestra la vista principal del módulo de repartos
     */
    public function index()
    {
        // 1. Pedidos pendientes: Busca por tipo_pedido o si la mesa asociada es virtual de domicilio/llevar
        $ordenesPendientes = Orden::where(function($query) {
                $query->whereIn('tipo_pedido', ['domicilio', 'delivery'])
                      ->orWhereHas('mesa', function($m) {
                          $m->where('numero', 'like', 'DOM-%');
                      });
            })
            ->whereNull('repartidor_id')
            ->where('estado', '!=', 'pagada')
            ->with(['cliente', 'direccion', 'mesa'])
            ->get();

        // 2. Órdenes que ya van en camino
        $ordenesEnCamino = Orden::where('estado_reparto', 'en_camino')
            ->with(['cliente', 'direccion', 'repartidor', 'mesa'])
            ->get();

        // 3. Obtener empleados con el rol de Repartidor
        $repartidores = User::whereHas('rol', function($q) {
            $q->where('nombre', 'Repartidor');
        })->whereNull('deleted_at')->get();

        return view('admin.repartidores.index', compact('ordenesPendientes', 'ordenesEnCamino', 'repartidores'));
    }
    
    /**
     * Asigna el repartidor y pasa la orden a "En Camino"
     */
    public function asignarRepartidor(Request $request, $id)
    {
        $request->validate([
            'repartidor_id' => 'required|exists:users,id'
        ]);

        $orden = Orden::findOrFail($id);
        
        $orden->update([
            'repartidor_id' => $request->repartidor_id,
            'estado_reparto' => 'en_camino',
            'updated_at' => now(), // Nos sirve para medir el tiempo de salida
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pedido asignado y en camino.'
        ]);
    }

    /**
     * Marca la orden como entregada
     */
    public function marcarEntregado($id)
    {
        $orden = Orden::findOrFail($id);
        
        $orden->update([
            'estado_reparto' => 'entregado',
            'estado' => 'pagada',
            'cerrada_el' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pedido marcado como entregado.'
        ]);
    }
}