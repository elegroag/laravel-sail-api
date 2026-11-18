<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsTrabajador;
use App\Models\Mercurio31;
use App\Services\Api\ApiSubsidio;
use App\Services\Cajas\Solicitudes\AprobacionPersonaService;
use App\Services\CajaServices\TrabajadorServices;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Aprobación de trabajadores (Mercurio31, tipopc 1).
 */
class AprobacionTrabajadorService extends AprobacionPersonaService
{
    public const TIPOPC = '1';

    /** @var array<string, array<string, mixed>> */
    private array $cacheEmpresa = [];

    public function routePrefix(): string
    {
        return 'cajas.aprobaciontra';
    }

    protected function modelClass(): string
    {
        return Mercurio31::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'T';
    }

    protected function legacy(): object
    {
        return new TrabajadorServices;
    }

    protected function paramsClass(): string
    {
        return ParamsTrabajador::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_trabajadores';
    }

    protected function deshacerMetodo(): ?string
    {
        return 'deshacer_aprobacion_trabajador';
    }

    protected function searchColumns(): array
    {
        return ['cedtra', 'nit', 'prinom', 'segnom', 'priape', 'segape'];
    }

    public function capacidades(): array
    {
        return [
            'editar' => true,
            'aportes' => true,
            'deshacer' => true,
            'reaprobar' => true,
            'multiafiliacion' => true,
            'trayectoria' => true,
        ];
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [];
    }

    protected function catalogoGetters(): array
    {
        return [
            'coddoc' => 'getTiposDocumentos',
            'codciu' => 'getCiudades',
            'ciunac' => 'getCiudades',
            'codzon' => 'getZonas',
            'sexo' => 'getSexos',
            'estciv' => 'getEstadoCivil',
            'cabhog' => 'getCabezaHogar',
            'captra' => 'getCapacidadTrabajar',
            'tipdis' => 'getTipoDiscapacidad',
            'nivedu' => 'getNivelEducativo',
            'rural' => 'getRural',
            'tipcon' => 'getTipoContrato',
            'vivienda' => 'getVivienda',
            'tipafi' => 'getTipoAfiliado',
            'trasin' => 'getSindicalizado',
            'cargo' => 'getOcupaciones',
            'codban' => 'getBancos',
            'tipcue' => 'getTipoCuenta',
            'giro' => 'getGiro',
            'codgir' => 'getCodigoGiro',
            'vendedor' => 'getVendedor',
            'empleador' => 'getEmpleador',
        ];
    }

    protected function aprobarCampos(): array
    {
        $sino = [['value' => 'N', 'label' => 'NO'], ['value' => 'S', 'label' => 'SI']];

        return [
            ['name' => 'codsuc', 'label' => 'Sucursal', 'type' => 'select', 'dinamico' => 'sucursales', 'fallback' => [['value' => '001', 'label' => '001']], 'default' => '001'],
            ['name' => 'codlis', 'label' => 'Lista', 'type' => 'select', 'dinamico' => 'listas', 'fallback' => [['value' => '001', 'label' => '001']], 'default' => '001'],
            ['name' => 'vendedor', 'label' => 'Vendedor', 'type' => 'select', 'fallback' => $sino, 'default' => 'N'],
            ['name' => 'empleador', 'label' => 'Empleador', 'type' => 'select', 'fallback' => $sino, 'default' => 'N'],
            ['name' => 'tippag', 'label' => 'Tipo pago cuota', 'type' => 'select', 'opciones' => $this->opciones(self::PAGOS), 'default' => 'T'],
            ['name' => 'codban', 'label' => 'Banco', 'type' => 'select', 'search' => true, 'required_if' => ['field' => 'tippag', 'in' => ['A', 'D']], 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'numcue', 'label' => 'Número cuenta', 'max' => 20, 'required_if' => ['field' => 'tippag', 'in' => ['D']], 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'tipcue', 'label' => 'Tipo cuenta', 'type' => 'select', 'required' => false, 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'giro', 'label' => 'Giro', 'type' => 'select', 'fallback' => $sino, 'default' => 'N'],
            ['name' => 'codgir', 'label' => 'Motivo no giro', 'type' => 'select', 'required' => false, 'search' => true, 'default' => 'NO'],
            ['name' => 'fecafi', 'label' => 'Fecha afiliación', 'type' => 'date'],
            ['name' => 'fecapr', 'label' => 'Fecha aprobación resolución', 'type' => 'date'],
        ];
    }

