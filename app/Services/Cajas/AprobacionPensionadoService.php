<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsPensionado;
use App\Models\Mercurio38;
use App\Services\Cajas\Solicitudes\AprobacionPersonaService;
use App\Services\CajaServices\PensionadoServices;
use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de pensionados (Mercurio38, tipopc 9).
 */
class AprobacionPensionadoService extends AprobacionPersonaService
{
    public const TIPOPC = '9';

    public function routePrefix(): string
    {
        return 'cajas.aprobacionpen';
    }

    protected function modelClass(): string
    {
        return Mercurio38::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'P';
    }

    protected function legacy(): object
    {
        return new PensionadoServices;
    }

    protected function paramsClass(): string
    {
        return ParamsPensionado::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_pensionado';
    }

    protected function deshacerMetodo(): ?string
    {
        return 'deshacer_aprobacion_pensionado';
    }

    protected function indicesValidos(): array
    {
        return ['07', '47', '48', '49'];
    }

    protected function notaMin(): int
    {
        return 10;
    }

    public function capacidades(): array
    {
        return ['editar' => true, 'aportes' => true, 'deshacer' => true, 'reaprobar' => true];
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return parent::aprobarFijos($solicitud, $data) + ['tipapo' => 'P', 'tipsoc' => '06'];
    }

    protected function aprobarCampos(): array
    {
        return [
            ['name' => 'codind', 'label' => 'Índice', 'type' => 'select', 'search' => true],
            ['name' => 'todmes', 'label' => 'Paga mes', 'type' => 'select', 'default' => 'S'],
            ['name' => 'forpre', 'label' => 'Forma de presentación', 'type' => 'select', 'default' => 'S'],
            ['name' => 'tipdur', 'label' => 'Duración', 'type' => 'select', 'default' => 'S'],
            ['name' => 'codsuc', 'label' => 'Sucursal planilla', 'max' => 10],
            ['name' => 'actapr', 'label' => 'Acta de aprobación', 'max' => 50],
            ['name' => 'diahab', 'label' => 'Día hábil de pago', 'type' => 'number', 'min' => 0, 'max' => 31],
            ...$this->camposPago(),
            ['name' => 'fecapr', 'label' => 'Fecha aprobación resolución', 'type' => 'date'],
            ['name' => 'fecafi', 'label' => 'Fecha de afiliación', 'type' => 'date'],
        ];
    }
}
