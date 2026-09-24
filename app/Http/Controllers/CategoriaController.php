<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    /**
     * Muestra el panel principal con todas las categorías activas.
     */
    public function index()
    {
        // Traemos todas las categorías ordenadas por 'nombre'
        // y contamos solo los productos activos (disponibles)
        $categorias = Categoria::withCount(['productos as productos_count' => function ($query) {
            $query->where('esta_disponible', true);
        }])->orderBy('nombre')->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Guarda una nueva categoría desde el modal.
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos estrictamente (Se limpia el input antes de validar la unicidad)
        $request->merge(['nombre' => trim($request->nombre)]);

        $request->validate([
            'nombre'              => 'required|string|max:255|unique:categorias,nombre',
            'color'               => 'nullable|string|max:20',
            'area_impresion'      => 'required|string|in:Cocina,Barra,Parrilla' // <-- Validación agregada
        ]);

        // 2. Creamos la categoría
        Categoria::create([
            'nombre'              => $request->nombre,
            'slug'                => Str::slug($request->nombre),
            'color'               => $request->color ?? '#3B82F6', // Azul por defecto
            'area_impresion'      => $request->area_impresion // <-- Guardado agregado
        ]);

        return redirect()->route('admin.categorias.index')
                         ->with('success', '¡Categoría creada exitosamente!');
    }

    /**
     * Actualiza una categoría existente.
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        // Limpiamos espacios antes de la validación
        $request->merge(['nombre' => trim($request->nombre)]);

        $request->validate([
            'nombre'              => 'required|string|max:255|unique:categorias,nombre,' . $id,
            'color'               => 'nullable|string|max:20',
            'area_impresion'      => 'required|string|in:Cocina,Barra,Parrilla' // <-- Validación agregada
        ]);

        $categoria->update([
            'nombre'              => $request->nombre,
            'slug'                => Str::slug($request->nombre),
            'color'               => $request->color ?? $categoria->color,
            'area_impresion'      => $request->area_impresion // <-- Actualización agregada
        ]);

        return redirect()->route('admin.categorias.index')
                         ->with('success', '¡La categoría fue actualizada!');
    }

 public function destroy($id)
{
    $categoria = Categoria::findOrFail($id);

    // 1. Si tiene productos ACTIVOS, detenemos el borrado
    if ($categoria->productos()->exists()) {
        return redirect()->back()
                         ->with('error', 'No puedes eliminar esta categoría porque tiene productos activos.');
    }

    // 2. Obtener IDs de productos eliminados (en papelera)
    $productoIds = $categoria->productos()->onlyTrashed()->pluck('id');

    if ($productoIds->isNotEmpty()) {
        // 3. Guardar el nombre del producto en los detalles de orden antes de borrar
        foreach ($productoIds as $productoId) {
            $producto = \App\Models\Producto::withTrashed()->find($productoId);
            if ($producto) {
                \App\Models\DetalleOrden::where('producto_id', $productoId)
                    ->whereNull('nombre_producto')
                    ->update(['nombre_producto' => $producto->nombre]);
            }
        }

        // 4. Borrar solo los detalles que no pudieron guardarse (sin nombre)
        \App\Models\DetalleOrden::whereIn('producto_id', $productoIds)
            ->whereNull('nombre_producto')
            ->delete();
    }

    // 5. Destruimos definitivamente los productos en la papelera
    $categoria->productos()->onlyTrashed()->forceDelete();

    // 6. Eliminamos la categoría permanentemente
    $categoria->forceDelete();

    return redirect()->route('admin.categorias.index')
                     ->with('success', 'Categoría eliminada con éxito.');
}
}