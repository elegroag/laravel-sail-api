<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsFacultativo;
use App\Models\Mercurio36;
use App\Services\Cajas\Solicitudes\AprobacionPersonaService;
use App\Services\CajaServices\FacultativoServices;
use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de facultativos (Mercurio36, tipopc 10).
 */
class AprobacionFacultativoService extends AprobacionPersonaService
{
    public const TIPOPC = '10';

    public function routePrefix(): string
    {
        return 'cajas.aprobacionfac';
    }

    protected function modelClass(): string
    {
        return Mercurio36::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'F';
    }

    protected function legacy(): object
    {
        return new FacultativoServices;
    }

    protected function paramsClass(): string
    {
        return ParamsFacultativo::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_facultativo';
    }

    protected function indicesValidos(): array
    {
        return ['46'];
    }

    public function capacidades(): array
    {
        return ['editar' => true, 'aportes' => false, 'deshacer' => false, 'reaprobar' => false];
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return parent::aprobarFijos($solicitud, $data) + ['tipapo' => 'F', 'tipsoc' => '06'];
    }

    protected function aprobarCampos(): array
    {
        return [
            ['name' => 'codind', 'label' => 'Índice', 'type' => 'select', 'search' => true, 'default' => '46'],
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
