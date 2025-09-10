<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Exceptions\DebugException;
use App\Services\Utils\Pagination;
use App\Services\CajaServices\PensionadoServices;
use App\Models\Mercurio38;
use App\Models\Mercurio01;
use App\Models\Mercurio06;
use App\Models\Mercurio11;
use App\Models\Mercurio37;
use App\Library\Collections\ParamsPensionado;
use App\Library\Auth;
use App\Models\Gener42;
use App\Services\Utils\NotifyEmailServices;
use App\Library\DbException;
use Illuminate\Support\Facades\View;
use App\Services\Utils\Comman;
use App\Services\Aprueba\ApruebaSolicitud;

class ApruebaPensionadoController extends ApplicationController
{

    protected $tipopc = 9;
    protected $db;
    protected $user;
    protected $tipo;
    /**
     * services variable
     * @var Services
     */
    protected $services;

    /**
     * pensionadoServices variable
     * @var PensionadoServices
     */
    protected $pensionadoServices;

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
     * @param string $estado
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

        set_flashdata("filter_pensionado", $query, true);

        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(
            new PensionadoServices()
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
        $this->setParamToView("title", "Aprueba Pensionado");
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
        //$this->loadParametrosView();
    }


    /**
     * name function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $estado
     * @return void
     */
    public function buscarAction(Request $request, $estado)
    {
        $this->setResponse("ajax");
        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;
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
            get_flashdata_item("filter_pensionado") != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata("filter_pensionado", $query, true);
        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(
            new PensionadoServices()
        );

        return $this->renderObject($response, false);
    }

    public function inforAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            if (!$id) {
                throw new DebugException("Error se requiere del id independiente", 501);
            }

