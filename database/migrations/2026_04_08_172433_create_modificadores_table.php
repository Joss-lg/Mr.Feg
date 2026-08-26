<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modificadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variante_id')->nullable();
            $table->string('nombre');
            $table->enum('tipo', ['opcion', 'extra', 'quitar'])->default('extra');
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modificadores');
    }
};