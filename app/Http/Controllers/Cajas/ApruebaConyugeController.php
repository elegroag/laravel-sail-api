<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\AprobacionConyugeService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;

class ApruebaConyugeController extends ApruebaSolicitudController
{
    protected string $tipopc = AprobacionConyugeService::TIPOPC;

    public function __construct(private readonly AprobacionConyugeService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de cónyuges',
            'singular' => 'cónyuge',
            'identificacion' => 'Cédula',
            'nombre' => 'Cónyuge',
            'busqueda' => 'Cédula del cónyuge, del trabajador o nombre…',
        ];
    }
}
