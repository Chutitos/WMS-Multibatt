<?php

namespace App\Console\Commands;

use App\Services\Erp\ErpClient;
use Illuminate\Console\Command;

class DefontanaSync extends Command
{
    protected $signature = 'defontana:sync';

    protected $description = 'Sincroniza documentos desde el ERP Defontana (pendiente de credenciales de API)';

    public function handle(ErpClient $erp): int
    {
        if (! $erp->estaConfigurado()) {
            $this->warn('Defontana no está configurado: define DEFONTANA_BASE_URL y DEFONTANA_API_KEY en el .env.');

            return self::FAILURE;
        }

        $resultado = $erp->sincronizarDocumentos();

        $this->info("Documentos importados: {$resultado['importados']}. {$resultado['mensaje']}");

        return self::SUCCESS;
    }
}
