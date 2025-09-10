<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobaciondomController extends ApplicationController
{

    protected $tipopc = 12;

    /**
     * services variable
     * @var Services
     */
    protected $services;

    /**
     * pagination variable
     * @var Pagination
     */
    protected $pagination;

    /**
     * servicioDomesticoServices variable
     * @var ServicioDomesticoServices
     */
    protected $servicioDomesticoServices;

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
        Core::importLibrary("ParamsPensionado", "Collections");
        $this->setTemplateAfter('bone');
        $this->services = Services::Init();
        $this->pagination = new Pagination();
    }

    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "83", "info" => "84", "buscar" => "85", "aprobar" => "86", "devolver" => "87", "rechazar" => "88");
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
     * @changed [2023-12-00]
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

        $this->pagination->setters(
            "cantidadPaginas: {$cantidad_pagina}",
            "query: usuario='{$usuario}' and estado='{$estado}'",
            "estado: {$estado}"
        );

        $query = $this->pagination->filter(
            $this->getPostParam('campo'),
            $this->getPostParam('condi'),
            $this->getPostParam('value')
        );

        Flash::set_flashdata("filter_domestico", $query, true);

        Flash::set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->render(
            new ServicioDomesticoServices()
        );
        return $this->renderObject($response, false);
    }

    /**
     * name function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function changeCantidadPaginaAction($estado = 'P')
    {
        $this->buscarAction($estado);
    }

    /**
     * name function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
    public function indexAction($estado = 'P')
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
        $this->setParamToView("title", "Aprueba Pensionado");
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("pagina_con_estado", $estado);
        $this->loadParametrosView();
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
    }


    /**
     * name function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $estado
     * @return void
     */
    public function buscarAction($estado)
    {
        $this->setResponse("ajax");
        $pagina = ($this->getPostParam('pagina')) ? $this->getPostParam('pagina') : 1;
        $cantidad_pagina = ($this->getPostParam("numero")) ? $this->getPostParam("numero") : 10;
        $usuario = parent::getActUser();
        $query = "usuario='{$usuario}' and estado='{$estado}'";

        $this->pagination->setters(
            "cantidadPaginas: $cantidad_pagina",
            "pagina: {$pagina}",
            "query: {$query}",
            "estado: {$estado}"
        );

        if (
            Flash::get_flashdata_item("filter_domestico") != false
        ) {
            $query = $this->pagination->persistencia(Flash::get_flashdata_item("filter_params"));
        }

        Flash::set_flashdata("filter_domestico", $query, true);
        Flash::set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->render(
            new servicioDomesticoServices()
        );

        return $this->renderObject($response, false);
    }

    public function inforAction($id)
    {
        $servicioDomesticoServices = new ServicioDomesticoServices();
        if (!$id) {
            Router::rTa("aprobaindepen/index");
            exit;
        }
        $this->setParamToView("hide_header", true);

        $mercurio38 = $this->Mercurio38->findFirst("id='{$id}'");
        if ($mercurio38->getEstado() == "A") {
            Flash::set_flashdata("success", array(
                "msj" => "La empresa {$mercurio38->getNit()}, ya se encuentra aprobada su afiliación. Y no requiere de más acciones.",
                "code" => 200
            ));
            Router::rTa("aprobaindepen/index");
            exit;
        }
        $this->setParamToView("mercurio38", $mercurio38);

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

        $det_tipo = $this->Mercurio06->findFirst("tipo = '{$mercurio38->getTipo()}'")->getDetalle();

        $this->setParamToView("adjuntos", $servicioDomesticoServices->adjuntos($mercurio38));

        $this->setParamToView("seguimiento", $servicioDomesticoServices->seguimiento($mercurio38));

        $htmlEmpresa = View::render('aprobaciondom/tmp/consulta', array(
            'mercurio38' => $mercurio38,
            'mercurio01' => $this->Mercurio01->findFirst(),
            'det_tipo' => $det_tipo,
            '_coddoc' => ParamsPensionado::getTipoDocumentos(),
            '_calemp' => ParamsPensionado::getCalidadEmpresa(),
            '_codciu' => ParamsPensionado::getCiudades(),
            '_codzon' => ParamsPensionado::getZonas(),
            '_codact' => ParamsPensionado::getActividades(),
            '_tipsoc' => ParamsPensionado::getTipoSociedades(),
            '_tipdur' => ParamsPensionado::getTipoDuracion(),
            '_codind' => ParamsPensionado::getCodigoIndice(),
            '_todmes' => ParamsPensionado::getPagaMes(),
            '_forpre' => ParamsPensionado::getFormaPresentacion(),
            '_tippag' => ParamsPensionado::getTipoPago(),
            '_tipcue' => ParamsPensionado::getTipoCuenta(),
            '_giro' => ParamsPensionado::getGiro(),
            "_codgir" =>  ParamsPensionado::getCodigoGiro()
        ));

        $this->setParamToView("consulta_empresa", $htmlEmpresa);
        $this->setParamToView("mercurio11", $this->Mercurio11->find());

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $mercurio38->getCedtra()
                )
            )
        );
        $out =  $procesadorComando->toArray();

        if ($out['success']) {
            $this->setParamToView("empresa_sisuweb", $out['data']);
        }

        $this->loadParametrosView();
        $servicioDomesticoServices->loadDisplay($mercurio38);
        $this->setParamToView("mercurio38", $mercurio38);
        $this->setParamToView("title", "Solicitud Pensionado - {$mercurio38->getCedtra()} - {$mercurio38->getEstadoDetalle()}");
    }

    function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_pensionado"
            )
        );

        $paramsPensionado = new ParamsPensionado();
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

        $this->setParamToView("_codzon", ParamsPensionado::getZonas());
        $this->setParamToView("_codciu", ParamsPensionado::getCiudades());
        $this->setParamToView("_codact", ParamsPensionado::getActividades());
        $this->setParamToView("_coddoc", ParamsPensionado::getTipoDocumentos());
        $this->setParamToView("_coddoc", ParamsPensionado::getTipoDocumentos());
        $this->setParamToView("_tipdur", ParamsPensionado::getTipoDuracion());
        $this->setParamToView("_codind", ParamsPensionado::getCodigoIndice());
        $this->setParamToView("_todmes", ParamsPensionado::getPagaMes());
        $this->setParamToView("_forpre", ParamsPensionado::getFormaPresentacion());
        $this->setParamToView("_tippag", ParamsPensionado::getTipoPago());
        $this->setParamToView("_bancos", ParamsPensionado::getBancos());
        $this->setParamToView("_tipcue", ParamsPensionado::getTipoCuenta());
        $this->setParamToView("_giro", ParamsPensionado::getGiro());
        $this->setParamToView("_codgir", ParamsPensionado::getCodigoGiro());
    }

    /**
     * apruebaAction function
     * @changed [2023-12-21]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function apruebaAction()
    {
        $this->setResponse("ajax");
        $user = Auth::getActiveIdentity();
        $acceso = $this->Gener42->count("permiso='74' AND usuario='{$user['usuario']}'");
        if ($acceso == 0) {
            return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
        }

        $this->apruebaSolicitud = $this->services->get('ApruebaSolicitud', true);
        try {
            try {
                $postData = $_POST;
                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $calemp = 'E';
                $solicitud = $this->apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $postData
                );

                $this->apruebaSolicitud->endTransa();
                $solicitud->enviarMail($this->getPostParam('actapr'), $this->getPostParam('feccap'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (Exception $err) {
                $this->apruebaSolicitud->closeTransa($err->getMessage());
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

        return $this->renderObject($salida, false);
    }

    public function devolverAction()
    {
        $this->setResponse("ajax");
        $servicioDomesticoServices =  new ServicioDomesticoServices();
        $notifyEmailServices = new NotifyEmailServices();
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($this->getPostParam('nota'));
            $array_corregir = $this->getPostParam('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $mercurio39 = $this->Mercurio39->findFirst("id='{$id}'");
            if ($mercurio39->getEstado() == 'D') {
                throw new Exception("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }
            $servicioDomesticoServices->devolver($mercurio39, $nota, $codest, $campos_corregir);
            $notifyEmailServices->emailDevolver(
                $mercurio39,
                $servicioDomesticoServices->msjDevolver($mercurio39, $nota)
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

    public function rechazarAction()
    {
        $this->setResponse("ajax");

        $notifyEmailServices = new NotifyEmailServices();
        $servicioDomesticoServices =  new ServicioDomesticoServices();
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($this->getPostParam('nota'));
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio39 = $this->Mercurio39->findFirst(" id='{$id}'");

            if ($mercurio39->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }

            $servicioDomesticoServices->rechazar($mercurio39, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio39, $servicioDomesticoServices->msjRechazar($mercurio39, $nota));

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

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        Flash::set_flashdata("filter_domestico", false, true);
        Flash::set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => Flash::get_flashdata_item("filter_domestico"),
            'filter' => Flash::get_flashdata_item("filter_params"),
        ));
    }

    public function buscarEnSisuViewAction($id, $nit)
    {
        $user = Auth::getActiveIdentity();
        $mercurio38 = $this->Mercurio38->findFirst("nit='{$nit}'");
        if (!$mercurio38) {
            Flash::set_flashdata("error", array(
                "msj" => "La empresa no se encuentra registrada.",
                "code" => 201
            ));
            Router::rTa("aprobaindepen/index");
            exit();
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $nit
                )
            )
        );
        $response =  $procesadorComando->toArray();
        if (!$response['success']) {
            Flash::set_flashdata("error", array(
                "msj" => "La empresa no se encuentra registrada.",
                "code" => 201
            ));
            Router::rTa("aprobaindepen/index");
            exit();
        }

        $this->setParamToView("idEmpresa", $id);
        $this->setParamToView("empresa", $response['data']);
        $this->setParamToView("trayectoria", $response['trayectoria']);
        $this->setParamToView("sucursales", $response['sucursales']);
        $this->setParamToView("listas", $response['listas']);
        $this->setParamToView("title", "Empresa SisuWeb - {$nit}");
    }

    public function editarViewAction($id)
    {
        if (!$id) {
            Router::rTa("aprobaindepen/index");
            exit;
        }
        $servicioDomesticoServices = new ServicioDomesticoServices();
        $this->setParamToView("hide_header", true);
        $mercurio38 = $this->Mercurio38->findFirst("id='{$id}'");
        $this->setParamToView("mercurio38", $mercurio38);
        $this->setParamToView("tipopc", 2);
        $this->setParamToView("seguimiento", $servicioDomesticoServices->seguimiento($mercurio38));

        $mercurio01 = $this->Mercurio01->findFirst();
        $this->setParamToView("mercurio01", $mercurio01);
        $mercurio37 = $this->Mercurio37->find(" tipopc=2 AND numero='{$mercurio38->getId()}'");
        $this->setParamToView("mercurio37", $mercurio37);
        $this->setParamToView("idModel", $id);
        $this->setParamToView("det_tipo", $this->Mercurio06->findFirst("tipo = '{$mercurio38->getTipo()}'")->getDetalle());


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
        $servicioDomesticoServices->loadDisplay($mercurio38);
        $this->setParamToView("title", "Editar Ficha Pensionado " . $mercurio38->getCedtra());
    }

    public function edita_empresaAction()
    {
        $this->setResponse("ajax");
        $nit = $this->getPostParam('nit');
        $id = $this->getPostParam('id');
        try {
            $mercurio38 = $this->Mercurio38->findFirst("nit='{$nit}' AND id='{$id}'");
            if (!$mercurio38) {
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
                $this->Mercurio38->updateAll($setters, "conditions: id='{$id}' AND nit='{$nit}'");
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
}
