<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NivelFidelidad;

class NivelFidelidadSeeder extends Seeder
{
    public function run(): void
    {
        NivelFidelidad::insert([
            ['compras_requeridas' => 5, 'monto_minimo' => 150.00, 'premio_descripcion' => 'Papas a la francesa chicas', 'valor_premio' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['compras_requeridas' => 8, 'monto_minimo' => 150.00, 'premio_descripcion' => 'Frappé o malteada', 'valor_premio' => 65.00, 'created_at' => now(), 'updated_at' => now()],
            ['compras_requeridas' => 15, 'monto_minimo' => 150.00, 'premio_descripcion' => 'Alitas o boneless chicos con papas', 'valor_premio' => 0.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}