<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La columna puede existir ya en bases de datos donde se agregó a mano.
        if (Schema::hasColumn('ordenes', 'referencia_pago')) {
            return;
        }

        Schema::table('ordenes', function (Blueprint $table) {
            $table->string('referencia_pago')->nullable()->after('metodo_pago');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('ordenes', 'referencia_pago')) {
            return;
        }

        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn('referencia_pago');
        });
    }
};