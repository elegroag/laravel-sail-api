<?php

namespace App\Http\Controllers\Cajas;

use App\Services\Cajas\AprobacionCertificadoService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;

class ApruebaCertificadoController extends ApruebaSolicitudController
{
    protected string $tipopc = AprobacionCertificadoService::TIPOPC;

    public function __construct(private readonly AprobacionCertificadoService $service, SolicitudDetalleService $detalle)
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
            'titulo' => 'Aprobación de certificados',
            'singular' => 'certificado',
            'identificacion' => 'Cédula',
            'nombre' => 'Beneficiario',
            'busqueda' => 'Cédula, documento del beneficiario o certificado…',
        ];
    }
}
