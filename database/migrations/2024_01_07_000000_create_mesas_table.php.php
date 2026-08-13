<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('tipo', 20)->default('local');
            $table->unsignedBigInteger('plataforma_delivery_id')->nullable();
            $table->decimal('comision_porcentaje', 5, 2)->nullable();
            $table->decimal('comision_iva_porcentaje', 5, 2)->nullable();
            $table->integer('capacidad');
            $table->enum('estado', ['disponible', 'ocupada', 'reservada', 'limpieza'])->default('disponible');
            $table->decimal('total_consumo', 10, 2)->default(0);
            $table->string('seccion')->nullable();
            $table->enum('forma', ['redonda', 'cuadrada'])->default('redonda');
            $table->integer('posicion_x')->nullable();
            $table->integer('posicion_y')->nullable();
            $table->integer('ancho')->default(60);
            $table->integer('alto')->default(60);
            $table->string('zona')->nullable()->default('Salón');
            $table->timestamps();
            $table->foreignId('mesero_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};