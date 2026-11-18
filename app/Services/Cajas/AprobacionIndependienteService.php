<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsIndependiente;
use App\Models\Mercurio41;
use App\Services\Cajas\Solicitudes\AprobacionPersonaService;
use App\Services\CajaServices\IndependienteServices;
use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de independientes (Mercurio41, tipopc 13).
 */
class AprobacionIndependienteService extends AprobacionPersonaService
{
    public const TIPOPC = '13';

    public function routePrefix(): string
    {
        return 'cajas.aprobaindepen';
    }

    protected function modelClass(): string
    {
        return Mercurio41::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'I';
    }

    protected function legacy(): object
    {
        return new IndependienteServices;
    }

    protected function paramsClass(): string
    {
        return ParamsIndependiente::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_independiente';
    }

    protected function deshacerMetodo(): ?string
    {
        return 'deshacer_aprobacion_independiente';
    }

    protected function indicesValidos(): array
    {
        return ['50', '14'];
    }

    public function capacidades(): array
    {
        return ['editar' => true, 'aportes' => true, 'deshacer' => true, 'reaprobar' => true];
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return parent::aprobarFijos($solicitud, $data) + ['tipdur' => 'S', 'forpre' => 'S'];
    }

    protected function aprobarCampos(): array
    {
        return [
            ['name' => 'codind', 'label' => 'Índice', 'type' => 'select', 'search' => true],
            ['name' => 'todmes', 'label' => 'Paga mes', 'type' => 'select', 'default' => 'S'],
            ['name' => 'tipapo', 'label' => 'Tipo aportante', 'type' => 'select', 'search' => true, 'default' => 'P'],
            ['name' => 'tipsoc', 'label' => 'Tipo sociedad', 'type' => 'select', 'search' => true],
            ['name' => 'codsuc', 'label' => 'Sucursal planilla', 'max' => 10],
            ['name' => 'actapr', 'label' => 'Acta de aprobación', 'max' => 50],
            ['name' => 'diahab', 'label' => 'Día hábil de pago', 'type' => 'number', 'min' => 0, 'max' => 31],
            ...$this->camposPago(),
            ['name' => 'fecapr', 'label' => 'Fecha aprobación resolución', 'type' => 'date'],
            ['name' => 'fecafi', 'label' => 'Fecha de afiliación', 'type' => 'date'],
        ];
    }
}
