<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobacionempController extends ApplicationController
{
    protected $tipopc = 2;
    protected $services;
    /**
     * independienteServices variable
     * @var EmpresaServices
     */
    protected $empresaServices;

    public function initialize()
    {
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        Core::importLibrary("ParamsEmpresa", "Collections");
        $this->setTemplateAfter('bone');
        $this->services = Services::Init();
    }

    /**
     * beforeFilter function
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param array $permisos
     * @return void
     */
    public function beforeFilter($permisos = array())
    {
        $permisos = array(
            "aplicarFiltro" => "59",
            "info" => "60",
            "buscar" => "61"
        );
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
     * @changed [2023-12-19]
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
        $pagination = new Pagination(new Request(array(
            'query' => $query_str,
            'estado' => $estado,
            'cantidadPaginas' => $cantidad_pagina
        )));

        $query = $pagination->filter(
            $this->getPostParam('campo'),
            $this->getPostParam('condi'),
            $this->getPostParam('value')
        );

        Flash::set_flashdata("filter_empresa", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new EmpresaServices());
        return $this->renderObject($response, false);
    }

    public function listarAction($estado = 'P')
    {
        $this->setResponse("ajax");
        try {
            $pagination = new Pagination();
            $filtro = $pagination->filter(
                $this->getPostParam('campo'),
                $this->getPostParam('condi'),
                $this->getPostParam('value')
            );

            $empresaServices = new EmpresaServices();
            $out = $empresaServices->findByUserAndEstado(new Request(array(
                'usuario' => parent::getActUser(),
                'estado' => $estado,
                'filtro' => $filtro
            )));

            $response = array(
                'success' => true,
                'msj' => 'Consulta realizada con exito',
                'data' => $out
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
     * changeCantidadPaginaAction function
     * @changed [2023-12-19]
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
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
    public function indexAction()
    {
        $this->setParamToView("hide_header", true);
        $campo_field = array(
            "nit" => "NIT",
            "razsoc" => "Razon social",
            "codzon" => "Codigo zona",
            "documento" => "ID",
            "fecini" => "Fecha inicio",
            "cedrep" => "Cedula representante"
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", Flash::get_flashdata_item("filter_params"));
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("title", "Aprueba Empresa");
        $this->loadParametrosView();
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
    }

    /**
     * opcionalAction function
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return void
     */
    public function opcionalAction($estado = 'P')
    {
        $this->setParamToView("hide_header", true);
        $campo_field = array(
            "nit" => "Nit",
            "razsoc" => "Razon Social"
        );
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Aprueba Empresa");
        $collection = $this->Mercurio30->find("estado='{$estado}' AND usuario=" . parent::getActUser() . " ORDER BY fecini ASC");

        $empresaServices = new EmpresaServices();
        $data = $empresaServices->dataOptional($collection, $estado);

        $this->setParamToView("empresas", $data);
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("pagina_con_estado", $estado);
    }

    /**
     * buscarAction function
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
                    'query' => $query_str,
                    'estado' => $estado,
                    'cantidadPaginas' => $cantidad_pagina,
                    'pagina' => $pagina
                )
            )
        );

        if (
            Flash::get_flashdata_item("filter_empresa") != false
        ) {
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

        $response = $pagination->render(new EmpresaServices());
        return $this->renderObject($response, false);
    }

    /**
     * devolverAction function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function devolverAction()
    {
        $this->setResponse("ajax");
        try {
            $empresaServices = new EmpresaServices();
            $notifyEmailServices = new NotifyEmailServices();

            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $this->getPostParam('nota');
            $array_corregir = $this->getPostParam('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $mercurio30 = (new Mercurio30)->findFirst("id='{$id}'");
            if ($mercurio30->getEstado() == 'D') {
                throw new Exception("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }

            $empresaServices->devolver($mercurio30, $nota, $codest, $campos_corregir);
            $notifyEmailServices->emailDevolver(
                $mercurio30,
                $empresaServices->msjDevolver($mercurio30, $nota)
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
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function rechazarAction()
    {
        $this->setResponse("ajax");
        $notifyEmailServices = new NotifyEmailServices();
        $empresaServices =  new EmpresaServices();
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $this->getPostParam('nota');
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio30 = (new Mercurio30)->findFirst(" id='{$id}'");

            if ($mercurio30->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }

            $empresaServices->rechazar($mercurio30, $nota, $codest);

            $notifyEmailServices->emailRechazar(
                $mercurio30,
                $empresaServices->msjRechazar($mercurio30, $nota)
            );

            $salida = array(
                "success" => true,
                "msj" => "El proceso se ha completado con éxito"
            );

            /*
			$satServices = new SatServices();
			if ($mercurio30->getDocumentoRepresentanteSat() > 0) {
				$resultado_tramite = '2'; //Afiliación Rechazada
				$fecha = date('Y-m-d');
				$rqs_sat = $satServices->notificarServicioSat($mercurio30, $resultado_tramite, $fecha, $nota);
				$salida['respuesta_solicitud_sat'] = $rqs_sat;
			}*/
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

    /**
     * pendiente_email function
     * metodo vista
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     * @return void
     */
    public function pendiente_emailAction()
    {
        $flash_mensaje = SESSION::getData("flash_mensaje");
        SESSION::setData("flash_mensaje", null);
        $this->setParamToView("flash_mensaje", $flash_mensaje);
        $this->setParamToView("title", "Procesar Notificación Pendiente");
    }

    /**
     * rezagoCorreo function
     * metodo ajax
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     * @return void
     */
    public function rezagoCorreoAction()
    {
        $this->setResponse("view");
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $nit = $this->getPostParam('nit');
        $anexo_final = $this->getPostParam('anexo_final');
        $anexo_inicial = $this->getPostParam('anexo_inicial');
        $feccap = $this->getPostParam('feccap');
        $feccap = new DateTime($feccap);

        try {
            $mercurio30 = $this->Mercurio30->findFirst("nit='{$nit}' AND estado='A'");
            if (!$mercurio30) {
                throw new Exception("Error la empresa no es valida para envio de correo.", 501);
            }
            $consultasOldServices = new GeneralService();
            $servicio = $consultasOldServices->webService("datosEmpresa", $_POST);
            if ($servicio['flag'] == false) {
                throw new Exception("Error al buscar la empresa en SISUWEB.", 502);
            }
            if (!$servicio['data']) {
                throw new Exception("Los datos de la empresa no está disponible en SISUWEB.", 503);
            }

            $asunto = "Afiliacion de la empresa realizada con Exito. Nit: " . $mercurio30->getNit();
            $mercurio07 = $this->Mercurio07->findFirst("tipo='{$mercurio30->getTipo()}' and coddoc='{$mercurio30->getCoddoc()}' and documento='{$mercurio30->getDocumento()}'");
            if (!$mercurio07) {
                throw new Exception("Error no hay usuario empresa para el servicio de autogestión de comfaca en línea.", 504);
            }
            $mercurio07->setTipo('E');
            $mercurio07->save();
            $mercurio01 = $this->Mercurio01->findFirst();
            $mercurio02 = $this->Mercurio02->findFirst();
            $_email = trim($mercurio01->getEmail());
            $_clave = trim($mercurio01->getClave());

            //Prueba
            //$_email = "soporte_sistemas@comfaca.com";
            //$_clave = "";

            $mensaje = "";
            ob_start();
            $this->setParamToView("rutaImg", "https://comfacaenlinea.com.co/Mercurio/public/img/Mercurio/logob.png");
            $this->setParamToView("mercurio30", $mercurio30);
            $this->setParamToView("actapr", $this->getPostParam('actapr'));
            $this->setParamToView("dia", $feccap->format("d"));
            $this->setParamToView("mes", $meses[intval($feccap->format("m") - 1)]);
            $this->setParamToView("anno", $feccap->format("Y"));
            $this->setParamToView("ruta_firma", "https://comfacaenlinea.com.co/Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg");
            $this->setParamToView("mercurio02", $mercurio02);
            $this->setParamToView("anexo_final", $anexo_final);
            $this->setParamToView("anexo_inicial", $anexo_inicial);
            echo View::renderView("aprobacionemp/mail/aprobar");
            $mensaje = ob_get_contents();
            ob_end_clean();

            Core::importFromLibrary("Swift", "Swift.php");
            Core::importFromLibrary("Swift", "Swift/Connection/SMTP.php");
            $smtp = new Swift_Connection_SMTP(
                "smtp.gmail.com",
                Swift_Connection_SMTP::PORT_SECURE,
                Swift_Connection_SMTP::ENC_TLS
            );

            $smtp->setUsername($_email);
            $smtp->setPassword($_clave);
            $smsj = new Swift_Message();
            $smsj->setSubject($asunto);
            $smsj->setContentType("text/html");
            $smsj->setBody($mensaje);
            $swift = new Swift($smtp);
            $recip = new Swift_RecipientList();

            $email = $mercurio07->getEmail();
            $nombre = $mercurio07->getNombre();

            $recip->addTo($email, $nombre);
            $swift->send($smsj, $recip, new Swift_Address($_email));
            SESSION::setData("flash_mensaje", "El envío se ha completado a la dirección de email: " . $mercurio07->getEmail() . " nombre: " . $mercurio07->getNombre());
        } catch (\Exception $err) {
            SESSION::setData("flash_mensaje", $err->getMessage());
        }
        Router::redirectToApplication('Cajas/aprobacionemp/pendiente_email');
    }

    /**
     * empresa_search function
     * metodo ajax
     * autor edwin andres legro agudelo
     * fecha 24-08-2021
     * @return void
     */
    public function empresa_searchAction()
    {
        $this->setResponse("ajax");
        $nit = $this->getPostParam('nit');
        try {
            $mercurio30 = $this->Mercurio30->findFirst("nit='{$nit}' AND estado='A'");
            if (!$mercurio30) {
                throw new Exception("La empresa no está disponible para notificar por email", 501);
            } else {
                $data07 = $this->Mercurio07->find("conditions: documento='{$mercurio30->getDocumento()}'");
                $consultasOldServices = new GeneralService();
                $servicio = $consultasOldServices->webService("datosEmpresa", $_POST);
                if ($servicio['flag'] == false) {
                    throw new Exception("Error al buscar la empresa en SISUWEB.", 502);
                }
                if (!$servicio['data']) {
                    throw new Exception("Los datos de la empresa no está disponible en SISUWEB.", 503);
                }

                $mercurio07 = array();
                foreach ($data07 as $ai => $row) {
                    $mercurio07[] = array(
                        "tipo" => $row->getTipo(),
                        "estado" => $row->getEstado(),
                        "coddoc" => $row->getCoddoc(),
                        "fecreg" => $row->getFecreg()->getUsingFormatDefault()
                    );
                }

                $salida = array(
                    "success" => true,
                    "mercurio30" => array(
                        "id" => $mercurio30->getId(),
                        "nit" => $mercurio30->getNit(),
                        "tipdoc" => $mercurio30->getTipdoc(),
                        "razsoc" => $mercurio30->getRazsoc(),
                        "email" => $mercurio30->getEmail(),
                        "estado" => $mercurio30->getEstado(),
                        "fecest" => $mercurio30->getFecest()->getUsingFormatDefault(),
                        "fecini" => $mercurio30->getFecini()->getUsingFormatDefault()
                    ),
                    "mercurio07" => $mercurio07,
                    "subsi02" => $servicio['data']
                );
            }
        } catch (\Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }

        return $this->renderObject($salida, false);
    }

    /**
     * info_empresaAction function
     * mostrar la ficha de afiliación de la empresa
     * @return void
     */
    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $empresaServices = new EmpresaServices();
            $id = $this->getPostParam('id');
            if (!$id) {
                Router::rTa("aprobacionemp/index");
                exit;
            }

            $mercurio30 = $this->Mercurio30->findFirst("id='{$id}'");
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

            $adjuntos = $empresaServices->adjuntos($mercurio30);
            $seguimiento = $empresaServices->seguimiento($mercurio30);

            $htmlEmpresa = View::render(
                'aprobacionemp/tmp/consulta',
                array(
                    'mercurio30' => $mercurio30,
                    'mercurio01' => $this->Mercurio01->findFirst(),
                    'det_tipo' => $this->Mercurio06->findFirst("tipo = '{$mercurio30->getTipo()}'")->getDetalle(),
                    '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
                    '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
                    '_codciu' => ParamsEmpresa::getCiudades(),
                    '_codzon' => ParamsEmpresa::getZonas(),
                    '_codact' => ParamsEmpresa::getActividades(),
                    '_tipsoc' => ParamsEmpresa::getTipoSociedades()
                )
            );

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio30->getNit()
                    )
                )
            );

            $out = $procesadorComando->toArray();
            $empresa_sisuweb = ($out['success']) ? $out['data'] : false;

            $campos_disponibles =  $mercurio30->CamposDisponibles();
            $response = array(
                'success' => true,
                'data' => $mercurio30->getArray(),
                'empresa_sisuweb' => $empresa_sisuweb,
                'mercurio11' => $this->Mercurio11->find(),
                "consulta_empresa" => $htmlEmpresa,
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

        $_coddocrepleg = array();
        foreach (ParamsEmpresa::getCodruaDocumentos()  as $ai =>  $valor) {
            if ($valor == 'TI' || $valor == 'RC') continue;
            $_coddocrepleg[$ai] = $valor;
        }

        $this->setParamToView("_tipdur", ParamsEmpresa::getTipoDuracion());
        $this->setParamToView("_codind", ParamsEmpresa::getCodigoIndice());
        $this->setParamToView("_contratista", ParamsEmpresa::getContratista());
        $this->setParamToView("_todmes", ParamsEmpresa::getPagaMes());
        $this->setParamToView("_forpre", ParamsEmpresa::getFormaPresentacion());
        $this->setParamToView("_tipsoc", ParamsEmpresa::getTipoSociedades());
        $this->setParamToView("_pymes", ParamsEmpresa::getPymes());
        $this->setParamToView("_tipemp", ParamsEmpresa::getTipoEmpresa());
        $this->setParamToView("_tipapo", ParamsEmpresa::getTipoAportante());
        $this->setParamToView("_ofiafi", ParamsEmpresa::getOficina());
        $this->setParamToView("_colegio", ParamsEmpresa::getColegio());
        $this->setParamToView("_tipper", ParamsEmpresa::getTipoPersona());
        $this->setParamToView("_codzon", ParamsEmpresa::getZonas());
        $this->setParamToView("_calemp", ParamsEmpresa::getCalidadEmpresa());
        $this->setParamToView("_codciu", ParamsEmpresa::getCiudades());
        $this->setParamToView("_codact", ParamsEmpresa::getActividades());
        $this->setParamToView("_coddoc", ParamsEmpresa::getTipoDocumentos());
        $this->setParamToView("_ciupri", ParamsEmpresa::getCiudadesComerciales());
        $this->setParamToView("_coddocrepleg", $_coddocrepleg);
    }

    public function editarViewAction($id)
    {
        $this->empresaServices = new EmpresaServices();
        if (!$id) {
            Router::rTa("aprobacionemp/index");
            exit;
        }
        $this->setParamToView("hide_header", true);
        $mercurio30 = $this->Mercurio30->findFirst("id='{$id}'");
        $this->setParamToView("tipopc", 2);
        $this->setParamToView("seguimiento", $this->empresaServices->seguimiento($mercurio30));

        $mercurio01 = $this->Mercurio01->findFirst();
        $this->setParamToView("mercurio01", $mercurio01);
        $mercurio37 = $this->Mercurio37->find(" tipopc=2 AND numero='{$mercurio30->getId()}'");
        $this->setParamToView("mercurio37", $mercurio37);
        $this->setParamToView("idModel", $id);
        $this->setParamToView("det_tipo", $this->Mercurio06->findFirst("tipo = '{$mercurio30->getTipo()}'")->getDetalle());
        $this->loadParametrosView();
        $this->empresaServices->loadDisplay($mercurio30);
        $this->setParamToView("mercurio30", $mercurio30);
        $this->setParamToView("title", "Editar Ficha Empresa " . $mercurio30->getNit());
    }

    public function edita_empresaAction()
    {
        $this->setResponse("ajax");
        $nit = $this->getPostParam('nit');
        $id = $this->getPostParam('id');
        try {
            $mercurio30 = $this->Mercurio30->findFirst("nit='{$nit}' AND id='{$id}'");
            if (!$mercurio30) {
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
                $this->Mercurio30->updateAll($setters, "conditions: id='{$id}' AND nit='{$nit}'");
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

    /**
     * empresa_sisuwebAction function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     * @param [type] $nit
     * @return void
     */
    public function buscarEnSisuViewAction($id, $nit)
    {
        $mercurio30 = $this->Mercurio30->findFirst("nit='{$nit}'");
        if (!$mercurio30) {
            Flash::set_flashdata("error", array(
                "msj" => "La empresa no se encuentra registrada.",
                "code" => 201
            ));
            Router::rTa("aprobacionemp/index");
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
            Router::rTa("aprobacionemp/index");
            exit();
        }

        $this->setParamToView("idEmpresa", $id);
        $this->setParamToView("empresa", $response['data']);
        $this->setParamToView("trayectoria", $response['trayectoria']);
        $this->setParamToView("sucursales", $response['sucursales']);
        $this->setParamToView("listas", $response['listas']);
        $this->setParamToView("title", "Empresa SisuWeb - {$nit}");
    }

    /**
     * excel_reporte function
     * pendientes, devueltos y rechazados
     * @return void
     */
    public function excel_reporteAction($estado = 'P')
    {
        Core::importLibrary("CalculatorDias", "Pagination");

        $this->setResponse('view');
        $fecha = new Date();
        $file = "public/temp/" . "reporte_solicitudes_" . $fecha->getUsingFormatDefault() . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        $excel->setMerge(0, 1, 0, 6);
        $excel->write(0, 1, 'Reporte De Solicitudes Afiliacion Empresas', $title);
        $columns = array('Documento', 'Nit', 'Razon social', 'Cedula Representante', 'Cod documento', 'Dias vencidos', 'Estado', 'Tipsoc', 'Tipper', 'Email', 'Cod actividad', 'Telefono');
        $excel->setColumn(0, 0, 16);
        $excel->setColumn(1, 1, 16);
        $excel->setColumn(2, 2, 35);
        $excel->setColumn(3, 3, 25);
        $excel->setColumn(4, 4, 25);
        $excel->setColumn(5, 5, 25);
        $excel->setColumn(6, 6, 30);
        $excel->setColumn(7, 7, 10);
        $excel->setColumn(8, 8, 10);
        $excel->setColumn(9, 9, 50);
        $excel->setColumn(10, 10, 20);
        $excel->setColumn(11, 11, 20);
        $i = 0;
        $j = 2;
        foreach ($columns as $column) {
            $value = ucfirst($column);
            $excel->write($j, $i++, $value, $column_title);
        }
        $usuario = parent::getActUser();
        $solicitudes = $this->Mercurio30->find(" estado='{$estado}' AND usuario='{$usuario}' ORDER BY fecini DESC");
        $j++;
        foreach ($solicitudes as $solicitud) {
            $i = 0;
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $solicitud->getId(), $solicitud->getFecini());
            $excel->write($j, $i++, $solicitud->getDocumento(), $column_style);
            $excel->write($j, $i++, $solicitud->getNit(), $column_style);
            $excel->write($j, $i++, $solicitud->getRazsoc(), $column_style);
            $excel->write($j, $i++, $solicitud->getCedrep(), $column_style);
            $excel->write($j, $i++, $solicitud->getCoddoc(), $column_style);
            $excel->write($j, $i++, $dias_vencidos, $column_style);
            $excel->write($j, $i++, $solicitud->getEstadoDetalle(), $column_style);
            $excel->write($j, $i++, $solicitud->getTipsoc(), $column_style);
            $excel->write($j, $i++, $solicitud->getTipper(), $column_style);
            $excel->write($j, $i++, $solicitud->getEmail(), $column_style);
            $excel->write($j, $i++, '#' . $solicitud->getCodact(), $column_style);
            $excel->write($j, $i++, $solicitud->getTelefono(), $column_style);
            $j++;
        }
        $excels->close();
        header("location: " . Core::getInstancePath() . "/{$file}");
    }

    /**
     * aprobar function
     * Aprobación de empresa
     * @return void
     */
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
                $apruebaSolicitud = new ApruebaSolicitud();
                $apruebaSolicitud->setTransa();

                $postData = $_POST;
                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $calemp = 'E';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $postData
                );

                $apruebaSolicitud->endTransa();
                $solicitud->enviarMail($this->getPostParam('actapr'), $this->getPostParam('fecapr'));
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

    public function borrarFiltroAction()
    {
        $this->setResponse("ajax");
        Flash::set_flashdata("filter_independiente", false, true);
        Flash::set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => Flash::get_flashdata_item("filter_independiente"),
            'filter' => Flash::get_flashdata_item("filter_params"),
        ));
    }

    public function aportesViewAction($id)
    {
        $mercurio30 = $this->Mercurio30->findFirst(" id='{$id}'");
        if (!$mercurio30) {
            Flash::set_flashdata("error", array(
                "msj" => "La empresa no se encuentra registrada.",
                "code" => 201
            ));
            Router::rTa("aprobacionemp/info_empresa/" . $id);
            exit();
        }

        $this->setParamToView("hide_header", true);
        $this->setParamToView("idModel", $id);
        $this->setParamToView("nit", $mercurio30->getNit());
        $this->setParamToView("title", "Aportes de empresa " . $mercurio30->getNit());
    }

    public function aportesAction($id)
    {
        $this->setResponse("ajax");
        $comando = '';
        try {
            try {
                $mercurio30 = (new Mercurio30)->findFirst(" id='{$id}'");
                if (!$mercurio30) {
                    throw new Exception("La empresa no se encuentra registrada.", 201);
                }

                $ps = Comman::Api();
                $ps->runCli(
                    array(
                        "servicio" => "AportesEmpresas",
                        "metodo" => "buscarAportesEmpresa",
                        "params" => $mercurio30->getNit()
                    )
                );

                if ($ps->isJson() == False) throw new Exception("Error procesando la consulta de aportes", 501);

                $salida = $ps->toArray();
                $salida['solicitud'] =  $mercurio30->getArray();
            } catch (TransactionFailed $e) {
                throw new Exception($e->getMessage(), 501);
            }
        } catch (Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => "No se pudo realizar el movimiento " . "\n" . $err->getMessage() . "\n " . $err->getLine(),
                "comando" => $comando
            );
        }
        return $this->renderObject($salida, false);
    }

    /**
     * infoAprobadoView function
     * Detalle de la aprobacion de la empresa, traer los datos de SISU de la empresa
     * @param [type] $id
     * @return void
     */
    public function infoAprobadoViewAction($id)
    {
        try {
            $mercurio30 = $this->Mercurio30->findFirst(" id='{$id}' and estado='A' ");
            if (!$mercurio30) {
                throw new DebugException("La empresa no se encuentra aprobada para consultar sus datos.", 501);
            }

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_empresa"
                )
            );
            $datos_captura =  $ps->toArray();
            $paramsEmpresa = new ParamsEmpresa();
            $paramsEmpresa->setDatosCaptura($datos_captura);

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio30->getNit()
                    )
                )
            );

            if ($ps->isJson() == false) throw new DebugException("Error al buscar la empresa en Sisuweb", 501);

            $out = $ps->toArray();
            if ($out['success'] == false)  throw new DebugException("Los datos de la empresa no se encuentra disponibles.", 501);

            $empresa = $out['data'];

            $mercurio01 = $this->Mercurio01->findFirst();
            $det_tipo = $this->Mercurio06->findFirst("tipo = '{$mercurio30->getTipo()}'")->getDetalle();

            $mercurio30 = new Mercurio30();
            $mercurio30->createAttributes($empresa);

            $htmlEmpresa = View::render('aprobacionemp/tmp/consulta', array(
                'mercurio30' => $mercurio30,
                'mercurio01' => $mercurio01,
                'det_tipo' => $det_tipo,
                '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
                '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
                '_codciu' => ParamsEmpresa::getCiudades(),
                '_codzon' => ParamsEmpresa::getZonas(),
                '_codact' => ParamsEmpresa::getActividades(),
                '_tipsoc' => ParamsEmpresa::getTipoSociedades()
            ));

            $code_estados = array();
            $query = $this->Mercurio11->find();
            foreach ($query as $row) $code_estados[$row->getCodest()] = $row->getDetalle();

            $this->setParamToView("code_estados", $code_estados);
            $this->setParamToView("mercurio30", $mercurio30);
            $this->setParamToView("consulta_empresa", $htmlEmpresa);
            $this->setParamToView("hide_header", true);
            $this->setParamToView("idModel", $id);
            $this->setParamToView("nit", $mercurio30->getNit());
            $this->setParamToView("title", "Empresa Aprobada " . $mercurio30->getNit());
        } catch (DebugException $err) {
            Flash::set_flashdata("error", array(
                "msj" => $err->getMessage(),
                "code" => 201
            ));
            Router::rTa("aprobacionemp/index/A");
            exit;
        }
    }

    /**
     * deshacerAction function
     * metodo ajax permite deshacer la afiliacion de la empresa
     * @param [type] $id
     * @return void
     */
    public function deshacerAction()
    {
        $this->setResponse("ajax");

        $procesadorComando = Comman::Api();
        $empresaServices = new EmpresaServices();
        $notifyEmailServices = new NotifyEmailServices();
        $action = $this->getPostParam('action');
        $codest = $this->getPostParam('codest');
        $sendEmail = $this->getPostParam('send_email');
        $nota = sanetizar($this->getPostParam('nota'));
        $comando = '';
        try {

            $id = $this->getPostParam('id');

            $mercurio30 = (new Mercurio30)->findFirst(" id='{$id}' and estado='A' ");
            if (!$mercurio30) {
                throw new Exception("Los datos de la empresa no son validos para procesar.", 501);
            }

            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio30->getNit()
                    )
                )
            );

            $out = $procesadorComando->toArray();
            $empresaSisu = $out['data'];

            $procesadorComando->runCli(
                array(
                    "servicio" => "DeshacerAfiliaciones",
                    "metodo" => "deshacer_aprobacion_empresa",
                    "params" => array(
                        "nit" => $mercurio30->getNit(),
                        "documento" => $mercurio30->getDocumento(),
                        "tipo_documento" => $mercurio30->getTipdoc(),
                        'fecha_aprueba' => $mercurio30->getFecest(),
                        'nota' => $nota
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
                    'msj' => 'No se realizo ninguna acción, el estado de la empresa no es valido para realizar la acción requerida.',
                    'data' => $empresaSisu
                );
            } else {

                //procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $empresaServices->devolver($mercurio30, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') $notifyEmailServices->emailDevolver($mercurio30, $empresaServices->msjDevolver($mercurio30, $nota));
                }

                if ($action == 'R') {
                    $empresaServices->rechazar($mercurio30, $nota, $codest);
                    if ($sendEmail == 'S')  $notifyEmailServices->emailRechazar($mercurio30, $empresaServices->msjRechazar($mercurio30, $nota));
                }

                if ($action == 'I') {
                    $mercurio30->setEstado('I');
                    $mercurio30->setFecest(date('Y-m-d'));
                    $mercurio30->save();
                }

                $salida = array(
                    'data' => $empresaSisu,
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

    /**
     * reaprobarAction function
     * @return void
     */
    public function reaprobarAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = sanetizar($this->getPostParam('nota'));
            $today = new Date();
            (new Mercurio30)->updateAll("estado='A', fecest='{$today->getUsingFormatDefault()}'", "conditions: id='{$id}' ");

            $item = (new Mercurio10)->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;
            $mercurio10 = new Mercurio10();
            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado("A");
            $mercurio10->setNota($nota);
            $mercurio10->setFecsis($today->getUsingFormatDefault());
            $mercurio10->save();

            $response = array(
                "success" => true,
                "msj" => "Movimiento realizado con éxito"
            );
        } catch (Exception $e) {
            $response = array(
                "success" => false,
                "msj" => "No se pudo realizar el movimiento " . "\n" . $e->getMessage() . "\n " . $e->getLine(),
            );
        }
        $this->renderObject($response);
    }
}
