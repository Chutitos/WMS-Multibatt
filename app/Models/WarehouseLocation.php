<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'pos_x',
        'pos_y',
        'width',
        'height',
        'columnas',
        'niveles',
        'activa',
    ];

    protected $casts = [
        'pos_x' => 'integer',
        'pos_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'columnas' => 'integer',
        'niveles' => 'integer',
        'activa' => 'boolean',
    ];

    public function productLocations()
    {
        return $this->hasMany(ProductLocation::class);
    }
}
