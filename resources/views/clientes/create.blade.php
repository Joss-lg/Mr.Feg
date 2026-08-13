@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight mb-6">
                Registrar Nuevo Cliente
            </h2>

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                    <strong>¡Ups! Hubo un problema con tus datos:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border border-gray-200 dark:border-gray-700">
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                    
                    <!-- SECCIÓN: DATOS DEL CLIENTE -->
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">1. Datos del Cliente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                        </div>

                        <!-- Apellido -->
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellido(s)</label>
                            <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" placeholder="Ej. 5512345678"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                        </div>
                    </div>

                    <!-- SECCIÓN: DIRECCIÓN -->
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">2. Dirección de Entrega</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        
                        <!-- Calle -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="calle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Calle y Número *</label>
                            <input type="text" name="calle" id="calle" value="{{ old('calle') }}" required placeholder="Ej. Av. Hidalgo 123"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                        </div>

                        <!-- Manzana -->
                        <div>
                            <label for="manzana" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manzana</label>
                            <input type="text" name="manzana" id="manzana" value="{{ old('manzana') }}" placeholder="Ej. Mz 45"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                        </div>

                        <!-- Lote -->
                        <div>
                            <label for="lote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lote</label>
                            <input type="text" name="lote" id="lote" value="{{ old('lote') }}" placeholder="Ej. Lt 12"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                        </div>

                        <!-- Colonia -->
                        <div>
                            <label for="colonia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Colonia</label>
                            <input type="text" name="colonia" id="colonia" value="{{ old('colonia') }}"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                        </div>

                        <!-- Referencia -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="referencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Referencia del Domicilio</label>
                            <textarea name="referencia" id="referencia" rows="2" placeholder="Ej. Casa verde de dos pisos, portón negro..."
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">{{ old('referencia') }}</textarea>
                        </div>
                    </div>

                    <!-- BOTONES -->
                    <div class="flex justify-end items-center mt-8 space-x-4">
                        <a href="{{ route('clientes.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 font-medium text-sm transition duration-150">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition duration-200">
                            Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection