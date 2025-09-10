<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobacionconController extends ApplicationController
{
    private $tipopc = 3;

    /**
     * services variable
     * @var Services
     */
    protected $services;


    /**
     * trabajadorServices variable
     * @var ConyugeServices
     */
    protected $conyugeServices;


    /**
     * initialize function
     * @changed [2023-12-20]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function initialize()
    {
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        Core::importLibrary("ProcesadorComandos", "ProcesadorComandos");
        Core::importLibrary("ParamsConyuge", "Collections");
        $this->setTemplateAfter('bone');
        $this->services = Services::Init();
    }

    /**
     * beforeFilter function
     * @changed [2023-12-20]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param array $permisos
     * @return void
     */
    public function beforeFilter($permisos = array())
    {
        $permisos = array(
            "aplicarFiltro" => "101",
            "info" => "102",
            "buscar" => "103",
            "aprobar" => "104",
            "devolver" => "105",
            "rechazar" => "106"
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
     *
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

        Flash::set_flashdata("filter_conyuge", $query, true);

        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new ConyugeServices());
        return $this->renderObject($response, false);
    }

    /**
     * changeCantidadPaginaAction function
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
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
            "cedcon" => "Cedula",
            "priape" => "Primer apellido",
            "segape" => "Segundo apellido",
            "prinom" => "Primer nombre",
            "segnom" => "Segundo nombre",
            "nit"    => "Nit",
            "fecsol" => "Fecha Solicitud",
        );

        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", Flash::get_flashdata_item("filter_params"));
        $this->setParamToView("title", "Aprueba Conyuge");
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
        $this->loadParametrosView();
    }

    /**
     * buscarAction function
     * @changed [2023-12-00]
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
                    'pagina' => $pagina
                )
            )
        );

        if (
            Flash::get_flashdata_item("filter_conyuge") != false
        ) {
            $query = $pagination->persistencia(Flash::get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $this->getPostParam('campo'),
                $this->getPostParam('condi'),
                $this->getPostParam('value')
            );
        }

        Flash::set_flashdata("filter_conyuge", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new ConyugeServices());
        return $this->renderObject($response, false);
    }

    /**
     * aprobarAction function
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function apruebaAction()
    {
        $this->setResponse("ajax");
        $debuginfo = array();
        try {
            try {
                $user = Auth::getActiveIdentity();
                $acceso = $this->Gener42->count("permiso='92' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
                }
                $apruebaSolicitud = new ApruebaConyuge();
                $apruebaSolicitud->setTransa();

                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $solicitud = $apruebaSolicitud->findSolicitud($idSolicitud);
                $apruebaSolicitud->findSolicitante($solicitud);
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

    /**
     * devolverAction function
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function devolverAction()
    {
        $this->conyugeServices =  $this->services->get('ConyugeServices');
        $notifyEmailServices = new NotifyEmailServices();

        $this->setResponse("ajax");
        $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
        $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
        $nota = sanetizar($this->getPostParam('nota'));

        $array_corregir = $this->getPostParam('campos_corregir');
        try {
            $campos_corregir = implode(";", $array_corregir);

            $mercurio32 = $this->Mercurio32->findFirst("id='{$id}'");

            $this->conyugeServices->devolver($mercurio32, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio32,
                $this->conyugeServices->msjDevolver($mercurio32, $nota)
            );

            $response = array(
                "success" => true,
                "msj" => "Movimiento realizado con exito"
            );
        } catch (DebugException $err) {
            $response = $err->getMessage();
        }
        return $this->renderObject($response, false);
    }

    public function rechazarAction()
    {
        $notifyEmailServices = new NotifyEmailServices();
        $this->conyugeServices =  new ConyugeServices;
        $this->setResponse("ajax");
        $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
        $nota = sanetizar($this->getPostParam('nota'));
        $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
        try {
            $mercurio32 = $this->Mercurio32->findFirst("id='{$id}'");
            $this->conyugeServices->rechazar($mercurio32, $nota, $codest);
            $notifyEmailServices->emailRechazar(
                $mercurio32,
                $this->conyugeServices->msjRechazar($mercurio32, $nota)
            );
            $response = array(
                "success" => true,
                "msj" => "Movimiento realizado con exito"
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "code" => 500
            );
        }
        return $this->renderObject($response, false);
    }

    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id');
            if (!$id) {
                Router::rTa("aprobacioncon/index");
                exit;
            }
            $this->conyugeServices =  new ConyugeServices();

            $solicitud = $this->Mercurio32->findFirst("id='{$id}'");
            if ($solicitud == false) {
                Flash::set_flashdata("error", array(
                    "msj" => "La solicitud de afiliación de conyugue no es valida.",
                    "code" => 500
                ));
                Router::rTa("aprobacioncon/index");
                exit;
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
            $trabajador = new stdClass;

            if ($procesadorComando->isJson()) {
                $rqs =  $procesadorComando->toArray();
                if (!empty($rqs)) {
                    $trabajador_sisu = ($rqs['success']) ? $rqs['data'] : false;
                }
                if (!$trabajador_sisu) {
                    $tr = $this->Mercurio31->findFirst("cedtra='{$solicitud->getCedtra()}' and estado='A'");
                    $trabajador->estado = ($tr) ? $tr->getEstado() : 'I';
                } else {
                    $trabajador->estado = $trabajador_sisu['estado'];
                }
            }

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_conyuge",
                    "params" => array(
                        'cedcon' => $solicitud->getCedcon()
                    )
                )
            );

            $relacion_multiple = false;
            $tippag = '';
            $codcue = '';
            $numcue = '';
            $estado = '';
            $tipcue = '';
            $recsub = 'N';

            if ($ps->isJson()) {
                $rqs = $ps->toArray();
                if ($rqs['success'] == true) {
                    $sys_conyuge = $rqs['data'];
                    $this->setParamToView("conyuge_sisuweb", $sys_conyuge);
                    $tippag = $sys_conyuge['tippag'];
                    $codcue = $sys_conyuge['codcue'];
                    $numcue = $sys_conyuge['numcue'];
                    $tipcue = $sys_conyuge['tipcue'];
                    $estado = $sys_conyuge['estado'];

                    if (count($rqs['relaciones']) > 0) {
                        $recsub = $rqs['relaciones'][0]['recsub'];
                        $relacion_multiple = count($rqs['relaciones']);
                    }
                }

                $tippag = ($solicitud->getTippag()) ? $solicitud->getTippag() : $tippag;
                $numcue = ($solicitud->getNumcue()) ? $solicitud->getNumcue() : $numcue;
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo"  => "parametros_conyuges"
                )
            );
            $paramsConyuge = new ParamsConyuge();
            $paramsConyuge->setDatosCaptura($procesadorComando->toArray());

            $html = View::render(
                'aprobacioncon/tmp/consulta',
                array(
                    'conyuge' => $solicitud,
                    'detTipo' => $this->Mercurio06->findFirst("tipo='{$solicitud->getTipo()}'")->getDetalle(),
                    '_coddoc' => ParamsConyuge::getTiposDocumentos(),
                    '_codciu' => ParamsConyuge::getCiudades(),
                    '_sexo' => ParamsConyuge::getSexos(),
                    '_estciv' => ParamsConyuge::getEstadoCivil(),
                    '_captra' => ParamsConyuge::getCapacidadTrabajar(),
                    '_nivedu' => ParamsConyuge::getNivelEducativo(),
                    '_ciunac' => ParamsConyuge::getCiudades(),
                    '_tippag' => ParamsConyuge::getTipoPago(),
                    '_codcue' => ParamsConyuge::getCodigoCuenta(),
                    '_tipcue' => ParamsConyuge::getTipoCuenta(),
                    '_recsub' => ParamsConyuge::getRecibeSubsidio(),
                    '_comper' => ParamsConyuge::getCompaneroPermanente(),
                    '_ciures' => ParamsConyuge::getCiudades(),
                    '_vivienda' => ParamsConyuge::getVivienda(),
                    '_codocu' => ParamsConyuge::getOcupaciones(),
                    '_tipsal' => array("", "NINGUNO")
                )
            );

            $this->setParamToView("consulta_detalle", $html);

            $seguimiento = $this->conyugeServices->seguimiento($solicitud);
            $adjuntos = $this->conyugeServices->adjuntos($solicitud);
            $campos_disponibles = $solicitud->CamposDisponibles();

            $response = array(
                'success' => true,
                'data' => $solicitud->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta" => $html,
                'adjuntos' => $adjuntos,
                'seguimiento' => $seguimiento,
                'campos_disponibles' => $campos_disponibles
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
        $_codciu = array();
        $_ciunac = array();
        $_ciures = array();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo"  => "parametros_conyuges"
            )
        );
        $paramsConyuge = new ParamsConyuge();
        $paramsConyuge->setDatosCaptura($procesadorComando->toArray());

        foreach (ParamsConyuge::getZonas() as $ai => $valor) {
            if ($ai < 19001 && $ai >= 18001) $_ciures[$ai] = $valor;
        }

        foreach (ParamsConyuge::getCiudades() as $ai => $valor) {
            $_ciunac[$ai] = $valor;
            if ($ai < 19001 && $ai >= 18001) $_codciu[$ai] = $valor;
        }

        $this->setParamToView("_coddoc", ParamsConyuge::getTiposDocumentos());
        $this->setParamToView("_sexo", ParamsConyuge::getSexos());
        $this->setParamToView("_estciv", ParamsConyuge::getEstadoCivil());
        $this->setParamToView("_codciu", $_codciu);
        $this->setParamToView("_codzon", ParamsConyuge::getZonas());
        $this->setParamToView("_captra", ParamsConyuge::getCapacidadTrabajar());
        $this->setParamToView("_nivedu", ParamsConyuge::getNivelEducativo());
        $this->setParamToView("_ciunac", $_ciunac);
        $this->setParamToView("_tippag", ParamsConyuge::getTipoPago());
        $this->setParamToView("_codcue", ParamsConyuge::getCodigoCuenta());
        $this->setParamToView("_tipcue", ParamsConyuge::getTipoCuenta());
        $this->setParamToView("_recsub", ParamsConyuge::getRecibeSubsidio());
        $this->setParamToView("_comper", ParamsConyuge::getCompaneroPermanente());
        $this->setParamToView("_bancos", ParamsConyuge::getBancos());
        $this->setParamToView("_ciures", $_ciures);
        $this->setParamToView("_vivienda", ParamsConyuge::getVivienda());
        $this->setParamToView("_codocu", ParamsConyuge::getOcupaciones());

        $mercurio32 = new Mercurio32();
        $this->setParamToView("_tipsal", $mercurio32->getTipsalArray());
        $this->setParamToView("tipo",   parent::getActUser("tipo"));
        $this->setParamToView("tipopc",  $this->tipopc);
    }

    /**
     * empresa_sisuwebAction function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     * @param [type] $nit
     * @return void
     */
    public function buscarEnSisuViewAction($id)
    {
        $mercurio32 = $this->Mercurio32->findFirst("id='{$id}'");
        if (!$mercurio32) {
            Flash::set_flashdata("error", array(
                "msj" => "El conyuge no se encuentra registrado.",
                "code" => 201
            ));
            Router::rTa('aprobacioncon/index');
            exit;
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_conyuge",
                "params" => array(
                    'cedcon' => $mercurio32->getCedcon()
                )
            )
        );

        $rqs =  $procesadorComando->toArray();
        if (!$rqs['success']) {
            Flash::set_flashdata("error", array(
                "msj" => "El conyuge no se encuentra registrado.",
                "code" => 201
            ));
            Router::rTa("aprobacioncon/index");
            exit();
        }
        $relaciones = array();
        if ($rqs['success'] == true) {
            $conyuge = $rqs['data'];
            $relaciones = $rqs['data']['relaciones'];
        }

        $this->setParamToView("id", $id);
        $this->setParamToView("cedcon", $mercurio32->getCedcon());
        $this->setParamToView("cedtra", $mercurio32->getCedtra());
        $this->setParamToView("conyuge", $conyuge);
        $this->setParamToView("relaciones", $relaciones);
        $this->setParamToView("title", "Conyuge SisuWeb - {$mercurio32->getCedcon()}");
    }

    public function editar_solicitudAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $cedcon = $this->getPostParam('cedcon', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio32 = $this->Mercurio32->findFirst(" id='{$id}' and cedcon='{$cedcon}'");
            if (!$mercurio32) {
                throw new Exception("La cónyuge no está disponible para editar", 501);
            } else {
                $data = array(
                    "cedtra" => $this->clp('cedtra'),
                    "cedcon" => $this->clp('cedcon'),
                    "tipdoc" => $this->clp('tipdoc'),
                    "priape" => $this->clp('priape'),
                    "segape" => $this->clp('segape'),
                    "prinom" => $this->clp('prinom'),
                    "segnom" => $this->clp('segnom'),
                    "fecnac" => $this->clp('fecnac'),
                    "ciunac" => $this->clp('ciunac'),
                    "sexo" => $this->clp('sexo'),
                    "estciv" => $this->clp('estciv'),
                    "comper" => $this->clp('comper'),
                    "tiecon" => $this->clp('tiecon'),
                    "ciures" => $this->clp('ciures'),
                    "codzon" => $this->clp('codzon'),
                    "tipviv" => $this->clp('tipviv'),
                    "direccion" => $this->clp('direccion'),
                    "barrio" => $this->clp('barrio'),
                    "telefono" => $this->clp('telefono'),
                    "celular" => $this->clp('celular'),
                    "email" => $this->clp('email'),
                    "nivedu" => $this->clp('nivedu'),
                    "fecing" => $this->clp('fecing'),
                    "codocu" => $this->clp('codocu'),
                    "salario" => $this->clp('salario'),
                    "captra" => $this->clp('captra'),
                    "tipsal" => $this->clp('tipsal')
                );
                $setters = "";
                foreach ($data as $ai => $row) {
                    if (strlen($row) > 0) {
                        $setters .= " $ai='{$row}',";
                    }
                }
                $setters  = trim($setters, ',');
                $this->Mercurio32->updateAll($setters, "conditions: id='{$id}' AND cedcon='{$cedcon}'");

                $db = (object) DbBase::rawConnect();
                $db->setFetchMode(DbBase::DB_ASSOC);

                $data = $db->fetchOne("SELECT max(id), mercurio32.* FROM mercurio32 WHERE cedcon='{$cedcon}'");
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

    function clp($name)
    {
        return $this->getPostParam($name, "addslaches", "extraspaces", "striptags");
    }

    public function editarViewAction($id = '')
    {
        $this->setParamToView("hide_header", true);

        if (empty($id)) {
            Router::rTa("aprobacioncon/index");
            exit;
        }
        $conyuge = $this->Mercurio32->findFirst("id='{$id}'");
        $trabajador = $this->Mercurio31->findFirst("cedtra='{$conyuge->getCedtra()}'");

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo"  => "parametros_conyuges"
            )
        );

        $paramsConyuge = new ParamsConyuge();
        $paramsConyuge->setDatosCaptura($procesadorComando->toArray());

        $this->loadParametrosView();
        $this->setParamToView("mercurio32", $conyuge);
        $this->setParamToView("mercurio31", $trabajador);
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
        $this->setParamToView("title", "Solicitud Cónyuge - {$conyuge->getCedcon()}");
    }

    public function opcionalAction($estado = 'P')
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("title", "Aprobación Conyuge");

        $collection = $this->Mercurio32->find("estado='{$estado}' AND usuario=" . parent::getActUser() . " ORDER BY fecsol ASC");
        $conyugeServices = new ConyugeServices();
        $data = $conyugeServices->dataOptional($collection, $estado);

        $this->setParamToView("conyuges", $data);
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("pagina_con_estado", $estado);
    }

    public function reaprobarAction()
    {
        $this->setResponse("ajax");
        $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
        $nota = sanetizar($this->getPostParam('nota'));
        $today = new Date();
        try {
            $this->Mercurio32->updateAll("estado='A',fecest='{$today->getUsingFormatDefault()}'", "conditions: id='{$id}' ");
            $item = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;
            $mercurio10 = new Mercurio10();
            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado("A");
            $mercurio10->setNota($nota);
            $mercurio10->setFecsis($today->getUsingFormatDefault());
            $mercurio10->save();
            $mercurio32 = $this->Mercurio32->findFirst("id='{$id}'");

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_conyuge",
                    "params" =>  array(
                        'cedcon' => $mercurio32->getCedcon()
                    )
                )
            );
            $out = $procesadorComando->toArray();

            $fecsol = $mercurio32->getFecsol();
            $fecafi = $this->Mercurio10->maximum("fecsis", "conditions: tipopc='{$this->tipopc}' and numero='{$id}' and estado='P'");
            $params = array_merge($mercurio32->getArray(), $out['data']);
            $params['fecafi'] =  ($fecsol) ? $fecsol : $fecafi;
            $params['recsub'] = 'N';

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "afilia_conyuge",
                    "params" => $params
                )
            );

            $result =  $procesadorComando->toArray();
            $comando = $procesadorComando->getLineaComando();

            $response = array(
                'success' => true,
                'msj' => "Movimiento realizado con éxito",
                'comando' => $comando,
                'result' => $result,
                'out' => $out
            );
        } catch (Exception $e) {
            $response = array(
                "success" => false,
                "msj" => "No se pudo realizar el movimiento " . "\n" . $e->getMessage() . "\n " . $e->getLine(),
            );
        }
        return $this->renderObject($response, false);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        Flash::set_flashdata("filter_conyuge", false, true);
        Flash::set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => Flash::get_flashdata_item("filter_conyuge"),
            'filter' => Flash::get_flashdata_item("filter_params"),
        ));
    }

    /**
     * infoAprobadoViewAction function
     * datos del solicitud aprobada en sisu
     * @param [type] $id
     * @return void
     */
    public function infoAprobadoViewAction($id)
    {
        $this->tipopc = "3";
        try {
            $mercurio32 = $this->Mercurio32->findFirst(" id='{$id}' and estado='A' ");
            if (!$mercurio32) {
                throw new DebugException("Error al buscar la beneficiario", 501);
            }

            $cedtra = $mercurio32->getCedtra();
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo"  => "parametros_conyuges",
                    "params"  => true
                )
            );

            $datos_captura =  $procesadorComando->toArray();
            $paramsConyuge = new ParamsConyuge();
            $paramsConyuge->setDatosCaptura($datos_captura);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_conyuge",
                    "params" => array(
                        'cedcon' => $mercurio32->getCedcon()
                    )
                )
            );

            if ($procesadorComando->isJson() == False) {
                throw new DebugException("Error al buscar la beneficiario en Sisuweb", 501);
            }

            $out = $procesadorComando->toArray();
            $beneSisu = $out['data'];

            $conyuge = new Mercurio32();
            $conyuge->createAttributes($beneSisu);
            $conyuge->setTipo('E');
            $conyuge->setCedcon($beneSisu['cedcon']);
            $html = View::render(
                'aprobacioncon/tmp/consulta',
                array(
                    'conyuge' => $conyuge,
                    'detTipo' => $this->Mercurio06->findFirst("tipo='{$conyuge->getTipo()}'")->getDetalle(),
                    '_coddoc' => ParamsConyuge::getTiposDocumentos(),
                    '_codciu' => ParamsConyuge::getCiudades(),
                    '_sexo' => ParamsConyuge::getSexos(),
                    '_estciv' => ParamsConyuge::getEstadoCivil(),
                    '_captra' => ParamsConyuge::getCapacidadTrabajar(),
                    '_nivedu' => ParamsConyuge::getNivelEducativo(),
                    '_ciunac' => ParamsConyuge::getCiudades(),
                    '_tippag' => ParamsConyuge::getTipoPago(),
                    '_codcue' => ParamsConyuge::getCodigoCuenta(),
                    '_tipcue' => ParamsConyuge::getTipoCuenta(),
                    '_recsub' => ParamsConyuge::getRecibeSubsidio(),
                    '_comper' => ParamsConyuge::getCompaneroPermanente(),
                    '_ciures' => ParamsConyuge::getCiudades(),
                    '_vivienda' => ParamsConyuge::getVivienda(),
                    '_codocu' => ParamsConyuge::getOcupaciones(),
                    '_tipsal' => array("", "NINGUNO")
                )
            );

            $code_estados = array();
            $query = $this->Mercurio11->find();
            foreach ($query as $row) $code_estados[$row->getCodest()] = $row->getDetalle();

            $this->setParamToView("code_estados", $code_estados);
            $this->setParamToView("mercurio32", $conyuge);
            $this->setParamToView("consulta_trabajador", $html);
            $this->setParamToView("hide_header", true);
            $this->setParamToView("idModel", $id);
            $this->setParamToView("cedtra", $cedtra);
            $this->setParamToView("title", "Conyuge Aprobado {$conyuge->getCedcon()}");
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
        $this->setResponse("ajax");
        $action = $this->getPostParam('action');
        $codest = $this->getPostParam('codest');
        $sendEmail = $this->getPostParam('send_email');
        $nota = $this->getPostParam('nota');
        $this->conyugeServices = new ConyugeServices();
        $notifyEmailServices = new NotifyEmailServices();
        $comando = '';

        try {
            $id = $this->getPostParam('id');

            $mercurio32 = (new Mercurio32)->findFirst(" id='{$id}' and estado='A' ");
            if (!$mercurio32) {
                throw new Exception("Los datos del cónyuge no son validos para procesar.", 501);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_conyuge",
                    "params" => array(
                        'cedcon' => $mercurio32->getCedcon()
                    )
                )
            );

            if ($procesadorComando->isJson() == False) {
                throw new Exception("Error al buscar al cónyuge en Sisuweb", 501);
            }

            $out = $procesadorComando->toArray();
            $beneficiarioSisu = $out['data'];

            $procesadorComando->runCli(
                array(
                    "servicio" => "DeshacerAfiliaciones",
                    "metodo" => "deshacerAprobacionConyuge",
                    "params" => array(
                        "cedtra" => $mercurio32->getCedtra(),
                        "cedcon" => $mercurio32->getCedcon(),
                        "tipo_documento" => $mercurio32->getTipdoc(),
                        "nota" => $nota
                    )
                )
            );

            if ($procesadorComando->isJson() == False) {
                throw new Exception("Error al procesar el deshacer la aprobación en SisuWeb.", 501);
            }

            $resdev = $procesadorComando->toArray();
            if ($resdev['success'] !== true) throw new Exception($resdev['message'], 501);

            $datos = $resdev['data'];
            if ($datos['noAction']) {
                $salida = array(
                    'success' => false,
                    'msj' => 'No se realizo ninguna acción, el estado del cónyuge no es valido para realizar la acción requerida.',
                    'data' => $beneficiarioSisu
                );
            } else {
                //procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $this->conyugeServices->devolver($mercurio32, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') $notifyEmailServices->emailDevolver($mercurio32, $this->conyugeServices->msjDevolver($mercurio32, $nota));
                }

                if ($action == 'R') {
                    $this->conyugeServices->rechazar($mercurio32, $nota, $codest);
                    if ($sendEmail == 'S') $notifyEmailServices->emailRechazar($mercurio32, $this->conyugeServices->msjRechazar($mercurio32, $nota));
                }

                if ($action == 'I') {
                    $mercurio32->setEstado('I');
                    $mercurio32->setFecest(date('Y-m-d'));
                    $mercurio32->save();
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

    public function valida_conyugeAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $cedtra = $this->getPostParam('cedtra', "addslaches", "alpha", "extraspaces", "striptags");
            $coddoc = $this->getPostParam('tipdoc', "addslaches", "alpha", "extraspaces", "striptags");

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "trabajador",
                    "params" => array(
                        "cedtra" => $cedtra,
                        "coddoc" => $coddoc,
                        "estado" => 'A'
                    )
                )
            );

            if ($ps->isJson() == False) {
                throw new Exception("Error al buscar al cónyuge en Sisuweb", 501);
            }
            $trabajador_sisu = false;

            $rqs =  $ps->toArray();
            if (!empty($rqs)) {
                $trabajador_sisu = ($rqs['success']) ? $rqs['data'] : false;
            }

            if (!$trabajador_sisu) throw new DebugException("El trabajador aun no está activo en el sistema principal de subsidio.", 505);

            $salida = array(
                "success" => true,
                "msj" => "El trabajador se encuentra activo en el sistema principal de subsidio.",
                "data" => $trabajador_sisu
            );
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida);
    }
}
