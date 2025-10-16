<?php

namespace App\Services\Reportes;

use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Services\FactoryReportes\ExcelReportFactory;
use App\Services\Srequest;

class ReporteSolicitudes
{
    /**
     * main function
     *
     * @param [type] $recurso
     * @return string
     */
    public function main(Srequest $request)
    {
        $estado = $request->getParam('estado');
        $fecha_solicitud = $request->getParam('fecha_solicitud');
        $fecha_aprueba = $request->getParam('fecha_aprueba');

        $query = '1=1 ';
        if ($estado) {
            $query .= " AND estado = '{$estado}' ";
        }
        if ($fecha_solicitud) {
            $query .= " AND fecsol >= '{$fecha_solicitud}' ";
        }
        if ($fecha_aprueba) {
            $query .= " AND fecest <= '{$fecha_aprueba}' ";
        }
        switch ($request->getParam('tipo')) {
            case '1':
                $models = Mercurio31::whereRaw("{$query}")->get();
                return $this->tableMercurio31($models);
                break;
            case '2':
                $models = Mercurio30::whereRaw("{$query}")->get();
                return $this->tableMercurio30($models);
                break;
            case '3':
                $models = Mercurio32::whereRaw("{$query}")->get();
                return $this->tableMercurio32($models);
                break;
            case '4':
                $models = Mercurio34::whereRaw("{$query}")->get();
                return $this->tableMercurio34($models);
                break;
        }
    }

    /**
     * tableMercurio30 function
     *
     * @param [type] $models
     * @return string
     */
    public function tableMercurio30($models)
    {
        $fields = [
            ['Estado', 1, 1, 15],
            ['Fecha solicitud', 2, 2, 18],
            ['Nit', 3, 4, 30],
            ['Tipo documento empresa', 4, 4, 30],
            ['Razon social', 5, 5, 30],
            ['Sigla', 6, 6, 8],
            ['Digito verificador', 7, 7, 8],
            ['Calidad empresa', 8, 8, 30],
            ['Cedula representante', 9, 9, 30],
            ['Representante legal', 10, 10, 30],
            ['Direccion', 11, 11, 30],
            ['Ciudad', 12, 12, 10],
            ['Zona', 13, 13, 10],
            ['Telefono', 14, 14, 10],
            ['Celular', 15, 15, 10],
            ['Email', 16, 16, 30],
            ['Code actividad económica', 17, 17, 30],
            ['Fecha inicio', 18, 18, 15],
            ['Total trabajadores', 19, 19, 30],
            ['Valor nomina', 20, 20, 30],
            ['Tipo sociedad', 21, 21, 30],
            ['Code estado', 22, 22, 30],
            ['Motivo', 23, 23, 30],
            ['Fecha aprobacion', 24, 24, 30],
            ['Usuario', 25, 25, 30],
            ['Direccion principal', 26, 26, 30],
            ['Ciudad principal', 27, 27, 30],
            ['Telefono principal', 28, 28, 30],
            ['Celular principal', 29, 29, 30],
            ['Email principal', 30, 30, 30],
            ['Tipo representante', 31, 31, 30],
            ['Tipo documento representante', 32, 32, 30],
            ['Apellido paterno representante', 33, 33, 30],
            ['Apellido materno representante', 34, 34, 30],
            ['Nombre representante', 35, 35, 30],
            ['Priape', 36, 36, 30],
            ['Segape', 37, 37, 30],
            ['Prinom', 38, 38, 30],
            ['Segnom', 39, 39, 30],
            ['Matricula', 40, 40, 30],
            ['Tipo empresa', 41, 41, 30],
        ];

        $factory = new ExcelReportFactory;
        $generator = $factory->createReportGenerator();
        $generator->generateReport('Listado De Solicitudes Empresas', 'reporte_solicitudes', $fields);

        foreach ($models as $model) {
            $datos = [
                $model->getEstado(),
                $model->getFecsol(),
                $model->getNit(),
                $model->getTipdoc(),
                $model->getRazsoc(),
                $model->getSigla(),
                $model->getDigver(),
                $model->getCalemp(),
                $model->getCedrep(),
                $model->getRepleg(),
                $model->getDireccion(),
                $model->getCodciu(),
                $model->getCodzon(),
                $model->getTelefono(),
                $model->getCelular(),
                $model->getEmail(),
                $model->getCodact(),
                $model->getFecini(),
                $model->getTottra(),
                $model->getValnom(),
                $model->getTipsoc(),
                $model->getCodest(),
                $model->getMotivo(),
                $model->getFecest(),
                $model->getUsuario(),
                $model->getDirpri(),
                $model->getCiupri(),
                $model->getTelpri(),
                $model->getCelpri(),
                $model->getEmailpri(),
                $model->getTipper(),
                $model->getCoddocrepleg(),
                $model->getPriaperepleg(),
                $model->getSegaperepleg(),
                $model->getPrinomrepleg(),
                $model->getSegnomrepleg(),
                $model->getPriape(),
                $model->getSegape(),
                $model->getPrinom(),
                $model->getSegnom(),
                $model->getMatmer(),
                $model->getTipemp(),
            ];
            $generator->addLine($datos, 9);
        }

        $out = $generator->outFile();

        return $out;
    }

