<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErpDocument;
use App\Services\Erp\ErpClient;

class ErpIntegrationController extends Controller
{
    public function index(ErpClient $erp)
    {
        $configurado = $erp->estaConfigurado();
        $conexion = $erp->probarConexion();

        $documentos = ErpDocument::with('importer')
            ->latest()
            ->paginate(15);

        return view('erp.index', compact('configurado', 'conexion', 'documentos'));
    }
}
