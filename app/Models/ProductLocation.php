<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_location_id',
        'lote',
        'fecha_ingreso',
        'cantidad',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'cantidad' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    /**
     * Con existencia disponible, ordenado FIFO (el lote más antiguo primero).
     */
    public function scopeDisponibleFifo(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId)
            ->where('cantidad', '>', 0)
            ->orderBy('fecha_ingreso')
            ->orderBy('id');
    }
}
