<?php

namespace App\Services\CajaServices;

use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsConyuge;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsTrabajador;
use App\Services\Api\ApiSubsidio;
use Throwable;

/**
 * Definición del formulario de edición (etiquetas, tipos y opciones) por flujo de afiliación.
 */
class EditarSolicitudFormulario
{
    private const ETIQUETAS = [
        'razsoc' => 'Razón social',
        'sigla' => 'Sigla',
        'digver' => 'Dígito de verificación',
        'calemp' => 'Calidad empresa',
        'cedrep' => 'Documento representante legal',
        'repleg' => 'Representante legal',
        'direccion' => 'Dirección',
        'codciu' => 'Ciudad',
        'codzon' => 'Zona',
        'telefono' => 'Teléfono',
        'celular' => 'Celular',
        'fax' => 'Fax',
        'email' => 'Email',
        'codact' => 'Actividad económica',
        'fecini' => 'Fecha inicial',
        'tottra' => 'Total trabajadores',
        'valnom' => 'Valor nómina',
        'tipsoc' => 'Tipo sociedad',
        'dirpri' => 'Dirección comercial',
        'ciupri' => 'Ciudad comercial',
        'telpri' => 'Teléfono comercial',
        'celpri' => 'Celular comercial',
        'emailpri' => 'Email comercial',
        'tipemp' => 'Tipo empresa',
        'tipper' => 'Tipo persona',
        'prinom' => 'Primer nombre',
        'segnom' => 'Segundo nombre',
        'priape' => 'Primer apellido',
        'segape' => 'Segundo apellido',
        'matmer' => 'Matrícula mercantil',
        'coddocrepleg' => 'Tipo documento representante',
        'priaperepleg' => 'Primer apellido representante',
        'segaperepleg' => 'Segundo apellido representante',
        'prinomrepleg' => 'Primer nombre representante',
        'segnomrepleg' => 'Segundo nombre representante',
        'barnotif' => 'Barrio notificación',
        'barcomer' => 'Barrio comercial',
        'fecnac' => 'Fecha nacimiento',
        'ciunac' => 'Ciudad nacimiento',
        'sexo' => 'Sexo',
        'orisex' => 'Orientación sexual',
        'estciv' => 'Estado civil',
        'cabhog' => 'Cabeza de hogar',
        'barrio' => 'Barrio',
        'fecing' => 'Fecha ingreso',
        'salario' => 'Salario',
        'tipsal' => 'Tipo salario',
        'captra' => 'Capacidad de trabajo',
        'tipdis' => 'Tipo discapacidad',
        'nivedu' => 'Nivel educativo',
        'rural' => 'Residencia rural',
        'horas' => 'Horas',
        'tipcon' => 'Tipo contrato',
        'trasin' => 'Sindicalizado',
        'vivienda' => 'Vivienda',
        'tipafi' => 'Tipo afiliado',
        'profesion' => 'Profesión',
        'cargo' => 'Cargo u ocupación',
        'autoriza' => 'Autoriza tratamiento de datos',
        'facvul' => 'Factor de vulnerabilidad',
        'peretn' => 'Pertenencia étnica',
        'dirlab' => 'Dirección laboral',
        'ciulab' => 'Ciudad laboral',
        'ruralt' => 'Labor rural',
        'comision' => 'Comisión',
        'tipjor' => 'Tipo jornada',
        'codsuc' => 'Sucursal',
        'tippag' => 'Tipo pago',
        'numcue' => 'Número de cuenta',
        'otra_empresa' => 'Labora en otra empresa',
        'resguardo_id' => 'Resguardo',
        'pub_indigena_id' => 'Pueblo indígena',
        'codban' => 'Banco',
        'tipcue' => 'Tipo cuenta',
        'comper' => 'Compañero permanente',
        'tiecon' => 'Tiempo de convivencia',
        'ciures' => 'Ciudad residencia',
        'tipviv' => 'Tipo vivienda',
        'codocu' => 'Ocupación',
        'empresalab' => 'Empresa donde labora',
        'zoneurbana' => 'Zona urbana',
        'parent' => 'Parentesco',
        'huerfano' => 'Huérfano',
        'tiphij' => 'Tipo hijo',
        'calendario' => 'Calendario',
        'cedacu' => 'Documento acudiente',
        'biocedu' => 'Documento padre/madre biológico',
        'biotipdoc' => 'Tipo documento padre/madre biológico',
        'bioprinom' => 'Primer nombre padre/madre biológico',
        'biosegnom' => 'Segundo nombre padre/madre biológico',
        'biopriape' => 'Primer apellido padre/madre biológico',
        'biosegape' => 'Segundo apellido padre/madre biológico',
        'bioemail' => 'Email padre/madre biológico',
        'biophone' => 'Teléfono padre/madre biológico',
        'biocodciu' => 'Ciudad padre/madre biológico',
        'biodire' => 'Dirección padre/madre biológico',
        'biourbana' => 'Zona urbana padre/madre biológico',
        'biodesco' => 'Desconoce padre/madre biológico',
    ];