    public function aprobarFormulario(Model $solicitud): array
    {
        $formulario = parent::aprobarFormulario($solicitud);
        $codsuc = (string) ($solicitud->getAttribute('codsuc') ?: '001');
        $formulario['defaults']['codsuc'] = $codsuc;

        return $formulario;
    }

    /**
     * Sucursales y listas de la empresa del trabajador en SISU.
     */
    protected function opcionesDinamicas(Model $solicitud, string $clave): array
    {
        [$metodo, $columna] = match ($clave) {
            'sucursales' => ['buscar_sucursales_en_empresa', 'codsuc'],
            'listas' => ['buscar_listas_en_empresa', 'codlis'],
            default => [null, null],
        };
        if ($metodo === null) {
            return [];
        }

        $nit = (string) $solicitud->getAttribute('nit');
        try {
            $api = new ApiSubsidio;
            $api->send(['servicio' => 'ComfacaEmpresas', 'metodo' => $metodo, 'params' => $nit]);
            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación de trabajador: SISU no disponible', ['metodo' => $metodo, 'error' => $e->getMessage()]);

            return [];
        }

        $opciones = [];
        foreach (($out['success'] ?? false) ? ($out['data'] ?? []) : [] as $fila) {
            $codigo = (string) ($fila[$columna] ?? '');
            if ($codigo === '') {
                continue;
            }
            $detalle = trim((string) ($fila['detalle'] ?? ''));
            $opciones[$codigo] = ['value' => $codigo, 'label' => trim($codigo.' '.$detalle)];
        }

        return array_values($opciones);
    }

    protected function sisuRequest(Model $solicitud): array
    {
        return [
            'servicio' => 'ComfacaAfilia',
            'metodo' => 'trabajador',
            'params' => ['cedtra' => (string) $solicitud->getAttribute('cedtra')],
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

    /** Empresa del trabajador en SISU. */
    public function sisuExtra(Model $solicitud): ?array
    {
        return $this->consultarSisuRequest(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => (string) $solicitud->getAttribute('nit')],
            ],
            ['A', 'S', 'D'],
            'Empresa en SISU'
        );
    }

    /** Los aportes relevantes son los de la empresa del trabajador. */
    protected function aportesDocumento(Model $solicitud): string
    {
        return (string) $solicitud->getAttribute('nit');
    }

    protected function deshacerSisu(Model $solicitud, string $metodo, string $nota): array
    {
        return [
            'servicio' => 'ComfacaAfilia',
            'metodo' => $metodo,
            'params' => [
                'nit' => $solicitud->getAttribute('nit'),
                'cedtra' => $solicitud->getAttribute('cedtra'),
                'coddoc' => $solicitud->getAttribute('tipdoc'),
                'fecafi' => $solicitud->getAttribute('fecafi'),
                'nota' => $nota,
            ],
        ];
    }

