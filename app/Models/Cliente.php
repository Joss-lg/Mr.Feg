<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'status',
    ];

    /**
     * Relación: Un cliente tiene muchas direcciones (1 a N)
     */
    public function direcciones()
    {
        return $this->hasMany(Direccion::class);
    }
}