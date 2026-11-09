<?php

namespace App\Services\Ecommerce;

/**
 * Clasifica el mensaje de rechazo de Movil/guardar-venta
 * para detectar vencimiento por cupos o plazo (criterio 1a).
 */
class VencimientoSubsidioDetector
{
    /**
     * @return array{vencida: bool, motivo: ?string}
     */
    public function clasificar(?string $mensaje): array
    {
        $texto = mb_strtolower(trim((string) $mensaje));
        if ($texto === '') {
            return ['vencida' => false, 'motivo' => null];
        }

        if ($this->esSinCupos($texto)) {
            return [
                'vencida' => true,
                'motivo' => EstadoPrecompra::MOTIVO_VENCIDA_SIN_CUPOS,
            ];
        }

        if ($this->esFueraDePlazo($texto)) {
            return [
                'vencida' => true,
                'motivo' => EstadoPrecompra::MOTIVO_VENCIDA_PLAZO,
            ];
        }

        return ['vencida' => false, 'motivo' => null];
    }

    public function esVencimiento(?string $mensaje): bool
    {
        return $this->clasificar($mensaje)['vencida'] === true;
    }

    protected function esSinCupos(string $texto): bool
    {
        $patrones = [
            'sin cupo',
            'sin cupos',
            'no hay cupo',
            'no hay cupos',
            'cupos agotados',
            'cupo agotado',
            'cupos insuficientes',
            'cupo insuficiente',
            'no dispone de cupo',
            'no dispone de cupos',
            'sin disponibilidad de cupo',
        ];

        foreach ($patrones as $patron) {
            if (str_contains($texto, $patron)) {
                return true;
            }
        }

        return (bool) preg_match('/\bcupos?\b.*(agot|insuf|disponib|cero|0\b)/u', $texto)
            || (bool) preg_match('/(agot|insuf|disponib).*\bcupos?\b/u', $texto);
    }

    protected function esFueraDePlazo(string $texto): bool
    {
        $patrones = [
            'fuera de plazo',
            'fuera de fecha',
            'plazo vencido',
            'plazo expirado',
            'fecha vencida',
            'fecha expirada',
            'periodo cerrado',
            'período cerrado',
            'inscripcion cerrada',
            'inscripción cerrada',
            'registro cerrado',
            'venta cerrada',
            'servicio cerrado',
            'fuera del rango de fechas',
            'no esta vigente',
            'no está vigente',
            'ya no admite',
            'no admite ventas',
            'no admite registro',
        ];

        foreach ($patrones as $patron) {
            if (str_contains($texto, $patron)) {
                return true;
            }
        }

        return (bool) preg_match('/\b(plazo|fecha|periodo|período)\b.*(venc|expir|cerrad|fuera)/u', $texto)
            || (bool) preg_match('/(venc|expir|cerrad|fuera).*\b(plazo|fecha|periodo|período)\b/u', $texto);
    }
}
