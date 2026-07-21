<?php

namespace App\Services\Reportes;

use App\Models\Gener02;
use App\Models\Gener18;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Services\Srequest;
use Carbon\Carbon;
use DateTimeInterface;

class ReporteSolicitudes
{
    /** @var array<string, string>|null */
    private ?array $tiposDocumentoCache = null;

    /** @var array<string, string>|null */
    private ?array $usuariosCache = null;

    /**
     * Devuelve el dataset (cabeceras + filas) para un reporte de solicitudes
     * segun el tipo indicado. El controlador se encarga de serializar el
     * resultado a un archivo descargable.
     *
     * @return array{title: string, headers: array<int, string>, rows: array<int, array<int, mixed>>}|null
     */
    public function buildDataset(Srequest $request): ?array
    {
        $models = $this->queryModels($request);

        return match ($request->getParam('tipo')) {
            '1' => $this->datasetMercurio31($models),
            '2' => $this->datasetMercurio30($models),
            '3' => $this->datasetMercurio32($models),
            '4' => $this->datasetMercurio34($models),
            default => null,
        };
    }

    public function titleFor(?string $tipo): string
    {
        return match ($tipo) {
            '1' => 'Listado De Solicitudes Trabajadores',
            '2' => 'Listado De Solicitudes Empresas',
            '3' => 'Listado De Solicitudes Conyuges',
            '4' => 'Listado De Solicitudes Beneficiarios',
            default => 'Listado De Solicitudes',
        };
    }

    /**
     * Construye el WHERE crudo compartido por los cuatro modelos.
     * Acepta estado, fecha_solicitud y fecha_aprueba opcionales.
     */
    private function queryModels(Srequest $request)
    {
        $estado = $request->getParam('estado');
        $fecha_solicitud = $request->getParam('fecha_solicitud');
        $fecha_aprueba = $request->getParam('fecha_aprueba');

        $query = '1 = 1';
        $bindings = [];

        if ($estado) {
            $query .= ' AND estado = ?';
            $bindings[] = $estado;
        }
        if ($fecha_solicitud) {
            $query .= ' AND fecsol >= ?';
            $bindings[] = $fecha_solicitud;
        }
        if ($fecha_aprueba) {
            $query .= ' AND fecest <= ?';
            $bindings[] = $fecha_aprueba;
        }

        $modelClass = match ($request->getParam('tipo')) {
            '1' => Mercurio31::class,
            '2' => Mercurio30::class,
            '3' => Mercurio32::class,
            '4' => Mercurio34::class,
            default => null,
        };

        if ($modelClass === null) {
            return collect();
        }

        return $modelClass::whereRaw($query, $bindings)->get();
    }

