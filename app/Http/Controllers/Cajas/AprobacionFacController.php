<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobacionfacController extends ApplicationController
{

    protected $tipopc = 10;

    /**
     * services variable
     * @var Services
     */
    protected $services;

    /**
     * facultativoServices variable
     * @var FacultativoServices
     */
    protected $facultativoServices;

    /**
     * apruebaSolicitud variable
     * @var ApruebaSolicitud
     */
    protected $apruebaSolicitud;

    public function initialize()
    {
        $this->setPersistance(false);
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        Core::importLibrary("ParamsFacultativo", "Collections");
        $this->setTemplateAfter('bone');
        $this->services = Services::Init();
    }


    /**
     * beforeFilter function
     * @changed [2023-12-21]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param array $permisos
     * @return void
     */
    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "65", "info" => "66", "buscar" => "67", "aprobar" => "68", "devolver" => "69", "rechazar" => "70");
        $flag = parent::beforeFilter($permisos);
        if (!$flag) {
            if (is_ajax()) {
                $this->setResponse("ajax");
                $response = parent::errorFunc("No cuenta con los permisos para este proceso");
                $this->renderObject($response, false);
            } else {
                $this->redirect("principal/index/0");
            }
            return false;
        }
    }

    /**
     * aplicarFiltroAction function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
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

        Flash::set_flashdata("filter_facultativo", $query, true);

        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(
            new FacultativoServices()
        );
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
            "cedtra" => "Cedula",
            "priape" => "Primer Apellido",
            "segape" => "Segundo Apellido",
            "prinom" => "Primer Nombre",
            "segnom" => "Segundo Nombre",
        );

        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", Flash::get_flashdata_item("filter_params"));
        $this->setParamToView("title", "Aprueba Facultativo");
        $this->setParamToView("buttons", array("F"));
        $this->loadParametrosView();
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
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

        if (
            Flash::get_flashdata_item("filter_facultativo") != false
        ) {
            $query = $this->pagination->persistencia(Flash::get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $this->getPostParam('campo'),
                $this->getPostParam('condi'),
                $this->getPostParam('value')
            );
        }

        Flash::set_flashdata("filter_facultativo", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(
            new FacultativoServices()
        );

        return $this->renderObject($response, false);
    }

    /**
     * infor function
     * @return void
     */
    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id');
            if (!$id) {
                throw new Exception("Error se requiere del id independiente", 501);
            }
            $facultativoServices = new FacultativoServices();
            $mercurio36 = (new Mercurio36)->findFirst("id='{$id}'");
            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_facultativo"
                )
            );

            $datos_captura =  $ps->toArray();
            $paramsfac = new ParamsFacultativo();
            $paramsfac->setDatosCaptura($datos_captura);

            $htmlEmpresa = View::render('aprobacionfac/tmp/consulta', array(
                'mercurio36' => $mercurio36,
                'mercurio01' => (new Mercurio01)->findFirst(),
                'det_tipo' => (new Mercurio06)->findFirst("tipo = '{$mercurio36->getTipo()}'")->getDetalle(),
                '_coddoc' => ParamsFacultativo::getTipoDocumentos(),
                '_calemp' => ParamsFacultativo::getCalidadEmpresa(),
                '_codciu' => ParamsFacultativo::getCiudades(),
                '_codzon' => ParamsFacultativo::getZonas(),
                '_codact' => ParamsFacultativo::getActividades(),
                '_tipsoc' => ParamsFacultativo::getTipoSociedades(),
                '_tipdur' => ParamsFacultativo::getTipoDuracion(),
                '_codind' => ParamsFacultativo::getCodigoIndice(),
                '_todmes' => ParamsFacultativo::getPagaMes(),
                '_forpre' => ParamsFacultativo::getFormaPresentacion(),
                '_tippag' => ParamsFacultativo::getTipoPago(),
                '_tipcue' => ParamsFacultativo::getTipoCuenta(),
                '_giro' => ParamsFacultativo::getGiro(),
                "_codgir" =>  ParamsFacultativo::getCodigoGiro()
            ));

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio36->getCedtra()
                    )
                )
            );
            $out =  $ps->toArray();

            if ($out['success']) {
                $this->setParamToView("empresa_sisuweb", $out['data']);
            }
            $response = array(
                'success' => true,
                'data' => $mercurio36->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta_empresa" => $htmlEmpresa,
                'adjuntos' => $facultativoServices->adjuntos($mercurio36),
                'seguimiento' => $facultativoServices->seguimiento($mercurio36),
                'campos_disponibles' => $mercurio36->CamposDisponibles()
            );
        } catch (Exception $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * apruebaAction function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function apruebaAction()
    {
        $this->setResponse("ajax");
        $user = Auth::getActiveIdentity();
        $debuginfo = array();

        $acceso = (new Gener42)->count("permiso='62' AND usuario='{$user['usuario']}'");
        if ($acceso == 0) {
            return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
        }
        $apruebaSolicitud = new ApruebaSolicitud();
        $apruebaSolicitud->setTransa();
        try {
            try {
                $postData = $_POST;
                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $calemp = 'F';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $postData
                );

                $apruebaSolicitud->endTransa();
                $solicitud->enviarMail($this->getPostParam('actapr'), $this->getPostParam('feccap'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (DebugException $err) {
                $debuginfo = $err->getDebugInfo();
                $apruebaSolicitud->closeTransa($err->getMessage());
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

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        Flash::set_flashdata("filter_facultativo", false, true);
        Flash::set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => Flash::get_flashdata_item("filter_facultativo"),
            'filter' => Flash::get_flashdata_item("filter_params"),
        ));
    }

    public function editarViewAction($id)
    {
        if (!$id) {
            Router::rTa("aprobacionfac/index");
            exit;
        }
        $facultativoServices = new FacultativoServices();
        $this->setParamToView("hide_header", true);
        $mercurio36 = (new Mercurio36)->findFirst("id='{$id}'");
        $this->setParamToView("mercurio36", $mercurio36);
        $this->setParamToView("tipopc", 2);
        $this->setParamToView("seguimiento", $facultativoServices->seguimiento($mercurio36));

        $mercurio01 = $this->Mercurio01->findFirst();
        $this->setParamToView("mercurio01", $mercurio01);
        $mercurio37 = $this->Mercurio37->find(" tipopc=2 AND numero='{$mercurio36->getId()}'");
        $this->setParamToView("mercurio37", $mercurio37);
        $this->setParamToView("idModel", $id);
        $this->setParamToView("det_tipo", $this->Mercurio06->findFirst("tipo = '{$mercurio36->getTipo()}'")->getDetalle());

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_pensionado"
            ),
            false
        );

        $paramsPensionado = new ParamsPensionado();
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

        $this->loadParametrosView();
        $facultativoServices->loadDisplay($mercurio36);
        $this->setParamToView("title", "Editar Ficha Pensionado " . $mercurio36->getCedtra());
    }

    public function edita_empresaAction()
    {
        $this->setResponse("ajax");
        $nit = $this->getPostParam('nit');
        $id = $this->getPostParam('id');
        try {
            $mercurio36 = $this->Mercurio36->findFirst("nit='{$nit}' AND id='{$id}'");
            if (!$mercurio36) {
                throw new Exception("La empresa no está disponible para notificar por email", 501);
            } else {
                $tipsoc = $this->getPostParam('tipsoc');
                if (strlen($tipsoc) == 1) {
                    $tipsoc = str_pad($tipsoc, 2, '0', STR_PAD_LEFT);
                }
                $data = array(
                    "razsoc" => $this->getPostParam('razsoc'),
                    "codact" => $this->getPostParam('codact'),
                    "digver" => $this->getPostParam('digver'),
                    "calemp" => $this->getPostParam('calemp'),
                    "cedrep" => $this->getPostParam('cedrep'),
                    "repleg" => $this->getPostParam('repleg'),
                    "direccion" => $this->getPostParam('direccion'),
                    "codciu" => $this->getPostParam('codciu'),
                    "codzon" => $this->getPostParam('codzon'),
                    "telefono" => $this->getPostParam('telefono'),
                    "celular" => $this->getPostParam('celular'),
                    "fax" => $this->getPostParam('fax'),
                    "email" => $this->getPostParam('email'),
                    "sigla" => $this->getPostParam('sigla'),
                    "fecini" => $this->getPostParam('fecini'),
                    "tottra" => $this->getPostParam('tottra'),
                    "valnom" => $this->getPostParam('valnom'),
                    "tipsoc" => $tipsoc,
                    "dirpri" => $this->getPostParam('dirpri'),
                    "ciupri" => $this->getPostParam('ciupri'),
                    "celpri" => $this->getPostParam('celpri'),
                    'tipemp' => $this->getPostParam('tipemp'),
                    "emailpri" => $this->getPostParam('emailpri'),
                    "tipper" => $this->getPostParam('tipper'),
                    "matmer" => $this->getPostParam('matmer'),
                    "coddocrepleg" => (!$this->getPostParam('coddocrepleg')) ? '1' : $this->getPostParam('coddocrepleg'),
                    "prinom" => ($this->getPostParam('tipper') == 'N') ? $this->getPostParam('prinom') : $this->getPostParam('prinomrepleg'),
                    "priape" => ($this->getPostParam('tipper') == 'N') ? $this->getPostParam('priape') : $this->getPostParam('priaperepleg'),
                    "segnom" => ($this->getPostParam('tipper') == 'N') ? $this->getPostParam('segnom') : $this->getPostParam('segnomrepleg'),
                    "segape" => ($this->getPostParam('tipper') == 'N') ? $this->getPostParam('segape') : $this->getPostParam('segaperepleg'),
                    "prinomrepleg" => ($this->getPostParam('tipper') == 'J') ? $this->getPostParam('prinomrepleg') : '',
                    "priaperepleg" => ($this->getPostParam('tipper') == 'J') ? $this->getPostParam('priaperepleg') : '',
                    "segnomrepleg" => ($this->getPostParam('tipper') == 'J') ? $this->getPostParam('segnomrepleg') : '',
                    "segaperepleg" => ($this->getPostParam('tipper') == 'J') ? $this->getPostParam('segaperepleg') : '',
                    "telpri" => $this->getPostParam('telpri')
                );
                $setters = "";
                foreach ($data as $ai => $row) $setters .= " $ai='{$row}',";
                $setters  = trim($setters, ',');
                $this->Mercurio36->updateAll($setters, "conditions: id='{$id}' AND nit='{$nit}'");
                $salida = array(
                    "msj" => "Proceso se ha completado con éxito",
                    "success" => true
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

    function loadParametrosView($datos_captura = '')
    {
        $ps = Comman::Api();
        if ($datos_captura == '') {
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_facultativo"
                )
            );

            $datos_captura =  $ps->toArray();
            $paramsEmpresa = new ParamsFacultativo();
            $paramsEmpresa->setDatosCaptura($datos_captura);
        }

        $_coddocrepleg = array();
        foreach (ParamsFacultativo::getCodruaDocumentos()  as $ai =>  $valor) {
            if ($valor == 'TI' || $valor == 'RC') continue;
            $_coddocrepleg[$ai] = $valor;
        }

        $this->setParamToView("_tipdur", ParamsFacultativo::getTipoDuracion());
        $this->setParamToView("_codind", ParamsFacultativo::getCodigoIndice());
        $this->setParamToView("_todmes", ParamsFacultativo::getPagaMes());
        $this->setParamToView("_forpre", ParamsFacultativo::getFormaPresentacion());
        $this->setParamToView("_tipsoc", ParamsFacultativo::getTipoSociedades());
        $this->setParamToView("_tipemp", ParamsFacultativo::getTipoEmpresa());
        $this->setParamToView("_tipapo", ParamsFacultativo::getTipoAportante());
        $this->setParamToView("_tipper", ParamsFacultativo::getTipoPersona());
        $this->setParamToView("_codzon", ParamsFacultativo::getZonas());
        $this->setParamToView("_calemp", ParamsFacultativo::getCalidadEmpresa());
        $this->setParamToView("_codciu", ParamsFacultativo::getCiudades());
        $this->setParamToView("_codact", ParamsFacultativo::getActividades());
        $this->setParamToView("_coddoc", ParamsFacultativo::getTipoDocumentos());
        $this->setParamToView("_tippag", ParamsFacultativo::getTipoPago());
        $this->setParamToView("_bancos", ParamsFacultativo::getBancos());
        $this->setParamToView("_tipcue", ParamsFacultativo::getTipoCuenta());
        $this->setParamToView("_giro", ParamsFacultativo::getGiro());
        $this->setParamToView("_codgir", ParamsFacultativo::getCodigoGiro());
        $this->setParamToView("_coddocrepleg", $_coddocrepleg);
    }

    public function rechazarAction()
    {
        $this->setResponse("ajax");
        $notifyEmailServices = new NotifyEmailServices();
        $this->independienteServices =  $this->services->get('IndependienteServices');
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($this->getPostParam('nota'));
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio41 = $this->Mercurio41->findFirst(" id='{$id}'");

            if ($mercurio41->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }

            $this->independienteServices->rechazar($mercurio41, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio41, $this->independienteServices->msjRechazar($mercurio41, $nota));

            $salida = array(
                "success" => true,
                "msj" => "El proceso se ha completado con éxito"
            );
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage(),
                "code" => $err->getCode()
            );
        } catch (DbException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "code" => 500
            );
        }
        return  $this->renderObject($salida, false);
    }

    public function devolverAction()
    {
        $this->setResponse("ajax");
        $this->independienteServices =  $this->services->get('IndependienteServices');
        $notifyEmailServices = new NotifyEmailServices();
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($this->getPostParam('nota'));
            $array_corregir = $this->getPostParam('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $mercurio41 = $this->Mercurio41->findFirst("id='{$id}'");
            if ($mercurio41->getEstado() == 'D') {
                throw new Exception("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }

            $this->independienteServices->devolver($mercurio41, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio41,
                $this->independienteServices->msjDevolver($mercurio41, $nota)
            );

            $salida = array(
                "success" => true,
                "msj" => "El proceso se ha completado con éxito"
            );
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage(),
                "code" => $err->getCode()
            );
        } catch (DbException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "code" => 500
            );
        } catch (Exception $ei) {
            $salida = array(
                "success" => false,
                "msj" => $ei->getMessage() . ' ' . $ei->getLine(),
                "code" => 500
            );
        }
        return $this->renderObject($salida, false);
    }
}
