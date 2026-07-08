<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErpDocumentItem extends Model
{
    protected $fillable = [
        'erp_document_id',
        'producto_codigo',
        'producto_nombre',
        'cantidad',
    ];

    public function document()
    {
        return $this->belongsTo(ErpDocument::class, 'erp_document_id');
    }
}
