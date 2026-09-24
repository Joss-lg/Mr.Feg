<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });

        // 1) Soltar la llave foránea que apuntaba a las categorías del POS.
        Schema::table('insumos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
        });

        // 2) Copiar a la tabla nueva las categorías que los insumos ya usaban.
        $insumos = DB::table('insumos')->whereNotNull('categoria_id')->get(['id', 'categoria_id']);

        if ($insumos->isNotEmpty()) {
            $nombresPos = DB::table('categorias')
                ->whereIn('id', $insumos->pluck('categoria_id')->unique()->all())
                ->pluck('nombre', 'id');

            $ahora = now();
            $idPorNombre = [];

            foreach ($insumos as $insumo) {
                $nombre = $nombresPos[$insumo->categoria_id] ?? null;

                if ($nombre === null) {
                    DB::table('insumos')->where('id', $insumo->id)->update(['categoria_id' => null]);
                    continue;
                }

                // Mismo nombre (sin importar mayúsculas) = misma categoría nueva.
                $clave = mb_strtolower(trim($nombre));
                $idPorNombre[$clave] ??= DB::table('categorias_insumos')->insertGetId([
                    'nombre'     => trim($nombre),
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);

                DB::table('insumos')->where('id', $insumo->id)->update(['categoria_id' => $idPorNombre[$clave]]);
            }
        }

        // 3) Nueva llave foránea hacia las categorías de inventario.
        Schema::table('insumos', function (Blueprint $table) {
            $table->foreign('categoria_id')->references('id')->on('categorias_insumos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('insumos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
        });

        // Los ids de categorias_insumos no existen en `categorias`: se limpian.
        DB::table('insumos')->update(['categoria_id' => null]);

        Schema::table('insumos', function (Blueprint $table) {
            $table->foreign('categoria_id')->references('id')->on('categorias')->nullOnDelete();
        });

        Schema::dropIfExists('categorias_insumos');
    }
};
