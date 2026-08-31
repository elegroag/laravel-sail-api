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

            $id = null;
            if (is_object($modelo)) {
                $id = $modelo->id ?? (method_exists($modelo, 'getId') ? $modelo->getId() : null);
            } elseif (is_array($modelo)) {
                $id = $modelo['id']
                    ?? ($modelo[1]['id'] ?? ($modelo[1]->id ?? (isset($modelo[0]) && is_object($modelo[0]) ? ($modelo[0]->id ?? null) : null)));
            }

            (new GuardarArchivoService(
                [
                    'tipopc' => $tipopc,
                    'coddoc' => $documento['coddoc'],
                    'id' => $id,
                ]
            ))->salvarDatos($out);
        }
    }
}
