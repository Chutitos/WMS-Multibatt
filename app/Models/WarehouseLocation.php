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
        'activa',
    ];

    protected $casts = [
        'pos_x' => 'integer',
        'pos_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'activa' => 'boolean',
    ];

    public function productLocations()
    {
        return $this->hasMany(ProductLocation::class);
    }
}
