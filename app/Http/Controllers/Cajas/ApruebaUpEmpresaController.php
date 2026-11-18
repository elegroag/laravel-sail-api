<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\ActualizacionEmpresaService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;
use Illuminate\Database\Eloquent\Model;

class ApruebaUpEmpresaController extends ApruebaSolicitudController
{
    protected string $tipopc = ActualizacionEmpresaService::TIPOPC;

    public function __construct(private readonly ActualizacionEmpresaService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de actualización de datos de empresas',
            'singular' => 'actualización de datos',
            'identificacion' => 'NIT',
            'nombre' => 'Solicitante',
            'busqueda' => 'NIT de la empresa…',
        ];
    }

    protected function cambios(Model $solicitud): array
    {
        return $this->service->cambios($solicitud);
    }
}
