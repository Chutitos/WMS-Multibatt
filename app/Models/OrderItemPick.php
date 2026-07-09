<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemPick extends Model
{
    protected $fillable = [
        'order_item_id',
        'product_location_id',
        'warehouse_location_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productLocation()
    {
        return $this->belongsTo(ProductLocation::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }
}
