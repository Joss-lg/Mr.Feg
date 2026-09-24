<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Categorías propias del inventario (insumos).
 *
 * Son independientes de las categorías del punto de venta (tabla `categorias`,
 * modelo Categoria), que agrupan los productos del menú.
 */
class CategoriaInsumo extends Model
{
    protected $table = 'categorias_insumos';

    protected $fillable = ['nombre'];

    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'categoria_id');
    }
}
