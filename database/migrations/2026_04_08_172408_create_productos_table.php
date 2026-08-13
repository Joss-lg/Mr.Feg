<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Importante para usar DB::raw si fuera necesario

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);

            // Venta por peso (add_se_vende_por_peso_y_precio_por_100g)
            $table->boolean('se_vende_por_peso')->default(false);
            // Precio de referencia por cada 100g. Nullable porque solo aplica
            // cuando se_vende_por_peso = true. El precio final se calcula como
            // (precio_por_100g / 100) * gramos elegidos en el modal de Gramaje.
            $table->decimal('precio_por_100g', 10, 2)->nullable();

            $table->boolean('esta_disponible')->default(true);
            // Lógica de imágenes desactivada temporalmente.
            // $table->binary('imagen')->nullable()->comment('Los bytes binarios de la imagen');
            // $table->string('imagen_mime_type', 100)->nullable()->comment('Ej. image/jpeg, image/png');
            // ---------------------------------------------------------

            $table->softDeletes();
            $table->timestamps();
        });

        // Lógica de imágenes desactivada temporalmente.
        // DB::statement('ALTER TABLE productos MODIFY imagen LONGBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};