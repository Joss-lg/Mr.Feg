<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Mesa extends Model
{
    use HasFactory, SoftDeletes;

    // --- Definición de Constantes ---
    const ESTADO_DISPONIBLE = 'disponible';
    const ESTADO_OCUPADA = 'ocupada';

    const TIPO_LOCAL = 'local';
    const TIPO_DELIVERY = 'delivery';
    const TIPO_VIRTUAL = 'virtual';

    protected $table = 'mesas';

    protected $fillable = [
        'numero', 
        'capacidad', 
        'estado', 
        'seccion', 
        'zona',
        'forma',
        'posicion_x', 
        'posicion_y',
        'ancho',
        'alto',
        'mesero_id',
        'total_consumo',
        'tipo',
        'plataforma_delivery_id',
        'comision_porcentaje',
        'comision_iva_porcentaje',
    ];

    // Relación con Mesero
    public function mesero()
    {
        return $this->belongsTo(User::class, 'mesero_id');
    }

    // Relación con la plataforma de delivery (Rappi/Uber/DiDi), si aplica
    public function plataformaDelivery()
    {
        return $this->belongsTo(PlataformaDelivery::class, 'plataforma_delivery_id');
    }

    public function esDelivery(): bool
    {
        return $this->tipo === self::TIPO_DELIVERY;
    }

    public function esParaLlevar(): bool
    {
        $ordenActiva = $this->ordenesActivas()->first();
        if ($ordenActiva && $ordenActiva->tipo_pedido) {
            return strtolower($ordenActiva->tipo_pedido) === 'llevar';
        }

        return str_starts_with(strtoupper((string)($this->numero ?? '')), 'LLEVAR-');
    }

    public function esADomicilio(): bool
    {
        $ordenActiva = $this->ordenesActivas()->first();
        if ($ordenActiva && $ordenActiva->tipo_pedido) {
            return strtolower($ordenActiva->tipo_pedido) === 'domicilio';
        }

        return str_starts_with(strtoupper((string)($this->numero ?? '')), 'DOM-');
    }

    public function getNombreVisualAttribute(): string
    {
        $num = (string)($this->numero ?? 'S/N');

        // Apps de Delivery externas (DiDi, Uber, Rappi)
        if ($this->esDelivery()) {
            return strtoupper($this->plataformaDelivery->nombre ?? 'DELIVERY');
        }

        // Para Llevar
        if ($this->esParaLlevar()) {
            $nombre = preg_replace('/^LLEVAR-/i', '', $num);
            return preg_replace('/-\d+$/', '', $nombre);
        }

        // A Domicilio propio
        if ($this->esADomicilio()) {
            $nombre = preg_replace('/^DOM-/i', '', $num);
            return preg_replace('/-\d+$/', '', $nombre);
        }

        // Comedor / Mesas físicas
        return str_starts_with(strtolower($num), 'mesa') 
            ? strtoupper($num) 
            : 'MESA ' . strtoupper($num);
    }

    public function scopeSoloLocales($query)
    {
        return $query->where(function ($q) {
            $q->where('tipo', self::TIPO_LOCAL)->orWhereNull('tipo');
        });
    }

    // Relación con Órdenes
    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'mesa_id');
    }

    // Relación con Órdenes activas
    public function ordenesActivas()
    {
        return $this->ordenes()
            ->whereIn('ordenes.estado', ['pendiente', 'en proceso', 'servida'])
            ->whereNull('ordenes.deleted_at');
    }

    // Relación con las partes de la cuenta cuando la mesa está dividida
    public function cuentasDivision()
    {
        return $this->hasMany(CuentaDivision::class, 'mesa_id');
    }

    // Partes de la división que aún no se han cobrado
    public function cuentasDivisionPendientes()
    {
        return $this->cuentasDivision()->where('estado', 'pendiente');
    }

    public function getTieneDivisionActivaAttribute(): bool
    {
        return $this->cuentasDivision()->exists();
    }

    public function getTotalConsumoAttribute()
    {
        $total = $this->ordenesActivas->sum(function($orden) {
            return $orden->detalles->sum(function($detalle) {
                return $detalle->cantidad * $detalle->precio_unitario;
            });
        });

        return round($total * 1.16, 2);
    }

    public function getProductosAttribute()
    {
        return $this->ordenesActivas()
            ->with('detalles.producto')
            ->get()
            ->flatMap(function ($orden) {
                return $orden->detalles;
            });
    }

    public function getNumeroProductosPendientesAttribute()
    {
        return $this->getProductosAttribute()->where('estado', '!=', 'entregado')->count();
    }

    public function getEstadoVisualAttribute()
    {
        if ($this->estado === self::ESTADO_DISPONIBLE) {
            return 'blue'; 
        }

        $ordenActiva = $this->ordenesActivas()->latest()->first();
        if (!$ordenActiva) {
            return 'blue';
        }

        $tiempoDesdeCreacion = now()->diffInMinutes($ordenActiva->created_at);
        
        if ($tiempoDesdeCreacion < 30) {
            return 'blue'; 
        } elseif ($tiempoDesdeCreacion < 60) {
            return 'yellow'; 
        } else {
            return 'red'; 
        }
    }

/**
    * Determina si es una mesa virtual generada al vuelo para pedidos
     * rápidos/delivery/llevar (las cuales sí deben eliminarse al cobrar).
     * Las mesas físicas del salón (con coordenadas o tipo 'local') nunca son virtuales.
     */
    public function esMesaVirtual(): bool
    {
        // El tipo explícito es la fuente de verdad: una mesa del salón
        // nunca se elimina al cobrar, aunque su número tenga un prefijo.
        if ($this->tipo === self::TIPO_LOCAL) {
            return false;
        }

        if (in_array($this->tipo, [self::TIPO_DELIVERY, self::TIPO_VIRTUAL], true)) {
            return true;
        }

        // Compatibilidad con registros antiguos creados sin tipo explícito.
        $numero = strtoupper((string)($this->numero ?? ''));

        // 2. Si el número inicia con prefijos de pedidos rápidos/temporales
        if (
            str_starts_with($numero, 'LLEVAR-') ||
            str_starts_with($numero, 'DOM-') ||
            str_starts_with($numero, 'DEL-') ||
            str_starts_with($numero, 'RAPIDO-') ||
            str_starts_with($numero, 'PEDIDO-')
        ) {
            return true;
        }

        // 3. Mesas físicas del plano (tienen posiciones asignadas o tipo 'local')
        return false;
    }

}