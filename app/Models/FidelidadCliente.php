<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FidelidadCliente extends Model
{
    use HasFactory;

    protected $table = 'fidelidad_clientes';

    protected $fillable = [
        'cliente_id',
        'compras_acumuladas',
        'total_canjes_realizados'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}