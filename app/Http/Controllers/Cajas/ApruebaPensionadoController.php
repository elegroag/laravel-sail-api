<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\AprobacionPensionadoService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;

class ApruebaPensionadoController extends ApruebaSolicitudController
{
    protected string $tipopc = AprobacionPensionadoService::TIPOPC;

    public function __construct(private readonly AprobacionPensionadoService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de pensionados',
            'singular' => 'pensionado',
            'identificacion' => 'Cédula',
            'nombre' => 'Nombre',
            'busqueda' => 'Cédula o nombre del solicitante…',
        ];
    }
}
