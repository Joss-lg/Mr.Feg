<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    use HasFactory;

    protected $table = 'variantes';

    protected $fillable = [
        'producto_id',
        'nombre',
        'precio',
        'esta_disponible',
    ];

    // Relación con el producto padre
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con los modificadores/extras por variante (el flujo activo)
    public function modificadores()
    {
        return $this->hasMany(Modificador::class, 'variante_id');
    }

    // Relación con extras globales/pivote (opcional/esquema anterior)
    public function extras()
    {
        return $this->belongsToMany(Extra::class, 'extra_variante')
                    ->withPivot('precio_adicional');
    }
}