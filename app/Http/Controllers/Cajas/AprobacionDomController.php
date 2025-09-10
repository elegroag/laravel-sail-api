<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Services\CajaServices\ServicioDomesticoServices;
use App\Services\Utils\Comman;
use App\Services\Utils\Pagination;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Exceptions\DebugException;
use App\Library\Collections\ParamsPensionado;
use App\Library\Auth;
use App\Models\Mercurio38;
use App\Models\Mercurio06;
use App\Models\Mercurio01;
use App\Models\Mercurio11;
use App\Models\Mercurio37;
use App\Models\Gener42;
use App\Services\Utils\NotifyEmailServices;
use App\Library\DbException;
use Illuminate\Support\Facades\View;

class AprobaciondomController extends ApplicationController
{

    protected $tipopc = 12;
    protected $db;
    protected $user;
    protected $tipo;

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



    public function __construct()
    {
        $this->pagination = new Pagination();
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * aplicarFiltroAction function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
    public function aplicarFiltroAction(Request $request, Response $response, string $estado = 'P')
    {
        $this->setResponse("ajax");
        $cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;
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

        set_flashdata("filter_domestico", $query, true);

        set_flashdata("filter_params", $this->pagination->filters, true);

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
        //$this->buscarAction($estado);
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
        $this->setParamToView("filters", get_flashdata_item("filter_params"));
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
    public function buscarAction(Request $request, Response $response, string $estado)
    {
        $this->setResponse("ajax");
        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;
        $usuario = parent::getActUser();
        $query = "usuario='{$usuario}' and estado='{$estado}'";

        $this->pagination->setters(
            "cantidadPaginas: $cantidad_pagina",
            "pagina: {$pagina}",
            "query: {$query}",
            "estado: {$estado}"
        );

        if (
            get_flashdata_item("filter_domestico") != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item("filter_params"));
        }

        set_flashdata("filter_domestico", $query, true);
        set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->render(
            new servicioDomesticoServices()
        );

        return $this->renderObject($response, false);
    }

    public function inforAction($id)
    {
        $servicioDomesticoServices = new ServicioDomesticoServices();
        if (!$id) {
            return redirect("aprobaindepen/index");
            exit;
        }
        $this->setParamToView("hide_header", true);

        $mercurio38 = $this->Mercurio38->findFirst("id='{$id}'");
        if ($mercurio38->getEstado() == "A") {
            set_flashdata("success", array(
                "msj" => "La empresa {$mercurio38->getNit()}, ya se encuentra aprobada su afiliación. Y no requiere de más acciones.",
                "code" => 200
            ));
            return redirect("aprobaindepen/index");
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
    public function apruebaAction(Request $request)
    {
        $this->setResponse("ajax");
        $user = session()->get('user');
        $acceso = $this->Gener42->count("permiso='74' AND usuario='{$user['usuario']}'");
        if ($acceso == 0) {
            return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
        }

        $this->apruebaSolicitud = $this->services->get('ApruebaSolicitud', true);
        try {
            $postData = $request->all();
            $idSolicitud = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $calemp = 'E';

            $solicitud = $this->apruebaSolicitud->main(
                $calemp,
                $idSolicitud,
                $postData
            );

            $this->apruebaSolicitud->endTransa();
            $solicitud->enviarMail($request->input('actapr'), $request->input('feccap'));

            return $this->renderObject([
                'success' => true,
                'msj' => 'El registro se completo con éxito'
            ], false);
        } catch (DebugException $e) {
            return $this->renderObject([
                "success" => false,
                "msj" => $e->getMessage()
            ], false);
        }
    }

    public function devolverAction(Request $request)
    {
        $this->setResponse("ajax");
        $servicioDomesticoServices =  new ServicioDomesticoServices();
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
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
                "code" => 500
            );
        }
        return $this->renderObject($salida, false);
    }

    public function rechazarAction(Request $request)
    {
        $this->setResponse("ajax");

        $notifyEmailServices = new NotifyEmailServices();
        $servicioDomesticoServices =  new ServicioDomesticoServices();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($request->input('nota'));
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");

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
        }
        return  $this->renderObject($salida, false);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        set_flashdata("filter_domestico", false, true);
        set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => get_flashdata_item("filter_domestico"),
            'filter' => get_flashdata_item("filter_params"),
        ));
    }

    public function buscarEnSisuViewAction($id, $nit)
    {
        $user = session()->get('user');
        $mercurio38 = $this->Mercurio38->findFirst("nit='{$nit}'");
        if (!$mercurio38) {
            set_flashdata("error", array(
                "msj" => "La empresa no se encuentra registrada.",
                "code" => 201
            ));
            return redirect("aprobaindepen/index");
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
            set_flashdata("error", array(
                "msj" => "La empresa no se encuentra registrada.",
                "code" => 201
            ));
            return redirect("aprobaindepen/index");
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
            return redirect("aprobaindepen/index");
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
        $this->setParamToView("mercurio38", $mercurio38);
        $this->setParamToView("title", "Editar Ficha Pensionado " . $mercurio38->getCedtra());
    }

    public function edita_empresaAction(Request $request)
    {
        $this->setResponse("ajax");
        $nit = $request->input('nit');
        $id = $request->input('id');
        try {
            $mercurio38 = $this->Mercurio38->findFirst("nit='{$nit}' AND id='{$id}'");
            if (!$mercurio38) {
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
                $this->Mercurio38->updateAll($setters, "conditions: id='{$id}' AND nit='{$nit}'");
                $salida = array(
                    "msj" => "Proceso se ha completado con éxito",
                    "success" => true
                );
            }
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }
}
