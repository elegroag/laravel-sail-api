<?php

namespace App\Services\Reportes;

use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Services\Srequest;

class ReporteSolicitudes
{
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
            'Motivo',
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

        $rows = $models->map(fn($m) => [
            $m->getEstado(),
            $m->getFecsol(),
            $m->getNit(),
            $m->getTipdoc(),
            $m->getRazsoc(),
            $m->getSigla(),
            $m->getDigver(),
            $m->getCalemp(),
            $m->getCedrep(),
            $m->getRepleg(),
            $m->getDireccion(),
            $m->getCodciu(),
            $m->getCodzon(),
            $m->getTelefono(),
            $m->getCelular(),
            $m->getEmail(),
            $m->getCodact(),
            $m->getFecini(),
            $m->getTottra(),
            $m->getValnom(),
            $m->getTipsoc(),
            $m->getCodest(),
            $m->getMotivo(),
            $m->getFecest(),
            $m->getUsuario(),
            $m->getDirpri(),
            $m->getCiupri(),
            $m->getTelpri(),
            $m->getCelpri(),
            $m->getEmailpri(),
            $m->getTipper(),
            $m->getCoddocrepleg(),
            $m->getPriaperepleg(),
            $m->getSegaperepleg(),
            $m->getPrinomrepleg(),
            $m->getSegnomrepleg(),
            $m->getPriape(),
            $m->getSegape(),
            $m->getPrinom(),
            $m->getSegnom(),
            $m->getMatmer(),
            $m->getTipemp(),
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
            'Fax',
            'Email',
            'Fecha solicitud',
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
            'Profesion',
            'Cargo',
            'Autoriza',
            'Usuario',
            'Estado',
            'Codigo estado',
            'Motivo',
            'Fecha estado',
            'Tipo',
            'Codigo documento',
            'Documento',
            'Factor vulnerabilidad',
            'Pertenencia etnica',
            'Direccion laboral',
            'Ciudad laboral',
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

        $rows = $models->map(fn($m) => [
            $m->getEstado(),
            $m->getNit(),
            $m->getRazsoc(),
            $m->getCedtra(),
            $m->getTipdoc(),
            $m->getPriape(),
            $m->getSegape(),
            $m->getPrinom(),
            $m->getSegnom(),
            $m->getFecnac(),
            $m->getCiunac(),
            $m->getSexo(),
            $m->getOrisex(),
            $m->getEstciv(),
            $m->getCabhog(),
            $m->getCodciu(),
            $m->getCodzon(),
            $m->getDireccion(),
            $m->getBarrio(),
            $m->getTelefono(),
            $m->getCelular(),
            $m->getFax(),
            $m->getEmail(),
            $m->getFecsol(),
            $m->getFecing(),
            $m->getSalario(),
            $m->getCaptra(),
            $m->getTipdis(),
            $m->getNivedu(),
            $m->getRural(),
            $m->getHoras(),
            $m->getTipcon(),
            $m->getTrasin(),
            $m->getVivienda(),
            $m->getTipafi(),
            $m->getProfesion(),
            $m->getCargo(),
            $m->getAutoriza(),
            $m->getUsuario(),
            $m->getEstado(),
            $m->getCodest(),
            $m->getMotivo(),
            $m->getFecest(),
            $m->getTipo(),
            $m->getCoddoc(),
            $m->getDocumento(),
            $m->getFacvul(),
            $m->getPeretn(),
            $m->getDirlab(),
            $m->getCiulab(),
            $m->getRuralt(),
            $m->getComision(),
            $m->getTipjor(),
            $m->getCodsuc(),
            $m->getTipsal(),
            $m->getTippag(),
            $m->getNumcue(),
            $m->getCodban(),
            $m->getTipcue(),
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
            'Motivo',
            'Fecha estado',
            'Tipo',
            'Codigo documento',
            'Documento',
            'Tiempo convivencia',
            'Tipo salario',
            'Fecha solicitud',
            'Tipo pago',
            'Numero cuenta',
            'Empresa labora',
        ];

        $rows = $models->map(fn($m) => [
            $m->getId(),
            $m->getCedtra(),
            $m->getCedcon(),
            $m->getTipdoc(),
            $m->getPriape(),
            $m->getSegape(),
            $m->getPrinom(),
            $m->getSegnom(),
            $m->getFecnac(),
            $m->getCiunac(),
            $m->getSexo(),
            $m->getEstciv(),
            $m->getComper(),
            $m->getCiures(),
            $m->getCodzon(),
            $m->getTipviv(),
            $m->getDireccion(),
            $m->getBarrio(),
            $m->getTelefono(),
            $m->getCelular(),
            $m->getEmail(),
            $m->getNivedu(),
            $m->getFecing(),
            $m->getCodocu(),
            $m->getSalario(),
            $m->getCaptra(),
            $m->getUsuario(),
            $m->getEstado(),
            $m->getCodest(),
            $m->getMotivo(),
            $m->getFecest(),
            $m->getTipo(),
            $m->getCoddoc(),
            $m->getDocumento(),
            $m->getTiecon(),
            $m->getTipsal(),
            $m->getFecsol(),
            $m->getTippag(),
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
            'Motivo',
            'Fecha estado',
            'Codigo beneficiario',
            'Tipo',
            'Codigo documento',
            'Documento',
            'Cedula acude',
            'Fecha solicitud',
        ];

        $rows = $models->map(fn($m) => [
            $m->getEstado(),
            $m->getId(),
            $m->getLog(),
            $m->getNit(),
            $m->getCedtra(),
            $m->getCedcon(),
            $m->getNumdoc(),
            $m->getTipdoc(),
            $m->getPriape(),
            $m->getSegape(),
            $m->getPrinom(),
            $m->getSegnom(),
            $m->getFecnac(),
            $m->getCiunac(),
            $m->getSexo(),
            $m->getParent(),
            $m->getHuerfano(),
            $m->getTiphij(),
            $m->getNivedu(),
            $m->getCaptra(),
            $m->getTipdis(),
            $m->getCalendario(),
            $m->getUsuario(),
            $m->getEstado(),
            $m->getCodest(),
            $m->getMotivo(),
            $m->getFecest(),
            $m->getCodben(),
            $m->getTipo(),
            $m->getCoddoc(),
            $m->getDocumento(),
            $m->getCedacu(),
            $m->getFecsol(),
        ])->all();

        return [
            'title' => 'Listado De Solicitudes Beneficiarios',
            'headers' => $headers,
            'rows' => $rows,
        ];
    }
}
