<?php

namespace App\Support;

use Carbon\Carbon;

class AfiliacionNormalizer
{
    /**
     * @param  array<string, string>  $titularesIndex  cedtra => nombre
     */
    public static function normalize(object $model, int $tipopc, array $config, array $titularesIndex = []): array
    {
        $tipdoc = self::tipdoc($model);
        $documento = self::documento($model, $tipopc, $config);
        $titularField = $config['titular_field'] ?? null;
        $cedtraTitular = $titularField ? trim((string) self::value($model, $titularField)) : '';

        $fecaprRaw = self::value($model, $config['afiliacion_field'] ?? 'fecapr');
        $fecestRaw = self::value($model, 'fecest');
        $estadoCodigo = strtoupper(trim((string) self::value($model, 'estado')));
        $esPendiente = in_array($estadoCodigo, ['P', 'T', 'D'], true);

        $fecaprFmt = self::formatFecha($fecaprRaw);
        $fecestFmt = self::formatFecha($fecestRaw);
        $fechaCierre = $esPendiente ? null : ($fecaprFmt ?? $fecestFmt);

        return [
            'tipopc' => $tipopc,
            'label' => $config['label'],
            'id' => self::value($model, 'id'),
            'fecsol' => self::formatFecha(self::value($model, 'fecsol')),
            'fecapr' => $esPendiente ? null : $fecaprFmt,
            'fecest' => $esPendiente ? null : $fecestFmt,
            'fecha_cierre' => $fechaCierre,
            'estado' => self::estadoDetalle($model),
            'estado_codigo' => self::value($model, 'estado'),
            'tipdoc' => $tipdoc,
            'documento' => $documento,
            'tipo_identificacion' => trim($tipdoc.' '.$documento),
            'cedula' => $documento,
            'nombre' => self::nombre($model),
            'nit' => self::nit($model),
            'razsoc' => self::razsoc($model),
            'cedtra' => self::value($model, 'cedtra'),
            'cedcon' => self::value($model, 'cedcon'),
            'cedtra_titular' => $cedtraTitular,
            'nombre_titular' => $cedtraTitular !== '' ? ($titularesIndex[$cedtraTitular] ?? '') : '',
            'ruuid' => self::value($model, 'ruuid'),
        ];
    }

    public static function documento(object $model, int $tipopc, array $config): string
    {
        return match ($tipopc) {
            1, 9, 10, 11 => (string) self::value($model, 'cedtra'),
            2 => (string) self::value($model, 'nit'),
            3 => (string) self::value($model, 'cedcon'),
            4 => (string) (self::value($model, 'numdoc') ?: self::value($model, 'documento')),
            default => (string) (self::value($model, $config['doc_field'] ?? 'documento') ?: self::value($model, 'documento')),
        };
    }

    public static function nombre(object $model): string
    {
        if (method_exists($model, 'getNombreCompleto')) {
            $nombre = trim((string) $model->getNombreCompleto());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        if (method_exists($model, 'getNombre')) {
            $nombre = trim((string) $model->getNombre());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        $partes = array_filter([
            self::value($model, 'priape'),
            self::value($model, 'segape'),
            self::value($model, 'prinom'),
            self::value($model, 'segnom'),
        ], fn ($parte) => $parte !== null && trim((string) $parte) !== '');

        $nombre = trim(implode(' ', $partes));
        if ($nombre !== '') {
            return $nombre;
        }

        // Empresa u otros: representante legal si no hay nombres desglosados.
        // No mezclar con razsoc (tiene columna propia) ni con partes ya usadas.
        return trim((string) (self::value($model, 'repleg') ?? ''));
    }

    public static function nit(object $model): string
    {
        return (string) (self::value($model, 'nit') ?? '');
    }

    public static function razsoc(object $model): string
    {
        if (method_exists($model, 'getRazsoc')) {
            return trim((string) $model->getRazsoc());
        }

        return trim((string) (self::value($model, 'razsoc') ?? ''));
    }

    public static function tipdoc(object $model): string
    {
        if (method_exists($model, 'getTipdoc')) {
            return trim((string) $model->getTipdoc());
        }

        return trim((string) (self::value($model, 'tipdoc') ?? 'CC'));
    }

    public static function fechaAfiliacion(object $model, array $config): ?string
    {
        $field = $config['afiliacion_field'] ?? 'fecapr';

        $value = self::value($model, $field);
        $fecha = self::formatFecha($value);
        if ($fecha !== null) {
            return $fecha;
        }

        $fecest = self::value($model, 'fecest');
        $fechaEst = self::formatFecha($fecest);
        if ($fechaEst !== null) {
            return $fechaEst;
        }

        return null;
    }

    public static function estadoDetalle(object $model): string
    {
        if (method_exists($model, 'getEstadoDetalle')) {
            return (string) $model->getEstadoDetalle();
        }

        $estado = self::value($model, 'estado');
        if (function_exists('estado_detalle_value') && is_string($estado) && $estado !== '') {
            return (string) (estado_detalle_value($estado) ?? $estado);
        }

        return (string) ($estado ?? '');
    }

    private static function value(object $model, string $field): mixed
    {
        $getter = 'get'.ucfirst($field);
        if (method_exists($model, $getter)) {
            try {
                $value = $model->{$getter}();
            } catch (\Throwable) {
                return null;
            }

            if ($value instanceof \DateTimeInterface) {
                return $value;
            }

            return $value;
        }

        return $model->{$field} ?? null;
    }

    private static function formatFecha(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
