<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'source_type',
        'source_reference',
        'cliente_nombre',
        'rut_cliente',
        'tipo_entrega',
        'estado',
        'observaciones',
        'creado_por',
        'liberado_por',
        'fecha_liberacion',
    ];

    protected $casts = [
        'fecha_liberacion' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function releaser()
    {
        return $this->belongsTo(User::class, 'liberado_por');
    }
}
