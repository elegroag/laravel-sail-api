<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\AprobacionFacultativoService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;

class ApruebaFacultativoController extends ApruebaSolicitudController
{
    protected string $tipopc = AprobacionFacultativoService::TIPOPC;

    public function __construct(private readonly AprobacionFacultativoService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de facultativos',
            'singular' => 'facultativo',
            'identificacion' => 'Cédula',
            'nombre' => 'Nombre',
            'busqueda' => 'Cédula o nombre del solicitante…',
        ];
    }
}