    /**
     * Undocumented function
     *
     * @param [type] $models
     * @return void
     */
    public function tableMercurio32($models)
    {
        $fields = [
            ['id', 1, 1, 15],
            ['cedtra', 2, 2, 15],
            ['cedcon', 3, 3, 15],
            ['tipdoc', 4, 4, 15],
            ['priape', 5, 5, 15],
            ['segape', 6, 6, 15],
            ['prinom', 7, 7, 15],
            ['segnom', 8, 8, 15],
            ['fecnac', 9, 9, 15],
            ['ciunac', 10, 10, 15],
            ['sexo', 11, 11, 15],
            ['estciv', 12, 12, 15],
            ['comper', 13, 13, 15],
            ['ciures', 14, 14, 15],
            ['codzon', 15, 15, 15],
            ['tipviv', 16, 16, 15],
            ['direccion', 17, 17, 15],
            ['barrio', 18, 18, 15],
            ['telefono', 19, 19, 15],
            ['celular', 20, 20, 15],
            ['email', 21, 21, 15],
            ['nivedu', 22, 22, 15],
            ['fecing', 23, 23, 15],
            ['codocu', 24, 24, 15],
            ['salario', 25, 25, 15],
            ['captra', 26, 26, 15],
            ['usuario', 27, 27, 15],
            ['estado', 28, 28, 15],
            ['codest', 29, 29, 15],
            ['motivo', 30, 30, 15],
            ['fecest', 31, 31, 15],
            ['tipo', 32, 32, 15],
            ['coddoc', 33, 33, 15],
            ['documento', 34, 34, 15],
            ['tiecon', 35, 35, 15],
            ['tipsal', 36, 36, 15],
            ['fecsol', 37, 37, 15],
            ['tippag', 38, 38, 15],
            ['numcue', 39, 39, 15],
            ['empresalab', 40, 40, 15],
        ];

        $factory = new ExcelReportFactory;
        $generator = $factory->createReportGenerator();
        $generator->generateReport('Listado De Solicitudes Conyuges', 'reporte_solicitudes', $fields);

        foreach ($models as $model) {
            $datos = [
                $model->getEstado(),
                $model->getId(),
                $model->getCedtra(),
                $model->getCedcon(),
                $model->getTipdoc(),
                $model->getPriape(),
                $model->getSegape(),
                $model->getPrinom(),
                $model->getSegnom(),
                $model->getFecnac(),
                $model->getCiunac(),
                $model->getSexo(),
                $model->getEstciv(),
                $model->getComper(),
                $model->getCiures(),
                $model->getCodzon(),
                $model->getTipviv(),
                $model->getDireccion(),
                $model->getBarrio(),
                $model->getTelefono(),
                $model->getCelular(),
                $model->getEmail(),
                $model->getNivedu(),
                $model->getFecing(),
                $model->getCodocu(),
                $model->getSalario(),
                $model->getCaptra(),
                $model->getUsuario(),
                $model->getEstado(),
                $model->getCodest(),
                $model->getMotivo(),
                $model->getFecest(),
                $model->getTipo(),
                $model->getCoddoc(),
                $model->getDocumento(),
                $model->getTiecon(),
                $model->getTipsal(),
                $model->getFecsol(),
                $model->getTippag(),
                $model->getNumcue(),
                $model->getEmpresalab(),
            ];
            $generator->addLine($datos, 9);
        }

        $out = $generator->outFile();

        return $out;
    }

