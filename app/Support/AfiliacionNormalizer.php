<?php

namespace App\Support;

use Carbon\Carbon;

class AfiliacionNormalizer
{
    public static function normalize(object $model, int $tipopc, array $config): array
    {
        $tipdoc = self::tipdoc($model);
        $documento = self::documento($model, $tipopc, $config);

        return [
            'tipopc' => $tipopc,
            'label' => $config['label'],
            'id' => self::value($model, 'id'),
            'fecsol' => self::formatFecha(self::value($model, 'fecsol')),
            'fecapr' => self::fechaAfiliacion($model, $config),
            'sat_fecapr' => self::fechaRegistroSisu($model, $config),
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
            self::value($model, 'razsoc'),
            self::value($model, 'repleg'),
        ], fn ($parte) => $parte !== null && $parte !== '');

        return trim(implode(' ', $partes));
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

        return self::formatFecha(self::value($model, $field));
    }

    public static function fechaRegistroSisu(object $model, array $config): ?string
    {
        if (! ($config['has_sat_fecapr'] ?? false)) {
            return self::formatFecha(self::value($model, 'fecapr'));
        }

        return self::formatFecha(self::value($model, 'sat_fecapr') ?: self::value($model, 'fecapr'));
    }

    public static function estadoDetalle(object $model): string
    {
        if (method_exists($model, 'getEstadoDetalle')) {
            return (string) $model->getEstadoDetalle();
        }

        return (string) (self::value($model, 'estado') ?? '');
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