            $pensionadoServices = new PensionadoServices();
            $mercurio38 = $this->Mercurio38->findFirst("id='{$id}'");

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_pensionado"
                )
            );
            $paramsPensionado = new ParamsPensionado();
            $paramsPensionado->setDatosCaptura($ps->toArray());

            $det_tipo = $this->Mercurio06->findFirst("tipo = '{$mercurio38->getTipo()}'")->getDetalle();

            $this->setParamToView("adjuntos", $pensionadoServices->adjuntos($mercurio38));

            $this->setParamToView("seguimiento", $pensionadoServices->seguimiento($mercurio38));

            $htmlEmpresa = View::render('aprobacionpen/tmp/consulta', array(
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

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio38->getCedtra()
                    )
                )
            );

            $out =  $ps->toArray();
            if ($out['success']) {
                $this->setParamToView("empresa_sisuweb", $out['data']);
            }

            $response = array(
                'success' => true,
                'data' => $mercurio38->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta_empresa" => $htmlEmpresa,
                'adjuntos' => $pensionadoServices->adjuntos($mercurio38),
                'seguimiento' => $pensionadoServices->seguimiento($mercurio38),
                'campos_disponibles' => $mercurio38->CamposDisponibles()
            );
        } catch (DebugException $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function apruebaAction(Request $request)
    {
        $this->setResponse("ajax");
        $debuginfo = null;
        try {
            try {
                $user = session()->get('user');
                $acceso = (new Gener42)->count("permiso='74' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(array("success" => false, "msj" => "El usuario no dispone de permisos de aprobación"), false);
                }

                $apruebaSolicitud = new ApruebaSolicitud();
                $this->db->begin();

                $calemp = 'P';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $request->input('id', "addslaches", "alpha", "extraspaces", "striptags"),
                    $request->all()
                );

                $this->db->commit();
                $solicitud->enviarMail($request->input('actapr'), $request->input('fecapr'));

                return $this->renderObject([
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                ], false);
            } catch (DebugException $e) {
                $this->db->rollback();
                return $this->renderObject([
                    "success" => false,
                    "msj" => $e->getMessage()
                ], false);
            }
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => "No se pudo realizar el movimiento " . "\n" . $err->getMessage() . "\n " . $err->getLine(),
            );
        }
        return $this->renderObject($salida);
    }

    public function devolverAction(Request $request)
    {
        $this->setResponse("ajax");
        $pensionadoServices =  new PensionadoServices();
        $notifyEmailServices = new NotifyEmailServices();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $request->input('nota');
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $mercurio38 = (new Mercurio38)->findFirst("id='{$id}'");
            if ($mercurio38->getEstado() == 'D') {
                throw new DebugException("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }

            $pensionadoServices->devolver($mercurio38, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio38,
                $pensionadoServices->msjDevolver($mercurio38, $nota)
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

    public function rechazarAction(Request $request)
    {
        $this->setResponse("ajax");
        $notifyEmailServices = new NotifyEmailServices();
        $pensionadoServices =  new PensionadoServices();
        try {
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $request->input('nota');
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio38 = (new Mercurio38)->findFirst(" id='{$id}'");

            if ($mercurio38->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }
            $pensionadoServices->rechazar($mercurio38, $nota, $codest);
            $notifyEmailServices->emailRechazar($mercurio38, $pensionadoServices->msjRechazar($mercurio38, $nota));

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

    public function borrarFiltroAction(Request $request)
    {
        $this->setResponse("ajax");
        set_flashdata("filter_pensionado", false, true);
        set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => get_flashdata_item("filter_pensionado"),
            'filter' => get_flashdata_item("filter_params"),
        ));
    }

    public function buscarEnSisuViewAction(Request $request, $id, $nit)
    {
        $user = session()->get('user');
        $mercurio38 = (new Mercurio38)->findFirst("nit='{$nit}'");
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

    public function editarViewAction(Request $request, $id)
    {
        if (!$id) {
            return redirect("aprobaindepen/index");
            exit;
        }
        $this->pensionadoServices = new PensionadoServices();
        $this->setParamToView("hide_header", true);
        $mercurio38 = $this->Mercurio38->findFirst("id='{$id}'");
        $this->setParamToView("mercurio38", $mercurio38);
        $this->setParamToView("tipopc", 2);
        $this->setParamToView("seguimiento", $this->pensionadoServices->seguimiento($mercurio38));

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

        //$this->loadParametrosView();
        $this->pensionadoServices->loadDisplay($mercurio38);
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

    public function paginationAction(Request $request, $estado = 'P')
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

        set_flashdata("filter_pensionado", $query, true);

        set_flashdata("filter_params", $this->pagination->filters, true);

        $response = $this->pagination->getCollection(
            new PensionadoServices()
        );
        $response['success'] = true;
        $response['msj'] = 'Consulta realizada con éxito';
        return $this->renderObject($response, false);
    }


    /**
     * aportes function
     * @param [type] $id
     * @return void
     */
    public function aportesAction($id)
    {
        $this->setResponse("ajax");
        try {
            try {
                $mercurio38 = (new Mercurio38)->findFirst(" id='{$id}'");
                if (!$mercurio38) {
                    throw new DebugException("La empresa no se encuentra registrada.", 201);
                }

                $procesadorComando = Comman::Api();
                $procesadorComando->runCli(
                    array(
                        "servicio" => "AportesEmpresas",
                        "metodo" => "buscarAportesEmpresa",
                        "params" => $mercurio38->getNit()
                    )
                );

                if ($procesadorComando->isJson() == False) {
                    throw new DebugException("Error procesando la consulta de aportes", 501);
                }

                $salida = $procesadorComando->toArray();
                $salida['solicitud'] = $mercurio38->getArray();
            } catch (DebugException $e) {
                throw new DebugException($e->getMessage(), 501);
            }
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => "No se pudo realizar el movimiento " . "\n" . $err->getMessage() . "\n " . $err->getLine(),
            );
        }
        return $this->renderObject($salida);
    }

    public function deshacerAction(Request $request)
    {
        $this->setResponse("ajax");

        $pensionadoServices = new PensionadoServices();
        $notifyEmailServices = new NotifyEmailServices();
        $action = $request->input('action');
        $codest = $request->input('codest');
        $sendEmail = $request->input('send_email');
        $nota = sanetizar($request->input('nota'));
        $comando = '';

        try {
            $id = $request->input('id');

            $mercurio38 = (new Mercurio38)->findFirst("id='{$id}'");
            if (!$mercurio38) {
                throw new DebugException("Los datos del pensionado no son validos para procesar.", 501);
            }

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio38->getCedtra(),
                        "coddoc" => $mercurio38->getTipdoc(),
                    )
                )
            );
            $out = $ps->toArray();
            $pensionadoSisu = $out['data'];

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "DeshacerAfiliaciones",
                    "metodo" => "deshacer_aprobacion_pensionado",
                    "params" => array(
                        "cedtra" => $mercurio38->getCedtra(),
                        "coddoc" => $mercurio38->getTipdoc(),
                        "fecafi" => $mercurio38->getFecapr(),
                        'fecapr' => $mercurio38->getFecapr(),
                        'nota' => $nota
                    )
                )
            );

            if ($ps->isJson() == False) {
                throw new DebugException("Error al procesar el deshacer la aprobación en SisuWeb.", 501);
            }

            $resdev = $ps->toArray();
            if ($resdev['success'] !== true) throw new DebugException($resdev['message'], 501);

            $datos = $resdev['data'];
            if ($datos['noAction']) {
                $salida = array(
                    'success' => false,
                    'msj' => 'No se realizo ninguna acción, el estado del pensionado no es valido para realizar la acción requerida.',
                    'data' => $pensionadoSisu
                );
            } else {
                //procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $pensionadoServices->devolver($mercurio38, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') $notifyEmailServices->emailDevolver($mercurio38, $pensionadoServices->msjDevolver($mercurio38, $nota));
                }

                if ($action == 'R') {
                    $pensionadoServices->rechazar($mercurio38, $nota, $codest);
                    if ($sendEmail == 'S') $notifyEmailServices->emailRechazar($mercurio38, $pensionadoServices->msjRechazar($mercurio38, $nota));
                }

                if ($action == 'I') {
                    $mercurio38->setEstado('I');
                    $mercurio38->setFecest(date('Y-m-d'));
                    $mercurio38->save();
                }

                $salida = array(
                    'data' => $pensionadoSisu,
                    'success' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? true : false,
                    'msj' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? 'Se completo el proceso con éxito.' : 'No se realizo el cambio requerido, se debe comunicar al área de soporte de las TICS.',
                    'isDeleteTrayecto' => $datos['isDeleteTrayecto'],
                    'noAction' => $datos['noAction'],
                    'isDelete' => $datos['isDelete'],
                );
            }
        } catch (DebugException $err) {
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
