@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight mb-6">
                Listado de Clientes
            </h2>

            <!-- Mensajes de sesión -->
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-700">Directorio de Clientes</h3>
                    
                    <!-- Protegemos el botón de crear según el rol -->
                    @if(auth()->user()->tienePermiso(14, 'crear'))
                        <a href="{{ route('clientes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition duration-200">
                            + Nuevo Cliente
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 border-collapse">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nombre</th>
                                <th scope="col" class="px-6 py-3">Teléfono</th>
                                <th scope="col" class="px-6 py-3">Dirección Principal</th>
                                <th scope="col" class="px-6 py-3">Estatus</th>
                                <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes as $cliente)
                                <tr class="bg-white border-b hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $cliente->nombre }} {{ $cliente->apellido }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $cliente->telefono ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($cliente->direcciones->count() > 0)
                                            {{ $cliente->direcciones->first()->calle }}
                                            @if($cliente->direcciones->first()->colonia)
                                                , {{ $cliente->direcciones->first()->colonia }}
                                            @endif
                                        @else
                                            <span class="text-gray-400 italic">Sin dirección</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($cliente->status == 1)
                                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Activo</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-3 flex justify-center">
                                        
                                        <!-- Protegemos el botón de editar -->
                                        @if(auth()->user()->tienePermiso(14, 'editar'))
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                        @endif

                                        <!-- Protegemos el botón de eliminar y aplicamos la clase para SweetAlert2 -->
                                        @if(auth()->user()->tienePermiso(14, 'eliminar'))
                                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline-block form-eliminar">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                                            </form>
                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 py-8">
                                        No hay clientes registrados en el sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Script de SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Seleccionamos todos los formularios que tengan la clase 'form-eliminar'
            const formsEliminar = document.querySelectorAll('.form-eliminar');
            
            formsEliminar.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Detenemos el envío automático del formulario
                    
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Toda la información del cliente y sus direcciones se eliminarán de forma permanente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444', // Color rojo (red-500)
                        cancelButtonColor: '#6b7280',  // Color gris (gray-500)
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si el usuario confirma, enviamos el formulario manualmente
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection