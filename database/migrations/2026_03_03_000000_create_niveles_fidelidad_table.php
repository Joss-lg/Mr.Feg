<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('niveles_fidelidad', function (Blueprint $table) {
            $table->id();
            $table->integer('compras_requeridas'); // Ej: 5, 8, 15
            $table->decimal('monto_minimo', 10, 2)->default(150.00); // El valor mínimo por compra ($150)
            $table->string('premio_descripcion'); // Ej: "Papas a la francesa chicas", "Frappé sencillo"
            $table->decimal('valor_premio', 10, 2)->default(0.00); // Ej: 65.00 o 0 si es cortesía
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveles_fidelidad');
    }
};