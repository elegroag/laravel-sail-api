<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\ActualizacionTrabajadorService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;
use Illuminate\Database\Eloquent\Model;

class ApruebaUpTrabajadorController extends ApruebaSolicitudController
{
    protected string $tipopc = ActualizacionTrabajadorService::TIPOPC;

    public function __construct(private readonly ActualizacionTrabajadorService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de actualización de datos de trabajadores',
            'singular' => 'actualización de datos',
            'identificacion' => 'Cédula',
            'nombre' => 'Solicitante',
            'busqueda' => 'Cédula del trabajador…',
        ];
    }

    protected function cambios(Model $solicitud): array
    {
        return $this->service->cambios($solicitud);
    }
}
