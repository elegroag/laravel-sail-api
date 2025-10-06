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
     * @param Srequest $request
     * @param [type] $recurso
     * @return string
     */
    public function main(Srequest $request)
    {
        $estado = $request->getParam('estado');
        $fecha_solicitud = $request->getParam('fecha_solicitud');
        $fecha_aprueba = $request->getParam('fecha_aprueba');

        $query = "1=1 ";
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
                $models = (new Mercurio31())->find("conditions: {$query}");
                return $this->tableMercurio31($models);
                break;
            case '2':
                $models = (new Mercurio30())->find("conditions: {$query}");
                return $this->tableMercurio30($models);
                break;
            case '3':
                $models = (new Mercurio32())->find("conditions: {$query}");
                return $this->tableMercurio32($models);
                break;
            case '4':
                $models = (new Mercurio34())->find("conditions: {$query}");
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
        $fields = array(
            array('Estado', 1, 1, 15),
            array('Fecha solicitud', 2, 2, 18),
            array('Nit', 3, 4, 30),
            array('Tipo documento empresa', 4, 4, 30),
            array('Razon social', 5, 5, 30),
            array('Sigla', 6, 6, 8),
            array('Digito verificador', 7, 7, 8),
            array('Calidad empresa', 8, 8, 30),
            array('Cedula representante', 9, 9, 30),
            array('Representante legal', 10, 10, 30),
            array('Direccion', 11, 11, 30),
            array('Ciudad', 12, 12, 10),
            array('Zona', 13, 13, 10),
            array('Telefono', 14, 14, 10),
            array('Celular', 15, 15, 10),
            array('Email', 16, 16, 30),
            array('Code actividad económica', 17, 17, 30),
            array('Fecha inicio', 18, 18, 15),
            array('Total trabajadores', 19, 19, 30),
            array('Valor nomina', 20, 20, 30),
            array('Tipo sociedad', 21, 21, 30),
            array('Code estado', 22, 22, 30),
            array('Motivo', 23, 23, 30),
            array('Fecha aprobacion', 24, 24, 30),
            array('Usuario', 25, 25, 30),
            array('Direccion principal', 26, 26, 30),
            array('Ciudad principal', 27, 27, 30),
            array('Telefono principal', 28, 28, 30),
            array('Celular principal', 29, 29, 30),
            array('Email principal', 30, 30, 30),
            array('Tipo representante', 31, 31, 30),
            array('Tipo documento representante', 32, 32, 30),
            array('Apellido paterno representante', 33, 33, 30),
            array('Apellido materno representante', 34, 34, 30),
            array('Nombre representante', 35, 35, 30),
            array('Priape', 36, 36, 30),
            array('Segape', 37, 37, 30),
            array('Prinom', 38, 38, 30),
            array('Segnom', 39, 39, 30),
            array('Matricula', 40, 40, 30),
            array('Tipo empresa', 41, 41, 30)
        );

        $factory = new ExcelReportFactory();
        $generator = $factory->createReportGenerator();
        $generator->generateReport("Listado De Solicitudes Empresas", "reporte_solicitudes", $fields);

        foreach ($models as $model) {
            $datos = array(
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
            );
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
        $fields = array(
            array('id', 1, 1, 15),
            array('cedtra', 2, 2, 15),
            array('cedcon', 3, 3, 15),
            array('tipdoc', 4, 4, 15),
            array('priape', 5, 5, 15),
            array('segape', 6, 6, 15),
            array('prinom', 7, 7, 15),
            array('segnom', 8, 8, 15),
            array('fecnac', 9, 9, 15),
            array('ciunac', 10, 10, 15),
            array('sexo', 11, 11, 15),
            array('estciv', 12, 12, 15),
            array('comper', 13, 13, 15),
            array('ciures', 14, 14, 15),
            array('codzon', 15, 15, 15),
            array('tipviv', 16, 16, 15),
            array('direccion', 17, 17, 15),
            array('barrio', 18, 18, 15),
            array('telefono', 19, 19, 15),
            array('celular', 20, 20, 15),
            array('email', 21, 21, 15),
            array('nivedu', 22, 22, 15),
            array('fecing', 23, 23, 15),
            array('codocu', 24, 24, 15),
            array('salario', 25, 25, 15),
            array('captra', 26, 26, 15),
            array('usuario', 27, 27, 15),
            array('estado', 28, 28, 15),
            array('codest', 29, 29, 15),
            array('motivo', 30, 30, 15),
            array('fecest', 31, 31, 15),
            array('tipo', 32, 32, 15),
            array('coddoc', 33, 33, 15),
            array('documento', 34, 34, 15),
            array('tiecon', 35, 35, 15),
            array('tipsal', 36, 36, 15),
            array('fecsol', 37, 37, 15),
            array('tippag', 38, 38, 15),
            array('numcue', 39, 39, 15),
            array('empresalab', 40, 40, 15),
        );

        $factory = new ExcelReportFactory();
        $generator = $factory->createReportGenerator();
        $generator->generateReport("Listado De Solicitudes Conyuges", "reporte_solicitudes", $fields);

        foreach ($models as $model) {
            $datos = array(
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
                $model->getEmpresalab()
            );
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
        $fields = array(
            array('Estado', 1, 1, 15),
            array('id', 2, 2, 15),
            array('log', 3, 3, 15),
            array('nit', 4, 4, 15),
            array('cedtra', 5, 5, 15),
            array('cedcon', 6, 6, 15),
            array('numdoc', 7, 7, 15),
            array('tipdoc', 8, 8, 15),
            array('priape', 9, 9, 15),
            array('segape', 10, 10, 15),
            array('prinom', 11, 11, 15),
            array('segnom', 12, 12, 15),
            array('fecnac', 13, 13, 15),
            array('ciunac', 14, 14, 15),
            array('sexo', 15, 15, 15),
            array('parent', 16, 16, 15),
            array('huerfano', 17, 17, 15),
            array('tiphij', 18, 18, 15),
            array('nivedu', 19, 19, 15),
            array('captra', 20, 20, 15),
            array('tipdis', 21, 21, 15),
            array('calendario', 22, 22, 15),
            array('usuario', 23, 23, 15),
            array('estado', 24, 24, 15),
            array('codest', 25, 25, 15),
            array('motivo', 26, 26, 15),
            array('fecest', 27, 27, 15),
            array('codben', 28, 28, 15),
            array('tipo', 29, 29, 15),
            array('coddoc', 30, 30, 15),
            array('documento', 31, 31, 15),
            array('cedacu', 32, 32, 15),
            array('fecsol', 33, 33, 15),
        );

        $factory = new ExcelReportFactory();
        $generator = $factory->createReportGenerator();
        $generator->generateReport("Listado De Solicitudes Beneficiarios", "reporte_solicitudes", $fields);

        foreach ($models as $model) {
            $datos = array(
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
                $model->getFecsol()
            );
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
        $fields = array(
            array('Estado', 1, 1, 15),
            array('nit', 2, 2, 25),
            array('razsoc', 3, 3, 25),
            array('cedtra', 4, 4, 25),
            array('tipdoc', 5, 5, 25),
            array('priape', 6, 6, 25),
            array('segape', 7, 7, 25),
            array('prinom', 8, 8, 25),
            array('segnom', 9, 9, 25),
            array('fecnac', 10, 10, 25),
            array('ciunac', 11, 11, 25),
            array('sexo', 12, 12, 25),
            array('orisex', 13, 13, 25),
            array('estciv', 14, 14, 25),
            array('cabhog', 15, 15, 25),
            array('codciu', 16, 16, 25),
            array('codzon', 17, 17, 25),
            array('direccion', 18, 18, 25),
            array('barrio', 19, 19, 25),
            array('telefono', 20, 20, 25),
            array('celular', 21, 21, 25),
            array('fax', 22, 22, 25),
            array('email', 23, 23, 25),
            array('fecsol', 24, 24, 25),
            array('fecing', 25, 25, 25),
            array('salario', 26, 26, 25),
            array('captra', 27, 27, 25),
            array('tipdis', 28, 28, 25),
            array('nivedu', 29, 29, 25),
            array('rural', 30, 30, 25),
            array('horas', 31, 31, 25),
            array('tipcon', 32, 32, 25),
            array('trasin', 33, 33, 25),
            array('vivienda', 34, 34, 25),
            array('tipafi', 35, 35, 25),
            array('profesion', 36, 36, 25),
            array('cargo', 37, 37, 25),
            array('autoriza', 38, 38, 25),
            array('usuario', 39, 39, 25),
            array('estado', 40, 40, 25),
            array('codest', 41, 41, 25),
            array('motivo', 42, 42, 25),
            array('fecest', 43, 43, 25),
            array('tipo', 44, 44, 25),
            array('coddoc', 45, 45, 25),
            array('documento', 46, 46, 25),
            array('facvul', 47, 47, 25),
            array('peretn', 48, 48, 25),
            array('dirlab', 49, 49, 25),
            array('ciulab', 50, 50, 25),
            array('ruralt', 51, 51, 25),
            array('comision', 52, 52, 25),
            array('tipjor', 53, 53, 25),
            array('codsuc', 54, 54, 25),
            array('tipsal', 55, 55, 25),
            array('tippag', 56, 56, 25),
            array('numcue', 57, 57, 25),
            array('codban', 58, 58, 25),
            array('tipcue', 59, 59, 25),
        );

        $factory = new ExcelReportFactory();
        $generator = $factory->createReportGenerator();
        $generator->generateReport("Listado De Solicitudes Trabajadores", "reporte_solicitudes", $fields);

        foreach ($models as $model) {
            $datos = array(
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
                $model->getTipcue()
            );
            $generator->addLine($datos, 9);
        }

        $out = $generator->outFile();
        return $out;
    }
}
