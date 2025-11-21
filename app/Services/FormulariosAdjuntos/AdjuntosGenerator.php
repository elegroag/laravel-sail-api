<?php

namespace App\Services\FormulariosAdjuntos;

use App\Services\Utils\GuardarArchivoService;

class AdjuntosGenerator
{
    public static function generar($service, string $tipopc, $modelo, array $documentos): void
    {
        foreach ($documentos as $documento) {
            if (! isset($documento['method'], $documento['coddoc'])) {
                continue;
            }

            $method = $documento['method'];

            if (! method_exists($service, $method)) {
                continue;
            }

            $out = $service->{$method}()->getResult();

            (new GuardarArchivoService(
                [
                    'tipopc' => $tipopc,
                    'coddoc' => $documento['coddoc'],
                    'id' => $modelo->id,
                ]
            ))->salvarDatos($out);
        }
    }
}
