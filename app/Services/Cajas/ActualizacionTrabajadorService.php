<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsTrabajador;
use App\Services\CajaServices\UpDatosTrabajadorService;
use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de actualizaciones de datos de trabajador (Mercurio47 tipact T + cambios en Mercurio33, tipopc 14).
 */
class ActualizacionTrabajadorService extends ActualizacionEmpresaService
{
    public const TIPOPC = '14';

    public function routePrefix(): string
    {
        return 'cajas.actualizatra';
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function tipact(): string
    {
        return 'T';
    }

    protected function calemp(): string
    {
        return 'UT';
    }

    protected function legacy(): object
    {
        return new UpDatosTrabajadorService;
    }

    protected function paramsClass(): string
    {
        return ParamsTrabajador::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_trabajadores';
    }

    public function capacidades(): array
    {
        return ['editar' => false, 'aportes' => false, 'deshacer' => false, 'reaprobar' => false, 'devolver' => false];
    }

    protected function usaControles(): bool
    {
        return false;
    }

    /** Solo los usuarios con el permiso 62 de Gener42 pueden aprobar actualizaciones de trabajador. */
    protected function permisoAprobar(): ?string
    {
        return '62';
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [];
    }

    protected function aprobarCampos(): array
    {
        return [];
    }

    /** El formulario solo pide la nota: no se consultan catálogos de SISU. */
    public function catalogos(): array
    {
        return [];
    }

    public function aprobarFormulario(Model $solicitud): array
    {
        return ['catalogos_disponibles' => true] + parent::aprobarFormulario($solicitud);
    }

    protected function sisuRequest(Model $solicitud): array
    {
        return [
            'servicio' => 'ComfacaEmpresas',
            'metodo' => 'informacion_trabajador',
            'params' => (string) $solicitud->getAttribute('documento'),
        ];
    }

    protected function estadosAfiliada(): array
    {
        return ['A'];
    }

    protected function sisuTitulo(): string
    {
        return 'Trabajador en SISU';
    }

    public function secciones(Model $solicitud): array
    {
        return [
            [
                'titulo' => 'Solicitud de actualización de datos del trabajador',
                'campos' => $this->campos([
                    'Radicado (RUUID)' => $solicitud->getAttribute('ruuid'),
                    'Cédula' => $solicitud->getAttribute('documento'),
                    'Solicitante' => $this->nombre($solicitud),
                    'Estado' => $this->estadoDetalle($solicitud),
                    'Fecha de solicitud' => $this->fecha($solicitud->getAttribute('fecsol')),
                    'Fecha de aprobación' => $this->fecha($solicitud->getAttribute('fecapr')),
                ]),
            ],
        ];
    }

    protected function aplicarDevolucion(Model $solicitud, string $nota, string $codest, string $camposCorregir): void
    {
        throw new \LogicException('La actualización de datos del trabajador no admite devolución.');
    }
}
