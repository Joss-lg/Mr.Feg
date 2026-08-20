<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel recomienda nombres de tablas en minúsculas y plural
        Schema::create('clientes', function (Blueprint $table) {
            $table->id(); // Crea un BigInteger autoincrementable (El estándar moderno de Laravel)
            $table->string('nombre', 255);
            $table->string('apellido', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->tinyInteger('status')->default(1); // tinyInteger es más ligero que integer y perfecto para estatus (0, 1, 2)
            $table->timestamps(); // Altamente recomendado dejarlo para saber cuándo se registró un cliente
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};