<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Exceptions\DebugException;
use App\Services\Utils\Pagination;
use App\Services\CajaServices\MadreComuniServices;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Models\Mercurio39;
use App\Models\Mercurio10;
use App\Library\Auth;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Gener42;
use App\Services\Utils\NotifyEmailServices;
use App\Library\DbException;
use App\Library\View;
use App\Services\Utils\Comman;


class ApruebaComunitariaController extends ApplicationController
{
    protected $tipopc = 11;
    protected $db;
    protected $user;
    protected $tipo;
    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    /**
     * pagination variable
     * @var Pagination
     */
    protected $pagination;

    /**
     * madreComuniServices variable
     * @var MadreComuniServices
     */
    protected $madreComuniServices;

    /**
     * apruebaSolicitud variable
     * @var ApruebaSolicitud
     */
    protected $apruebaSolicitud;


    public function __construct()
    {
        $this->pagination = new Pagination();
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function aplicarFiltroAction(Request $request, string $estado = 'P')
    {
        $this->setResponse("ajax");
        $cantidad_pagina = $request->input("numero", 10);
        $usuario = parent::getActUser();

        $this->pagination->setters(
            "cantidadPaginas: {$cantidad_pagina}",
            "query: usuario='{$usuario}' and estado='{$estado}'",
            "estado: {$estado}"
        );

        $query = $this->pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata("filter_madres", $query, true);

        set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->render(
            new MadreComuniServices()
        );
        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction($estado = 'P')
    {
        //$this->buscarAction($estado);
    }


    public function indexAction()
    {
        $this->setParamToView("hide_header", true);
        $campo_field = array(
            "cedtra" => "Cedula",
            "prinom" => "Nombre",
            "priape" => "Apellido",
            "fecini" => "Fecha inicio",
            "fecsol" => "Fecha solicitud"
        );

        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", get_flashdata_item("filter_params"));
        $this->setParamToView("title", "Aprueba Madres Comunitarias");
        $this->setParamToView("buttons", array("F"));
        $this->loadParametrosView();
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
    }


    public function buscarAction(Request $request, $estado = 'P')
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input("numero", 10);
        $usuario = parent::getActUser();
        $query = "usuario='{$usuario}' and estado='{$estado}'";

        $this->pagination->setters(
            "cantidadPaginas: $cantidad_pagina",
            "pagina: {$pagina}",
            "query: {$query}",
            "estado: {$estado}"
        );

        if (
            get_flashdata_item("filter_madres") != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item("filter_params"));
        }

        set_flashdata("filter_madres", $query, true);
        set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->render(
            new MadreComuniServices()
        );

        return $this->renderObject($response, false);
    }

