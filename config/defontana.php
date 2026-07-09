<?php

/**
 * Integración con el ERP Defontana. La conexión real está pendiente
 * de que Multibatt obtenga credenciales de API; mientras tanto todo
 * el sistema consulta este config a través de App\Services\Erp\ErpClient,
 * de modo que al llegar el token baste con llenar el .env.
 */
return [

    // URL base de la API de Defontana (se define cuando exista contrato).
    'base_url' => env('DEFONTANA_BASE_URL', ''),

    // Credenciales de API entregadas por Defontana. NUNCA se commitean:
    // van solo en el .env de cada ambiente.
    'api_key' => env('DEFONTANA_API_KEY', ''),

    // Identificador de la empresa/base dentro de Defontana, si aplica.
    'company_id' => env('DEFONTANA_COMPANY_ID', ''),

    // Cada cuántos minutos se sincronizarán documentos cuando esté activo.
    'sync_interval_minutes' => env('DEFONTANA_SYNC_INTERVAL', 15),
];
