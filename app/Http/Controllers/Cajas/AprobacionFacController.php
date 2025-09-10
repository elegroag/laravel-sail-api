<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Services\CajaServices\FacultativoServices;
use App\Services\Utils\Pagination;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Exceptions\DebugException;
use App\Library\Collections\ParamsFacultativo;
use App\Library\Auth;
use App\Library\Collections\ParamsPensionado;
use App\Models\Mercurio36;
use App\Models\Mercurio01;
use App\Models\Mercurio06;
use App\Models\Mercurio11;
use App\Models\Mercurio37;
use App\Models\Gener42;
use App\Services\Utils\NotifyEmailServices;
use App\Library\DbException;
use App\Services\Aprueba\ApruebaSolicitud;
use Illuminate\Support\Facades\View;
use App\Services\Utils\Comman;
use Exception;

class AprobacionfacController extends ApplicationController
{

    protected $tipopc = 10;
    protected $db;
    protected $user;
    protected $tipo;

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

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * aplicarFiltroAction function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function aplicarFiltroAction(Request $request, $estado = 'P')
    {
        $this->setResponse("ajax");
        $cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;
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
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata("filter_facultativo", $query, true);

        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(
            new FacultativoServices()
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
            "priape" => "Primer Apellido",
            "segape" => "Segundo Apellido",
            "prinom" => "Primer Nombre",
            "segnom" => "Segundo Nombre",
        );

        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", get_flashdata_item("filter_params"));
        $this->setParamToView("title", "Aprueba Facultativo");
        $this->setParamToView("buttons", array("F"));
        $this->loadParametrosView();
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
    }

    public function buscarAction(Request $request, string $estado = 'P')
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input("numero", 10);
        $usuario = session()->get('user');
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Request([
                "cantidadPaginas" => $cantidad_pagina,
                "pagina" => $pagina,
                "query" => $query_str,
                "estado" => $estado
            ])
        );

        if (
            get_flashdata_item("filter_facultativo") != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata("filter_facultativo", $query, true);
        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(
            new FacultativoServices()
        );

        return $this->renderObject($response, false);
    }

    /**
     * infor function
     * @return void
     */
    public function inforAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            if (!$id) {
                throw new DebugException("Error se requiere del id independiente", 501);
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
    public function apruebaAction(Request $request)
    {
        $this->setResponse("ajax");
        $user = session()->get('user');
        $debuginfo = array();

        $acceso = (new Gener42)->count("permiso='62' AND usuario='{$user['usuario']}'");
        if ($acceso == 0) {
            return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
        }
        $apruebaSolicitud = new ApruebaSolicitud();
        $this->db->begin();
        try {
            try {
                $postData = $_POST;
                $idSolicitud = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
                $calemp = 'F';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $postData
                );

                $this->db->commit();
                $solicitud->enviarMail($request->input('actapr'), $request->input('feccap'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (DebugException $err) {

                $this->db->rollback();
                $salida = array(
                    "success" => false,
                    "msj" => $err->getMessage(),
                );
            }
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
            );
        }
        if ($debuginfo) $salida['info'] = $debuginfo;
        return $this->renderObject($salida, false);
    }

    public function borrarFiltroAction(Request $request)
    {
        $this->setResponse("ajax");
        set_flashdata("filter_facultativo", false, true);
        set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => get_flashdata_item("filter_facultativo"),
            'filter' => get_flashdata_item("filter_params"),
        ));
    }

    public function editarViewAction($id)
    {
        if (!$id) {
            return redirect("aprobacionfac/index");
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

    public function edita_empresaAction(Request $request)
    {
        $this->setResponse("ajax");
        $nit = $request->input('nit');
        $id = $request->input('id');
        try {
            $mercurio36 = $this->Mercurio36->findFirst("nit='{$nit}' AND id='{$id}'");
            if (!$mercurio36) {
                throw new DebugException("La empresa no está disponible para notificar por email", 501);
            } else {
                $tipsoc = $request->input('tipsoc');
                if (strlen($tipsoc) == 1) {
                    $tipsoc = str_pad($tipsoc, 2, '0', STR_PAD_LEFT);
                }
                $data = array(
                    "razsoc" => $request->input('razsoc'),
                    "codact" => $request->input('codact'),
                    "digver" => $request->input('digver'),
                    "calemp" => $request->input('calemp'),
                    "cedrep" => $request->input('cedrep'),
                    "repleg" => $request->input('repleg'),
                    "direccion" => $request->input('direccion'),
                    "codciu" => $request->input('codciu'),
                    "codzon" => $request->input('codzon'),
                    "telefono" => $request->input('telefono'),
                    "celular" => $request->input('celular'),
                    "fax" => $request->input('fax'),
                    "email" => $request->input('email'),
                    "sigla" => $request->input('sigla'),
                    "fecini" => $request->input('fecini'),
                    "tottra" => $request->input('tottra'),
                    "valnom" => $request->input('valnom'),
                    "tipsoc" => $tipsoc,
                    "dirpri" => $request->input('dirpri'),
                    "ciupri" => $request->input('ciupri'),
                    "celpri" => $request->input('celpri'),
                    'tipemp' => $request->input('tipemp'),
                    "emailpri" => $request->input('emailpri'),
                    "tipper" => $request->input('tipper'),
                    "matmer" => $request->input('matmer'),
                    "coddocrepleg" => (!$request->input('coddocrepleg')) ? '1' : $request->input('coddocrepleg'),
                    "prinom" => ($request->input('tipper') == 'N') ? $request->input('prinom') : $request->input('prinomrepleg'),
                    "priape" => ($request->input('tipper') == 'N') ? $request->input('priape') : $request->input('priaperepleg'),
                    "segnom" => ($request->input('tipper') == 'N') ? $request->input('segnom') : $request->input('segnomrepleg'),
                    "segape" => ($request->input('tipper') == 'N') ? $request->input('segape') : $request->input('segaperepleg'),
                    "prinomrepleg" => ($request->input('tipper') == 'J') ? $request->input('prinomrepleg') : '',
                    "priaperepleg" => ($request->input('tipper') == 'J') ? $request->input('priaperepleg') : '',
                    "segnomrepleg" => ($request->input('tipper') == 'J') ? $request->input('segnomrepleg') : '',
                    "segaperepleg" => ($request->input('tipper') == 'J') ? $request->input('segaperepleg') : '',
                    "telpri" => $request->input('telpri')
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

    public function rechazarAction(Request $request)
    {
        $this->setResponse("ajax");
        $notifyEmailServices = new NotifyEmailServices();
        $this->facultativoServices =  new FacultativoServices();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($request->input('nota'));
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio41 = $this->Mercurio41->findFirst(" id='{$id}'");

            if ($mercurio41->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }

            $this->facultativoServices->rechazar($mercurio41, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio41, $this->facultativoServices->msjRechazar($mercurio41, $nota));

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

    public function devolverAction(Request $request)
    {
        $this->setResponse("ajax");
        $this->facultativoServices =  $this->services->get('facultativoServices');
        $notifyEmailServices = new NotifyEmailServices();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($request->input('nota'));
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $mercurio41 = $this->Mercurio41->findFirst("id='{$id}'");
            if ($mercurio41->getEstado() == 'D') {
                throw new DebugException("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }

            $this->facultativoServices->devolver($mercurio41, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio41,
                $this->facultativoServices->msjDevolver($mercurio41, $nota)
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
}
