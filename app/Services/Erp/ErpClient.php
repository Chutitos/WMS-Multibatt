<?php

namespace App\Services\Erp;

/**
 * Contrato del cliente ERP. Todo lo que el WMS necesite del ERP pasa
 * por aquí: cuando llegue el acceso real a Defontana se implementa
 * este contrato con HTTP y el resto del sistema no cambia.
 */
interface ErpClient
{
    /**
     * ¿Hay credenciales configuradas en el ambiente?
     */
    public function estaConfigurado(): bool;

    /**
     * Intenta contactar al ERP y reporta el resultado de forma legible.
     *
     * @return array{ok: bool, mensaje: string}
     */
    public function probarConexion(): array;

    /**
     * Descarga documentos de venta nuevos desde el ERP y los deja en
     * erp_documents (idempotente vía external_id único).
     *
     * @return array{importados: int, mensaje: string}
     */
    public function sincronizarDocumentos(): array;
}
