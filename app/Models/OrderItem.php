<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'producto_codigo',
        'producto_nombre',
        'cantidad_solicitada',
        'cantidad_confirmada',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}