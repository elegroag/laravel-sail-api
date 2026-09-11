<?php

namespace App\Http\Controllers\Cajas;

use App\Models\Gener02;
use App\Http\Controllers\Adapter\ApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use UserReportExcel;

class ReportesController extends ApplicationController
{

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function index() {}

    public function novedadesSubsidioView() {}

    public function novedadesSubsidio(Request $request)
    {
        $this->setParamToView('titulo', 'Reporte de Novedades de Subsidio');
        $mfecini = $request->input('fecini');
        $mfecfin = $request->input('fecfin');
        $mtipnov = $request->input('tipnov');
        $mdocumento = $request->input('documento');
        $fecini = new \DateTime($mfecini);
        $fecfin = new \DateTime($mfecfin);
        $mwhere_tipnov = '';
        $mwhere_documento = '';
        if (! empty($mtipnov)) {
            $detalle = '';
            if ($mtipnov == '1') {
                $detalle = 'EMPLEADORES PRIMERA VEZ';
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
            }
            if ($mtipnov == '2') {
                $detalle = 'EMPLEADORES SEGUNDA VEZ';
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title2 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '5') {
                $detalle = 'DESAFILIACIONES EMPLEADORES';
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title3 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '7') {
                $detalle = 'CAUSA GRAVE';
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title4 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '8') {
                $detalle = 'NICIO LABORAL TRABAJADORES  ';
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '5';
                $title5 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '9') {
                $detalle = 'TERMINACION LABORAL TRABAJADORES  ';
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title6 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '10') {
                $detalle = 'SUSPENCION TEMPORAL';
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $title7 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '11') {
                $detalle = 'LICENCIAS';
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '8';
                $title8 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '12') {
                $detalle = 'MODIFICACION SALARIO';
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '9';
                $title9 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            if ($mtipnov == '13') {
                $detalle = 'RETIRO EMPLEADOR';
                $mwhere_tipnov = " AND tiptra = '$mtipnov'";
                if (! empty($mdocumento)) {
                    $mwhere_documento = " AND numdocemp = '$mdocumento'";
                }
                $mtit = '10';
                $title10 = [
                    'REPORTE NOVEDADES ' . $detalle,
                    'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
                ];
            }
            $title = 'title';
            $title1 = [
                'REPORTE NOVEDADES 3.2.1  AFILIACIONES EMPLEADORES PRIMERA VEZ',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
        } else {
            $title1 = [
                'REPORTE NOVEDADES 3.2.1  AFILIACIONES EMPLEADORES PRIMERA VEZ',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title2 = [
                'REPORTE NOVEDADES 3.2.2  AFILIACIONES EMPLEADORES SEGUNDA VEZ',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title3 = [
                'REPORTE NOVEDADES 3.2.5  DESAFILIACIONES EMPLEADORES',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title4 = [
                'REPORTE NOVEDADES 3.2.7  PERDIDA DE AFILIACION EMPLEADORES POR CAUSA GRAVE',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title5 = [
                'REPORTE NOVEDADES 3.2.8 INICIO LABORAL TRABAJADORES ',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title6 = [
                'REPORTE NOVEDADES 3.2.9 TERMINACION LABORAL TRABAJADORES  ',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title7 = [
                'REPORTE NOVEDADES 3.2.10 SUSPENCION TEMPORAL DEL CONTRATO DE TRABAJO',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title8 = [
                'REPORTE NOVEDADES 3.2.11 LICENCIAS REMUNERADAS Y NO REMUNERADAS',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
            $title9 = [
                'REPORTE NOVEDADES 3.2.12 MODIFICACION DEL SALARIO',
                'RANGO DE FECHAS: ' . $fecini->format('Y-m-d') . ' AL ' . $fecfin->format('Y-m-d'),
            ];
        }
        $_fields['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
        $_fields['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
        $_fields['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
        $_fields['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
        $_fields['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
        $_fields['tipper'] = ['header' => 'TIPO_PERSONA', 'size' => 15, 'align' => 'C'];
        $_fields['tipemp'] = ['header' => 'TIPO_EMPRESA', 'size' => 15, 'align' => 'C'];
        $_fields['tipdoc'] = ['header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C'];
        $_fields['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C'];
        $_fields['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
        $_fields['priape'] = ['header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C'];
        $_fields['segape'] = ['header' => 'SEGUNDO_APELLIDO', 'size' => 20, 'align' => 'C'];
        $_fields['prinom'] = ['header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C'];
        $_fields['segnom'] = ['header' => 'SEGUNDO_NOMBRE', 'size' => 20, 'align' => 'C'];
        $_fields['fecsol'] = ['header' => 'FECHA_SOLICITUD', 'size' => 15, 'align' => 'C'];
        $_fields['fecafi'] = ['header' => 'FEC._AFILIACION', 'size' => 15, 'align' => 'C'];
        $_fields['razsoc'] = ['header' => 'RAZON_SOCIAL', 'size' => 40, 'align' => 'C'];
        $_fields['matmer'] = ['header' => 'MATRICULA_MERCANTIL', 'size' => 40, 'align' => 'C'];
        $_fields['coddep'] = ['header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C'];
        $_fields['codmun'] = ['header' => 'COD_MUNICIPIO', 'size' => 10, 'align' => 'C'];
        $_fields['direccion'] = ['header' => 'DIRECCION', 'size' => 10, 'align' => 'C'];
        $_fields['email'] = ['header' => 'EMAIL', 'size' => 20, 'align' => 'C'];
        $_fields['tipdocrep'] = ['header' => 'TIPO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 15, 'align' => 'C'];
        $_fields['numdocrep'] = ['header' => 'NUMERO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 10, 'align' => 'C'];
        $_fields['prinom2'] = ['header' => 'PRIMER_NOMBRE_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
        $_fields['segnom2'] = ['header' => 'SEGUNDO_NOMBRE SAT_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
        $_fields['priape2'] = ['header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
        $_fields['segape2'] = ['header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
        $_fields['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
        $_fields['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
        $_fields['noafissfant'] = ['header' => 'MANIFESTACION_NO_AFILIACION_OTRA_CAJA', 'size' => 15, 'align' => 'C'];
        $_fields['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
        $_fields['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
        $_fields['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        // /////////////////////////////////////////////////////////////////////////////
        // /////////////////////////////////////////////////////////////////////////////
        if ($mtipnov == '2' || $mtipnov == '') {
            $_fields2['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields2['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields2['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields2['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields2['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
            $_fields2['tipper'] = ['header' => 'TIPO_PERSONA', 'size' => 15, 'align' => 'C'];
            $_fields2['tipemp'] = ['header' => 'TIPO_EMPRESA', 'size' => 15, 'align' => 'C'];
            $_fields2['tipdoc'] = ['header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C'];
            $_fields2['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C'];
            $_fields2['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields2['priape'] = ['header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields2['segape'] = ['header' => 'SEGUNDO_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields2['prinom'] = ['header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields2['segnom'] = ['header' => 'SEGUNDO_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields2['fecsol'] = ['header' => 'FECHA_SOLICITUD', 'size' => 15, 'align' => 'C'];
            $_fields2['fecafi'] = ['header' => 'FEC._AFILIACION', 'size' => 15, 'align' => 'C'];
            $_fields2['razsoc'] = ['header' => 'RAZON_SOCIAL', 'size' => 40, 'align' => 'C'];
            $_fields2['matmer'] = ['header' => 'MATRICULA_MERCANTIL', 'size' => 40, 'align' => 'C'];
            $_fields2['coddep'] = ['header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C'];
            $_fields2['codmun'] = ['header' => 'COD_MUNICIPIO', 'size' => 10, 'align' => 'C'];
            $_fields2['direccion'] = ['header' => 'DIRECCION', 'size' => 10, 'align' => 'C'];
            $_fields2['email'] = ['header' => 'EMAIL', 'size' => 20, 'align' => 'C'];
            $_fields2['tipdocrep'] = ['header' => 'TIPO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 15, 'align' => 'C'];
            $_fields2['tipdocrep'] = ['header' => 'TIPO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 15, 'align' => 'C'];
            $_fields2['numdocrep'] = ['header' => 'NUMERO_DOCUMENTO_REPRESENTANTE_LEG', 'size' => 10, 'align' => 'C'];
            $_fields2['prinom2'] = ['header' => 'PRIMER_NOMBRE_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
            $_fields2['segnom2'] = ['header' => 'SEGUNDO_NOMBRE SAT_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
            $_fields2['priape2'] = ['header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
            $_fields2['segape2'] = ['header' => 'PRIMER_APELLIDO_REPRESENTANTE_LEG', 'size' => 20, 'align' => 'C'];
            $_fields2['codcaj'] = ['header' => 'CODIGO_CAJA', 'size' => 20, 'align' => 'C'];
            $_fields2['pazsal'] = ['header' => 'PAZ_Y_SALVO', 'size' => 20, 'align' => 'C'];
            $_fields2['fecpazsal'] = ['header' => 'FECHA_PAZ_Y_SALVO', 'size' => 20, 'align' => 'C'];
            $_fields2['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
            $_fields2['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
            $_fields2['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields2['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields2['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        if ($mtipnov == '5' || $mtipnov == '') {
            $_fields3['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields3['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields3['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields3['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields3['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
            $_fields3['tipdoc'] = ['header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C'];
            $_fields3['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C'];
            $_fields3['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields3['fecsol'] = ['header' => 'FECHA_SOLICITUD', 'size' => 15, 'align' => 'C'];
            $_fields3['fecdes'] = ['header' => 'FECHA_DESAFILIACION', 'size' => 15, 'align' => 'C'];
            $_fields3['coddep'] = ['header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C'];
            $_fields3['pazsal'] = ['header' => 'PAZ_Y_SALVO', 'size' => 20, 'align' => 'C'];
            $_fields3['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
            $_fields3['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
            $_fields3['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields3['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields3['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        if ($mtipnov == '7' || $mtipnov == '') {
            $_fields4['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields4['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields4['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields4['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields4['tipdoc'] = ['header' => 'TIPO_DOCUMENTO', 'size' => 15, 'align' => 'C'];
            $_fields4['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO', 'size' => 10, 'align' => 'C'];
            $_fields4['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields4['fecper'] = ['header' => 'FECHA_PERDIDA_AFILIACION', 'size' => 15, 'align' => 'C'];
            $_fields4['razsoc'] = ['header' => 'RAZON_SOCIAL', 'size' => 40, 'align' => 'C'];
            $_fields4['coddep'] = ['header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C'];
            $_fields4['causa'] = ['header' => 'CAUSAL_DE_RETIRO', 'size' => 15, 'align' => 'C'];
            $_fields4['estado'] = ['header' => 'ESTADO_DEL_REPORTE', 'size' => 15, 'align' => 'C'];
            $_fields4['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields4['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields4['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        if ($mtipnov == '8' || $mtipnov == '') {
            $_fields5['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields5['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields5['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields5['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields5['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
            $_fields5['tipdoc'] = ['header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C'];
            $_fields5['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C'];
            $_fields5['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields5['tipini'] = ['header' => 'TIPO', 'size' => 15, 'align' => 'C'];
            $_fields5['fecini'] = ['header' => 'FECHA_INICIO_LABORAL', 'size' => 15, 'align' => 'C'];
            $_fields5['tipdoctra'] = ['header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields5['numdoctra'] = ['header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields5['prinom'] = ['header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields5['segnom'] = ['header' => 'SEGUNDO_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields5['priape'] = ['header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields5['segape'] = ['header' => 'SEGUNDO_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields5['sexo'] = ['header' => 'SEXO', 'size' => 40, 'align' => 'C'];
            $_fields5['fecnac'] = ['header' => 'FEC_NACIMIENTO', 'size' => 15, 'align' => 'C'];
            $_fields5['coddep'] = ['header' => 'COD_DEPARTAMENTO', 'size' => 15, 'align' => 'C'];
            $_fields5['codmun'] = ['header' => 'COD_MUNICIPIO', 'size' => 10, 'align' => 'C'];
            $_fields5['direccion'] = ['header' => 'DIRECCION', 'size' => 10, 'align' => 'C'];
            $_fields5['telefono'] = ['header' => 'TELEFONO', 'size' => 40, 'align' => 'C'];
            $_fields5['email'] = ['header' => 'EMAIL', 'size' => 20, 'align' => 'C'];
            $_fields5['salario'] = ['header' => 'SALARIO', 'size' => 20, 'align' => 'C'];
            $_fields5['tipsal'] = ['header' => 'TIPO_SALARIO', 'size' => 20, 'align' => 'C'];
            $_fields5['hortra'] = ['header' => 'HORAS_DE_TRABAJO', 'size' => 20, 'align' => 'C'];
            $_fields5['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
            $_fields5['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
            $_fields5['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields5['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields5['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        if ($mtipnov == '9' || $mtipnov == '') {
            $_fields6['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields6['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields6['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields6['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields6['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
            $_fields6['tipdoc'] = ['header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C'];
            $_fields6['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C'];
            $_fields6['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields6['tipter'] = ['header' => 'TIPO_TERMINACION', 'size' => 15, 'align' => 'C'];
            $_fields6['fecter'] = ['header' => 'FECHA_TERMINACION_LABORAL', 'size' => 15, 'align' => 'C'];
            $_fields6['tipdoctra'] = ['header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields6['numdoctra'] = ['header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields6['prinom'] = ['header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields6['priape'] = ['header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields6['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
            $_fields6['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
            $_fields6['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields6['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields6['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        if ($mtipnov == '10' || $mtipnov == '') {
            $_fields7['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields7['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields7['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields7['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields7['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
            $_fields7['tipdoc'] = ['header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C'];
            $_fields7['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C'];
            $_fields7['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields7['fecini'] = ['header' => 'FECHA_INICIO_SUSPENCION', 'size' => 15, 'align' => 'C'];
            $_fields7['tipdoctra'] = ['header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields7['numdoctra'] = ['header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields7['prinom'] = ['header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields7['priape'] = ['header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields7['fecfin'] = ['header' => 'FECHA_FIN_SUSPENCION', 'size' => 15, 'align' => 'C'];
            $_fields7['indnov'] = ['header' => 'INDICADOR_DE_LA_NOVEDAD', 'size' => 15, 'align' => 'C'];
            $_fields7['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
            $_fields7['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
            $_fields7['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields7['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields7['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        if ($mtipnov == '11' || $mtipnov == '') {
            $_fields8['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields8['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields8['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields8['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields8['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
            $_fields8['tipdoc'] = ['header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C'];
            $_fields8['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C'];
            $_fields8['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields8['tiplin'] = ['header' => 'TIPO_LICENCIA', 'size' => 15, 'align' => 'C'];
            $_fields8['fecini'] = ['header' => 'FECHA_INICIO_LICENCIA', 'size' => 15, 'align' => 'C'];
            $_fields8['fecfin'] = ['header' => 'FECHA_FIN_LICENCIA', 'size' => 15, 'align' => 'C'];
            $_fields8['tipdoctra'] = ['header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields8['numdoctra'] = ['header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields8['prinom'] = ['header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields8['priape'] = ['header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields8['indnov'] = ['header' => 'INDICADOR_DE_LA_NOVEDAD', 'size' => 15, 'align' => 'C'];
            $_fields8['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
            $_fields8['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
            $_fields8['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields8['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields8['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        if ($mtipnov == '12' || $mtipnov == '') {
            $_fields9['fecha'] = ['header' => 'FECHA', 'size' => 15, 'align' => 'C'];
            $_fields9['hora'] = ['header' => 'HORA', 'size' => 15, 'align' => 'C'];
            $_fields9['usuario'] = ['header' => 'USUARIO', 'size' => 15, 'align' => 'C'];
            $_fields9['numtraccf'] = ['header' => 'NUMERO_TRANSACCION', 'size' => 10, 'align' => 'C'];
            $_fields9['numtrasat'] = ['header' => 'NUMERO_TRANSACCION_SAT', 'size' => 15, 'align' => 'C'];
            $_fields9['tipper'] = ['header' => 'TIPO_PERSONA', 'size' => 15, 'align' => 'C'];
            $_fields9['tipdoc'] = ['header' => 'TIPO_DOCUMENTO_EMPLEADOR', 'size' => 15, 'align' => 'C'];
            $_fields9['numdocemp'] = ['header' => 'NUMERO_DOCUMENTO_EMPLEADOR', 'size' => 10, 'align' => 'C'];
            $_fields9['serialsat'] = ['header' => 'SERIALSAT', 'size' => 10, 'align' => 'C'];
            $_fields9['fecmod'] = ['header' => 'FECHA_MODIFICACION_SALARIO', 'size' => 15, 'align' => 'C'];
            $_fields9['tipdoctra'] = ['header' => 'TIPO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields9['numdoctra'] = ['header' => 'NUMERO_DOCUMENTO_TRABAJADOR', 'size' => 15, 'align' => 'C'];
            $_fields9['prinom'] = ['header' => 'PRIMER_NOMBRE', 'size' => 20, 'align' => 'C'];
            $_fields9['priape'] = ['header' => 'PRIMER_APELLIDO', 'size' => 20, 'align' => 'C'];
            $_fields9['salario'] = ['header' => 'SALARIO', 'size' => 15, 'align' => 'C'];
            $_fields9['tipsal'] = ['header' => 'TIPO_SALARIO', 'size' => 15, 'align' => 'C'];
            $_fields9['autmandat'] = ['header' => 'AUTORIZACION_DATOS', 'size' => 15, 'align' => 'C'];
            $_fields9['autenvnot'] = ['header' => 'AUTORIZACION_ENVIO_NOTIFICACIONES', 'size' => 15, 'align' => 'C'];
            $_fields9['rsultado'] = ['header' => 'RESULTADO', 'size' => 25, 'align' => 'C'];
            $_fields9['mensaje'] = ['header' => 'MENSAJE', 'size' => 30, 'align' => 'C'];
            $_fields9['codigo'] = ['header' => 'CODIGO', 'size' => 10, 'align' => 'C'];
        }
        $report = new UserReportExcel($title1, $_fields);
        $report->startReport('EMPLEADOR PRIMERA VEZ', [
            'razsoc' => 'CAJA DE COMPENSACIÓN FAMILIAR DEL CAQUETÁ',
            'nit' => '891.190.047-2',
        ]);
        $msat02 = DB::table('sat02')
            ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                $q->select('numtraccf')
                    ->from('empresa.sat20 as sat20')
                    ->where('fecha', '>=', $fecini->format('Y-m-d'))
                    ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                    ->where('tiptra', '1');
            })
            ->orderBy('numtraccf')
            ->get();
        foreach ($msat02 as $sat02) {
            $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat02->numtraccf)->first();
            $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
            $report->put('fecha', trim($sat20->fecha));
            $report->put('hora', trim($sat20->hora));
            $report->put('usuario', $sat20->usuario . ' ' . trim($mgener02->getNombre()));
            $report->put('numtraccf', trim($sat20->numtraccf));
            $report->put('numtrasat', trim($sat02->numtrasat));
            $report->put('tipper', trim($sat02->tipper));
            $report->put('tipemp', trim($sat02->tipemp));
            $report->put('tipdoc', trim($sat02->tipdocemp));
            $report->put('numdocemp', trim($sat02->numdocemp));
            $report->put('serialsat', trim($sat02->sersat));
            $report->put('priape', trim($sat02->priape));
            $report->put('segape', trim($sat02->segape));
            $report->put('prinom', trim($sat02->prinom));
            $report->put('segnom', trim($sat02->segnom));
            $report->put('fecsol', trim($sat02->fecsol));
            $report->put('fecafi', trim($sat02->fecafi));
            $report->put('razsoc', trim($sat02->razsoc));
            $report->put('matmer', trim($sat02->matmer));
            $report->put('coddep', trim($sat02->coddep));
            $report->put('codmun', trim($sat02->codmun));
            $report->put('direccion', trim($sat02->direccion));
            $report->put('email', trim($sat02->email));
            $report->put('tipdocrep', trim($sat02->tipdocrep));
            $report->put('numdocrep', trim($sat02->numdocrep));
            $report->put('prinom2', trim($sat02->prinom2));
            $report->put('segnom2', trim($sat02->segnom2));
            $report->put('priape2', trim($sat02->priape2));
            $report->put('segape2', trim($sat02->segape2));
            $report->put('autmandat', trim($sat02->autmandat));
            $report->put('autenvnot', trim($sat02->autenvnot));
            $report->put('noafissfant', trim($sat02->noafissfant));
            $report->put('rsultado', trim($sat02->resultado));
            $report->put('mensaje', trim($sat02->mensaje));
            $report->put('codigo', trim($sat02->codigo));
            $report->outPutToReport();
        }
        if ($mtipnov == '2' || $mtipnov == '') {
            $report->startReport('EMPLEADOR SEGUNDA VEZ', $title2, $_fields2);
            $msat03 = DB::table('sat03')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '2');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat03 as $sat03) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat03->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('numtrasat', trim($sat03->numtrasat));
                $report->put('tipper', trim($sat03->tipper));
                $report->put('tipemp', trim($sat03->tipemp));
                $report->put('tipdoc', trim($sat03->tipdocemp));
                $report->put('numdocemp', trim($sat03->numdocemp));
                $report->put('serialsat', trim($sat03->sersat));
                $report->put('priape', trim($sat03->priape));
                $report->put('segape', trim($sat03->segape));
                $report->put('prinom', trim($sat03->prinom));
                $report->put('segnom', trim($sat03->segnom));
                $report->put('fecsol', trim($sat03->fecsol));
                $report->put('fecafi', trim($sat03->fecafi));
                $report->put('razsoc', trim($sat03->razsoc));
                $report->put('matmer', trim($sat03->matmer));
                $report->put('coddep', trim($sat03->coddep));
                $report->put('codmun', trim($sat03->codmun));
                $report->put('direccion', trim($sat03->direccion));
                $report->put('email', trim($sat03->email));
                $report->put('tipdocrep', trim($sat03->tipdocrep));
                $report->put('numdocrep', trim($sat03->numdocrep));
                $report->put('priape2', trim($sat03->priape2));
                $report->put('segape2', trim($sat03->segape2));
                $report->put('prinom2', trim($sat03->prinom2));
                $report->put('segnom2', trim($sat03->segnom2));
                $report->put('codcaj', trim($sat03->codcaj));
                $report->put('pazsal', trim($sat03->pazsal));
                $report->put('fecpazsal', trim($sat03->fecpazsal));
                $report->put('autmandat', trim($sat03->autmandat));
                $report->put('autenvnot', trim($sat03->autenvnot));
                $report->put('rsultado', trim($sat03->resultado));
                $report->put('mensaje', trim($sat03->mensaje));
                $report->put('codigo', trim($sat03->codigo));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '5' || $mtipnov == '') {
            $report->startReport('DESAFILIACION EMPLEADOR', $title3, $_fields3);
            $msat06 = DB::table('sat06')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '3');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat06 as $sat06) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat06->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('numtrasat', trim($sat06->numtrasat));
                $report->put('tipdoc', trim($sat06->tipdocemp));
                $report->put('numdocemp', trim($sat06->numdocemp));
                $report->put('serialsat', trim($sat06->sersat));
                $report->put('fecsol', trim($sat06->fecsol));
                $report->put('fecdes', trim($sat06->fecdes));
                $report->put('coddep', trim($sat06->coddep));
                $report->put('pazsal', trim($sat06->pazsal));
                $report->put('autmandat', trim($sat06->autmandat));
                $report->put('autenvnot', trim($sat06->autenvnot));
                $report->put('rsultado', trim($sat06->resultado));
                $report->put('mensaje', trim($sat06->mensaje));
                $report->put('codigo', trim($sat06->codigo));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '7' || $mtipnov == '') {
            $report->startReport('CAUSA GRAVE', $title4, $_fields4);
            $msat08 = DB::table('sat08')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '4');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat08 as $sat08) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat08->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('tipdoc', trim($sat08->tipdocemp));
                $report->put('numdocemp', trim($sat08->numdocemp));
                $report->put('serialsat', trim($sat08->sersat));
                $report->put('fecper', trim($sat08->fecper));
                $report->put('razsoc', trim($sat08->razsoc));
                $report->put('coddep', trim($sat08->coddep));
                $report->put('causa', trim($sat08->causa));
                $report->put('estado', trim($sat08->estado));
                $report->put('rsultado', trim($sat08->resultado));
                $report->put('mensaje', trim($sat08->mensaje));
                $report->put('codigo', trim($sat08->codigo));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '8' || $mtipnov == '') {
            $report->startReport('INICIO LABORAL', $title5, $_fields5);
            $msat09 = DB::table('sat09')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '5');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat09 as $sat09) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat09->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('numtrasat', trim($sat09->numtrasat));
                $report->put('tipdoc', trim($sat09->tipdocemp));
                $report->put('numdocemp', trim($sat09->numdocemp));
                $report->put('serialsat', trim($sat09->sersat));
                $report->put('tipini', trim($sat09->tipini));
                $report->put('fecini', trim($sat09->fecini));
                $report->put('tipdoctra', trim($sat09->tipdoctra));
                $report->put('numdoctra', trim($sat09->numdoctra));
                $report->put('prinom', trim($sat09->prinom));
                $report->put('segnom', trim($sat09->segnom));
                $report->put('priape', trim($sat09->priape));
                $report->put('segape', trim($sat09->segape));
                $report->put('sexo', trim($sat09->sexo));
                $report->put('fecnac', trim($sat09->fecnac));
                $report->put('coddep', trim($sat09->coddep));
                $report->put('codmun', trim($sat09->codmun));
                $report->put('direccion', trim($sat09->direccion));
                $report->put('telefono', trim($sat09->telefono));
                $report->put('email', trim($sat09->email));
                $report->put('salario', trim($sat09->salario));
                $report->put('tipsal', trim($sat09->tipsal));
                $report->put('hortra', trim($sat09->hortra));
                $report->put('autmandat', trim($sat09->autmandat));
                $report->put('autenvnot', trim($sat09->autenvnot));
                $report->put('rsultado', trim($sat09->resultado));
                $report->put('mensaje', trim($sat09->mensaje));
                $report->put('codigo', trim($sat09->codigo));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '9' || $mtipnov == '') {
            $report->startReport('TERMINACION LABORAL', $title6, $_fields6);
            $msat10 = DB::table('sat10')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '6');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat10 as $sat10) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat10->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('numtrasat', trim($sat10->numtrasat));
                $report->put('tipdoc', trim($sat10->tipdocemp));
                $report->put('numdocemp', trim($sat10->numdocemp));
                $report->put('serialsat', trim($sat10->sersat));
                $report->put('tipter', trim($sat10->tipter));
                $report->put('fecter', trim($sat10->fecter));
                $report->put('tipdoctra', trim($sat10->tipdoctra));
                $report->put('numdoctra', trim($sat10->numdoctra));
                $report->put('prinom', trim($sat10->prinom));
                $report->put('priape', trim($sat10->priape));
                $report->put('autmandat', trim($sat10->autmandat));
                $report->put('autenvnot', trim($sat10->autenvnot));
                $report->put('rsultado', trim($sat10->resultado));
                $report->put('mensaje', trim($sat10->mensaje));
                $report->put('codigo', trim($sat10->codigo));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '10' || $mtipnov == '') {
            $report->startReport('SUSPENCION TEMPORAL CT', $title7, $_fields7);
            $msat11 = DB::table('sat11')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '7');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat11 as $sat11) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat11->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('numtrasat', trim($sat11->numtrasat));
                $report->put('tipdoc', trim($sat11->tipdocemp));
                $report->put('numdocemp', trim($sat11->numdocemp));
                $report->put('serialsat', trim($sat11->sersat));
                $report->put('fecini', trim($sat11->fecini));
                $report->put('tipdoctra', trim($sat11->tipdoctra));
                $report->put('numdoctra', trim($sat11->numdoctra));
                $report->put('prinom', trim($sat11->prinom));
                $report->put('priape', trim($sat11->priape));
                $report->put('fecfin', trim($sat11->fecfin));
                $report->put('indnov', trim($sat11->indnov));
                $report->put('autmandat', trim($sat11->autmandat));
                $report->put('autenvnot', trim($sat11->autenvnot));
                $report->put('rsultado', trim($sat11->resultado));
                $report->put('mensaje', trim($sat11->mensaje));
                $report->put('codigo', trim($sat11->codigo));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '11' || $mtipnov == '') {
            $report->startReport('LICENCIAS', $title8, $_fields8);
            $msat12 = DB::table('sat12')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '8');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat12 as $sat12) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat12->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('numtrasat', trim($sat12->numtrasat));
                $report->put('tipdoc', trim($sat12->tipdocemp));
                $report->put('numdocemp', trim($sat12->numdocemp));
                $report->put('serialsat', trim($sat12->sersat));
                $report->put('tiplin', trim($sat12->tiplin));
                $report->put('fecini', trim($sat12->fecini));
                $report->put('fecfin', trim($sat12->fecfin));
                $report->put('tipdoctra', trim($sat12->tipdoctra));
                $report->put('numdoctra', trim($sat12->numdoctra));
                $report->put('prinom', trim($sat12->prinom));
                $report->put('priape', trim($sat12->priape));
                $report->put('indnov', trim($sat12->indnov));
                $report->put('autmandat', trim($sat12->autmandat));
                $report->put('autenvnot', trim($sat12->autenvnot));
                $report->put('rsultado', trim($sat12->resultado));
                $report->put('mensaje', trim($sat12->mensaje));
                $report->put('codigo', trim($sat12->codigo));
                $report->outPutToReport();
            }
        }
        if ($mtipnov == '12' || $mtipnov == '') {
            $report->startReport('MODIFICACION SALARIO', $title9, $_fields9);
            $msat13 = DB::table('sat13')
                ->whereIn('numtraccf', function ($q) use ($fecini, $fecfin) {
                    $q->select('numtraccf')
                        ->from('empresa.sat20 as sat20')
                        ->where('fecha', '>=', $fecini->format('Y-m-d'))
                        ->where('fecha', '<=', $fecfin->format('Y-m-d'))
                        ->where('tiptra', '9');
                })
                ->orderBy('numtraccf')
                ->get();
            foreach ($msat13 as $sat13) {
                $sat20 = DB::table('empresa.sat20')->where('numtraccf', $sat13->numtraccf)->first();
                $mgener02 = Gener02::where('usuario', $sat20->usuario)->first();
                $report->put('fecha', trim($sat20->fecha));
                $report->put('hora', trim($sat20->hora));
                $report->put('usuario', trim($sat20->usuario . ' ' . $mgener02->getNombre()));
                $report->put('numtraccf', trim($sat20->numtraccf));
                $report->put('numtrasat', trim($sat13->numtrasat));
                $report->put('tipdoc', trim($sat13->tipdocemp));
                $report->put('numdocemp', trim($sat13->numdocemp));
                $report->put('serialsat', trim($sat13->sersat));
                $report->put('fecmod', trim($sat13->fecmod));
                $report->put('tipdoctra', trim($sat13->tipdoctra));
                $report->put('numdoctra', trim($sat13->numdoctra));
                $report->put('priape', trim($sat13->priape));
                $report->put('prinom', trim($sat13->prinom));
                $report->put('salario', trim($sat13->salario));
                $report->put('tipsal', trim($sat13->tipsal));
                $report->put('autmandat', trim($sat13->autmandat));
                $report->put('autenvnot', trim($sat13->autenvnot));
                $report->put('rsultado', trim($sat13->resultado));
                $report->put('mensaje', trim($sat13->mensaje));
                $report->put('codigo', trim($sat13->codigo));
                $report->outPutToReport();
            }
        }

        ob_end_clean();
        $report->finishReport("novedades_SAT_{$fecfin->format('Y-m-d')}", 'D');
    }
}
