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
        $productos = Producto::with(['categoria:id,nombre', 'insumos:id,nombre,unidad_medida', 'modificadores', 'variantes'])
                             ->select([
                                 'id', 'categoria_id', 'nombre', 'descripcion', 'precio',
                                 'se_vende_por_peso', 'precio_por_100g', 'esta_disponible',
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
            
            // --- VALIDACIONES DE VARIANTES ---
            'tiene_variantes'   => 'sometimes|boolean',
            'variantes'         => 'required_if:tiene_variantes,1|array',
            'variantes.*.nombre'=> 'required_with:variantes|string',
            'variantes.*.precio'=> 'required_with:variantes|numeric|min:0',

            // --- VALIDACIONES DE MODIFICADORES (EXTRAS) ---
            'tiene_modificadores'   => 'sometimes|boolean',
            'modificadores'         => 'nullable|array',
            'modificadores.*.nombre'=> 'required_with:modificadores|string',
            'modificadores.*.precio'=> 'required_with:modificadores|numeric|min:0',

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
                'precio'            => ($sePorPeso || $tieneVariantes) ? 0 : $request->precio,
                'se_vende_por_peso' => $sePorPeso,
                'precio_por_100g'   => $sePorPeso ? $request->precio_por_100g : null,
                'tiene_variantes'   => $tieneVariantes,
                'esta_disponible'   => $request->boolean('esta_disponible', true),
            ]);

            $producto->save();

            // 2. Lógica para guardar Variantes (Tamaños)
            if ($tieneVariantes && $request->has('variantes')) {
                foreach ($request->variantes as $varianteData) {
                    $producto->variantes()->create([
                        'nombre' => $varianteData['nombre'],
                        'precio' => $varianteData['precio'],
                        'esta_disponible' => true
                    ]);
                }
            }

            // 3. Lógica para guardar Modificadores (Extras / Complementos)
            if ($request->boolean('tiene_modificadores') && $request->has('modificadores')) {
                $modificadoresIds = [];
                foreach ($request->modificadores as $modData) {
                    if (!empty($modData['nombre']) && isset($modData['precio'])) {
                        $modificador = Modificador::create([
                            'nombre' => trim($modData['nombre']),
                            'precio' => (float) $modData['precio'],
                        ]);
                        $modificadoresIds[] = $modificador->id;
                    }
                }
                $producto->modificadores()->sync($modificadoresIds);
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
            return response()->json(['message' => 'Error inesperado al guardar el producto.'], 500);
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
            
            // --- VALIDACIONES DE VARIANTES PARA ACTUALIZAR ---
            'tiene_variantes'   => 'sometimes|boolean',
            'variantes'         => 'required_if:tiene_variantes,1|array',
            'variantes.*.id'    => 'nullable|exists:variantes,id',
            'variantes.*.nombre'=> 'required_with:variantes|string',
            'variantes.*.precio'=> 'required_with:variantes|numeric|min:0',

            // --- VALIDACIONES DE MODIFICADORES (EXTRAS) ---
            'tiene_modificadores'   => 'sometimes|boolean',
            'modificadores'         => 'nullable|array',
            'modificadores.*.id'    => 'nullable',
            'modificadores.*.nombre'=> 'required_with:modificadores|string',
            'modificadores.*.precio'=> 'required_with:modificadores|numeric|min:0',

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
                'precio'            => ($sePorPeso || $tieneVariantes) ? 0 : $request->precio,
                'se_vende_por_peso' => $sePorPeso,
                'precio_por_100g'   => $sePorPeso ? $request->precio_por_100g : null,
                'tiene_variantes'   => $tieneVariantes,
                'esta_disponible'   => $request->boolean('esta_disponible', $producto->esta_disponible), 
            ]);

            // 2. Sincronizar Variantes (Tamaños)
            if ($tieneVariantes && $request->has('variantes')) {
                $variantesEnviadasIds = [];

                foreach ($request->variantes as $varianteData) {
                    if (!empty($varianteData['id'])) {
                        $variante = $producto->variantes()->find($varianteData['id']);
                        if ($variante) {
                            $variante->update([
                                'nombre' => $varianteData['nombre'],
                                'precio' => $varianteData['precio']
                            ]);
                            $variantesEnviadasIds[] = $variante->id;
                        }
                    } else {
                        $nuevaVariante = $producto->variantes()->create([
                            'nombre' => $varianteData['nombre'],
                            'precio' => $varianteData['precio'],
                            'esta_disponible' => true
                        ]);
                        $variantesEnviadasIds[] = $nuevaVariante->id;
                    }
                }

                $producto->variantes()->whereNotIn('id', $variantesEnviadasIds)->delete();
                
            } elseif (!$tieneVariantes) {
                $producto->variantes()->delete();
            }

            // 3. Sincronizar Modificadores (Extras / Complementos)
            if ($request->boolean('tiene_modificadores') && $request->has('modificadores')) {
                $modificadoresIds = [];

                foreach ($request->modificadores as $modData) {
                    if (!empty($modData['nombre']) && isset($modData['precio'])) {
                        $idMod = (!empty($modData['id']) && is_numeric($modData['id'])) ? (int)$modData['id'] : null;

                        if ($idMod) {
                            $modificador = Modificador::find($idMod);
                            if ($modificador) {
                                $modificador->nombre = trim($modData['nombre']);
                                $modificador->precio = (float) $modData['precio'];
                                $modificador->save();
                                $modificadoresIds[] = $modificador->id;
                            }
                        } else {
                            $modificador = Modificador::create([
                                'nombre' => trim($modData['nombre']),
                                'precio' => (float) $modData['precio'],
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
            return response()->json(['message' => 'Error inesperado al actualizar el producto.'], 500);
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
        $productos = Producto::with(['categoria', 'insumos', 'modificadores', 'variantes'])
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