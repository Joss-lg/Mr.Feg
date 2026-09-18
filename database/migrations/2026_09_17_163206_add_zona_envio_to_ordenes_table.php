<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->unsignedTinyInteger('zona_envio')->nullable()->after('direccion_id');
            $table->decimal('costo_envio', 8, 2)->default(0)->after('zona_envio');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn(['zona_envio', 'costo_envio']);
        });
    }
};