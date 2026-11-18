<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\AprobacionTrabajadorService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;

class ApruebaTrabajadorController extends ApruebaSolicitudController
{
    protected string $tipopc = AprobacionTrabajadorService::TIPOPC;

    public function __construct(private readonly AprobacionTrabajadorService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de trabajadores',
            'singular' => 'trabajador',
            'identificacion' => 'Cédula',
            'nombre' => 'Trabajador',
            'busqueda' => 'Cédula, NIT o nombre del trabajador…',
        ];
    }
}
