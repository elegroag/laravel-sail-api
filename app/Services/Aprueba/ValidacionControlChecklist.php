<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;

class ValidacionControlChecklist
{
    public const KEYS = [
        'guias',
        'adres',
        'ruaf',
        'cobertura_salud',
        'no_afiliado_otro_trabajador',
    ];

    public const LABELS = [
        'guias' => 'GUIAS',
        'adres' => 'ADRES',
        'ruaf' => 'RUAF',
        'cobertura_salud' => 'Cobertura en salud / pensionado',
        'no_afiliado_otro_trabajador' => 'No afiliado a esta Caja por otro trabajador',
    ];

    /**
     * @param  array<string, mixed>|string|null  $input
     * @return array<string, string>
     */
    public static function parse(array|string|null $input): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            $input = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($input)) {
            $input = [];
        }

        $flags = [];
        foreach (self::KEYS as $key) {
            $value = strtoupper((string) ($input[$key] ?? 'N'));
            $flags[$key] = $value === 'S' ? 'S' : 'N';
        }

        return $flags;
    }

    /**
     * @param  array<string, string>  $flags
     */
    public static function assertCompleto(array $flags): void
    {
        foreach (self::KEYS as $key) {
            if (($flags[$key] ?? 'N') !== 'S') {
                throw new DebugException(
                    'Debe marcar todas las validaciones de control (GUIAS, ADRES, RUAF, cobertura en salud y no afiliado por otro trabajador) para continuar.',
                    422
                );
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
     * Parsea, valida, anexa texto a nota_aprobar y limpia el campo del postData
     * para no contaminar entidades. Retorna el bloque a fusionar en params API.
     *
     * @param  array<string, mixed>  $postData
     * @return array{validaciones_control: array<string, string>}
     */
    public static function preparar(array &$postData): array
    {
        $flags = self::parse($postData['validaciones_control'] ?? null);
        self::assertCompleto($flags);

        $nota = trim((string) ($postData['nota_aprobar'] ?? ''));
        $auditoria = self::textoAuditoria($flags);
        $postData['nota_aprobar'] = $nota === '' ? $auditoria : $nota."\n".$auditoria;

        unset($postData['validaciones_control']);

        return self::payload($flags);
    }

    /**
     * @param  array<string, string>  $flags
     */
    public static function textoAuditoria(array $flags): string
    {
        $partes = [];
        foreach (self::KEYS as $key) {
            $estado = ($flags[$key] ?? 'N') === 'S' ? 'S' : 'N';
            $partes[] = self::LABELS[$key].': '.$estado;
        }

        return 'Validaciones de control: '.implode('; ', $partes);
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