    /**
     * @return array{title: string, headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function datasetMercurio30($models): array
    {
        $headers = [
            'Radicado',
            'Estado',
            'Fecha solicitud',
            'Nit',
            'Tipo documento empresa',
            'Razon social',
            'Sigla',
            'Digito verificador',
            'Calidad empresa',
            'Cedula representante',
            'Representante legal',
            'Direccion',
            'Ciudad',
            'Zona',
            'Telefono',
            'Celular',
            'Email',
            'Codigo actividad economica',
            'Fecha inicio',
            'Total trabajadores',
            'Valor nomina',
            'Tipo sociedad',
            'Codigo estado',
            'Fecha aprobacion',
            'Usuario',
            'Direccion principal',
            'Ciudad principal',
            'Telefono principal',
            'Celular principal',
            'Email principal',
            'Tipo representante',
            'Tipo documento representante',
            'Apellido paterno representante',
            'Apellido materno representante',
            'Nombre representante',
            'Priape',
            'Segape',
            'Prinom',
            'Segnom',
            'Matricula',
            'Tipo empresa',
        ];

        $rows = $models->map(fn ($m) => [
            $m->ruuid,
            $this->lookupEstado($m->getEstado()),
            $this->formatFecha($m->getFecsol()),
            $m->getNit(),
            $this->lookup($this->catalogoTiposDocumento(), $m->getTipdoc()),
            $m->getRazsoc(),
            $m->getSigla(),
            $m->getDigver(),
            $this->lookup(calemp_array(), $m->getCalemp()),
            $m->getCedrep(),
            $m->getRepleg(),
            $m->getDireccion(),
            $m->getCodciu(),
            $m->getCodzon(),
            $m->getTelefono(),
            $m->getCelular(),
            $m->getEmail(),
            $m->getCodact(),
            $this->formatFecha($m->getFecini()),
            $m->getTottra(),
            $m->getValnom(),
            $m->getTipsoc(),
            $m->getCodest(),
            $this->fechaAprobacion($m),
            $this->lookup($this->catalogoUsuarios(), $m->getUsuario()),
            $m->getDirpri(),
            $m->getCiupri(),
            $m->getTelpri(),
            $m->getCelpri(),
            $m->getEmailpri(),
            $this->lookup(tipper_array(), $m->getTipper()),
            $this->lookup(coddoc_repleg_array(), $m->getCoddocrepleg()),
            $m->getPriaperepleg(),
            $m->getSegaperepleg(),
            $m->getPrinomrepleg(),
            $m->getSegnomrepleg(),
            $m->getMatmer(),
            $this->lookup(get_array_tipos(), $m->getTipemp()),
        ])->all();

        return [
            'title' => 'Listado De Solicitudes Empresas',
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function datasetMercurio31($models): array
    {
        $headers = [
            'Radicado',
            'Estado',
            'Nit',
            'Razon social',
            'Cedula trabajador',
            'Tipo documento',
            'Priape',
            'Segape',
            'Prinom',
            'Segnom',
            'Fecha nacimiento',
            'Ciudad nacimiento',
            'Sexo',
            'Orientacion sexual',
            'Estado civil',
            'Cabeza de hogar',
            'Ciudad',
            'Zona',
            'Direccion',
            'Barrio',
            'Telefono',
            'Celular',
            'Email',
            'Fecha solicitud',
            'Fecha aprobacion',
            'Fecha ingreso',
            'Salario',
            'Captra',
            'Tipo discapacidad',
            'Nivel educacion',
            'Rural',
            'Horas',
            'Tipo contrato',
            'Traslado sindicato',
            'Vivienda',
            'Tipo afiliado',
            'Cargo',
            'Autoriza',
            'Usuario',
            'Estado',
            'Codigo estado',
            'Fecha estado',
            'Tipo solicitante',
            'Tipo documento solicitante',
            'Documento solicitante',
            'Factor vulnerabilidad',
            'Pertenencia etnica',
            'Direccion laboral',
            'Rural trabajo',
            'Comision',
            'Tipo jornada',
            'Codigo sucursal',
            'Tipo salario',
            'Tipo pago',
            'Numero cuenta',
            'Codigo banco',
            'Tipo cuenta',
        ];

        $rows = $models->map(fn ($m) => [
            $m->ruuid,
            $this->lookupEstado($m->getEstado()),
            $m->getNit(),
            $m->getRazsoc(),
            $m->getCedtra(),
            $this->lookup($this->catalogoTiposDocumento(), $m->getTipdoc()),
            $m->getPriape(),
            $m->getSegape(),
            $m->getPrinom(),
            $m->getSegnom(),
            $this->formatFecha($m->getFecnac()),
            $m->getCiunac(),
            $this->lookup(sexos_array(), $m->getSexo()),
            $this->lookup(orientacion_sexual_array(), $m->getOrisex()),
            $this->lookup(estados_civiles_array(), $m->getEstciv()),
            $this->lookup(condicionSN(), $m->getCabhog()),
            $m->getCodciu(),
            $m->getCodzon(),
            $m->getDireccion(),
            $m->getBarrio(),
            $m->getTelefono(),
            $m->getCelular(),
            $m->getEmail(),
            $this->formatFecha($m->getFecsol()),
            $this->fechaAprobacion($m),
            $this->formatFecha($m->getFecing()),
            $m->getSalario(),
            $this->lookup(captra_array(), $m->getCaptra()),
            $this->lookup(tipo_discapacidad_array(), $m->getTipdis()),
            $this->lookup(nivel_educativo_array(), $m->getNivedu()),
            $this->lookup(condicionSN(), $m->getRural()),
            $m->getHoras(),
            $this->lookup(tipo_contrato(), $m->getTipcon()),
            $this->lookup(condicionSN(), $m->getTrasin()),
            $this->lookup(vivienda_array(), $m->getVivienda()),
            $m->getTipafi(),
            $m->getCargo(),
            $this->lookup(condicionSN(), $m->getAutoriza()),
            $this->lookup($this->catalogoUsuarios(), $m->getUsuario()),
            $this->lookupEstado($m->getEstado()),
            $m->getCodest(),
            $this->formatFecha($m->getFecest()),
            $this->lookup(solicitud_tipo_actualizacion_array(), $m->getTipo()),
            $this->lookup($this->catalogoTiposDocumento(), $m->getCoddoc()),
            $m->getDocumento(),
            $this->lookup(vulnerabilidades_array(), $m->getFacvul()),
            $this->lookup(pertenencia_etnica_array(), $m->getPeretn()),
            $m->getDirlab(),
            $this->lookup(condicionSN(), $m->getRuralt()),
            $m->getComision(),
            $this->lookup(tipo_jornada_array(), $m->getTipjor()),
            $m->getCodsuc(),
            $this->lookup(tipsal_array(), $m->getTipsal()),
            $this->lookup(tipo_pago_array(), $m->getTippag()),
            $m->getNumcue(),
            $m->getCodban(),
            $this->lookup(tipo_cuenta_array(), $m->getTipcue()),
        ])->all();

        return [
            'title' => 'Listado De Solicitudes Trabajadores',
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function datasetMercurio32($models): array
    {
        $headers = [
            'Radicado',
            'Id',
            'Cedula trabajador',
            'Cedula conyuge',
            'Tipo documento',
            'Priape',
            'Segape',
            'Prinom',
            'Segnom',
            'Fecha nacimiento',
            'Ciudad nacimiento',
            'Sexo',
            'Estado civil',
            'Compania permanente',
            'Ciudad residencia',
            'Zona',
            'Tipo vivienda',
            'Direccion',
            'Barrio',
            'Telefono',
            'Celular',
            'Email',
            'Nivel educacion',
            'Fecha ingreso',
            'Codigo ocupacion',
            'Salario',
            'Captra',
            'Usuario',
            'Estado',
            'Codigo estado',
            'Fecha estado',
            'Tipo',
            'Codigo documento',
            'Documento',
            'Tiempo convivencia',
            'Tipo salario',
            'Fecha solicitud',
            'Fecha aprobacion',
            'Tipo pago',
            'Numero cuenta',
            'Empresa labora',
        ];

        $rows = $models->map(fn ($m) => [
            $m->ruuid,
            $m->getId(),
            $m->getCedtra(),
            $m->getCedcon(),
            $this->lookup($this->catalogoTiposDocumento(), $m->getTipdoc()),
            $m->getPriape(),
            $m->getSegape(),
            $m->getPrinom(),
            $m->getSegnom(),
            $this->formatFecha($m->getFecnac()),
            $m->getCiunac(),
            $this->lookup(sexos_array(), $m->getSexo()),
            $this->lookup(estados_civiles_array(), $m->getEstciv()),
            $this->lookup(convive_array(), $m->getComper()),
            $m->getCiures(),
            $m->getCodzon(),
            $this->lookup(vivienda_array(), $m->getTipviv()),
            $m->getDireccion(),
            $m->getBarrio(),
            $m->getTelefono(),
            $m->getCelular(),
            $m->getEmail(),
            $this->lookup(nivel_educativo_array(), $m->getNivedu()),
            $this->formatFecha($m->getFecing()),
            $m->getCodocu(),
            $m->getSalario(),
            $this->lookup(captra_array(), $m->getCaptra()),
            $this->lookup($this->catalogoUsuarios(), $m->getUsuario()),
            $this->lookupEstado($m->getEstado()),
            $m->getCodest(),
            $this->formatFecha($m->getFecest()),
            $this->lookup(solicitud_tipo_actualizacion_array(), $m->getTipo()),
            $this->lookup($this->catalogoTiposDocumento(), $m->getCoddoc()),
            $m->getDocumento(),
            $m->getTiecon(),
            $this->lookup(tipsal_array(), $m->getTipsal()),
            $this->formatFecha($m->getFecsol()),
            $this->fechaAprobacion($m),
            $this->lookup(tipo_pago_array(), $m->getTippag()),
            $m->getNumcue(),
            $m->getEmpresalab(),
        ])->all();

        return [
            'title' => 'Listado De Solicitudes Conyuges',
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function datasetMercurio34($models): array
    {
        $headers = [
            'Radicado',
            'Estado',
            'Id',
            'Log',
            'Nit',
            'Cedula trabajador',
            'Cedula conyuge',
            'Numero documento',
            'Tipo documento',
            'Priape',
            'Segape',
            'Prinom',
            'Segnom',
            'Fecha nacimiento',
            'Ciudad nacimiento',
            'Sexo',
            'Parentesco',
            'Huerfano',
            'Tipo hijo',
            'Nivel educacion',
            'Captra',
            'Tipo discapacidad',
            'Calendario',
            'Usuario',
            'Estado',
            'Codigo estado',
            'Fecha estado',
            'Codigo beneficiario',
            'Tipo',
            'Codigo documento',
            'Documento',
            'Cedula acude',
            'Fecha solicitud',
            'Fecha aprobacion',
        ];

        $rows = $models->map(fn ($m) => [
            $m->ruuid,
            $this->lookupEstado($m->getEstado()),
            $m->getId(),
            $m->getLog(),
            $m->getNit(),
            $m->getCedtra(),
            $m->getCedcon(),
            $m->getNumdoc(),
            $this->lookup($this->catalogoTiposDocumento(), $m->getTipdoc()),
            $m->getPriape(),
            $m->getSegape(),
            $m->getPrinom(),
            $m->getSegnom(),
            $this->formatFecha($m->getFecnac()),
            $m->getCiunac(),
            $this->lookup(sexos_array(), $m->getSexo()),
            $this->lookup(parentesco_array(), $m->getParent()),
            $this->lookup(huerfano_array(), $m->getHuerfano()),
            $this->lookup(tipo_hijo_array(), $m->getTiphij()),
            $this->lookup(nivel_educativo_array(), $m->getNivedu()),
            $this->lookup(captra_array(), $m->getCaptra()),
            $this->lookup(tipo_discapacidad_array(), $m->getTipdis()),
            $this->lookup(calendario_array(), $m->getCalendario()),
            $this->lookup($this->catalogoUsuarios(), $m->getUsuario()),
            $this->lookupEstado($m->getEstado()),
            $m->getCodest(),
            $this->formatFecha($m->getFecest()),
            $m->getCodben(),
            $this->lookup(solicitud_tipo_actualizacion_array(), $m->getTipo()),
            $this->lookup($this->catalogoTiposDocumento(), $m->getCoddoc()),
            $m->getDocumento(),
            $m->getCedacu(),
            $this->formatFecha($m->getFecsol()),
            $this->fechaAprobacion($m),
        ])->all();

        return [
            'title' => 'Listado De Solicitudes Beneficiarios',
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * Devuelve la fecha de aprobacion de una solicitud en formato `Y-m-d`.
     * Si `fecapr` esta vacia, recurre a `fecest` (fecha del ultimo cambio de estado).
     */
    private function fechaAprobacion(object $model): ?string
    {
        $fecapr = method_exists($model, 'getFecapr') ? $model->getFecapr() : null;
        if ($this->formatFecha($fecapr) !== null) {
            return $this->formatFecha($fecapr);
        }

        $fecest = method_exists($model, 'getFecest') ? $model->getFecest() : null;

        return $this->formatFecha($fecest);
    }

