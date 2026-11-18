<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\AprobacionBeneficiarioService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;

class ApruebaBeneficiarioController extends ApruebaSolicitudController
{
    protected string $tipopc = AprobacionBeneficiarioService::TIPOPC;

    public function __construct(private readonly AprobacionBeneficiarioService $service, SolicitudDetalleService $detalle)
    {
        parent::__construct($detalle);
    }

    protected function service(): SolicitudAprobacionService
    {
        return $this->service;
    }

    protected function modulo(): array
    {
        return [
            'titulo' => 'Aprobación de beneficiarios',
            'singular' => 'beneficiario',
            'identificacion' => 'Documento',
            'nombre' => 'Beneficiario',
            'busqueda' => 'Documento, cédula del trabajador o nombre…',
        ];
    }
}
