<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            
            // foreignId() es la forma moderna y segura de relacionar tablas en Laravel
            $table->foreignId('cliente_id')
                  ->constrained('clientes')
                  ->onDelete('cascade'); 
            
            $table->string('calle', 100);
            $table->string('manzana', 100)->nullable(); // Excelentes campos para direcciones en México
            $table->string('lote', 100)->nullable();
            $table->string('colonia', 100)->nullable();
            $table->string('referencia', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};