<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelFidelidad extends Model
{
    use HasFactory;

    protected $table = 'niveles_fidelidad';

    protected $fillable = [
        'compras_requeridas',
        'monto_minimo',
        'premio_descripcion',
        'valor_premio',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}