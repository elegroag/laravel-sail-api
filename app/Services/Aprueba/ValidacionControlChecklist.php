<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;

class ValidacionControlChecklist
{
    public const VARIANT_DEFAULT = 'default';

    public const VARIANT_EMPRESA = 'empresa';

    public const KEYS = [
        'guias',
        'adres',
        'ruaf',
        'cobertura_salud',
        'no_afiliado_otro_trabajador',
    ];

    public const KEYS_EMPRESA = [
        'camara_comercio',
        'paz_y_salvo',
        'aportes_empresa',
    ];

    /**
     * @return list<string>
     */
    public static function keysFor(string $variant = self::VARIANT_DEFAULT): array
    {
        return $variant === self::VARIANT_EMPRESA ? self::KEYS_EMPRESA : self::KEYS;
    }

    /**
     * @param  array<string, mixed>|string|null  $input
     * @return array<string, string>
     */
    public static function parse(array|string|null $input, string $variant = self::VARIANT_DEFAULT): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            $input = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($input)) {
            $input = [];
        }

        $flags = [];
        foreach (self::keysFor($variant) as $key) {
            $value = strtoupper((string) ($input[$key] ?? 'N'));
            $flags[$key] = $value === 'S' ? 'S' : 'N';
        }

        return $flags;
    }

    /**
     * @param  array<string, string>  $flags
     */
    public static function assertCompleto(array $flags, string $variant = self::VARIANT_DEFAULT): void
    {
        foreach (self::keysFor($variant) as $key) {
            if (($flags[$key] ?? 'N') !== 'S') {
                $mensaje = $variant === self::VARIANT_EMPRESA
                    ? 'Debe marcar todas las validaciones de control (Cámara de Comercio, paz y salvo y aportes de la empresa) para continuar.'
                    : 'Debe marcar todas las validaciones de control (GUIAS, ADRES, RUAF, cobertura en salud y no afiliado por otro trabajador) para continuar.';

                throw new DebugException($mensaje, 422);
            }
        }
    }

    /**
     * @param  array<string, string>  $flags
     * @return array{validaciones_control: array<string, string>}
     */
    public static function payload(array $flags): array
    {
        return [
            'validaciones_control' => $flags,
        ];
    }

    /**
     * Parsea, valida y limpia validaciones_control del postData para no contaminar
     * entidades ni nota_aprobar/observacion. Retorna el bloque a fusionar en params API.
     *
     * @param  array<string, mixed>  $postData
     * @return array{validaciones_control: array<string, string>}
     */
    public static function preparar(array &$postData, string $variant = self::VARIANT_DEFAULT): array
    {
        $flags = self::parse($postData['validaciones_control'] ?? null, $variant);
        self::assertCompleto($flags, $variant);

        unset($postData['validaciones_control']);

        return self::payload($flags);
    }

    /**
     * @param  array<string, mixed>  $params
     * @param  array{validaciones_control?: array<string, string>}  $payload
     * @return array<string, mixed>
     */
    public static function mergeIntoParams(array $params, array $payload): array
    {
        return array_merge($params, $payload);
    }
}
