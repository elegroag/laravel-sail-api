<?php

namespace App\Services\Cajas;

use App\Models\Mercurio45;
use App\Services\Aprueba\ApruebaCertificado;
use App\Services\Cajas\Solicitudes\AprobacionPersonaService;
use App\Services\CajaServices\CertificadosServices;
use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de certificados de beneficiarios (Mercurio45, tipopc 8).
 */
class AprobacionCertificadoService extends AprobacionPersonaService
{
    public const TIPOPC = '8';

    public function routePrefix(): string
    {
        return 'cajas.aprobacioncer';
    }

    protected function modelClass(): string
    {
        return Mercurio45::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'CE';
    }

    protected function legacy(): object
    {
        return new CertificadosServices;
    }

    protected function paramsClass(): string
    {
        return \stdClass::class;
    }

    protected function paramsMetodo(): string
    {
        return '';
    }

    protected function nombre(Model $row): string
    {
        return trim((string) $row->getAttribute('nombre'));
    }

    protected function searchColumns(): array
    {
        return ['cedtra', 'codben', 'nombre', 'nomcer'];
    }

    public function capacidades(): array
    {
        return ['editar' => false, 'aportes' => false, 'deshacer' => false, 'reaprobar' => false];
    }

    protected function usaControles(): bool
    {
        return false;
    }

    protected function usaSisu(): bool
    {
        return false;
    }

    /** La notificación de devolución y rechazo ya se envía desde CertificadosServices. */
    protected function notificaPorCorreo(): bool
    {
        return false;
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [];
    }

    protected function procesarAprobacion(int $id, array $data): object
    {
        return $this->procesarDirecto(new ApruebaCertificado, $id, $data);
    }

    /** El certificado no usa catálogos de SISU. */
    public function catalogos(): array
    {
        return [];
    }

    public function aprobarFormulario(Model $solicitud): array
    {
        return ['catalogos_disponibles' => true] + parent::aprobarFormulario($solicitud);
    }

    protected function aprobarCampos(): array
    {
        return [
            ['name' => 'fecapr', 'label' => 'Fecha aprobación resolución', 'type' => 'date'],
        ];
    }

    public function secciones(Model $solicitud): array
    {
        return array_values(array_filter([
            [
                'titulo' => 'Información del certificado',
                'campos' => $this->camposDe($solicitud, [
                    'Radicado (RUUID)' => ['ruuid'],
                    'Cédula del trabajador' => ['cedtra'],
                    'Documento del beneficiario' => ['codben'],
                    'Beneficiario' => ['nombre'],
                    'Certificado' => ['nomcer'],
                    'Fecha del certificado' => ['fecha', null, true],
                    'Estado' => ['__estado'],
                    'Fecha de solicitud' => ['fecsol', null, true],
                ]),
            ],
        ], fn (array $s) => $s['campos'] !== []));
    }
}
