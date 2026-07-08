<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLocationEvent extends Model
{
    protected $fillable = [
        'product_location_id',
        'user_id',
        'accion',
        'detalle',
    ];

    public function productLocation()
    {
        return $this->belongsTo(ProductLocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
