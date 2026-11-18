<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsConyuge;
use App\Models\Mercurio32;
use App\Services\Cajas\Solicitudes\AprobacionPersonaService;
use App\Services\CajaServices\ConyugeServices;
use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de cónyuges (Mercurio32, tipopc 3).
 */
class AprobacionConyugeService extends AprobacionPersonaService
{
    public const TIPOPC = '3';

    public function routePrefix(): string
    {
        return 'cajas.aprobacioncon';
    }

    protected function modelClass(): string
    {
        return Mercurio32::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'C';
    }

    protected function legacy(): object
    {
        return new ConyugeServices;
    }

    protected function paramsClass(): string
    {
        return ParamsConyuge::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_conyuges';
    }

    protected function deshacerMetodo(): ?string
    {
        return 'deshacer_aprobacion_conyuge';
    }

    protected function identificacion(Model $row): string
    {
        return (string) $row->getAttribute('cedcon');
    }

    protected function searchColumns(): array
    {
        return ['cedcon', 'cedtra', 'prinom', 'segnom', 'priape', 'segape'];
    }

    public function capacidades(): array
    {
        return ['editar' => true, 'aportes' => false, 'deshacer' => true, 'reaprobar' => true];
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [];
    }

    protected function catalogoGetters(): array
    {
        return [
            'tipdoc' => 'getTiposDocumentos',
            'codciu' => 'getCiudades',
            'ciunac' => 'getCiudades',
            'ciures' => 'getCiudades',
            'codzon' => 'getZonas',
            'sexo' => 'getSexos',
            'estciv' => 'getEstadoCivil',
            'captra' => 'getCapacidadTrabajar',
            'tipdis' => 'getTipoDiscapacidad',
            'nivedu' => 'getNivelEducativo',
            'comper' => 'getCompaneroPermanente',
            'tipviv' => 'getVivienda',
            'codocu' => 'getOcupaciones',
            'codban' => 'getBancos',
            'tipcue' => 'getTipoCuenta',
            'recsub' => 'getRecibeSubsidio',
        ];
    }

    protected function aprobarCampos(): array
    {
        return [
            ['name' => 'tippag', 'label' => 'Tipo pago cuota', 'type' => 'select', 'opciones' => $this->opciones(self::PAGOS), 'default' => 'T'],
            ['name' => 'codban', 'label' => 'Banco', 'type' => 'select', 'search' => true, 'required_if' => ['field' => 'tippag', 'in' => ['A', 'D']], 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'numcue', 'label' => 'Número cuenta', 'max' => 20, 'required_if' => ['field' => 'tippag', 'in' => ['D']], 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'tipcue', 'label' => 'Tipo cuenta', 'type' => 'select', 'required' => false, 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'recsub', 'label' => 'Recibe subsidio en otra caja', 'type' => 'select', 'fallback' => [['value' => 'N', 'label' => 'NO'], ['value' => 'S', 'label' => 'SI']], 'default' => 'N'],
            ['name' => 'fecafi', 'label' => 'Fecha afiliación', 'type' => 'date'],
            ['name' => 'fecapr', 'label' => 'Fecha aprobación resolución', 'type' => 'date'],
        ];
    }

    protected function sisuRequest(Model $solicitud): array
    {
        return [
            'servicio' => 'ComfacaEmpresas',
            'metodo' => 'informacion_conyuge',
            'params' => ['cedcon' => (string) $solicitud->getAttribute('cedcon')],
        ];
    }

    protected function estadosAfiliada(): array
    {
        return ['A'];
    }

    protected function sisuTitulo(): string
    {
        return 'Cónyuge en SISU';
    }

    /** El trabajador del cónyuge debe estar activo en SISU para aprobar. */
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
                'cedcon' => $solicitud->getAttribute('cedcon'),
                'tipo_documento' => $solicitud->getAttribute('tipdoc'),
                'nota' => $nota,
            ],
        ];
    }

    public function secciones(Model $solicitud): array
    {
        $secciones = [
            [
                'titulo' => 'Información del cónyuge',
                'campos' => $this->camposDe($solicitud, [
                    'Radicado (RUUID)' => ['ruuid'],
                    'Cédula del trabajador' => ['cedtra'],
                    'Tipo de documento' => ['tipdoc', 'tipdoc'],
                    'Cédula del cónyuge' => ['cedcon'],
                    'Nombre completo' => null,
                    'Fecha de nacimiento' => ['fecnac', null, true],
                    'Ciudad de nacimiento' => ['ciunac', 'ciunac'],
                    'Sexo' => ['sexo', 'sexo'],
                    'Estado civil' => ['estciv', 'estciv'],
                    'Compañero permanente' => ['comper', 'comper'],
                    'Tiempo de convivencia' => ['tiecon'],
                    'Estado' => ['__estado'],
                    'Fecha de solicitud' => ['fecsol', null, true],
                ]),
            ],
            [
                'titulo' => 'Ubicación y contacto',
                'campos' => $this->camposDe($solicitud, [
                    'Ciudad de residencia' => ['ciures', 'ciures'],
                    'Zona' => ['codzon', 'codzon'],
                    'Tipo de vivienda' => ['tipviv', 'tipviv'],
                    'Dirección' => ['direccion'],
                    'Barrio' => ['barrio'],
                    'Teléfono' => ['telefono'],
                    'Celular' => ['celular'],
                    'Email' => ['email'],
                ]),
            ],
            [
                'titulo' => 'Información laboral y personal',
                'campos' => $this->camposDe($solicitud, [
                    'Ocupación' => ['codocu', 'codocu'],
                    'Empresa donde labora' => ['empresalab'],
                    'Fecha de ingreso' => ['fecing', null, true],
                    'Salario' => ['salario'],
                    'Capacidad de trabajar' => ['captra', 'captra'],
                    'Discapacidad' => ['tipdis', 'tipdis'],
                    'Nivel de educación' => ['nivedu', 'nivedu'],
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
