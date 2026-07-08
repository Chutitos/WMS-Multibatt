<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'source_type',
        'source_reference',
        'erp_document_id',
        'cliente_nombre',
        'rut_cliente',
        'tipo_entrega',
        'estado',
        'observaciones',
        'creado_por',
        'liberado_por',
        'fecha_liberacion',
        'retirado_por_nombre',
        'retirado_por_rut',
        'transportista',
        'guia_despacho',
    ];

    protected $casts = [
        'fecha_liberacion' => 'datetime',
        'estado' => OrderStatus::class,
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

    public function events()
    {
        // Orden por id (no por created_at): dos transiciones dentro del
        // mismo segundo empatan en el timestamp y quedarían en orden
        // indeterminado si solo se ordenara por fecha.
        return $this->hasMany(OrderEvent::class)->latest('id');
    }

    public function erpDocument()
    {
        return $this->belongsTo(ErpDocument::class);
    }

    public function canBeCancelledBy(User $user): bool
    {
        if (! $this->estado->canTransitionTo(OrderStatus::CANCELADO)) {
            return false;
        }

        return match ($user->role->name) {
            'admin' => true,
            'jefe_bodega' => in_array($this->estado, [OrderStatus::CREADO, OrderStatus::LIBERADO], true),
            default => false,
        };
    }
}
