<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Muestra el listado de clientes (Display a listing of the resource).
     */
    public function index()
    {
        // Usamos with('direcciones') para evitar el problema N+1 consultas
        // Filtramos solo los clientes activos (status = 1) si lo deseas
        $clientes = Cliente::with('direcciones')->where('status', 1)->paginate(10);
        
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente (Show the form for creating a new resource).
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Guarda el cliente y su dirección en la bd (Store a newly created resource in storage).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'apellido'   => 'nullable|string|max:255',
            'telefono'   => 'nullable|string|max:20',
            
            'calle'      => 'required|string|max:100',
            'manzana'    => 'nullable|string|max:100',
            'lote'       => 'nullable|string|max:100',
            'colonia'    => 'nullable|string|max:100',
            'referencia' => 'nullable|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $cliente = Cliente::create([
                    'nombre'   => $request->nombre,
                    'apellido' => $request->apellido,
                    'telefono' => $request->telefono,
                    'status'   => 1,
                ]);

                $cliente->direcciones()->create([
                    'calle'      => $request->calle,
                    'manzana'    => $request->manzana,
                    'lote'       => $request->lote,
                    'colonia'    => $request->colonia,
                    'referencia' => $request->referencia,
                    'status'     => 1,
                ]);
            });

            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente y dirección registrados correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Muestra un cliente específico (Display the specified resource).
     */
    public function show(Cliente $cliente)
    {
        // Cargamos las direcciones para mostrarlas en la vista de detalle
        $cliente->load('direcciones');
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Muestra el formulario para editar un cliente (Show the form for editing the specified resource).
     */
    public function edit(Cliente $cliente)
    {
        // Cargamos la primera dirección asociada para llenar el formulario de edición
        $direccion = $cliente->direcciones()->first();
        return view('clientes.edit', compact('cliente', 'direccion'));
    }

    /**
     * Actualiza el cliente y su dirección en la bd (Update the specified resource in storage).
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'apellido'   => 'nullable|string|max:255',
            'telefono'   => 'nullable|string|max:20',
            
            'calle'      => 'required|string|max:100',
            'manzana'    => 'nullable|string|max:100',
            'lote'       => 'nullable|string|max:100',
            'colonia'    => 'nullable|string|max:100',
            'referencia' => 'nullable|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($request, $cliente) {
                // Actualizamos los datos principales del cliente
                $cliente->update([
                    'nombre'   => $request->nombre,
                    'apellido' => $request->apellido,
                    'telefono' => $request->telefono,
                ]);

                // Buscamos la primera dirección asociada al cliente para actualizarla
                // Si el restaurante requiriera editar múltiples direcciones, la lógica cambiaría un poco,
                // pero para actualizar la principal (que llenaste en el create), esto es perfecto.
                $direccion = $cliente->direcciones()->first();
                
                if ($direccion) {
                    $direccion->update([
                        'calle'      => $request->calle,
                        'manzana'    => $request->manzana,
                        'lote'       => $request->lote,
                        'colonia'    => $request->colonia,
                        'referencia' => $request->referencia,
                    ]);
                } else {
                    // Por si por alguna razón el cliente no tenía dirección registrada, la creamos
                    $cliente->direcciones()->create([
                        'calle'      => $request->calle,
                        'manzana'    => $request->manzana,
                        'lote'       => $request->lote,
                        'colonia'    => $request->colonia,
                        'referencia' => $request->referencia,
                        'status'     => 1,
                    ]);
                }
            });

            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', 'Ocurrió un error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Elimina logicamente o físicamente un cliente (Remove the specified resource from storage).
     */
    public function destroy(Cliente $cliente)
    {
        try {
            // Opción A: Borrado Físico (Elimina el registro por completo de la bd)
            // Como configuraste onDelete('cascade') en la migración de direcciones, 
            // al eliminar el cliente, sus direcciones se borrarán solas.
            $cliente->delete();

            // Opción B: Borrado Lógico (Recomendado para sistemas de ventas/restaurantes)
            // Si prefieres no perder el historial, simplemente cambia el status a 0
            /*
            $cliente->update(['status' => 0]);
            */

            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente eliminado correctamente.');
                             
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al eliminar: ' . $e->getMessage());
        }
    }
}