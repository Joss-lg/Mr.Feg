<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoExtra extends Model
{
    use HasFactory;

    protected $table = 'grupos_extras';

    protected $fillable = [
        'nombre',
        'min_seleccion',
        'max_seleccion',
    ];

    // Un grupo (Ej. Salsas) tiene muchas opciones (Ej. BBQ, Búfalo)
    public function extras()
    {
        return $this->hasMany(Extra::class);
    }
}