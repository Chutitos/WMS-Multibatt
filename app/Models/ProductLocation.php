<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_location_id',
        'columna',
        'nivel',
        'lote',
        'fecha_ingreso',
        'cantidad',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'columna' => 'integer',
        'nivel' => 'integer',
        'cantidad' => 'integer',
    ];

    /**
     * Etiqueta legible del puesto físico dentro del rack, o null si la
     * existencia no tiene puesto asignado.
     */
    public function puesto(): ?string
    {
        if (! $this->columna || ! $this->nivel) {
            return null;
        }

        return "columna {$this->columna}, nivel {$this->nivel}";
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    /**
     * Una batería almacenada se descarga sola: pasados los meses de
     * recarga del producto (default 6) hay que recargarla o se sulfata.
     */
    public function necesitaRecarga(): bool
    {
        if ($this->cantidad <= 0) {
            return false;
        }

        $meses = $this->product->meses_recarga ?? 6;

        return $this->fecha_ingreso->lte(now()->subMonths($meses));
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
