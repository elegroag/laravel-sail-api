<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobaciondatosController extends ApplicationController
{

    private $tipopc = "14";
    protected $db;

    /**
     * services variable
     * @var Services
     */
    protected $services;

    public function initialize()
    {
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        Core::importLibrary("ParamsTrabajador", "Collections");
        $this->services = Services::Init();
        $this->setTemplateAfter('bone');
        $this->setPersistance(false);
        if (!$this->db) {
            $this->db = (object) DbBase::rawConnect();
            $this->db->setFetchMode(DbBase::DB_ASSOC);
        }
    }

    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "131", "info" => "132", "buscar" => "133", "aprobar" => "134", "devolver" => "135", "rechazar" => "136");
        $flag = parent::beforeFilter($permisos);
        if (!$flag) {
            $response = parent::errorFunc("No cuenta con los permisos para este proceso");
            if (is_ajax()) {
                $this->setResponse("ajax");
                $this->renderObject($response, false);
            } else {
                Router::redirectToApplication('Cajas/principal/index');
            }
            return false;
        }
    }

    public function aplicarFiltroAction($estado = 'P')
    {
        $this->setResponse("ajax");
        $cantidad_pagina = ($this->getPostParam("numero")) ? $this->getPostParam("numero") : 10;
        $usuario = parent::getActUser();
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Request(
                array(
                    "cantidadPaginas" => $cantidad_pagina,
                    "query" => $query_str,
                    "estado" => $estado
                )
            )
        );

        $query = $pagination->filter(
            $this->getPostParam('campo'),
            $this->getPostParam('condi'),
            $this->getPostParam('value')
        );

        Flash::set_flashdata("filter_datos_empresa", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);
        $response = $pagination->render(new UpDatosTrabajadorService());
        return $this->renderObject($response, false);
    }


    public function changeCantidadPaginaAction($estado = 'P')
    {
        $this->buscarAction($estado);
    }

    public function indexAction()
    {
        $this->setParamToView("hide_header", true);
        $campo_field = array(
            "documento" => "Documento",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("title", "Aprobacion Datos Basicos");
        $this->setParamToView("buttons", array("F"));
        Tag::setDocumentTitle('Aprobacion Datos Basicos');
        $this->loadParametrosView();
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
    }

    function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo"  => "parametros_trabajadores",
            )
        );

        $paramsTrabajador = new ParamsTrabajador();
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

        $_codciu = ParamsTrabajador::getCiudades();
        $_ciunac = $_codciu;
        foreach (ParamsTrabajador::getZonas() as $ai => $valor) {
            if ($ai < 19001 && $ai >= 18001) $_codzon[$ai] = $valor;
        }
        $_tipsal = $this->Mercurio31->getTipsalArray();
        $this->setParamToView("_ciunac", $_ciunac);
        $this->setParamToView("_tipsal", $_tipsal);
        $this->setParamToView("_codciu", $_codciu);
        $this->setParamToView("_codzon", $_codzon);
        $this->setParamToView("_coddoc", ParamsTrabajador::getTiposDocumentos());
        $this->setParamToView("_sexo",  ParamsTrabajador::getSexos());
        $this->setParamToView("_estciv", ParamsTrabajador::getEstadoCivil());
        $this->setParamToView("_cabhog", ParamsTrabajador::getCabezaHogar());
        $this->setParamToView("_captra",  ParamsTrabajador::getCapacidadTrabajar());
        $this->setParamToView("_tipdis", ParamsTrabajador::getTipoDiscapacidad());
        $this->setParamToView("_nivedu", ParamsTrabajador::getNivelEducativo());
        $this->setParamToView("_rural", ParamsTrabajador::getRural());
        $this->setParamToView("_tipcon", ParamsTrabajador::getTipoContrato());
        $this->setParamToView("_trasin", ParamsTrabajador::getSindicalizado());
        $this->setParamToView("_vivienda", ParamsTrabajador::getVivienda());
        $this->setParamToView("_tipafi", ParamsTrabajador::getTipoAfiliado());
        $this->setParamToView("_cargo", ParamsTrabajador::getOcupaciones());
        $this->setParamToView("_orisex", ParamsTrabajador::getOrientacionSexual());
        $this->setParamToView("_facvul", ParamsTrabajador::getVulnerabilidades());
        $this->setParamToView("_peretn", ParamsTrabajador::getPertenenciaEtnicas());
        $this->setParamToView("_vendedor", ParamsTrabajador::getVendedor());
        $this->setParamToView("_empleador", ParamsTrabajador::getEmpleador());
        $this->setParamToView("_tippag", ParamsTrabajador::getTipoPago());
        $this->setParamToView("_tipcue", ParamsTrabajador::getTipoCuenta());
        $this->setParamToView("_giro", ParamsTrabajador::getGiro());
        $this->setParamToView("_codgir", ParamsTrabajador::getCodigoGiro());
        $this->setParamToView("_bancos", ParamsTrabajador::getBancos());
        $this->setParamToView("tipo",   'T');
        $this->setParamToView("tipopc",  $this->tipopc);
    }

    public function buscarAction($estado = 'P')
    {
        $this->setResponse("ajax");
        $pagina = ($this->getPostParam('pagina')) ? $this->getPostParam('pagina') : 1;
        $cantidad_pagina = ($this->getPostParam("numero")) ? $this->getPostParam("numero") : 10;
        $usuario = parent::getActUser();
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Request(
                array(
                    "cantidadPaginas" => $cantidad_pagina,
                    "pagina" => $pagina,
                    "query" => $query_str,
                    "estado" => $estado
                )
            )
        );

        if (Flash::get_flashdata_item("filter_empresa") != false) {
            $query = $pagination->persistencia(Flash::get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $this->getPostParam('campo'),
                $this->getPostParam('condi'),
                $this->getPostParam('value')
            );
        }

        Flash::set_flashdata("filter_empresa", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new UpDatosTrabajadorService());
        return $this->renderObject($response, false);
    }

    /**
     * inforAction function
     *
     * @return void
     */
    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $upServices = new UpDatosTrabajadorService();
            $id = $this->getPostParam('id');
            if (!$id) {
                throw new Exception("Error se requiere del id independiente", 501);
            }

            $mercurio47 = (new Mercurio47)->findFirst("id='{$id}' AND tipo_actualizacion='T'");
            $mercurio33 = (new Mercurio33)->find("actualizacion='{$id}'");
            $dataItems = array();

            foreach ($mercurio33 as $row) {
                $campo = $row->getCampo();
                $dataItems["{$campo}"] = $row->getValor();
            }

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_trabajadores"
                )
            );

            $datos_captura =  $ps->toArray();
            $paramsIndependiente = new ParamsTrabajador();
            $paramsIndependiente->setDatosCaptura($datos_captura);


            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_trabajador",
                    "params" => $mercurio47->getDocumento()
                )
            );
            $sout =  $ps->toArray();
            $datosTraSisu = ($sout['success'] == true) ? $sout['data'] : false;

            $datostra = array_merge($datosTraSisu, $mercurio47->getArray(), $dataItems);

            $htmlEmpresa = View::render('aprobaciondatos/tmp/consulta', array(
                'datostra' => $datostra,
                'dataItems' => $dataItems,
                'mercurio01' => $this->Mercurio01->findFirst(),
                'det_tipo' => $this->Mercurio06->findFirst("tipo = '{$mercurio47->getTipo()}'")->getDetalle(),
                '_coddoc' => ParamsTrabajador::getTiposDocumentos(),
                '_codciu' => ParamsTrabajador::getCiudades(),
                '_codzon' => ParamsTrabajador::getZonas(),
                '_sexos' => ParamsTrabajador::getSexos(),
                '_estciv' => ParamsTrabajador::getEstadoCivil(),
                '_cabhog' => ParamsTrabajador::getCabezaHogar(),
                '_captra' => ParamsTrabajador::getCapacidadTrabajar(),
                '_tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
                '_nivedu' => ParamsTrabajador::getNivelEducativo(),
                '_rural' => ParamsTrabajador::getRural(),
                '_tipcon' => ParamsTrabajador::getTipoContrato(),
                '_vivienda' => ParamsTrabajador::getVivienda(),
                '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
                '_trasin' => ParamsTrabajador::getSindicalizado(),
                '_bancos' => ParamsTrabajador::getBancos()
            ));

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio47->getDocumento()
                    )
                )
            );
            $out =  $ps->toArray();

            if ($out['success']) {
                $this->setParamToView("empresa_sisuweb", $out['data']);
            }
            $response = array(
                'success' => true,
                'data' => $mercurio47->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta" => $htmlEmpresa,
                'adjuntos' => $upServices->adjuntos($mercurio47),
                'seguimiento' => $upServices->seguimiento($mercurio47),
                'campos_disponibles' => $mercurio47->CamposDisponibles()
            );
        } catch (Exception $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function apruebaAction()
    {
        $this->setResponse("ajax");
        $debuginfo = array();
        try {
            try {
                $user = Auth::getActiveIdentity();
                $acceso = (new Gener42)->count("*", "conditions: permiso='62' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
                }
                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $apruebaSolicitud = new ApruebaDatosTrabajador();
                $apruebaSolicitud->setTransa();
                $apruebaSolicitud->findSolicitud($idSolicitud);
                $apruebaSolicitud->findSolicitante();
                $apruebaSolicitud->procesar($_POST);
                $apruebaSolicitud->endTransa();
                $apruebaSolicitud->enviarMail($this->getPostParam('actapr'), $this->getPostParam('fecapr'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (DebugException $err) {
                $debuginfo = $err->getDebugInfo();
                $apruebaSolicitud->closeTransa($err->getMessage());
                $salida = array(
                    "success" => false,
                    "msj" => $err->getMessage()
                );
            }
        } catch (TransactionFailed $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
            );
        }

        if ($debuginfo) $salida['info'] = $debuginfo;
        return $this->renderObject($salida, false);
    }

    public function rechazarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $nota = $this->getPostParam('nota', "addslaches", "alpha", "extraspaces", "striptags");
                $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio10", "mercurio33");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $today = new Date();
                $mercurio33 = $this->Mercurio33->findFirst("id='$id'");
                $this->Mercurio33->updateAll("estado='X',motivo='$nota',codest='$codest',fecest='{$today->getUsingFormatDefault()}'", "conditions: id='$id' ");
                $mercurio07 = $this->Mercurio07->findFirst("tipo='{$mercurio33->getTipo()}' and documento = '{$mercurio33->getDocumento()}'");
                $asunto = "Actualizacion de datos";
                $msj  = "Se rechazo la actualizacion de datos";
                $senderEmail = new SenderEmail();
                $senderEmail->sendEmail($mercurio07->getEmail(), $mercurio07->getNombre(), $asunto, $msj, "");
                parent::finishTrans();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }
}
