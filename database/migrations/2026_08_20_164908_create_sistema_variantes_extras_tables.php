<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de Variantes (Los tamaños: 6pz, 10pz, 500ml)
        Schema::create('variantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('nombre');
            $table->decimal('precio', 10, 2);
            $table->boolean('esta_disponible')->default(true);
            $table->timestamps();
        });

        // 2. Tabla de Grupos de Extras (Ej. Salsas, Complementos, Cubiertas)
        Schema::create('grupos_extras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            // 'min_seleccion' en 1 obliga al cliente a elegir algo (ej. el sabor de la salsa)
            $table->integer('min_seleccion')->default(0); 
            $table->integer('max_seleccion')->default(1);
            $table->timestamps();
        });

        // 3. Tabla de Extras (Ej. BBQ, Búfalo, Con Papas, Panko)
        Schema::create('extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_extra_id')->constrained('grupos_extras')->onDelete('cascade');
            $table->string('nombre');
            $table->timestamps();
        });

        // 4. TABLA PIVOTE: Une Variante + Extra y define el precio (+19, +20, etc.)
        Schema::create('extra_variante', function (Blueprint $table) {
            $table->foreignId('variante_id')->constrained('variantes')->onDelete('cascade');
            $table->foreignId('extra_id')->constrained('extras')->onDelete('cascade');
            $table->decimal('precio_adicional', 10, 2)->default(0.00);
            
            $table->primary(['variante_id', 'extra_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // El orden del rollback debe ser inverso a la creación por las llaves foráneas
        Schema::dropIfExists('extra_variante');
        Schema::dropIfExists('extras');
        Schema::dropIfExists('grupos_extras');
        Schema::dropIfExists('variantes');
    }
};