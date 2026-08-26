<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Insumo;
use App\Models\Modificador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    /**
     * Muestra el catálogo de Alimentos (Menú) y sus recetas.
     */
   public function index()
{
    $productos = Producto::with([
        'categoria:id,nombre', 
        'insumos:id,nombre,unidad_medida', 
        'modificadores', 
        'variantes.modificadores' // <-- Asegura que cada variante cargue sus modificadores
    ])
    ->select([
        'id', 'categoria_id', 'nombre', 'descripcion', 'precio',
        'se_vende_por_peso', 'precio_por_100g', 'esta_disponible', 'tiene_variantes',
        'created_at', 'updated_at', 'deleted_at',
    ])
    ->orderBy('nombre')
    ->get();

    $categorias = Categoria::orderBy('nombre')->select(['id', 'nombre'])->get();

    $insumosDisponibles = Insumo::where('esta_activo', true)
                                ->orderBy('nombre')
                                ->select(['id', 'nombre', 'unidad_medida', 'stock_actual'])
                                ->get();

    return view('admin.productos.index', compact('productos', 'categorias'), ['insumos' => $insumosDisponibles]);
}
    /**
     * Registra un nuevo platillo y guarda su receta (ingredientes), variantes y modificadores.
     */
    public function store(Request $request)
    {
        $request->merge([
            'nombre' => trim($request->nombre)
        ]);

      $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'categoria_id'      => 'required|exists:categorias,id',
            'precio'            => 'required_if:tiene_variantes,0|numeric|min:0',
            'se_vende_por_peso' => 'sometimes|boolean',
            'precio_por_100g'   => 'nullable|required_if:se_vende_por_peso,1|numeric|min:0',
            
            // --- VALIDACIONES DE VARIANTES (Solo si tiene_variantes = 1) ---
            'tiene_variantes'     => 'sometimes|boolean',
            'variantes'           => 'nullable|required_if:tiene_variantes,1|array',
            'variantes.*.nombre'  => 'required_if:tiene_variantes,1|string',
            'variantes.*.precio'  => 'nullable|numeric|min:0',
            'variantes.*.extras'  => 'nullable|array',
            'variantes.*.extras.*.nombre' => 'nullable|string',
            'variantes.*.extras.*.precio' => 'nullable|numeric|min:0',

            // --- VALIDACIONES DE MODIFICADORES GLOBALES (Solo si tiene_modificadores = 1) ---
            'tiene_modificadores'    => 'sometimes|boolean',
            'modificadores'          => 'nullable|array',
            'modificadores.*.nombre' => 'required_if:tiene_modificadores,1|string',
            'modificadores.*.precio' => 'nullable|numeric|min:0',

            'insumos'           => 'nullable|array',
            'insumos.*'         => 'exists:insumos,id',
            'cantidades'        => 'nullable|array',
            'cantidades.*'      => 'required_with:insumos|numeric|min:0.001',
        ]);

        try {
            DB::beginTransaction();

            $sePorPeso = $request->boolean('se_vende_por_peso');
            $tieneVariantes = $request->boolean('tiene_variantes');

            // 1. Guardar el Producto Base
            $producto = new Producto([
                'nombre'            => $request->nombre,
                'descripcion'       => $request->descripcion,
                'categoria_id'      => $request->categoria_id,
                'precio'            => ($sePorPeso || $tieneVariantes) ? 0 : ($request->precio ?? 0),
                'se_vende_por_peso' => $sePorPeso,
                'precio_por_100g'   => $sePorPeso ? $request->precio_por_100g : null,
                'tiene_variantes'   => $tieneVariantes,
                'esta_disponible'   => $request->boolean('esta_disponible', true),
            ]);

            $producto->save();

            // 2. Lógica para guardar Variantes y sus Modificadores específicos
            if ($tieneVariantes && $request->has('variantes')) {
                foreach ($request->variantes as $vData) {
                    if (empty($vData['nombre'])) continue;

                    $precioVariante = (!empty($vData['precio']) && is_numeric($vData['precio'])) 
                        ? (float)$vData['precio'] 
                        : 0.00;

                    $variante = $producto->variantes()->create([
                        'nombre'          => trim($vData['nombre']),
                        'precio'          => $precioVariante,
                        'esta_disponible' => true
                    ]);

                    // Guardar complementos/extras específicos pertenecientes a este tamaño
                    if (!empty($vData['extras']) && is_array($vData['extras'])) {
                        foreach ($vData['extras'] as $extraData) {
                            if (!empty($extraData['nombre'])) {
                                Modificador::create([
                                    'variante_id' => $variante->id,
                                    'nombre'      => trim($extraData['nombre']),
                                    'tipo'        => 'extra',
                                    'precio'      => (!empty($extraData['precio']) && is_numeric($extraData['precio'])) 
                                                        ? (float)$extraData['precio'] 
                                                        : 0.00,
                                ]);
                            }
                        }
                    }
                }
            }

            // 3. Lógica para guardar Modificadores Globales (solo si no tiene variantes)
            if (!$tieneVariantes && $request->boolean('tiene_modificadores') && $request->has('modificadores')) {
                $modificadoresGlobalesIds = [];
                foreach ($request->modificadores as $modData) {
                    if (!empty($modData['nombre'])) {
                        $modificador = Modificador::create([
                            'variante_id' => null,
                            'nombre'      => trim($modData['nombre']),
                            'tipo'        => 'extra',
                            'precio'      => (!empty($modData['precio']) && is_numeric($modData['precio'])) 
                                                ? (float)$modData['precio'] 
                                                : 0.00,
                        ]);
                        $modificadoresGlobalesIds[] = $modificador->id;
                    }
                }
                if (!empty($modificadoresGlobalesIds)) {
                    $producto->modificadores()->sync($modificadoresGlobalesIds);
                }
            }

            // 4. Lógica para guardar Insumos (Receta)
            if ($request->filled('insumos') && $request->filled('cantidades')) {
                $receta = [];
                foreach ($request->insumos as $index => $insumoId) {
                    if (isset($request->cantidades[$index]) && (float)$request->cantidades[$index] > 0) {
                        $receta[$insumoId] = [
                            'cantidad_usada' => (float)$request->cantidades[$index]
                        ];
                    }
                }

                if (!empty($receta)) {
                    $producto->insumos()->sync($receta);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Producto guardado correctamente.'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ProductoController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza un platillo y modifica su receta estructural, variantes y modificadores.
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'nombre' => trim($request->nombre)
        ]);

      $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'categoria_id'      => 'required|exists:categorias,id',
            'precio'            => 'required_if:tiene_variantes,0|numeric|min:0',
            'se_vende_por_peso' => 'sometimes|boolean',
            'precio_por_100g'   => 'nullable|required_if:se_vende_por_peso,1|numeric|min:0',
            
            // --- VALIDACIONES DE VARIANTES (Solo si tiene_variantes = 1) ---
            'tiene_variantes'     => 'sometimes|boolean',
            'variantes'           => 'nullable|required_if:tiene_variantes,1|array',
            'variantes.*.id'      => 'nullable',
            'variantes.*.nombre'  => 'required_if:tiene_variantes,1|string',
            'variantes.*.precio'  => 'nullable|numeric|min:0',
            'variantes.*.extras'  => 'nullable|array',
            'variantes.*.extras.*.id'     => 'nullable',
            'variantes.*.extras.*.nombre' => 'nullable|string',
            'variantes.*.extras.*.precio' => 'nullable|numeric|min:0',

            // --- VALIDACIONES DE MODIFICADORES (Solo si tiene_modificadores = 1) ---
            'tiene_modificadores'    => 'sometimes|boolean',
            'modificadores'          => 'nullable|array',
            'modificadores.*.id'     => 'nullable',
            'modificadores.*.nombre' => 'required_if:tiene_modificadores,1|string',
            'modificadores.*.precio' => 'nullable|numeric|min:0',

            'insumos'           => 'nullable|array',
            'insumos.*'         => 'exists:insumos,id',
            'cantidades'        => 'nullable|array',
            'cantidades.*'      => 'required_with:insumos|numeric|min:0.001',
        ]);
        
        try {
            DB::beginTransaction();

            $producto = Producto::findOrFail($id);
            $sePorPeso = $request->boolean('se_vende_por_peso');
            $tieneVariantes = $request->boolean('tiene_variantes');

            // 1. Actualizar el Producto Base
            $producto->update([
                'nombre'            => $request->nombre,
                'descripcion'       => $request->descripcion,
                'categoria_id'      => $request->categoria_id,
                'precio'            => ($sePorPeso || $tieneVariantes) ? 0 : ($request->precio ?? 0),
                'se_vende_por_peso' => $sePorPeso,
                'precio_por_100g'   => $sePorPeso ? $request->precio_por_100g : null,
                'tiene_variantes'   => $tieneVariantes,
                'esta_disponible'   => $request->boolean('esta_disponible', $producto->esta_disponible), 
            ]);

            // 2. Sincronizar Variantes y sus Modificadores específicos
            if ($tieneVariantes && $request->has('variantes')) {
                $variantesEnviadasIds = [];

                foreach ($request->variantes as $vData) {
                    if (empty($vData['nombre'])) continue;

                    $precioVariante = (!empty($vData['precio']) && is_numeric($vData['precio'])) 
                        ? (float)$vData['precio'] 
                        : 0.00;

                    $idVariante = (!empty($vData['id']) && is_numeric($vData['id'])) ? (int)$vData['id'] : null;

                    if ($idVariante) {
                        $variante = $producto->variantes()->find($idVariante);
                        if ($variante) {
                            $variante->update([
                                'nombre' => trim($vData['nombre']),
                                'precio' => $precioVariante
                            ]);
                        }
                    } else {
                        $variante = $producto->variantes()->create([
                            'nombre'          => trim($vData['nombre']),
                            'precio'          => $precioVariante,
                            'esta_disponible' => true
                        ]);
                    }

                    if ($variante) {
                        $variantesEnviadasIds[] = $variante->id;

                        // Sincronizar modificadores/extras de esta variante
                        $extrasEnviadosIds = [];
                        if (!empty($vData['extras']) && is_array($vData['extras'])) {
                            foreach ($vData['extras'] as $extraData) {
                                if (empty($extraData['nombre'])) continue;

                                $precioExtra = (!empty($extraData['precio']) && is_numeric($extraData['precio'])) 
                                    ? (float)$extraData['precio'] 
                                    : 0.00;

                                $idExtra = (!empty($extraData['id']) && is_numeric($extraData['id'])) ? (int)$extraData['id'] : null;

                                if ($idExtra) {
                                    $mod = Modificador::where('variante_id', $variante->id)->find($idExtra);
                                    if ($mod) {
                                        $mod->update([
                                            'nombre' => trim($extraData['nombre']),
                                            'precio' => $precioExtra
                                        ]);
                                        $extrasEnviadosIds[] = $mod->id;
                                    }
                                } else {
                                    $nuevoMod = Modificador::create([
                                        'variante_id' => $variante->id,
                                        'nombre'      => trim($extraData['nombre']),
                                        'tipo'        => 'extra',
                                        'precio'      => $precioExtra,
                                    ]);
                                    $extrasEnviadosIds[] = $nuevoMod->id;
                                }
                            }
                        }
                        // Eliminar extras que se borraron en la edición de este tamaño
                        Modificador::where('variante_id', $variante->id)
                                   ->whereNotIn('id', $extrasEnviadosIds)
                                   ->delete();
                    }
                }

                // Eliminar variantes que ya no existen
                $variantesAEliminar = $producto->variantes()->whereNotIn('id', $variantesEnviadasIds)->get();
                foreach ($variantesAEliminar as $vEliminar) {
                    Modificador::where('variante_id', $vEliminar->id)->delete();
                    $vEliminar->delete();
                }

            } elseif (!$tieneVariantes) {
                foreach ($producto->variantes as $vEliminar) {
                    Modificador::where('variante_id', $vEliminar->id)->delete();
                    $vEliminar->delete();
                }
            }

            // 3. Sincronizar Modificadores Globales (para productos sin variantes)
            if (!$tieneVariantes && $request->boolean('tiene_modificadores') && $request->has('modificadores')) {
                $modificadoresIds = [];

                foreach ($request->modificadores as $modData) {
                    if (!empty($modData['nombre'])) {
                        $precioMod = (!empty($modData['precio']) && is_numeric($modData['precio'])) 
                            ? (float)$modData['precio'] 
                            : 0.00;

                        $idMod = (!empty($modData['id']) && is_numeric($modData['id'])) ? (int)$modData['id'] : null;

                        if ($idMod) {
                            $modificador = Modificador::find($idMod);
                            if ($modificador) {
                                $modificador->update([
                                    'variante_id' => null,
                                    'nombre'      => trim($modData['nombre']),
                                    'precio'      => $precioMod,
                                ]);
                                $modificadoresIds[] = $modificador->id;
                            }
                        } else {
                            $modificador = Modificador::create([
                                'variante_id' => null,
                                'nombre'      => trim($modData['nombre']),
                                'tipo'        => 'extra',
                                'precio'      => $precioMod,
                            ]);
                            $modificadoresIds[] = $modificador->id;
                        }
                    }
                }

                $producto->modificadores()->sync($modificadoresIds);
            } else {
                $producto->modificadores()->detach();
            }

            // 4. Sincronizar Insumos (Receta)
            $receta = [];
            if ($request->filled('insumos') && $request->filled('cantidades')) {
                foreach ($request->insumos as $index => $insumoId) {
                    if (isset($request->cantidades[$index]) && (float)$request->cantidades[$index] > 0) {
                        $receta[$insumoId] = [
                            'cantidad_usada' => (float)$request->cantidades[$index]
                        ];
                    }
                }
            }
            $producto->insumos()->sync($receta);

            DB::commit();
            return response()->json(['message' => 'Producto actualizado correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ProductoController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Error inesperado al actualizar el producto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un platillo del menú (Soporta Soft Delete nativo).
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $nombre = $producto->nombre;
        $producto->delete();

        return response()->json(['message' => "El producto ({$nombre}) fue eliminado correctamente."]);
    }

    /**
     * Alterna la disponibilidad instantánea del platillo (Switch de operaciones).
     */
    public function toggleDisponibilidad($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->esta_disponible = !$producto->esta_disponible;
        $producto->save();

        $estadoStr = $producto->esta_disponible ? 'habilitado' : 'deshabilitado';

        return response()->json([
            'message'         => "El producto ({$producto->nombre}) ha sido {$estadoStr}.",
            'esta_disponible' => $producto->esta_disponible,
        ]);
    }

    /**
     * Devuelve los productos agrupados por categoría, para renderizar las tarjetas.
     */
    public function getProductos(): JsonResponse
    {
        $productos = Producto::with([
            'categoria', 
            'insumos', 
            'modificadores', 
            'variantes.modificadores'
        ])
        ->select([
            'id', 
            'categoria_id', 
            'nombre', 
            'descripcion', 
            'precio',
            'se_vende_por_peso', 
            'precio_por_100g', 
            'tiene_variantes',
            'esta_disponible',
            'updated_at',
        ])
        ->get()
        ->groupBy(function ($producto) {
            return $producto->categoria->nombre ?? 'Sin Categoría';
        });

        return response()->json($productos);
    }

    public function getEstadisticas(): JsonResponse
    {
        return response()->json([
            'total'       => Producto::count(),
            'disponibles' => Producto::where('esta_disponible', true)->count(),
            'categorias'  => Categoria::count(),
        ]);
    }
}