    public function validarMultiafiliacion(Model $solicitud): array
    {
        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => (string) $solicitud->getAttribute('cedtra'),
            ]);
            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación de trabajador: no se pudo validar la multiafiliación', ['error' => $e->getMessage()]);

            return ['multi' => false];
        }

        $datos = ($out['success'] ?? false) ? ($out['data'] ?? null) : null;
        if (! is_array($datos)) {
            return ['multi' => false];
        }

        $nitSisu = trim((string) ($datos['nit'] ?? ''));

        return [
            'multi' => ($datos['estado'] ?? null) === 'A'
                && $nitSisu !== ''
                && $nitSisu !== trim((string) $solicitud->getAttribute('nit')),
        ];
    }

    public function trayectoria(Model $solicitud): array
    {
        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => (string) $solicitud->getAttribute('cedtra'),
            ]);
            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación de trabajador: SISU no disponible para la trayectoria', ['error' => $e->getMessage()]);

            return ['consultado' => false, 'trayectorias' => [], 'salarios' => []];
        }

        $datos = ($out['success'] ?? false) ? ($out['data'] ?? []) : [];
        $texto = fn (mixed $v): string => is_scalar($v) ? (string) $v : '';

        return [
            'consultado' => true,
            'trayectorias' => collect($datos['trayectoria'] ?? [])->map(fn ($f) => [
                'fecafi' => $texto($f['fecafi'] ?? ''),
                'nit' => $texto($f['nit'] ?? ''),
                'codsuc' => $texto($f['codsuc'] ?? ''),
                'fecret' => $texto($f['fecret'] ?? ''),
            ])->values()->all(),
            'salarios' => collect($datos['salarios'] ?? [])->map(fn ($f) => [
                'fecha' => $texto($f['fecha'] ?? ''),
                'salario' => $texto($f['salario'] ?? ''),
            ])->values()->all(),
        ];
    }

    public function secciones(Model $solicitud): array
    {
        $secciones = [
            [
                'titulo' => 'Información del trabajador',
                'campos' => $this->camposDe($solicitud, [
                    'Radicado (RUUID)' => ['ruuid'],
                    'NIT de la empresa' => ['nit'],
                    'Razón social' => ['razsoc'],
                    'Tipo de documento' => ['tipdoc', 'coddoc'],
                    'Cédula' => ['cedtra'],
                    'Nombre completo' => null,
                    'Fecha de nacimiento' => ['fecnac', null, true],
                    'Ciudad de nacimiento' => ['ciunac', 'ciunac'],
                    'Sexo' => ['sexo', 'sexo'],
                    'Estado civil' => ['estciv', 'estciv'],
                    'Cabeza de hogar' => ['cabhog', 'cabhog'],
                    'Estado' => ['__estado'],
                    'Fecha de solicitud' => ['fecsol', null, true],
                ]),
            ],
            [
                'titulo' => 'Ubicación y contacto',
                'campos' => $this->camposDe($solicitud, [
                    'Ciudad' => ['codciu', 'codciu'],
                    'Zona' => ['codzon', 'codzon'],
                    'Dirección' => ['direccion'],
                    'Barrio' => ['barrio'],
                    'Teléfono' => ['telefono'],
                    'Celular' => ['celular'],
                    'Email' => ['email'],
                ]),
            ],
            [
                'titulo' => 'Información laboral',
                'campos' => $this->camposDe($solicitud, [
                    'Cargo' => ['cargo', 'cargo'],
                    'Fecha de ingreso' => ['fecing', null, true],
                    'Salario' => ['salario'],
                    'Horas' => ['horas'],
                    'Tipo de contrato' => ['tipcon', 'tipcon'],
                    'Sucursal' => ['codsuc'],
                    'Sindicalizado' => ['trasin', 'trasin'],
                    'Profesión' => ['profesion'],
                ]),
            ],
            [
                'titulo' => 'Condiciones personales',
                'campos' => $this->camposDe($solicitud, [
                    'Capacidad de trabajar' => ['captra', 'captra'],
                    'Discapacidad' => ['tipdis', 'tipdis'],
                    'Nivel de educación' => ['nivedu', 'nivedu'],
                    'Rural' => ['rural', 'rural'],
                    'Vivienda' => ['vivienda', 'vivienda'],
                    'Tipo de afiliado' => ['tipafi', 'tipafi'],
                    'Autoriza tratamiento de datos' => ['autoriza'],
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
