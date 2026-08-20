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
        Schema::create('detalles_orden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->onDelete('cascade');
            $table->string('lote_envio')->nullable();
            $table->foreignId('producto_id')->constrained('productos');
            $table->integer('cantidad');
            $table->decimal('gramaje', 8, 2)->nullable()->comment('Peso en gramos del producto');
            $table->decimal('precio_unitario', 10, 2);
            $table->string('estado');
            $table->unsignedTinyInteger('cuenta_division_numero')->nullable();
            $table->text('cancelado_motivo')->nullable();
            $table->unsignedBigInteger('cancelado_por')->nullable();
            $table->timestamp('cancelado_en')->nullable();
            $table->string('estado_preparacion')->default('pendiente');
            $table->unsignedBigInteger('transaccion_id')->nullable();
            $table->text('notas')->nullable();
            $table->string('tiempo')->nullable();
            $table->timestamps();

            $table->foreign('cancelado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_orden');
    }
};