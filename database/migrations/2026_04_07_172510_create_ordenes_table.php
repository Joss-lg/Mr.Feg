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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden')->unique();
            
            // --- DATOS DEL LUGAR Y PERSONAL ---
            $table->foreignId('mesa_id')->nullable()->constrained('mesas')->onDelete('set null');
            $table->foreignId('mesero_id')->constrained('users');
            $table->foreignId('capitan_id')->nullable()->constrained('users');

            // Personas en la mesa
            $table->unsignedInteger('personas')->nullable();

            // Estado principal de la orden (pendiente, en proceso, servida, pagada)
            $table->string('estado'); 

            // --- NUEVOS CAMPOS: MÓDULO DE REPARTIDORES Y CLIENTES ---
            $table->string('tipo_pedido')->default('comedor'); // comedor, llevar, domicilio
            
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones')->nullOnDelete();
            
            $table->string('estado_reparto')->nullable(); // pendiente, en_camino, entregado
            $table->foreignId('repartidor_id')->nullable()->constrained('users')->nullOnDelete();
            // ----------------------------------------------------------

            // --- CANCELACIONES ---
            $table->string('cancelada_motivo', 255)->nullable();
            $table->foreignId('cancelada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelada_en')->nullable()->default(null);
            $table->decimal('monto_cancelado', 10, 2)->nullable()->default(null);

            // --- CAMPOS FINANCIEROS ---
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('propina', 10, 2)->default(0);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->string('metodo_pago')->nullable(); // efectivo, tarjeta, transferencia

            // --- DIVISIÓN DE CUENTAS ---
            $table->boolean('cuenta_dividida')->default(false);
            $table->integer('numero_cuenta_division')->nullable();
            $table->integer('total_cuentas_division')->nullable();

            // --- TIEMPOS Y SOFT DELETES ---
            $table->timestamp('abierta_el')->nullable();
            $table->timestamp('cerrada_el')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};