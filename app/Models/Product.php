<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Tipos de batería que maneja la bodega. La clave se guarda en BD,
     * el valor es la etiqueta visible.
     *
     * @var array<string, string>
     */
    public const TIPOS = [
        'auto' => 'Auto',
        'camioneta' => 'Camioneta / 4x4',
        'camion' => 'Camión / Bus',
        'moto' => 'Moto',
        'nautica' => 'Náutica',
        'solar' => 'Solar / Ciclo profundo',
        'industrial' => 'Industrial',
        'otro' => 'Otro',
    ];

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'marca',
        'tipo',
        'voltaje',
        'capacidad_ah',
        'stock_minimo',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'capacidad_ah' => 'integer',
        'stock_minimo' => 'integer',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productLocations()
    {
        return $this->hasMany(ProductLocation::class);
    }

    public function tipoLabel(): ?string
    {
        return $this->tipo ? (self::TIPOS[$this->tipo] ?? ucfirst($this->tipo)) : null;
    }

    /**
     * Resumen técnico corto para mostrar junto al nombre: "12V · 75Ah · Bosch".
     */
    public function fichaCorta(): ?string
    {
        $partes = array_filter([
            $this->voltaje,
            $this->capacidad_ah ? "{$this->capacidad_ah}Ah" : null,
            $this->marca,
        ]);

        return $partes === [] ? null : implode(' · ', $partes);
    }

    /**
     * Existencia física total registrada en la bodega (capa física local,
     * no el stock contable del ERP).
     */
    public function existenciaFisica(): int
    {
        return (int) $this->productLocations()->sum('cantidad');
    }

    public function bajoStockMinimo(): bool
    {
        return $this->stock_minimo > 0 && $this->existenciaFisica() < $this->stock_minimo;
    }
}