    /**
     * Undocumented function
     *
     * @param [type] $models
     * @return void
     */
    public function tableMercurio34($models)
    {
        $fields = [
            ['Estado', 1, 1, 15],
            ['id', 2, 2, 15],
            ['log', 3, 3, 15],
            ['nit', 4, 4, 15],
            ['cedtra', 5, 5, 15],
            ['cedcon', 6, 6, 15],
            ['numdoc', 7, 7, 15],
            ['tipdoc', 8, 8, 15],
            ['priape', 9, 9, 15],
            ['segape', 10, 10, 15],
            ['prinom', 11, 11, 15],
            ['segnom', 12, 12, 15],
            ['fecnac', 13, 13, 15],
            ['ciunac', 14, 14, 15],
            ['sexo', 15, 15, 15],
            ['parent', 16, 16, 15],
            ['huerfano', 17, 17, 15],
            ['tiphij', 18, 18, 15],
            ['nivedu', 19, 19, 15],
            ['captra', 20, 20, 15],
            ['tipdis', 21, 21, 15],
            ['calendario', 22, 22, 15],
            ['usuario', 23, 23, 15],
            ['estado', 24, 24, 15],
            ['codest', 25, 25, 15],
            ['motivo', 26, 26, 15],
            ['fecest', 27, 27, 15],
            ['codben', 28, 28, 15],
            ['tipo', 29, 29, 15],
            ['coddoc', 30, 30, 15],
            ['documento', 31, 31, 15],
            ['cedacu', 32, 32, 15],
            ['fecsol', 33, 33, 15],
        ];

        $factory = new ExcelReportFactory;
        $generator = $factory->createReportGenerator();
        $generator->generateReport('Listado De Solicitudes Beneficiarios', 'reporte_solicitudes', $fields);

        foreach ($models as $model) {
            $datos = [
                $model->getEstado(),
                $model->getId(),
                $model->getLog(),
                $model->getNit(),
                $model->getCedtra(),
                $model->getCedcon(),
                $model->getNumdoc(),
                $model->getTipdoc(),
                $model->getPriape(),
                $model->getSegape(),
                $model->getPrinom(),
                $model->getSegnom(),
                $model->getFecnac(),
                $model->getCiunac(),
                $model->getSexo(),
                $model->getParent(),
                $model->getHuerfano(),
                $model->getTiphij(),
                $model->getNivedu(),
                $model->getCaptra(),
                $model->getTipdis(),
                $model->getCalendario(),
                $model->getUsuario(),
                $model->getEstado(),
                $model->getCodest(),
                $model->getMotivo(),
                $model->getFecest(),
                $model->getCodben(),
                $model->getTipo(),
                $model->getCoddoc(),
                $model->getDocumento(),
                $model->getCedacu(),
                $model->getFecsol(),
            ];
            $generator->addLine($datos, 9);
        }

        $out = $generator->outFile();

        return $out;
    }