    /**
     * inforAction function
     * mostrar la ficha de afiliación de la empresa
     * @return void
     */
    public function inforAction($id = 0)
    {
        $madreComuniServices = new MadreComuniServices();
        if (!$id) {
            return redirect("aprobacioncom/index");
            exit;
        }
        $this->setParamToView("hide_header", true);

        $mercurio39 = $this->Mercurio39->findFirst("id='{$id}'");
        if ($mercurio39->getEstado() == "A") {
            set_flashdata("success", array(
                "msj" => "La empresa {$mercurio39->getNit()}, ya se encuentra aprobada su afiliación. Y no requiere de más acciones.",
                "code" => 200
            ));
            return redirect("aprobacioncom/index");
            exit;
        }
        $this->setParamToView("mercurio39", $mercurio39);

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            )
        );

        $datos_captura =  $procesadorComando->toArray();
        $paramsEmpresa = new ParamsEmpresa();
        $paramsEmpresa->setDatosCaptura($datos_captura);

        $mercurio01 = $this->Mercurio01->findFirst();
        $det_tipo = $this->Mercurio06->findFirst("tipo = '{$mercurio39->getTipo()}'")->getDetalle();

        $this->setParamToView("adjuntos", $madreComuniServices->adjuntos($mercurio39));
        $this->setParamToView("seguimiento", $madreComuniServices->seguimiento($mercurio39));

        $htmlEmpresa = view('cajas/aprobacioncom/tmp/consulta', array(
            'mercurio39' => $mercurio39,
            'mercurio01' => $mercurio01,
            'det_tipo' => $det_tipo,
            '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
            '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
            '_codciu' => ParamsEmpresa::getCiudades(),
            '_codzon' => ParamsEmpresa::getZonas(),
            '_codact' => ParamsEmpresa::getActividades(),
            '_tipsoc' => ParamsEmpresa::getTipoSociedades()
        ))->render();

        $this->setParamToView("consulta_empresa", $htmlEmpresa);
        $this->setParamToView("mercurio11", $this->Mercurio11->find());

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $mercurio39->getCedtra()
                )
            )
        );
        $out =  $procesadorComando->toArray();

        if ($out['success']) {
            $this->setParamToView("empresa_sisuweb", $out['data']);
        }

        $this->loadParametrosView($datos_captura);
        $madreComuniServices->loadDisplay($mercurio39);
        $this->setParamToView("mercurio39", $mercurio39);
        $this->setParamToView("title", "Solicitud Madre Comunitaria - {$mercurio39->getCedtra()} - {$mercurio39->getEstadoDetalle()}");
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
        $paramsEmpresa = new ParamsTrabajador();
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());


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


    /**
     * aprobar function
     * Aprobación de empresa
     * @return void
     */
    public function apruebaAction(Request $request)
    {
        $this->setResponse("ajax");
        $user = session()->get('user');
        $acceso = $this->Gener42->count("permiso='62' AND usuario='{$user['usuario']}'");
        if ($acceso == 0) {
            return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
        }

        $this->apruebaSolicitud = $this->services->get('ApruebaSolicitud', true);
        try {
            $postData = $request->all();
            $idSolicitud = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $calemp = 'M';
            $solicitud = $this->apruebaSolicitud->main(
                $calemp,
                $idSolicitud,
                $postData
            );

            $this->db->begin();
            $solicitud->enviarMail($request->input('actapr'), $request->input('feccap'));
            $salida = array(
                'success' => true,
                'msj' => 'El registro se completo con éxito'
            );

            $this->db->commit();
        } catch (DebugException $e) {
            $this->db->rollBack();
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
            );
        }

        return $this->renderObject($salida, false);
    }

    /**
     * devolverAction function
     * @return void
     */
    public function devolverAction(Request $request)
    {
        $this->setResponse("ajax");
        $this->madreComuniServices = new MadreComuniServices();
        $notifyEmailServices = new NotifyEmailServices();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($request->input('nota'));
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $mercurio39 = $this->Mercurio39->findFirst("id='{$id}'");
            if ($mercurio39->getEstado() == 'D') {
                throw new DebugException("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }

            $today = Carbon::now();
            $this->Mercurio39->updateAll("estado='D', motivo='{$nota}', codest='{$codest}', fecest='" . $today->format('Y-m-d H:i:s') . "'", "conditions: id='{$id}'");

            $item = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'");
            $mercurio10 = new Mercurio10();

            $notifyEmailServices->emailDevolver(
                $mercurio39,
                $this->madreComuniServices->msjDevolver($mercurio39, $nota)
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
        }
        return $this->renderObject($salida, false);
    }

    /**
     * rechazarAction function
     * @return void
     */
    public function rechazarAction(Request $request)
    {
        $this->setResponse("ajax");
        $notifyEmailServices = new NotifyEmailServices();
        $this->madreComuniServices =  new MadreComuniServices();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($request->input('nota'));
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio39 = $this->Mercurio39->findFirst(" id='{$id}'");

            if ($mercurio39->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }

            $this->madreComuniServices->rechazar($mercurio39, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio39, $this->madreComuniServices->msjRechazar($mercurio39, $nota));

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
        }
        return  $this->renderObject($salida, false);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        set_flashdata("filter_madres", false, true);
        set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => get_flashdata_item("filter_madres"),
            'filter' => get_flashdata_item("filter_params"),
        ));
    }
}
