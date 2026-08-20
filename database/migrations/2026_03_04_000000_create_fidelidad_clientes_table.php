<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fidelidad_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->integer('compras_acumuladas')->default(0); // Sella cuántas compras válidas lleva
            $table->integer('total_canjes_realizados')->default(0); // Historial de premios ganados
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fidelidad_clientes');
    }
};