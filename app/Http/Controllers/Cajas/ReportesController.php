<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportesController extends ApplicationController
{

    public function __construct()
    {
       
        $this->setTemplateAfter("main");
    }

    public function indexAction() {}

    public function novedades_Subsidio_viewAction() {}

    public function novedades_SubsidioAction()
    {
        $this->setParamToView("titulo", "Reporte de Novedades de Subsidio");
        $mfecini = $request->input("fecini");
        $mfecfin = $request->input("fecfin");
        $mtipnov = $request->input("tipnov");
        $mdocumento = $request->input("documento");
        $fecini = new Date($mfecini);
        $fecfin = new Date($mfecfin);
        $mwhere_tipnov = "";
        $mwhere_documento = "";
        if (!empty($mtipnov)) {
            $detalle = "";
            if ($mtipnov == "1") {
                $detalle = "EMPLEADORES PRIMERA VEZ";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
            }
            if ($mtipnov == "2") {
                $detalle = "EMPLEADORES SEGUNDA VEZ";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title2 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "5") {
                $detalle = "DESAFILIACIONES EMPLEADORES";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title3 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "7") {
                $detalle = "CAUSA GRAVE";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title4 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "8") {
                $detalle = "NICIO LABORAL TRABAJADORES  ";
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '5';
                $title5 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "9") {
                $detalle = "TERMINACION LABORAL TRABAJADORES  ";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title6 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "10") {
                $detalle = "SUSPENCION TEMPORAL";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title7 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "11") {
                $detalle = "LICENCIAS";
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '8';
                $title8 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "12") {
                $detalle = "MODIFICACION SALARIO";
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '9';
                $title9 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            if ($mtipnov == "13") {
                $detalle = "RETIRO EMPLEADOR";
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (!empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '10';
                $title10 = array(
                    "REPORTE NOVEDADES " . $detalle,
                    "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
                );
            }
            $title = 'title';
            $title1 = array(
                "REPORTE NOVEDADES 3.2.1  AFILIACIONES EMPLEADORES PRIMERA VEZ",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
        } else {
            $title1 = array(
                "REPORTE NOVEDADES 3.2.1  AFILIACIONES EMPLEADORES PRIMERA VEZ",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title2 = array(
                "REPORTE NOVEDADES 3.2.2  AFILIACIONES EMPLEADORES SEGUNDA VEZ",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title3 = array(
                "REPORTE NOVEDADES 3.2.5  DESAFILIACIONES EMPLEADORES",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title4 = array(
                "REPORTE NOVEDADES 3.2.7  PERDIDA DE AFILIACION EMPLEADORES POR CAUSA GRAVE",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title5 = array(
                "REPORTE NOVEDADES 3.2.8 INICIO LABORAL TRABAJADORES ",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title6 = array(
                "REPORTE NOVEDADES 3.2.9 TERMINACION LABORAL TRABAJADORES  ",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title7 = array(
                "REPORTE NOVEDADES 3.2.10 SUSPENCION TEMPORAL DEL CONTRATO DE TRABAJO",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title8 = array(
                "REPORTE NOVEDADES 3.2.11 LICENCIAS REMUNERADAS Y NO REMUNERADAS",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
            $title9 = array(
                "REPORTE NOVEDADES 3.2.12 MODIFICACION DEL SALARIO",
                "RANGO DE FECHAS: " . $fecini->getUsingFormatDefault() . " AL " . $fecfin->getUsingFormatDefault(),
            );
        }
        $_fields['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
        $_fields['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
        $_fields['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
        $_fields['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
        $_fields['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
        $_fields['tipper'] = array('header' => 'TIPO_PERSONA', 'size' => 15, 'align' => 'C');
        $_fields['tipemp'] = array('header' => 'TIPO_EMPRESA', 'size' => 15, 'align' => 'C');
        $_fields['tipdoc'] = array('header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C');
        $_fields['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C');
        $_fields['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
        $_fields['priape'] = array('header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C');
        $_fields['segape'] = array('header' => 'SEGUNDO_APELLIDO', 'size' => 20, 'align' => 'C');
        $_fields['prinom'] = array('header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C');
        $_fields['segnom'] = array('header' => 'SEGUNDO_NOMBRE', 'size' => 20, 'align' => 'C');
        $_fields['fecsol'] = array('header' => 'FECHA_SOLICITUD', 'size' => 15, 'align' => 'C');
        $_fields['fecafi'] = array('header' => 'FEC._AFILIACION', 'size' => 15, 'align' => 'C');
        $_fields['razsoc'] = array('header' => 'RAZON_SOCIAL', 'size' => 40, 'align' => 'C');
        $_fields['matmer'] = array('header' => 'MATRICULA_MERCANTIL', 'size' => 40, 'align' => 'C');
        $_fields['coddep'] = array('header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C');
        $_fields['codmun'] = array('header' => 'COD_MUNICIPIO', 'size' => 10, 'align' => 'C');
        $_fields['direccion'] = array('header' => 'DIRECCION', 'size' => 10, 'align' => 'C');
        $_fields['email'] = array('header' => 'EMAIL', 'size' => 20, 'align' => 'C');
        $_fields['tipdocrep'] = array('header' => 'TIPO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 15, 'align' => 'C');
        $_fields['numdocrep'] = array('header' => 'NUMERO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 10, 'align' => 'C');
        $_fields['prinom2'] = array('header' => 'PRIMER_NOMBRE_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
        $_fields['segnom2'] = array('header' => 'SEGUNDO_NOMBRE SAT_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
        $_fields['priape2'] = array('header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
        $_fields['segape2'] = array('header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
        $_fields['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
        $_fields['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
        $_fields['noafissfant'] = array('header' => 'MANIFESTACION_NO_AFILIACION_OTRA_CAJA', 'size' => 15, 'align' => 'C');
        $_fields['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
        $_fields['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
        $_fields['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        ///////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////
        if ($mtipnov == '2' || $mtipnov == '') {
            $_fields2['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields2['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields2['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields2['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields2['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
            $_fields2['tipper'] = array('header' => 'TIPO_PERSONA', 'size' => 15, 'align' => 'C');
            $_fields2['tipemp'] = array('header' => 'TIPO_EMPRESA', 'size' => 15, 'align' => 'C');
            $_fields2['tipdoc'] = array('header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C');
            $_fields2['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C');
            $_fields2['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields2['priape'] = array('header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields2['segape'] = array('header' => 'SEGUNDO_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields2['prinom'] = array('header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields2['segnom'] = array('header' => 'SEGUNDO_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields2['fecsol'] = array('header' => 'FECHA_SOLICITUD', 'size' => 15, 'align' => 'C');
            $_fields2['fecafi'] = array('header' => 'FEC._AFILIACION', 'size' => 15, 'align' => 'C');
            $_fields2['razsoc'] = array('header' => 'RAZON_SOCIAL', 'size' => 40, 'align' => 'C');
            $_fields2['matmer'] = array('header' => 'MATRICULA_MERCANTIL', 'size' => 40, 'align' => 'C');
            $_fields2['coddep'] = array('header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C');
            $_fields2['codmun'] = array('header' => 'COD_MUNICIPIO', 'size' => 10, 'align' => 'C');
            $_fields2['direccion'] = array('header' => 'DIRECCION', 'size' => 10, 'align' => 'C');
            $_fields2['email'] = array('header' => 'EMAIL', 'size' => 20, 'align' => 'C');
            $_fields2['tipdocrep'] = array('header' => 'TIPO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 15, 'align' => 'C');
            $_fields2['tipdocrep'] = array('header' => 'TIPO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 15, 'align' => 'C');
            $_fields2['numdocrep'] = array('header' => 'NUMERO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 10, 'align' => 'C');
            $_fields2['prinom2'] = array('header' => 'PRIMER_NOMBRE_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
            $_fields2['segnom2'] = array('header' => 'SEGUNDO_NOMBRE SAT_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
            $_fields2['priape2'] = array('header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
            $_fields2['segape2'] = array('header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C');
            $_fields2['codcaj'] = array('header' => 'CODIGO_CAJA', 'size' => 20, 'align' => 'C');
            $_fields2['pazsal'] = array('header' => 'PAZ_Y_SALVO', 'size' => 20, 'align' => 'C');
            $_fields2['fecpazsal'] = array('header' => 'FECHA_PAZ_Y_SALVO', 'size' => 20, 'align' => 'C');
            $_fields2['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
            $_fields2['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
            $_fields2['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields2['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields2['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        if ($mtipnov == '5' || $mtipnov == '') {
            $_fields3['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields3['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields3['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields3['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields3['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
            $_fields3['tipdoc'] = array('header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C');
            $_fields3['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C');
            $_fields3['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields3['fecsol'] = array('header' => 'FECHA_SOLICITUD', 'size' => 15, 'align' => 'C');
            $_fields3['fecdes'] = array('header' => 'FECHA_DESAFILIACION', 'size' => 15, 'align' => 'C');
            $_fields3['coddep'] = array('header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C');
            $_fields3['pazsal'] = array('header' => 'PAZ_Y_SALVO', 'size' => 20, 'align' => 'C');
            $_fields3['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
            $_fields3['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
            $_fields3['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields3['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields3['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        if ($mtipnov == '7' || $mtipnov == '') {
            $_fields4['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields4['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields4['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields4['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields4['tipdoc'] = array('header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C');
            $_fields4['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C');
            $_fields4['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields4['fecper'] = array('header' => 'FECHA_PERDIDA_AFILIACION', 'size' => 15, 'align' => 'C');
            $_fields4['razsoc'] = array('header' => 'RAZON_SOCIAL', 'size' => 40, 'align' => 'C');
            $_fields4['coddep'] = array('header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C');
            $_fields4['causa'] = array('header' => 'CAUSAL_DE_RETIRO', 'size' => 15, 'align' => 'C');
            $_fields4['estado'] = array('header' => 'ESTADO_DEL_REPORTE', 'size' => 15, 'align' => 'C');
            $_fields4['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields4['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields4['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        if ($mtipnov == '8' || $mtipnov == '') {
            $_fields5['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields5['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields5['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields5['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields5['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
            $_fields5['tipdoc'] = array('header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C');
            $_fields5['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C');
            $_fields5['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields5['tipini'] = array('header' => 'TIPO', 'size' => 15, 'align' => 'C');
            $_fields5['fecini'] = array('header' => 'FECHA_INICIO_LABORAL', 'size' => 15, 'align' => 'C');
            $_fields5['tipdoctra'] = array('header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields5['numdoctra'] = array('header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields5['prinom'] = array('header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields5['segnom'] = array('header' => 'SEGUNDO_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields5['priape'] = array('header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields5['segape'] = array('header' => 'SEGUNDO_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields5['sexo'] = array('header' => 'SEXO', 'size' => 40, 'align' => 'C');
            $_fields5['fecnac'] = array('header' => 'FEC_NACIMIENTO', 'size' => 15, 'align' => 'C');
            $_fields5['coddep'] = array('header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C');
            $_fields5['codmun'] = array('header' => 'COD_MUNICIPIO', 'size' => 10, 'align' => 'C');
            $_fields5['direccion'] = array('header' => 'DIRECCION', 'size' => 10, 'align' => 'C');
            $_fields5['telefono'] = array('header' => 'TELEFONO', 'size' => 40, 'align' => 'C');
            $_fields5['email'] = array('header' => 'EMAIL', 'size' => 20, 'align' => 'C');
            $_fields5['salario'] = array('header' => 'SALARIO', 'size' => 20, 'align' => 'C');
            $_fields5['tipsal'] = array('header' => 'TIPO_SALARIO', 'size' => 20, 'align' => 'C');
            $_fields5['hortra'] = array('header' => 'HORAS_DE_TRABAJO', 'size' => 20, 'align' => 'C');
            $_fields5['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
            $_fields5['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
            $_fields5['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields5['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields5['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        if ($mtipnov == '9' || $mtipnov == '') {
            $_fields6['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields6['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields6['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields6['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields6['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
            $_fields6['tipdoc'] = array('header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C');
            $_fields6['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C');
            $_fields6['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields6['tipter'] = array('header' => 'TIPO_TERMINACION', 'size' => 15, 'align' => 'C');
            $_fields6['fecter'] = array('header' => 'FECHA_TERMINACION_LABORAL', 'size' => 15, 'align' => 'C');
            $_fields6['tipdoctra'] = array('header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields6['numdoctra'] = array('header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields6['prinom'] = array('header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields6['priape'] = array('header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields6['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
            $_fields6['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
            $_fields6['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields6['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields6['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        if ($mtipnov == '10' || $mtipnov == '') {
            $_fields7['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields7['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields7['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields7['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields7['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
            $_fields7['tipdoc'] = array('header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C');
            $_fields7['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C');
            $_fields7['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields7['fecini'] = array('header' => 'FECHA_INICIO_SUSPENCION', 'size' => 15, 'align' => 'C');
            $_fields7['tipdoctra'] = array('header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields7['numdoctra'] = array('header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields7['prinom'] = array('header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields7['priape'] = array('header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields7['fecfin'] = array('header' => 'FECHA_FIN_SUSPENCION', 'size' => 15, 'align' => 'C');
            $_fields7['indnov'] = array('header' => 'INDICADOR_DE_LA_NOVEDAD', 'size' => 15, 'align' => 'C');
            $_fields7['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
            $_fields7['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
            $_fields7['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields7['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields7['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        if ($mtipnov == '11' || $mtipnov == '') {
            $_fields8['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields8['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields8['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields8['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields8['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
            $_fields8['tipdoc'] = array('header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C');
            $_fields8['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C');
            $_fields8['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields8['tiplin'] = array('header' => 'TIPO_LICENCIA', 'size' => 15, 'align' => 'C');
            $_fields8['fecini'] = array('header' => 'FECHA_INICIO_LICENCIA', 'size' => 15, 'align' => 'C');
            $_fields8['fecfin'] = array('header' => 'FECHA_FIN_LICENCIA', 'size' => 15, 'align' => 'C');
            $_fields8['tipdoctra'] = array('header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields8['numdoctra'] = array('header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields8['prinom'] = array('header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields8['priape'] = array('header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields8['indnov'] = array('header' => 'INDICADOR_DE_LA_NOVEDAD', 'size' => 15, 'align' => 'C');
            $_fields8['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
            $_fields8['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
            $_fields8['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields8['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields8['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        if ($mtipnov == '12' || $mtipnov == '') {
            $_fields9['fecha'] = array('header' => 'FECHA', 'size' => 15, 'align' => 'C');
            $_fields9['hora'] = array('header' => 'HORA', 'size' => 15, 'align' => 'C');
            $_fields9['usuario'] = array('header' => 'USUARIO', 'size' => 15, 'align' => 'C');
            $_fields9['numtraccf'] = array('header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C');
            $_fields9['numtrasat'] = array('header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C');
            $_fields9['tipper'] = array('header' => 'TIPO_PERSONA', 'size' => 15, 'align' => 'C');
            $_fields9['tipdoc'] = array('header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C');
            $_fields9['numdocemp'] = array('header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C');
            $_fields9['serialsat'] = array('header' => 'SERIALSAT', 'size' => 10, 'align' => 'C');
            $_fields9['fecmod'] = array('header' => 'FECHA_MODIFICACION_SALARIO', 'size' => 15, 'align' => 'C');
            $_fields9['tipdoctra'] = array('header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields9['numdoctra'] = array('header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C');
            $_fields9['prinom'] = array('header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C');
            $_fields9['priape'] = array('header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C');
            $_fields9['salario'] = array('header' => 'SALARIO', 'size' => 15, 'align' => 'C');
            $_fields9['tipsal'] = array('header' => 'TIPO_SALARIO', 'size' => 15, 'align' => 'C');
            $_fields9['autmandat'] = array('header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C');
            $_fields9['autenvnot'] = array('header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C');
            $_fields9['rsultado'] = array('header' => 'RESULTADO', 'size' => 25, 'align' => 'C');
            $_fields9['mensaje'] = array('header' => 'MENSAJE', 'size' => 30, 'align' => 'C');
            $_fields9['codigo'] = array('header' => 'CODIGO', 'size' => 10, 'align' => 'C');
        }
        $report = new UserReportExcel($title1, $_fields);
        $report->startReport("EMPLEADOR PRIMERA VEZ");
        $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
        $msat02 = $this->Sat02->find("numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '1'  )", "order: numtraccf  ASC ");
        foreach ($msat02 as $sat02) {
            $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat02->getNumtraccf()}'   ");
            $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
            $report->put('fecha', trim($sat20->getFecha()));
            $report->put('hora', trim($sat20->getHora()));
            $report->put('usuario', $sat20->getUsuario() . " " . trim($mgener02->getNombre()));
            $report->put('numtraccf', trim($sat20->getNumtraccf()));
            $report->put('numtrasat', trim($sat02->getNumtrasat()));
            $report->put('tipper', trim($sat02->getTipper()));
            $report->put('tipemp', trim($sat02->getTipemp()));
            $report->put('tipdoc', trim($sat02->getTipdocemp()));
            $report->put('numdocemp', trim($sat02->getNumdocemp()));
            $report->put('serialsat', trim($sat02->getSersat()));
            $report->put('priape', trim($sat02->getPriape()));
            $report->put('segape', trim($sat02->getSegape()));
            $report->put('prinom', trim($sat02->getPrinom()));
            $report->put('segnom', trim($sat02->getSegnom()));
            $report->put('fecsol', trim($sat02->getFecsol()));
            $report->put('fecafi', trim($sat02->getFecafi()));
            $report->put('razsoc', trim($sat02->getRazsoc()));
            $report->put('matmer', trim($sat02->getMatmer()));
            $report->put('coddep', trim($sat02->getCoddep()));
            $report->put('codmun', trim($sat02->getCodmun()));
            $report->put('direccion', trim($sat02->getDireccion()));
            $report->put('email', trim($sat02->getEmail()));
            $report->put('tipdocrep', trim($sat02->getTipdocrep()));
            $report->put('numdocrep', trim($sat02->getNumdocrep()));
            $report->put('prinom2', trim($sat02->getPrinom2()));
            $report->put('segnom2', trim($sat02->getSegnom2()));
            $report->put('priape2', trim($sat02->getPriape2()));
            $report->put('segape2', trim($sat02->getSegape2()));
            $report->put('autmandat', trim($sat02->getAutmandat()));
            $report->put('autenvnot', trim($sat02->getAutenvnot()));
            $report->put('noafissfant', trim($sat02->getNoafissfant()));
            $report->put('rsultado', trim($sat02->getResultado()));
            $report->put('mensaje', trim($sat02->getMensaje()));
            $report->put('codigo', trim($sat02->getCodigo()));
            $report->outPutToReport();
        }
        if ($mtipnov == '2' || $mtipnov == '') {
            $report->startReport("EMPLEADOR SEGUNDA VEZ", $title2, $_fields2);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat03 = $this->Sat03->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '2'  )", "order: numtraccf  ASC ");
            foreach ($msat03 as $sat03) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat03->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('numtrasat', trim($sat03->getNumtrasat()));
                $report->put('tipper', trim($sat03->getTipper()));
                $report->put('tipemp', trim($sat03->getTipemp()));
                $report->put('tipdoc', trim($sat03->getTipdocemp()));
                $report->put('numdocemp', trim($sat03->getNumdocemp()));
                $report->put('serialsat', trim($sat03->getSersat()));
                $report->put('priape', trim($sat03->getPriape()));
                $report->put('segape', trim($sat03->getSegape()));
                $report->put('prinom', trim($sat03->getPrinom()));
                $report->put('segnom', trim($sat03->getSegnom()));
                $report->put('fecsol', trim($sat03->getFecsol()));
                $report->put('fecafi', trim($sat03->getFecafi()));
                $report->put('razsoc', trim($sat03->getRazsoc()));
                $report->put('matmer', trim($sat03->getMatmer()));
                $report->put('coddep', trim($sat03->getCoddep()));
                $report->put('codmun', trim($sat03->getCodmun()));
                $report->put('direccion', trim($sat03->getDireccion()));
                $report->put('email', trim($sat03->getEmail()));
                $report->put('tipdocrep', trim($sat03->getTipdocrep()));
                $report->put('numdocrep', trim($sat03->getNumdocrep()));
                $report->put('priape2', trim($sat03->getPriape2()));
                $report->put('segape2', trim($sat03->getSegape2()));
                $report->put('prinom2', trim($sat03->getPrinom2()));
                $report->put('segnom2', trim($sat03->getSegnom2()));
                $report->put('codcaj', trim($sat03->getCodcaj()));
                $report->put('pazsal', trim($sat03->getPazsal()));
                $report->put('fecpazsal', trim($sat03->getFecpazsal()));
                $report->put('autmandat', trim($sat03->getAutmandat()));
                $report->put('autenvnot', trim($sat03->getAutenvnot()));
                $report->put('rsultado', trim($sat03->getResultado()));
                $report->put('mensaje', trim($sat03->getMensaje()));
                $report->put('codigo', trim($sat03->getCodigo()));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '5' || $mtipnov == '') {
            $report->startReport("DESAFILIACION EMPLEADOR", $title3, $_fields3);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat06 = $this->Sat06->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '3'  )", "order: numtraccf  ASC  ");
            foreach ($msat06 as $sat06) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat06->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('numtrasat', trim($sat06->getNumtrasat()));
                $report->put('tipdoc', trim($sat06->getTipdocemp()));
                $report->put('numdocemp', trim($sat06->getNumdocemp()));
                $report->put('serialsat', trim($sat06->getSersat()));
                $report->put('fecsol', trim($sat06->getFecsol()));
                $report->put('fecdes', trim($sat06->getFecdes()));
                $report->put('coddep', trim($sat06->getCoddep()));
                $report->put('pazsal', trim($sat06->getPazsal()));
                $report->put('autmandat', trim($sat06->getAutmandat()));
                $report->put('autenvnot', trim($sat06->getAutenvnot()));
                $report->put('rsultado', trim($sat06->getResultado()));
                $report->put('mensaje', trim($sat06->getMensaje()));
                $report->put('codigo', trim($sat06->getCodigo()));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '7' || $mtipnov == '') {
            $report->startReport("CAUSA GRAVE", $title4, $_fields4);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat08 = $this->Sat08->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '4'  )", "order: numtraccf  ASC  ");
            foreach ($msat08 as $sat08) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat08->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('tipdoc', trim($sat08->getTipdocemp()));
                $report->put('numdocemp', trim($sat08->getNumdocemp()));
                $report->put('serialsat', trim($sat08->getSersat()));
                $report->put('fecper', trim($sat08->getFecper()));
                $report->put('razsoc', trim($sat08->getRazsoc()));
                $report->put('coddep', trim($sat08->getCoddep()));
                $report->put('causa', trim($sat08->getCausa()));
                $report->put('estado', trim($sat08->getEstado()));
                $report->put('rsultado', trim($sat08->getResultado()));
                $report->put('mensaje', trim($sat08->getMensaje()));
                $report->put('codigo', trim($sat08->getCodigo()));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '8' || $mtipnov == '') {
            $report->startReport("INICIO LABORAL", $title5, $_fields5);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat09 = $this->Sat09->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '5'  )", "order: numtraccf  ASC ");
            foreach ($msat09 as $sat09) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat09->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('numtrasat', trim($sat09->getNumtrasat()));
                $report->put('tipdoc', trim($sat09->getTipdocemp()));
                $report->put('numdocemp', trim($sat09->getNumdocemp()));
                $report->put('serialsat', trim($sat09->getSersat()));
                $report->put('tipini', trim($sat09->getTipini()));
                $report->put('fecini', trim($sat09->getFecini()));
                $report->put('tipdoctra', trim($sat09->getTipdoctra()));
                $report->put('numdoctra', trim($sat09->getNumdoctra()));
                $report->put('prinom', trim($sat09->getPrinom()));
                $report->put('segnom', trim($sat09->getSegnom()));
                $report->put('priape', trim($sat09->getPriape()));
                $report->put('segape', trim($sat09->getSegape()));
                $report->put('sexo', trim($sat09->getSexo()));
                $report->put('fecnac', trim($sat09->getFecnac()));
                $report->put('coddep', trim($sat09->getCoddep()));
                $report->put('codmun', trim($sat09->getCodmun()));
                $report->put('direccion', trim($sat09->getDireccion()));
                $report->put('telefono', trim($sat09->getTelefono()));
                $report->put('email', trim($sat09->getEmail()));
                $report->put('salario', trim($sat09->getSalario()));
                $report->put('tipsal', trim($sat09->getTipsal()));
                $report->put('hortra', trim($sat09->getHortra()));
                $report->put('autmandat', trim($sat09->getAutmandat()));
                $report->put('autenvnot', trim($sat09->getAutenvnot()));
                $report->put('rsultado', trim($sat09->getResultado()));
                $report->put('mensaje', trim($sat09->getMensaje()));
                $report->put('codigo', trim($sat09->getCodigo()));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '9' || $mtipnov == '') {
            $report->startReport("TERMINACION LABORAL", $title6, $_fields6);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat10 = $this->Sat10->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '6'  )", "order: numtraccf  ASC ");
            foreach ($msat10 as $sat10) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat10->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('numtrasat', trim($sat10->getNumtrasat()));
                $report->put('tipdoc', trim($sat10->getTipdocemp()));
                $report->put('numdocemp', trim($sat10->getNumdocemp()));
                $report->put('serialsat', trim($sat10->getSersat()));
                $report->put('tipter', trim($sat10->getTipter()));
                $report->put('fecter', trim($sat10->getFecter()));
                $report->put('tipdoctra', trim($sat10->getTipdoctra()));
                $report->put('numdoctra', trim($sat10->getNumdoctra()));
                $report->put('prinom', trim($sat10->getPrinom()));
                $report->put('priape', trim($sat10->getPriape()));
                $report->put('autmandat', trim($sat10->getAutmandat()));
                $report->put('autenvnot', trim($sat10->getAutenvnot()));
                $report->put('rsultado', trim($sat10->getResultado()));
                $report->put('mensaje', trim($sat10->getMensaje()));
                $report->put('codigo', trim($sat10->getCodigo()));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '10' || $mtipnov == '') {
            $report->startReport("SUSPENCION TEMPORAL CT", $title7, $_fields7);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat11 = $this->Sat11->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '7'  )", "order: numtraccf  ASC ");
            foreach ($msat11 as $sat11) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat11->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('numtrasat', trim($sat11->getNumtrasat()));
                $report->put('tipdoc', trim($sat11->getTipdocemp()));
                $report->put('numdocemp', trim($sat11->getNumdocemp()));
                $report->put('serialsat', trim($sat11->getSersat()));
                $report->put('fecini', trim($sat11->getFecini()));
                $report->put('tipdoctra', trim($sat11->getTipdoctra()));
                $report->put('numdoctra', trim($sat11->getNumdoctra()));
                $report->put('prinom', trim($sat11->getPrinom()));
                $report->put('priape', trim($sat11->getPriape()));
                $report->put('fecfin', trim($sat11->getFecfin()));
                $report->put('indnov', trim($sat11->getIndnov()));
                $report->put('autmandat', trim($sat11->getAutmandat()));
                $report->put('autenvnot', trim($sat11->getAutenvnot()));
                $report->put('rsultado', trim($sat11->getResultado()));
                $report->put('mensaje', trim($sat11->getMensaje()));
                $report->put('codigo', trim($sat11->getCodigo()));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '11' || $mtipnov == '') {
            $report->startReport("LICENCIAS", $title8, $_fields8);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat12 = $this->Sat12->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '8'  )", "order: numtraccf  ASC ");
            foreach ($msat12 as $sat12) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat12->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('numtrasat', trim($sat12->getNumtrasat()));
                $report->put('tipdoc', trim($sat12->getTipdocemp()));
                $report->put('numdocemp', trim($sat12->getNumdocemp()));
                $report->put('serialsat', trim($sat12->getSersat()));
                $report->put('tiplin', trim($sat12->getTiplin()));
                $report->put('fecini', trim($sat12->getFecini()));
                $report->put('fecfin', trim($sat12->getFecfin()));
                $report->put('tipdoctra', trim($sat12->getTipdoctra()));
                $report->put('numdoctra', trim($sat12->getNumdoctra()));
                $report->put('prinom', trim($sat12->getPrinom()));
                $report->put('priape', trim($sat12->getPriape()));
                $report->put('indnov', trim($sat12->getIndnov()));
                $report->put('autmandat', trim($sat12->getAutmandat()));
                $report->put('autenvnot', trim($sat12->getAutenvnot()));
                $report->put('rsultado', trim($sat12->getResultado()));
                $report->put('mensaje', trim($sat12->getMensaje()));
                $report->put('codigo', trim($sat12->getCodigo()));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '12' || $mtipnov == '') {
            $report->startReport("MODIFICACION SALARIO", $title9, $_fields9);
            $conditions = "fecha >= '" . $fecini->getUsingFormatDefault() . "' AND fecha <= '" . $fecfin->getUsingFormatDefault() . "'";
            $msat13 = $this->Sat13->find(" numtraccf IN (SELECT numtraccf FROM empresa.sat20 as sat20  WHERE  $conditions AND tiptra = '9'  )", "order: numtraccf  ASC ");
            foreach ($msat13 as $sat13) {
                $sat20 = $this->Sat20->findFirst("numtraccf = '{$sat13->getNumtraccf()}' ");
                $mgener02 = $this->Gener02->findFirst(" usuario = '{$sat20->getUsuario()}' ");
                $report->put('fecha', trim($sat20->getFecha()));
                $report->put('hora', trim($sat20->getHora()));
                $report->put('usuario', trim($sat20->getUsuario() . " " . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->getNumtraccf()));
                $report->put('numtrasat', trim($sat13->getNumtrasat()));
                $report->put('tipdoc', trim($sat13->getTipdocemp()));
                $report->put('numdocemp', trim($sat13->getNumdocemp()));
                $report->put('serialsat', trim($sat13->getSersat()));
                $report->put('fecmod', trim($sat13->getFecmod()));
                $report->put('tipdoctra', trim($sat13->getTipdoctra()));
                $report->put('numdoctra', trim($sat13->getNumdoctra()));
                $report->put('priape', trim($sat13->getPriape()));
                $report->put('prinom', trim($sat13->getPrinom()));
                $report->put('salario', trim($sat13->getSalario()));
                $report->put('tipsal', trim($sat13->getTipsal()));
                $report->put('autmandat', trim($sat13->getAutmandat()));
                $report->put('autenvnot', trim($sat13->getAutenvnot()));
                $report->put('rsultado', trim($sat13->getResultado()));
                $report->put('mensaje', trim($sat13->getMensaje()));
                $report->put('codigo', trim($sat13->getCodigo()));
                $report->outPutToReport();
            }
        }

        ob_end_clean();
        $report->finishReport("novedades_SAT_{$fecfin->getUsingFormatDefault()}", "D");
    }
}
