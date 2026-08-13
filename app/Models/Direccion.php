<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    // Es buena práctica especificar el nombre de la tabla
    protected $table = 'direcciones';

    protected $fillable = [
        'cliente_id',
        'calle',
        'manzana',
        'lote',
        'colonia',
        'referencia',
        'status',
    ];

    /**
     * Relación: Una dirección pertenece a un cliente (Inversa)
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}