    /**
     * Normaliza cualquier valor de fecha a string `Y-m-d`.
     * Devuelve `null` si el valor es vacio o no se puede parsear.
     */
    private function formatFecha(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Resuelve un codigo contra un catalogo de clave => descripcion.
     * Si no hay equivalencia, conserva el valor original.
     */
    private function lookup(array $catalogo, mixed $valor): mixed
    {
        if ($valor === null || $valor === '') {
            return $valor;
        }

        $clave = (string) $valor;

        if (array_key_exists($clave, $catalogo)) {
            return $catalogo[$clave];
        }

        if (is_numeric($clave) && array_key_exists((int) $clave, $catalogo)) {
            return $catalogo[(int) $clave];
        }

        return $valor;
    }

    /**
     * Catalogo de tipos de documento (gener18: coddoc => detdoc).
     *
     * @return array<string, string>
     */
    private function catalogoTiposDocumento(): array
    {
        if ($this->tiposDocumentoCache === null) {
            $this->tiposDocumentoCache = Gener18::query()
                ->pluck('detdoc', 'coddoc')
                ->all();
        }

        return $this->tiposDocumentoCache;
    }

    /**
     * Catalogo de usuarios (gener02: usuario => nombre).
     *
     * @return array<string, string>
     */
    private function catalogoUsuarios(): array
    {
        if ($this->usuariosCache === null) {
            $this->usuariosCache = Gener02::query()
                ->pluck('nombre', 'usuario')
                ->all();
        }

        return $this->usuariosCache;
    }

    /**
     * Resuelve el estado de una solicitud usando el catalogo de solicitudes.
     */
    private function lookupEstado(mixed $valor): mixed
    {
        return $this->lookup(array_merge(
            solicitud_estados_array(),
            ['T' => 'Temporal', 'C' => 'Cancelar']
        ), $valor);
    }
}
