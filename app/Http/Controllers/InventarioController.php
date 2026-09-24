<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\CategoriaInsumo;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InventarioController extends Controller
{
    public function index()
    {
        $insumos = Insumo::with('categoria:id,nombre')
                            ->where('esta_activo', true)
                            ->orderBy('nombre')
                            ->get();
                        
        // Categorías propias del inventario (independientes de las del POS)
        $categorias = CategoriaInsumo::withCount(['insumos' => fn ($q) => $q->where('esta_activo', true)])
                                    ->orderBy('nombre')
                                    ->get();
        
        $totalInsumos = $insumos->count();
        $valorInventario = $insumos->sum(function($insumo) {
            return (float)$insumo->stock_actual * (float)$insumo->precio_compra;
        });
        
        $alertasStock = $insumos->filter(function($insumo) {
            return $insumo->stock_actual <= $insumo->stock_minimo;
        });

        $ultimosMovimientos = MovimientoInventario::with(['insumo:id,nombre,unidad_medida', 'usuario:id,nombre'])
                                                    ->orderBy('created_at', 'desc')
                                                    ->take(10)
                                                    ->get();

        return view('admin.inventario.tabla-inventario', compact(
            'insumos', 
            'categorias', 
            'totalInsumos', 
            'valorInventario', 
            'alertasStock',
            'ultimosMovimientos'
        ));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nombre'        => trim($request->nombre),
            'unidad_medida' => trim($request->unidad_medida)
        ]);

        $request->validate([
            'nombre'        => 'required|string|max:255',
            'categoria_id'  => 'required|exists:categorias_insumos,id',
            'unidad_medida' => 'required|string|max:20', 
            'stock_minimo'  => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
        ]);

        $conteoHistorico = Insumo::withTrashed()->count();
        $codigo = 'INS-' . str_pad($conteoHistorico + 1, 3, '0', STR_PAD_LEFT);

        // Lógica agregada: Convertir litros a mililitros
        $unidad = $request->unidad_medida;
        $stockMinimo = (float)$request->stock_minimo;
        if ($unidad === 'l') { $unidad = 'ml'; $stockMinimo *= 1000; }

        Insumo::create([
            'codigo'        => $codigo,
            'nombre'        => $request->nombre,
            'categoria_id'  => $request->categoria_id,
            'unidad_medida' => $unidad,
            'stock_actual'  => 0,
            'stock_minimo'  => $stockMinimo,
            'precio_compra' => $request->precio_compra,
            'esta_activo'   => true,
        ]);

        return redirect()->route('admin.inventario.index')
                            ->with('success', 'Insumo registrado correctamente en el catálogo.');
    }

    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'tipo'      => 'required|in:entrada,salida,ajuste_positivo,ajuste_negativo',
            'cantidad'  => 'required|numeric|gt:0',
            'motivo'    => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $insumo = Insumo::findOrFail($request->insumo_id);
            $cantidad = (float)$request->cantidad;

            // Lógica agregada: Si el insumo es en ml y envían litros, convertir
            if ($insumo->unidad_medida === 'ml' && $request->has('unidad_movimiento') && $request->unidad_movimiento === 'l') {
                $cantidad *= 1000;
            }

            if (in_array($request->tipo, ['salida', 'ajuste_negativo'], true) && $insumo->stock_actual < $cantidad) {
                DB::rollBack();
                return redirect()->back()->with('error', "No hay suficiente stock en el almacén de ({$insumo->nombre}) para realizar la operación.");
            }

            match ($request->tipo) {
                'entrada', 'ajuste_positivo' => $insumo->stock_actual += $cantidad,
                'salida', 'ajuste_negativo'  => $insumo->stock_actual -= $cantidad,
            };

            MovimientoInventario::create([
                'insumo_id' => $insumo->id,
                'user_id'   => auth()->id(),
                'cantidad'  => $cantidad,
                'tipo'      => $request->tipo,
                'motivo'    => trim($request->motivo),
            ]);

            $insumo->save();
            DB::commit();

            return redirect()->back()->with('success', 'Movimiento de almacén procesado y stock actualizado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error inesperado al procesar el inventario.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'nombre'        => trim($request->nombre),
            'unidad_medida' => trim($request->unidad_medida)
        ]);

        $request->validate([
            'nombre'        => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:20',
            'categoria_id'  => 'nullable|exists:categorias_insumos,id',
            'stock_minimo'  => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
        ]);

        $insumo = Insumo::findOrFail($id);
        
        // Lógica agregada: Convertir si cambian a litros
        $unidad = $request->unidad_medida;
        $stockMinimo = (float)$request->stock_minimo;
        if ($unidad === 'l') { $unidad = 'ml'; $stockMinimo *= 1000; }

        $datos = [
            'nombre'        => $request->nombre,
            'unidad_medida' => $unidad,
            'stock_minimo'  => $stockMinimo,
        ];

        // La ventana de edición envía la categoría ("Sin categoría" llega vacía).
        if ($request->has('categoria_id')) {
            $datos['categoria_id'] = $request->categoria_id;
        }

        // La ventana de edición no trae "Precio compra": solo se modifica si
        // viene en la petición, para no borrarlo al editar otros campos.
        if ($request->has('precio_compra')) {
            $datos['precio_compra'] = $request->precio_compra;
        }

        $insumo->update($datos);

        return redirect()->route('admin.inventario.index')
                            ->with('success', "Los datos de {$insumo->nombre} fueron actualizados correctamente.");
    }

    public function destroy($id)
    {
        $insumo = Insumo::findOrFail($id);
        $insumo->update(['esta_activo' => false]);

        return redirect()->route('admin.inventario.index')
                            ->with('success', "El insumo {$insumo->nombre} fue dado de baja del almacén.");
    }

    // =========================================================================
    // CATEGORÍAS DE INVENTARIO (independientes de las categorías del POS)
    // =========================================================================

    public function storeCategoria(Request $request)
    {
        $request->merge(['nombre' => trim((string) $request->nombre)]);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_insumos,nombre',
        ], $this->mensajesCategoria());

        $categoria = CategoriaInsumo::create($validated);

        return response()->json(['success' => true, 'categoria' => $categoria], 201);
    }

    public function updateCategoria(Request $request, $id)
    {
        $categoria = CategoriaInsumo::findOrFail($id);

        $request->merge(['nombre' => trim((string) $request->nombre)]);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_insumos,nombre,' . $categoria->id,
        ], $this->mensajesCategoria());

        $categoria->update($validated);

        return response()->json(['success' => true, 'categoria' => $categoria]);
    }

    public function destroyCategoria($id)
    {
        $categoria = CategoriaInsumo::findOrFail($id);

        $enUso = $categoria->insumos()->where('esta_activo', true)->count();

        if ($enUso > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: {$enUso} artículo(s) usan esta categoría. Cámbiales la categoría primero.",
            ], 422);
        }

        // Los artículos dados de baja que la usaban quedan "sin categoría" (nullOnDelete).
        $categoria->delete();

        return response()->json(['success' => true]);
    }

    private function mensajesCategoria(): array
    {
        return [
            'nombre.required' => 'Escribe el nombre de la categoría.',
            'nombre.max'      => 'El nombre no puede pasar de 100 caracteres.',
            'nombre.unique'   => 'Ya existe una categoría con ese nombre.',
        ];
    }

    public function exportarPdfBajoStock()
    {
        if (!auth()->user()->tienePermiso('inventario.mostrar')) {
            return back()->with('error', 'No tienes permiso para generar reportes.');
        }

        $insumos = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')
                            ->where('esta_activo', true)
                            ->with('categoria:id,nombre')
                            ->get();

        $pdf = Pdf::loadView('admin.inventario.bajo_stock_pdf', compact('insumos'));

        return $pdf->download('Reporte_Bajo_Stock_' . date('Ymd_His') . '.pdf');
    }
}