    private const FUENTES = [
        'trabajador' => [ParamsTrabajador::class, 'parametros_trabajadores'],
        'empresa' => [ParamsEmpresa::class, 'parametros_empresa'],
        'conyuge' => [ParamsConyuge::class, 'parametros_conyuges'],
        'beneficiario' => [ParamsBeneficiario::class, 'parametros_beneficiarios'],
    ];

    /**
     * Origen de las opciones de cada campo tipo lista: [fuente, método].
     */
    private const OPCIONES_PERSONA = [
        'ciunac' => ['trabajador', 'getCiudades'],
        'codciu' => ['trabajador', 'getCiudades'],
        'ciulab' => ['trabajador', 'getCiudades'],
        'ciures' => ['trabajador', 'getCiudades'],
        'biocodciu' => ['trabajador', 'getCiudades'],
        'codzon' => ['trabajador', 'getZonas'],
        'sexo' => ['trabajador', 'getSexos'],
        'estciv' => ['trabajador', 'getEstadoCivil'],
        'cabhog' => ['trabajador', 'getCabezaHogar'],
        'captra' => ['trabajador', 'getCapacidadTrabajar'],
        'tipdis' => ['trabajador', 'getTipoDiscapacidad'],
        'nivedu' => ['trabajador', 'getNivelEducativo'],
        'rural' => ['trabajador', 'getRural'],
        'ruralt' => ['trabajador', 'getRural'],
        'tipcon' => ['trabajador', 'getTipoContrato'],
        'trasin' => ['trabajador', 'getSindicalizado'],
        'vivienda' => ['trabajador', 'getVivienda'],
        'tipviv' => ['trabajador', 'getVivienda'],
        'tipafi' => ['trabajador', 'getTipoAfiliado'],
        'cargo' => ['trabajador', 'getOcupaciones'],
        'codocu' => ['trabajador', 'getOcupaciones'],
        'orisex' => ['trabajador', 'getOrientacionSexual'],
        'facvul' => ['trabajador', 'getVulnerabilidades'],
        'peretn' => ['trabajador', 'getPertenenciaEtnicas'],
        'tippag' => ['trabajador', 'getTipoPago'],
        'tipcue' => ['trabajador', 'getTipoCuenta'],
        'codban' => ['trabajador', 'getBancos'],
        'resguardo_id' => ['trabajador', 'getResguardos'],
        'pub_indigena_id' => ['trabajador', 'getPueblosIndigenas'],
        'otra_empresa' => ['trabajador', 'getLaboraOtraEmpresa'],
        'biotipdoc' => ['trabajador', 'getTiposDocumentos'],
        'coddocrepleg' => ['trabajador', 'getTiposDocumentos'],
        'codact' => ['empresa', 'getActividades'],
        'calemp' => ['empresa', 'getCalidadEmpresa'],
        'tipsoc' => ['empresa', 'getTipoSociedades'],
        'tipper' => ['empresa', 'getTipoPersona'],
        'tipemp' => ['empresa', 'getTipoEmpresa'],
        'comper' => ['conyuge', 'getCompaneroPermanente'],
        'parent' => ['beneficiario', 'getParentesco'],
        'huerfano' => ['beneficiario', 'getHuerfano'],
        'tiphij' => ['beneficiario', 'getTipoHijo'],
        'calendario' => ['beneficiario', 'getCalendario'],
    ];

    private const OPCIONES_EMPRESA = [
        'codciu' => ['empresa', 'getCiudades'],
        'codzon' => ['empresa', 'getZonas'],
        'ciupri' => ['empresa', 'getCiudadesComerciales'],
        'coddocrepleg' => ['empresa', 'getTipoDocumentos'],
        'codact' => ['empresa', 'getActividades'],
        'calemp' => ['empresa', 'getCalidadEmpresa'],
        'tipsoc' => ['empresa', 'getTipoSociedades'],
        'tipper' => ['empresa', 'getTipoPersona'],
        'tipemp' => ['empresa', 'getTipoEmpresa'],
    ];

    /**
     * Secciones del formulario en orden de presentación.
     */
    private const GRUPOS = [
        'Datos empresa' => ['razsoc', 'sigla', 'digver', 'tipper', 'tipemp', 'tipsoc', 'calemp', 'matmer', 'codact', 'fecini', 'tottra', 'valnom'],
        'Datos personales' => [
            'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'orisex', 'estciv', 'cabhog', 'comper', 'tiecon',
            'parent', 'huerfano', 'tiphij', 'calendario', 'cedacu', 'captra', 'tipdis', 'nivedu', 'profesion', 'facvul', 'peretn',
            'resguardo_id', 'pub_indigena_id',
        ],
        'Representante legal' => ['coddocrepleg', 'cedrep', 'repleg', 'priaperepleg', 'segaperepleg', 'prinomrepleg', 'segnomrepleg'],
        'Residencia y contacto' => [
            'codciu', 'ciures', 'direccion', 'barrio', 'barnotif', 'codzon', 'zoneurbana', 'rural', 'vivienda', 'tipviv',
            'telefono', 'celular', 'fax', 'email',
        ],
        'Datos comerciales' => ['ciupri', 'dirpri', 'barcomer', 'telpri', 'celpri', 'emailpri'],
        'Datos laborales' => [
            'codsuc', 'fecing', 'salario', 'tipsal', 'horas', 'tipcon', 'tipjor', 'cargo', 'codocu', 'tipafi', 'trasin', 'comision',
            'ciulab', 'dirlab', 'ruralt', 'otra_empresa', 'empresalab',
        ],
        'Pago de subsidio' => ['tippag', 'codban', 'tipcue', 'numcue'],
        'Padre/madre biológico' => [
            'biotipdoc', 'biocedu', 'biopriape', 'biosegape', 'bioprinom', 'biosegnom', 'biocodciu', 'biodire', 'biourbana',
            'biophone', 'bioemail', 'biodesco',
        ],
        'Autorizaciones' => ['autoriza'],
    ];

