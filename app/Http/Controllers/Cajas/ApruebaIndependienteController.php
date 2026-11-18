<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\AprobacionIndependienteService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;

class ApruebaIndependienteController extends ApruebaSolicitudController
{
    protected string $tipopc = AprobacionIndependienteService::TIPOPC;

    public function __construct(private readonly AprobacionIndependienteService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de independientes',
            'singular' => 'independiente',
            'identificacion' => 'Cédula',
            'nombre' => 'Nombre',
            'busqueda' => 'Cédula o nombre del solicitante…',
        ];
    }
}