    /**
     * Undocumented function
     *
     * @param [type] $models
     * @return void
     */
    public function tableMercurio31($models)
    {
        $fields = [
            ['Estado', 1, 1, 15],
            ['nit', 2, 2, 25],
            ['razsoc', 3, 3, 25],
            ['cedtra', 4, 4, 25],
            ['tipdoc', 5, 5, 25],
            ['priape', 6, 6, 25],
            ['segape', 7, 7, 25],
            ['prinom', 8, 8, 25],
            ['segnom', 9, 9, 25],
            ['fecnac', 10, 10, 25],
            ['ciunac', 11, 11, 25],
            ['sexo', 12, 12, 25],
            ['orisex', 13, 13, 25],
            ['estciv', 14, 14, 25],
            ['cabhog', 15, 15, 25],
            ['codciu', 16, 16, 25],
            ['codzon', 17, 17, 25],
            ['direccion', 18, 18, 25],
            ['barrio', 19, 19, 25],
            ['telefono', 20, 20, 25],
            ['celular', 21, 21, 25],
            ['fax', 22, 22, 25],
            ['email', 23, 23, 25],
            ['fecsol', 24, 24, 25],
            ['fecing', 25, 25, 25],
            ['salario', 26, 26, 25],
            ['captra', 27, 27, 25],
            ['tipdis', 28, 28, 25],
            ['nivedu', 29, 29, 25],
            ['rural', 30, 30, 25],
            ['horas', 31, 31, 25],
            ['tipcon', 32, 32, 25],
            ['trasin', 33, 33, 25],
            ['vivienda', 34, 34, 25],
            ['tipafi', 35, 35, 25],
            ['profesion', 36, 36, 25],
            ['cargo', 37, 37, 25],
            ['autoriza', 38, 38, 25],
            ['usuario', 39, 39, 25],
            ['estado', 40, 40, 25],
            ['codest', 41, 41, 25],
            ['motivo', 42, 42, 25],
            ['fecest', 43, 43, 25],
            ['tipo', 44, 44, 25],
            ['coddoc', 45, 45, 25],
            ['documento', 46, 46, 25],
            ['facvul', 47, 47, 25],
            ['peretn', 48, 48, 25],
            ['dirlab', 49, 49, 25],
            ['ciulab', 50, 50, 25],
            ['ruralt', 51, 51, 25],
            ['comision', 52, 52, 25],
            ['tipjor', 53, 53, 25],
            ['codsuc', 54, 54, 25],
            ['tipsal', 55, 55, 25],
            ['tippag', 56, 56, 25],
            ['numcue', 57, 57, 25],
            ['codban', 58, 58, 25],
            ['tipcue', 59, 59, 25],
        ];

        $factory = new ExcelReportFactory;
        $generator = $factory->createReportGenerator();
        $generator->generateReport('Listado De Solicitudes Trabajadores', 'reporte_solicitudes', $fields);

        foreach ($models as $model) {
            $datos = [
                $model->getEstado(),
                $model->getNit(),
                $model->getRazsoc(),
                $model->getCedtra(),
                $model->getTipdoc(),
                $model->getPriape(),
                $model->getSegape(),
                $model->getPrinom(),
                $model->getSegnom(),
                $model->getFecnac(),
                $model->getCiunac(),
                $model->getSexo(),
                $model->getOrisex(),
                $model->getEstciv(),
                $model->getCabhog(),
                $model->getCodciu(),
                $model->getCodzon(),
                $model->getDireccion(),
                $model->getBarrio(),
                $model->getTelefono(),
                $model->getCelular(),
                $model->getFax(),
                $model->getEmail(),
                $model->getFecsol(),
                $model->getFecing(),
                $model->getSalario(),
                $model->getCaptra(),
                $model->getTipdis(),
                $model->getNivedu(),
                $model->getRural(),
                $model->getHoras(),
                $model->getTipcon(),
                $model->getTrasin(),
                $model->getVivienda(),
                $model->getTipafi(),
                $model->getProfesion(),
                $model->getCargo(),
                $model->getAutoriza(),
                $model->getUsuario(),
                $model->getEstado(),
                $model->getCodest(),
                $model->getMotivo(),
                $model->getFecest(),
                $model->getTipo(),
                $model->getCoddoc(),
                $model->getDocumento(),
                $model->getFacvul(),
                $model->getPeretn(),
                $model->getDirlab(),
                $model->getCiulab(),
                $model->getRuralt(),
                $model->getComision(),
                $model->getTipjor(),
                $model->getCodsuc(),
                $model->getTipsal(),
                $model->getTippag(),
                $model->getNumcue(),
                $model->getCodban(),
                $model->getTipcue(),
            ];
            $generator->addLine($datos, 9);
        }

        $out = $generator->outFile();

        return $out;
    }
}
