<?php

namespace App\Services\Erp;

/**
 * Implementación para Defontana. Hoy es un esqueleto honesto: valida
 * la configuración y responde con claridad que la conexión real está
 * pendiente de credenciales. Cuando Multibatt reciba el acceso a la
 * API, este archivo es el ÚNICO lugar donde se implementa el HTTP.
 */
class DefontanaClient implements ErpClient
{
    public function estaConfigurado(): bool
    {
        return config('defontana.base_url') !== ''
            && config('defontana.api_key') !== '';
    }

    public function probarConexion(): array
    {
        if (! $this->estaConfigurado()) {
            return [
                'ok' => false,
                'mensaje' => 'Faltan credenciales: define DEFONTANA_BASE_URL y DEFONTANA_API_KEY en el archivo .env.',
            ];
        }

        return [
            'ok' => false,
            'mensaje' => 'Credenciales presentes, pero la conexión HTTP con Defontana aún no está implementada (pendiente de contrato de API).',
        ];
    }

    public function sincronizarDocumentos(): array
    {
        if (! $this->estaConfigurado()) {
            return [
                'importados' => 0,
                'mensaje' => 'Sin credenciales configuradas: no hay nada que sincronizar.',
            ];
        }

        return [
            'importados' => 0,
            'mensaje' => 'La sincronización con Defontana aún no está implementada.',
        ];
    }
}
