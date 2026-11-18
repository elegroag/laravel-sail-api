<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsBeneficiario;
use App\Models\Mercurio34;
use App\Services\Aprueba\ApruebaBeneficiario;
use App\Services\Cajas\Solicitudes\AprobacionPersonaService;
use App\Services\CajaServices\BeneficiarioServices;
use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de beneficiarios (Mercurio34, tipopc 4).
 */
class AprobacionBeneficiarioService extends AprobacionPersonaService
{
    public const TIPOPC = '4';

    public function routePrefix(): string
    {
        return 'cajas.aprobacionben';
    }

    protected function modelClass(): string
    {
        return Mercurio34::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'B';
    }

    protected function legacy(): object
    {
        return new BeneficiarioServices;
    }

    protected function paramsClass(): string
    {
        return ParamsBeneficiario::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_beneficiarios';
    }

    protected function deshacerMetodo(): ?string
    {
        return 'deshacer_aprobacion_beneficiario';
    }

    protected function identificacion(Model $row): string
    {
        return (string) $row->getAttribute('numdoc');
    }

    protected function searchColumns(): array
    {
        return ['numdoc', 'cedtra', 'prinom', 'segnom', 'priape', 'segape'];
    }

    public function capacidades(): array
    {
        return ['editar' => true, 'aportes' => false, 'deshacer' => true, 'reaprobar' => true];
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [];
    }

    /** El procesador de beneficiarios no está registrado en ApruebaSolicitud::main. */
    protected function procesarAprobacion(int $id, array $data): object
    {
        return $this->procesarDirecto(new ApruebaBeneficiario, $id, $data);
    }

    protected function catalogoGetters(): array
    {
        return [
            'tipdoc' => 'getTiposDocumentos',
            'ciunac' => 'getCiudades',
            'sexo' => 'getSexos',
            'parent' => 'getParentesco',
            'huerfano' => 'getHuerfano',
            'tiphij' => 'getTipoHijo',
            'nivedu' => 'getNivelEducativo',
            'captra' => 'getCapacidadTrabajar',
            'tipdis' => 'getTipoDiscapacidad',
            'calendario' => 'getCalendario',
            'giro' => 'getTieneGiro',
            'codgir' => 'getCodigoGiro',
            'pago' => 'getPago',
            'codban' => 'getBancos',
            'tipcue' => 'getTipoCuenta',
            'biocodciu' => 'getCiudades',
        ];
    }

    protected function aprobarCampos(): array
    {
        return [
            ['name' => 'giro', 'label' => 'Giro', 'type' => 'select', 'fallback' => [['value' => 'N', 'label' => 'NO'], ['value' => 'S', 'label' => 'SI']]],
            ['name' => 'codgir', 'label' => 'Motivo no giro', 'type' => 'select', 'required' => false, 'search' => true, 'default' => 'NO'],
            ['name' => 'pago', 'label' => 'Pago', 'type' => 'select', 'default' => 'C', 'fallback' => [['value' => 'C', 'label' => 'CAJA']]],
            ['name' => 'numhij', 'label' => 'Número hijo', 'type' => 'number', 'required' => false, 'min' => 0, 'max' => 99, 'default' => '0'],
            ['name' => 'fecafi', 'label' => 'Fecha afiliación', 'type' => 'date'],
            ['name' => 'fecapr', 'label' => 'Fecha aprobación resolución', 'type' => 'date'],
            ['name' => 'fecpre', 'label' => 'Fecha presentación', 'type' => 'date', 'required' => false],
        ];
    }

    protected function sisuRequest(Model $solicitud): array
    {
        return [
            'servicio' => 'ComfacaEmpresas',
            'metodo' => 'informacion_beneficiario',
            'params' => ['numdoc' => (string) $solicitud->getAttribute('numdoc')],
        ];
    }

    protected function estadosAfiliada(): array
    {
        return ['A'];
    }

    protected function sisuTitulo(): string
    {
        return 'Beneficiario en SISU';
    }

    /** El trabajador del beneficiario debe estar activo en SISU para aprobar. */
    public function sisuExtra(Model $solicitud): ?array
    {
        return $this->consultarSisuRequest(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'trabajador',
                'params' => ['cedtra' => (string) $solicitud->getAttribute('cedtra'), 'estado' => 'A'],
            ],
            ['A'],
            'Trabajador en SISU'
        );
    }

    protected function deshacerSisu(Model $solicitud, string $metodo, string $nota): array
    {
        return [
            'servicio' => 'ComfacaAfilia',
            'metodo' => $metodo,
            'params' => [
                'cedtra' => $solicitud->getAttribute('cedtra'),
                'numdoc' => $solicitud->getAttribute('numdoc'),
                'tipo_documento' => $solicitud->getAttribute('tipdoc'),
                'nota' => $nota,
            ],
        ];
    }

    public function secciones(Model $solicitud): array
    {
        $secciones = [
            [
                'titulo' => 'Información del beneficiario',
                'campos' => $this->camposDe($solicitud, [
                    'Radicado (RUUID)' => ['ruuid'],
                    'Cédula del trabajador' => ['cedtra'],
                    'Cédula del cónyuge' => ['cedcon'],
                    'Tipo de documento' => ['tipdoc', 'tipdoc'],
                    'Documento del beneficiario' => ['numdoc'],
                    'Nombre completo' => null,
                    'Fecha de nacimiento' => ['fecnac', null, true],
                    'Ciudad de nacimiento' => ['ciunac', 'ciunac'],
                    'Sexo' => ['sexo', 'sexo'],
                    'Parentesco' => ['parent', 'parent'],
                    'Condición de huérfano' => ['huerfano', 'huerfano'],
                    'Tipo de hijo' => ['tiphij', 'tiphij'],
                    'Estado' => ['__estado'],
                    'Fecha de solicitud' => ['fecsol', null, true],
                ]),
            ],
            [
                'titulo' => 'Condiciones del beneficiario',
                'campos' => $this->camposDe($solicitud, [
                    'Nivel de educación' => ['nivedu', 'nivedu'],
                    'Capacidad de trabajo' => ['captra', 'captra'],
                    'Tipo de discapacidad' => ['tipdis', 'tipdis'],
                    'Calendario' => ['calendario', 'calendario'],
                    'Celular' => ['celular'],
                ]),
            ],
            [
                'titulo' => 'Padre o madre biológico',
                'campos' => $this->camposDe($solicitud, [
                    'Documento' => ['biocedu'],
                    'Primer nombre' => ['bioprinom'],
                    'Segundo nombre' => ['biosegnom'],
                    'Primer apellido' => ['biopriape'],
                    'Segundo apellido' => ['biosegape'],
                    'Email' => ['bioemail'],
                    'Teléfono' => ['biophone'],
                    'Ciudad' => ['biocodciu', 'biocodciu'],
                    'Dirección' => ['biodire'],
                ]),
            ],
            [
                'titulo' => 'Medio de pago',
                'campos' => $this->camposDe($solicitud, [
                    'Tipo medio pago cuota' => ['tippag', 'tippag'],
                    'Banco' => ['codban', 'codban'],
                    'Tipo de cuenta' => ['tipcue', 'tipcue'],
                    'Número de cuenta' => ['numcue'],
                ]),
            ],
        ];

        return array_values(array_filter($secciones, fn (array $s) => $s['campos'] !== []));
    }
}
