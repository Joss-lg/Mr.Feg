<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    use HasFactory;

    protected $fillable = [
        'grupo_extra_id',
        'nombre',
    ];

    // A qué grupo pertenece (Ej. BBQ pertenece a Salsas)
    public function grupo()
    {
        return $this->belongsTo(GrupoExtra::class, 'grupo_extra_id');
    }

    // La relación inversa con las variantes (opcional, pero útil si quieres 
    // saber en qué tamaños está disponible un extra en específico)
    public function variantes()
    {
        return $this->belongsToMany(Variante::class, 'extra_variante')
                    ->withPivot('precio_adicional');
    }
}