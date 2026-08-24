<?php

namespace App\Http\Controllers;

use App\Models\NivelFidelidad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NivelFidelidadController extends Controller
{
    /**
     * Listado de niveles de fidelidad (tarjetas), ordenados por compras requeridas.
     */
    public function index()
    {
        $niveles = NivelFidelidad::orderBy('compras_requeridas', 'asc')->get();

        return view('admin.fidelidad.index', compact('niveles'));
    }

    /**
     * No se usa como página independiente (el alta se hace por modal desde index),
     * pero se deja la ruta viva por si se navega directo a ella.
     */
    public function create()
    {
        return redirect()->route('admin.fidelidad.index');
    }

    /**
     * Guardar un nivel nuevo (llamado por fetch desde el modal de creación).
     */
    public function store(Request $request)
    {
        $validated = $this->validarDatos($request);

        NivelFidelidad::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.fidelidad.index')
            ->with('success', 'Nivel de fidelidad creado correctamente.');
    }

    /**
     * Devuelve los datos del nivel en JSON para precargar el modal de edición.
     */
    public function edit(NivelFidelidad $fidelidad)
    {
        if (request()->wantsJson()) {
            return response()->json(['nivel' => $fidelidad]);
        }

        return redirect()->route('admin.fidelidad.index');
    }

    /**
     * Actualizar un nivel existente (llamado por fetch desde el modal de edición).
     */
    public function update(Request $request, NivelFidelidad $fidelidad)
    {
        $validated = $this->validarDatos($request, $fidelidad->id);

        $fidelidad->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.fidelidad.index')
            ->with('success', 'Nivel de fidelidad actualizado correctamente.');
    }

    /**
     * Activar / desactivar un nivel (switch en la tarjeta, vía fetch PATCH).
     */
    public function toggleActivo(NivelFidelidad $fidelidad)
    {
        $fidelidad->update([
            'activo' => ! $fidelidad->activo,
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'activo' => $fidelidad->activo,
            ]);
        }

        return redirect()
            ->route('admin.fidelidad.index')
            ->with('success', 'Estado del nivel actualizado.');
    }

    /**
     * Eliminar un nivel (submit normal de formulario desde el modal de confirmación).
     */
    public function destroy(NivelFidelidad $fidelidad)
    {
        $fidelidad->delete();

        return redirect()
            ->route('admin.fidelidad.index')
            ->with('success', 'Nivel de fidelidad eliminado.');
    }

    /**
     * Reglas de validación compartidas entre store() y update().
     */
    private function validarDatos(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'compras_requeridas' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('niveles_fidelidad', 'compras_requeridas')->ignore($ignorarId),
            ],
            'monto_minimo' => ['required', 'numeric', 'min:0'],
            'premio_descripcion' => ['required', 'string', 'max:255'],
            'valor_premio' => ['required', 'numeric', 'min:0'],
        ]);
    }
}