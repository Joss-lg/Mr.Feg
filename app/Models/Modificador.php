<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modificador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modificadores';

    protected $fillable = [
        'variante_id', // <-- Campo indispensable para asociarlo al tamaño
        'nombre', 
        'tipo',
        'precio',
        'esta_activo',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Variante (tamaño) a la que pertenece este modificador/extra.
     */
    public function variante()
    {
        return $this->belongsTo(Variante::class, 'variante_id');
    }

    /**
     * Platillos a los que está asociado directamente (si es extra global).
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_modificadores');
    }
}