    private const GRUPO_DEFECTO = 'Otros datos';

    private const TIPOPC_EMPRESA = '2';

    private const CAMPOS_EMAIL = ['email', 'emailpri', 'bioemail'];

    private const CAMPOS_FECHA = ['fecnac', 'fecing', 'fecini'];

    private const CAMPOS_NUMERICOS = ['salario', 'tottra', 'valnom', 'horas'];

    public function __construct(private EditarSolicitudService $editarSolicitudService) {}

    /**
     * Componentes del formulario de edición del flujo, en el formato de ComponentModel del frontend.
     *
     * @param  string  $tipopc  Tipo de opción del flujo de afiliación.
     * @return array<int, array<string, mixed>>
     */
    public function campos(string $tipopc): array
    {
        $campos = $this->editarSolicitudService->campos($tipopc);
        $origenes = $tipopc === self::TIPOPC_EMPRESA ? self::OPCIONES_EMPRESA : self::OPCIONES_PERSONA;
        $cargadas = $this->cargarFuentes($campos, $origenes);
        $ordenGrupos = array_flip(array_keys(self::GRUPOS));

        $salida = [];
        foreach ($campos as $campo) {
            $opciones = $campo === 'tipsal' ? tipsal_array() : $this->opciones($origenes[$campo] ?? null, $cargadas);
            $label = self::ETIQUETAS[$campo] ?? $campo;
            $grupo = $this->grupo($campo);
            [$formType, $type] = $this->tipo($campo, $opciones);

            $salida[] = [
                'name' => $campo,
                'label' => $label,
                'placeholder' => $label,
                'type' => $type,
                'form_type' => $formType,
                'data_source' => collect($opciones)->mapWithKeys(fn ($texto, $valor) => [(string) $valor => (string) $texto])->all(),
                'search_type' => 'local',
                'css_classes' => '',
                'event_config' => null,
                'is_readonly' => false,
                'is_disabled' => false,
                'target' => -1,
                'grupo' => $grupo,
                'order' => $ordenGrupos[$grupo] ?? count($ordenGrupos),
            ];
        }

        usort($salida, fn ($a, $b) => $a['order'] <=> $b['order']);

        return $salida;
    }

    private function grupo(string $campo): string
    {
        foreach (self::GRUPOS as $titulo => $campos) {
            if (in_array($campo, $campos, true)) {
                return $titulo;
            }
        }

        return self::GRUPO_DEFECTO;
    }

    /**
     * @return array{0: string, 1: string} [form_type, type]
     */
    private function tipo(string $campo, array $opciones): array
    {
        return match (true) {
            count($opciones) > 0 => ['select', 'select'],
            in_array($campo, self::CAMPOS_FECHA, true) => ['date', 'text'],
            in_array($campo, self::CAMPOS_EMAIL, true) => ['input', 'email'],
            in_array($campo, self::CAMPOS_NUMERICOS, true) => ['input', 'number'],
            default => ['input', 'text'],
        };
    }

    /**
     * Carga los parámetros de SISU solo para las fuentes que usa el flujo.
     *
     * @return array<string, bool> Fuentes cargadas correctamente.
     */
    private function cargarFuentes(array $campos, array $origenes): array
    {
        $fuentes = collect($campos)
            ->map(fn ($campo) => $origenes[$campo][0] ?? null)
            ->filter()
            ->unique();

        $cargadas = [];
        foreach ($fuentes as $fuente) {
            [$clase, $metodo] = self::FUENTES[$fuente];
            try {
                $procesadorComando = new ApiSubsidio;
                $procesadorComando->send([
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => $metodo,
                ]);
                (new $clase)->setDatosCaptura($procesadorComando->toArray());
                $cargadas[$fuente] = true;
            } catch (Throwable $e) {
                $cargadas[$fuente] = false;
            }
        }

        return $cargadas;
    }

    private function opciones(?array $origen, array $cargadas): array
    {
        if (! $origen || empty($cargadas[$origen[0]])) {
            return [];
        }

        [$clase] = self::FUENTES[$origen[0]];
        try {
            $opciones = $clase::{$origen[1]}();
        } catch (Throwable $e) {
            return [];
        }

        return is_array($opciones) ? $opciones : [];
    }
}
