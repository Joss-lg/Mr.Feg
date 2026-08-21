<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'nombre',
        'precio',
        'esta_disponible',
    ];

    // Relación con el producto padre
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Relación de muchos a muchos con los extras.
    // Aquí usamos withPivot para traernos el 'precio_adicional' (+19, +20, etc.)
    public function extras()
    {
        return $this->belongsToMany(Extra::class, 'extra_variante')
                    ->withPivot('precio_adicional');
    }
}