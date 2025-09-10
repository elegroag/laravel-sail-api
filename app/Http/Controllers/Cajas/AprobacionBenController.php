<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobacionbenController extends ApplicationController
{
    protected $tipopc = 4;

    /**
     * services variable
     * @var Services
     */
    protected $services;

    /**
     * trabajadorServices variable
     * @var BeneficiarioServices
     */
    protected $beneficiarioServices;

    public function initialize()
    {
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        Core::importLibrary("ParamsBeneficiario", "Collections");
        $this->setTemplateAfter('bone');
        $this->services = Services::Init();
    }

    public function beforeFilter($permisos = array())
    {
        $permisos = array(
            "aplicarFiltro" => "107",
            "info" => "108",
            "buscar" => "109",
            "aprobar" => "110",
            "devolver" => "111",
            "rechazar" => "112"
        );
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

    /**
     * aplicarFiltroAction function
     * @changed [2023-12-20]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
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

        Flash::set_flashdata("filter_beneficiario", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new BeneficiarioServices());
        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction($estado = 'P')
    {
        $this->buscarAction($estado);
    }

    /**
     * indexAction function
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
    public function indexAction()
    {
        $this->setParamToView("hide_header", true);
        $campo_field = array(
            "numdoc" => "Identificación",
            "priape" => "Primer apellido",
            "segape" => "Segundo apellido",
            "prinom" => "Primer nombre",
            "segnom" => "Segundo nombre",
            "cedtra" => "Cedula trabajador",
            "fecsol" => "Fecha solicitud",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", Flash::get_flashdata_item("filter_params"));
        $this->setParamToView("title", "Aprueba Beneficiario");
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
        $this->loadParametrosView();
    }

    /**
     * buscarAction function
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
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
                    "query" => $query_str,
                    "estado" => $estado,
                    "pagina" => $pagina
                )
            )
        );
        if (Flash::get_flashdata_item("filter_beneficiario") != false) {
            $query = $pagination->persistencia(Flash::get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $this->getPostParam('campo'),
                $this->getPostParam('condi'),
                $this->getPostParam('value')
            );
        }
        Flash::set_flashdata("filter_beneficiario", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new BeneficiarioServices());
        return $this->renderObject($response, false);
    }

    /**
     * apruebaAction function
     * @changed [2023-12-19]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function apruebaAction()
    {
        $this->setResponse("ajax");
        Services::Init();
        $user = Auth::getActiveIdentity();
        $debuginfo = array();
        try {
            try {
                $acceso = (new Gener42)->count("*", "conditions: permiso='92' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(array(
                        "success" => false,
                        "msj" => "El usuario no dispone de permisos de aprobación"
                    ));
                }

                $aprueba = new ApruebaBeneficiario();
                $aprueba->setTransa();
                $postData = $_POST;
                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $aprueba->findSolicitud($idSolicitud);
                $aprueba->findSolicitante();
                $aprueba->procesar($postData);
                $aprueba->endTransa();
                $aprueba->enviarMail($this->getPostParam('actapr'), $this->getPostParam('feccap'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (DebugException $err) {
                $debuginfo = $err->getDebugInfo();
                $aprueba->closeTransa($err->getMessage());
                $salida = array(
                    "success" => false,
                    "msj" => $err->getMessage(),
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

    public function devolverAction()
    {
        $this->beneficiarioServices =  new BeneficiarioServices();
        $notifyEmailServices = new NotifyEmailServices();

        $this->setResponse("ajax");
        $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
        $nota = sanetizar($this->getPostParam('nota'));
        $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
        $array_corregir = $this->getPostParam('campos_corregir');

        try {
            $campos_corregir = implode(";", $array_corregir);
            $mercurio34 = $this->Mercurio34->findFirst("id='{$id}'");

            $this->beneficiarioServices->devolver($mercurio34, $nota, $codest, $campos_corregir);
            $notifyEmailServices->emailDevolver(
                $mercurio34,
                $this->beneficiarioServices->msjDevolver($mercurio34, $nota)
            );

            $response = array(
                "success" => true,
                "msj" => "Movimiento realizado con exito"
            );
        } catch (DbException $err) {
            $response = $err->getMessage();
        }
        $this->renderObject($response);
    }

    public function devolver($mercurio34, $nota, $codest, $campos_corregir)
    {
        $today = new Date();
        $id = $mercurio34->getId();
        $mercurio34 = new Mercurio34();
        $mercurio34->updateAll(" estado='D', motivo='{$nota}', codest='{$codest}', fecest='{$today->getUsingFormatDefault()}'", "conditions: id='{$id}'");

        $item = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'");
        $mercurio10 = new Mercurio10();
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem(intval($item) + 1);
        $mercurio10->setEstado("D");
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->getUsingFormatDefault());

        if (!$mercurio10->save()) {
            $msj = "";
            foreach ($mercurio10->getMessages() as $key => $message) $msj .= $message . "<br/>";
            throw new DbException("Error " . $msj, 501);
        }

        $this->Mercurio10->updateAll("campos_corregir='{$campos_corregir}'", "conditions: item='{$item}' AND numero='{$id}' AND tipopc='{$this->tipopc}'");
    }

    public function rechazarAction()
    {
        $notifyEmailServices = new NotifyEmailServices();
        $this->beneficiarioServices =  new BeneficiarioServices();
        $this->setResponse("ajax");
        $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
        $nota = sanetizar($this->getPostParam('nota'));
        $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
        try {
            $mercurio34 = $this->Mercurio34->findFirst("id='{$id}'");
            $this->beneficiarioServices->rechazar($mercurio34, $nota, $codest);
            $notifyEmailServices->emailRechazar(
                $mercurio34,
                $this->beneficiarioServices->msjRechazar($mercurio34, $nota)
            );
            $response = array(
                "success" => true,
                "msj" => "Movimiento Realizado Con Exito"
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "code" => 500
            );
        }
        $this->renderObject($response);
    }

    function rechazar($mercurio34, $nota, $codest)
    {
        $today = new Date();
        $id = $mercurio34->getId();
        $this->Mercurio34->updateAll("estado='X', motivo='{$nota}', codest='{$codest}', fecest='{$today->getUsingFormatDefault()}'", "conditions: id='{$id}' ");

        $item = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'");
        $mercurio10 = new Mercurio10();
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem(intval($item) + 1);
        $mercurio10->setEstado("X");
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->getUsingFormatDefault());
        $mercurio10->save();
        return true;
    }


    /**
     * inforAction function
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id');

            if (!$id) {
                throw new Exception("Error la solicitud es requerida para continuar", 501);
            }

            $beneficiarioServices = new BeneficiarioServices();
            $this->setParamToView("hide_header", true);
            $solicitud = $this->Mercurio34->findFirst("id='{$id}'");
            if ($solicitud == false) {
                throw new Exception("La solicitud de afiliación de beneficiario no es valida.", 501);
            }

            $trabajador_sisu = false;
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "trabajador",
                    "params" => array("cedtra" => $solicitud->getCedtra(), "estado" => 'A')
                )
            );

            $rqs =  $procesadorComando->toArray();
            if (!empty($rqs)) {
                $trabajador_sisu = ($rqs['success']) ? $rqs['data'] : false;
            }

            $trabajador = new stdClass();
            if (!$trabajador_sisu) {
                $tr = $this->Mercurio31->findFirst("cedtra='{$solicitud->getCedtra()}' and estado='A'");
                $trabajador->estado = ($tr) ? $tr->getEstado() : 'I';
            } else {
                $trabajador->estado = $trabajador_sisu['estado'];
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_beneficiario",
                    "params" => $solicitud->getNumdoc()
                )
            );

            $rqs =  $procesadorComando->toArray();
            $relacion_multiple = false;

            if ($rqs) {
                if ($rqs['success']) {
                    $sys_beneficiario = $rqs['data'];
                    $this->setParamToView("beneficiario_sisuweb", $sys_beneficiario);
                    $giro = $sys_beneficiario['giro'];
                    $vinculo_trabajador = false;

                    if ($rqs['relaciones']) {
                        $relacion_multiple = $rqs['relaciones'];
                        foreach ($rqs['relaciones'] as $ai => $relacion) {
                            if ($relacion['cedtra'] == $solicitud->getCedtra()) {
                                $vinculo_trabajador = true;
                                break;
                            }
                        }
                    }
                    $estado = ($vinculo_trabajador == FALSE) ? 'I' :  $sys_beneficiario['estado'];
                }
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo"  => "parametros_beneficiarios"
                )
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsBeneficiario = new ParamsBeneficiario();
            $paramsBeneficiario->setDatosCaptura($datos_captura);

            $html = View::render(
                'aprobacionben/tmp/consulta',
                array(
                    'beneficiario' => $solicitud,
                    'detTipo' => $this->Mercurio06->findFirst("tipo='{$solicitud->getTipo()}'")->getDetalle(),
                    '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
                    '_codciu' => ParamsBeneficiario::getCiudades(),
                    '_sexo' => ParamsBeneficiario::getSexos(),
                    '_estciv' => ParamsBeneficiario::getEstadoCivil(),
                    '_parent' => ParamsBeneficiario::getParentesco(),
                    '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
                    '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_giro'  => ParamsBeneficiario::getTieneGiro(),
                    '_pago'  => ParamsBeneficiario::getPago(),
                    '_ciunac' => ParamsBeneficiario::getCiudades(),
                    '_huerfano' => ParamsBeneficiario::getHuerfano(),
                    '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_calendario' => ParamsBeneficiario::getCalendario(),
                    '_codgir',
                    ParamsBeneficiario::getCodigoGiro()
                )
            );

            $response = array(
                'success' => true,
                'data' => $solicitud->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta" => $html,
                'adjuntos' => $beneficiarioServices->adjuntos($solicitud),
                'seguimiento' => $beneficiarioServices->seguimiento($solicitud),
                'campos_disponibles' => $this->Mercurio34->CamposDisponibles(),
                "relacion_multiple" => $relacion_multiple,
                "trabajador" => $trabajador
            );
        } catch (Exception $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_beneficiarios"
            )
        );

        $paramsBeneficiario = new ParamsBeneficiario();
        $paramsBeneficiario->setDatosCaptura($procesadorComando->toArray());

        $this->setParamToView("_cedcon", array());
        $this->setParamToView("_giro", ParamsBeneficiario::getTieneGiro());
        $this->setParamToView("_pago", ParamsBeneficiario::getPago());
        $this->setParamToView("_coddoc", ParamsBeneficiario::getTiposDocumentos());
        $this->setParamToView("_sexo", ParamsBeneficiario::getSexos());
        $this->setParamToView("_estciv", ParamsBeneficiario::getEstadoCivil());
        $this->setParamToView("_ciunac", ParamsBeneficiario::getCiudades());
        $this->setParamToView("_captra", ParamsBeneficiario::getCapacidadTrabajar());
        $this->setParamToView("_parent", ParamsBeneficiario::getParentesco());
        $this->setParamToView("_huerfano", ParamsBeneficiario::getHuerfano());
        $this->setParamToView("_tiphij", ParamsBeneficiario::getTipoHijo());
        $this->setParamToView("_nivedu", ParamsBeneficiario::getNivelEducativo());
        $this->setParamToView("_tipdis", ParamsBeneficiario::getTipoDiscapacidad());
        $this->setParamToView("_calendario", ParamsBeneficiario::getCalendario());
        $this->setParamToView("_codgir", ParamsBeneficiario::getCodigoGiro());
        $this->setParamToView("tipo", parent::getActUser("tipo"));
    }

    function clp($name)
    {
        return $this->getPostParam($name, "addslaches", "extraspaces", "striptags");
    }

    public function editar_solicitudAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $numdoc = $this->getPostParam('numdoc', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio34 = (new Mercurio34)->findFirst("id='{$id}' and numdoc='{$numdoc}'");
            if (!$mercurio34) {
                throw new Exception("El beneficiario no está disponible para notificar por email", 501);
            } else {
                $mercurio07 = (new Mercurio07)->findFirst("documento='{$mercurio34->getDocumento()}' and coddoc='{$mercurio34->getCoddoc()}'");
                if (!$mercurio07) {
                    throw new Exception("El usuario no está disponible para notificar por email", 501);
                }
                $asignarFuncionario = new AsignarFuncionario();
                $usuario = $asignarFuncionario->asignar($this->tipopc, $mercurio07->getCodciu());

                if (empty($usuario)) {
                    throw new Exception("No se puede realizar el registro, no hay usuario disponible para la atención de la solicitud, Comuniquese con la Atencion al cliente", 505);
                }
                $data = array(
                    "tipdoc" => $this->clp('tipdoc'),
                    "numdoc" => $this->clp('numdoc'),
                    "priape" => $this->clp('priape'),
                    "segape" => $this->clp('segape'),
                    "prinom" => $this->clp('prinom'),
                    "segnom" => $this->clp('segnom'),
                    "fecnac" => $this->clp('fecnac'),
                    "ciunac" => $this->clp('ciunac'),
                    "sexo"   => $this->clp('sexo'),
                    "parent" => $this->clp('parent'),
                    "huerfano" => $this->clp('huerfano'),
                    "tiphij" => $this->clp('tiphij'),
                    "nivedu" => $this->clp('nivedu'),
                    "captra" => $this->clp('captra'),
                    "tipdis" => $this->clp('tipdis'),
                    "calendario" => $this->clp('calendario'),
                    "cedacu" => $this->clp('cedacu')
                );
                $setters = "";
                foreach ($data as $ai => $row) {
                    if (strlen($row) > 0) {
                        $setters .= " $ai='{$row}',";
                    }
                }
                $setters  = trim($setters, ',');
                $this->Mercurio34->updateAll($setters, "conditions: id='{$id}' AND numdoc='{$numdoc}'");

                $db = (object) DbBase::rawConnect();
                $db->setFetchMode(DbBase::DB_ASSOC);

                $data = $db->fetchOne("SELECT max(id), mercurio34.* FROM mercurio34 WHERE numdoc='{$numdoc}'");
                $salida = array(
                    "msj" => "Proceso se ha completado con éxito",
                    "success" => true,
                    "data" => $data
                );
            }
        } catch (Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    /**
     * empresa_sisuwebAction function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     * @param [type] $nit
     * @return void
     */
    public function buscarEnSisuViewAction($id = 0)
    {

        if (!$id) {
            Router::rTa("aprobacionben/index");
            exit;
        }

        $mercurio34 = $this->Mercurio34->findFirst("id='{$id}'");

        if (!$mercurio34) {
            Flash::set_flashdata("error", array(
                "msj" => "El beneficiario no se encuentra registrado.",
                "code" => 201
            ));
            Router::rTa('aprobacionben/index');
            exit;
        }
        $numdoc = $mercurio34->getNumdoc();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_beneficiario",
                "params" => $numdoc
            )
        );
        $rqs =  $procesadorComando->toArray();

        if (!$rqs['success']) {
            Flash::set_flashdata("error", array(
                "msj" => "El beneficiario no se encuentra registrado.",
                "code" => 201
            ));
            Router::rTa("aprobacionben/index");
            exit();
        }

        $relaciones = array();
        if ($rqs['data']) {
            $beneficiario = $rqs['data'];
            $relaciones = $rqs['relaciones'];
        }

        $this->setParamToView("mercurio34", $mercurio34);
        $this->setParamToView("cedtra", $mercurio34->getCedtra());
        $this->setParamToView("relaciones", $relaciones);
        $this->setParamToView("beneficiario", $beneficiario);
        $this->setParamToView("title", "Beneficiario SISU - {$numdoc}");
    }

    public function opcionalAction($estado = 'P')
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("title", "Aprobación Beneficiario");

        $collection = $this->Mercurio34->find("estado='{$estado}' AND usuario=" . parent::getActUser() . " ORDER BY fecsol ASC");
        $beneficiarioServices = new BeneficiarioServices();
        $data = $beneficiarioServices->dataOptional($collection, $estado);

        $this->setParamToView("beneficiarios", $data);
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("pagina_con_estado", $estado);
    }

    public function reaprobarAction()
    {

        $this->setResponse("ajax");
        $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
        $giro =  $this->getPostParam('giro', "addslaches", "alpha", "extraspaces", "striptags");
        $codgir =  $this->getPostParam('codgir', "addslaches", "alpha", "extraspaces", "striptags");
        $nota = sanetizar($this->getPostParam('nota'));
        $today = new Date();
        $comando = '';
        try {
            try {
                $modelos = array("mercurio10", "mercurio34");
                $Transaccion = parent::startTrans($modelos);
                parent::startFunc();

                $this->Mercurio34->updateAll("estado='A',fecest='{$today->getUsingFormatDefault()}'", "conditions: id='{$id}' ");
                $item = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;

                $mercurio10 = new Mercurio10();
                $mercurio10->setTransaction($Transaccion);
                $mercurio10->setTipopc($this->tipopc);
                $mercurio10->setNumero($id);
                $mercurio10->setItem($item);
                $mercurio10->setEstado("A");
                $mercurio10->setNota($nota);
                $mercurio10->setFecsis($today->getUsingFormatDefault());
                $mercurio10->save();

                $beneficiario = $this->Mercurio34->findFirst(" id='{$id}'");

                $procesadorComando = Comman::Api();
                $procesadorComando->runCli(
                    array(
                        "servicio" => "ComfacaAfilia",
                        "metodo" => "actualiza_beneficiario",
                        "params" => array(
                            "numdoc" => $beneficiario->getNumdoc(),
                            "modelo" => array(
                                "prinom" => $beneficiario->getPrinom(),
                                "segnom" => $beneficiario->getSegnom(),
                                "priape" => $beneficiario->getPriape(),
                                "giro"   => $giro,
                                "codgir" => $codgir
                            )
                        )
                    )
                );

                $comando = $procesadorComando->getLineaComando();

                $result =  $procesadorComando->toArray();
                if (!$result) {
                    throw new Exception("Error, no hay respuesta del servidor para validación del resultado.", 1);
                } else {

                    parent::finishTrans();
                    $response = array(
                        "success" => true,
                        "msj" => "Movimiento realizado con éxito",
                        "result" => $result
                    );
                }
            } catch (TransactionFailed $err) {
                throw new Exception($err->getMessage(), 505);
            }
        } catch (Exception $e) {
            $response = array(
                "success" => false,
                "msj" => "No se pudo realizar el movimiento " . "\n" . $e->getMessage() . "\n " . $e->getLine(),
                "comando" => $comando
            );
        }
        $this->renderObject($response);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        Flash::set_flashdata("filter_beneficiario", false, true);
        Flash::set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => Flash::get_flashdata_item("filter_trabajador"),
            'filter' => Flash::get_flashdata_item("filter_params"),
        ));
    }

    /**
     * infoAprobadoViewAction function
     * datos del solicitud aprobada enn sisu
     * @param [type] $id
     * @return void
     */
    public function infoAprobadoViewAction($id)
    {
        $this->tipopc = "1";
        try {
            $mercurio34 = $this->Mercurio34->findFirst(" id='{$id}' and estado='A' ");
            if (!$mercurio34) {
                throw new DebugException("Error al buscar la beneficiario", 501);
            }

            $cedtra = $mercurio34->getCedtra();
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo"  => "parametros_beneficiarios"
                )
            );

            $datos_captura =  $procesadorComando->toArray();
            $paramsBeneficiario = new ParamsBeneficiario();
            $paramsBeneficiario->setDatosCaptura($datos_captura);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_beneficiario",
                    "params" => $mercurio34->getNumdoc()
                )
            );

            if ($procesadorComando->isJson() == False) {
                throw new DebugException("Error al buscar la beneficiario en Sisuweb", 501);
            }

            $out = $procesadorComando->toArray();
            $beneSisu = $out['data'];

            $beneficiario = new Mercurio34();
            $beneficiario->createAttributes($beneSisu);
            $beneficiario->setTipo('E');
            $beneficiario->setNumdoc($beneSisu['documento']);
            $html = View::render(
                'aprobacionben/tmp/consulta',
                array(
                    'beneficiario' => $beneficiario,
                    'detTipo' => $this->Mercurio06->findFirst("tipo='{$beneficiario->getTipo()}'")->getDetalle(),
                    '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
                    '_codciu' => ParamsBeneficiario::getCiudades(),
                    '_sexo' => ParamsBeneficiario::getSexos(),
                    '_estciv' => ParamsBeneficiario::getEstadoCivil(),
                    '_parent' => ParamsBeneficiario::getParentesco(),
                    '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
                    '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_giro'  => ParamsBeneficiario::getTieneGiro(),
                    '_pago'  => ParamsBeneficiario::getPago(),
                    '_ciunac' => ParamsBeneficiario::getCiudades(),
                    '_huerfano' => ParamsBeneficiario::getHuerfano(),
                    '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                    '_calendario' => ParamsBeneficiario::getCalendario(),
                    '_codgir',
                    ParamsBeneficiario::getCodigoGiro()
                )
            );

            $code_estados = array();
            $query = $this->Mercurio11->find();
            foreach ($query as $row) $code_estados[$row->getCodest()] = $row->getDetalle();

            $this->setParamToView("code_estados", $code_estados);
            $this->setParamToView("mercurio34", $beneficiario);
            $this->setParamToView("consulta_trabajador", $html);
            $this->setParamToView("hide_header", true);
            $this->setParamToView("idModel", $id);
            $this->setParamToView("cedtra", $cedtra);
            $this->setParamToView("title", "Beneficiario Aprobado {$beneficiario->getNumdoc()}");
        } catch (DebugException $err) {
            Flash::set_flashdata("error", array(
                "msj" => $err->getMessage(),
                "code" => 201
            ));
            Router::rTa("aprobacionben/index");
            exit;
        }
    }

    /**
     * deshacerAprobado function
     * metodo para deshacer una afilación, dado que se presente algun error por parte de los analistas encargados
     * @param [type] $id
     * @return void
     */
    public function deshacerAction()
    {
        $this->beneficiarioServices = $this->services->get('BeneficiarioServices');
        $notifyEmailServices = new NotifyEmailServices();
        $this->setResponse("ajax");
        $action = $this->getPostParam('action');
        $codest = $this->getPostParam('codest');
        $sendEmail = $this->getPostParam('send_email');
        $nota = sanetizar($this->getPostParam('nota'));
        $comando = '';

        try {

            $id = $this->getPostParam('id');

            $mercurio34 = (new Mercurio34)->findFirst(" id='{$id}' and estado='A' ");
            if (!$mercurio34) {
                throw new Exception("Los datos del beneficiario no son validos para procesar.", 501);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_beneficiario",
                    "params" => $mercurio34->getNumdoc()
                )
            );

            if ($procesadorComando->isJson() == False) {
                throw new Exception("Error al buscar al beneficiario en Sisuweb", 501);
            }

            $out = $procesadorComando->toArray();
            $beneficiarioSisu = $out['data'];

            $procesadorComando->runCli(
                array(
                    "servicio" => "DeshacerAfiliaciones",
                    "metodo" => "deshacerAprobacionBeneficiario",
                    "params" => array(
                        "cedtra" => $mercurio34->getCedtra(),
                        "numdoc" => $mercurio34->getNumdoc(),
                        "tipo_documento" => $mercurio34->getTipdoc(),
                        "nota" => $nota
                    )
                )
            );

            $comando = $procesadorComando->getLineaComando();
            if ($procesadorComando->isJson() == False) {
                throw new Exception("Error al procesar el deshacer la aprobación en SisuWeb.", 501);
            }

            $resdev = $procesadorComando->toArray();
            if ($resdev['success'] !== true) throw new Exception($resdev['message'], 501);

            $datos = $resdev['data'];
            if ($datos['noAction']) {
                $salida = array(
                    'success' => false,
                    'msj' => 'No se realizo ninguna acción, el estado del beneficiario no es valido para realizar la acción requerida.',
                    'data' => $beneficiarioSisu
                );
            } else {
                //procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $this->beneficiarioServices->devolver($mercurio34, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') $notifyEmailServices->emailDevolver($mercurio34, $this->beneficiarioServices->msjDevolver($mercurio34, $nota));
                }

                if ($action == 'R') {
                    $this->beneficiarioServices->rechazar($mercurio34, $nota, $codest);
                    if ($sendEmail == 'S') $notifyEmailServices->emailRechazar($mercurio34, $this->beneficiarioServices->msjRechazar($mercurio34, $nota));
                }

                if ($action == 'I') {
                    $mercurio34->setEstado('I');
                    $mercurio34->setFecest(date('Y-m-d'));
                    $mercurio34->save();
                }

                $salida = array(
                    'data' => $beneficiarioSisu,
                    'success' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? true : false,
                    'msj' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? 'Se completo el proceso con éxito.' : 'No se realizo el cambio requerido, se debe comunicar al área de soporte de las TICS.',
                    'isDeleteTrayecto' => $datos['isDeleteTrayecto'],
                    'noAction' => $datos['noAction'],
                    'isDelete' => $datos['isDelete'],
                );
            }
        } catch (Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => "Error no se pudo realizar el movimiento, " . $err->getMessage(),
                "comando" => $comando,
                "file" => $err->getFile(),
                "line" => $err->getLine(),
                'isDeleteTrayecto' => false,
                'noAction' => false,
                'isDelete' => false,
            );
        }
        return $this->renderObject($salida);
    }
}
