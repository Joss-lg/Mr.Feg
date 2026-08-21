<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modificador extends Model
{
    use HasFactory;

    // Le decimos a Laravel exactamente cómo se llama tu tabla
    protected $table = 'modificadores';

    // Los campos permitidos para asignación masiva
    protected $fillable = [
        'nombre', 
        'precio',
        'esta_activo',
    ];

    // ==========================================
    // RELACIÓN INVERSA
    // ==========================================
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_modificadores');
    }
}