<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErpDocument extends Model
{
    protected $fillable = [
        'external_id',
        'tipo_documento',
        'numero_documento',
        'cliente_nombre',
        'rut_cliente',
        'fecha_documento',
        'estado_sync',
        'payload_json',
        'imported_by',
        'imported_at',
        'attempts',
        'last_error',
        'last_attempt_at',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'imported_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'payload_json' => 'array',
        'attempts' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(ErpDocumentItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
