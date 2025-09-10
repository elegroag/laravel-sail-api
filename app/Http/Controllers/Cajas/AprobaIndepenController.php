<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobaindepenController extends ApplicationController
{

    protected $tipopc = 13;

    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    /**
     * independienteServices variable
     * @var IndependienteServices
     */
    protected $independienteServices;

    /**
     * apruebaSolicitud variable
     * @var ApruebaSolicitud
     */
    protected $apruebaSolicitud;

    public function initialize()
    {
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        Core::importLibrary("ParamsIndependiente", "Collections");
        $this->setTemplateAfter('bone');
        $this->services = Services::Init();
    }

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

        Flash::set_flashdata("filter_independiente", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new IndependienteServices());
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
            "prinom" => "Nombre",
            "priape" => "Apellido",
            "fecini" => "Fecha inicio",
            "fecsol" => "Fecha solicitud"
        );

        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("filters", Flash::get_flashdata_item("filter_params"));
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
        $this->setParamToView("title", "Aprueba Independientes");
        $this->setParamToView("buttons", array("F"));
        $this->loadParametrosView();
    }

    public function opcionalAction($estado = 'P')
    {
        $this->setParamToView("hide_header", true);
        $campo_field = array(
            "nit" => "Nit",
            "razsoc" => "Razon Social"
        );
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Aprobacion Empresa");
        $mercurio41 = $this->Mercurio41->find("estado='{$estado}' AND usuario=" . parent::getActUser() . " ORDER BY fecini ASC");
        $empresas = array();
        foreach ($mercurio41 as $ai => $mercurio) {
            $background = '';
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mercurio->getId(), $mercurio->getFecsol());

            if ($estado == 'P') {
                if ($dias_vencidos == 3) {
                    $background = '#f1f1ad';
                } else if ($dias_vencidos > 3) {
                    $background = '#f5b2b2';
                }
            }

            if ($mercurio->getEstado() == 'A') {
                $url = Core::getInstancePath() . "Cajas/aprobaindepen/infoAprobadoView/" . $mercurio->getId();
            } else {
                $url = Core::getInstancePath() . "Cajas/aprobaindepen/info_empresa/" . $mercurio->getId();
            }

            $sat = "NORMAL";
            $empresas[] = array(
                "estado" => $mercurio->getEstadoDetalle(),
                "recepcion" => $sat,
                "nit" => $mercurio->getCedtra(),
                "background" => $background,
                "razsoc" => $mercurio->getRazsoc(),
                "dias_vencidos" => $dias_vencidos,
                "id" => $mercurio->getId(),
                "url" => $url
            );
        }

        $this->setParamToView("empresas", $empresas);
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("pagina_con_estado", $estado);
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
            Flash::get_flashdata_item("filter_independiente") != false
        ) {
            $query = $pagination->persistencia(Flash::get_flashdata_item("filter_params"));
        } else {
            $query = $pagination->filter(
                $this->getPostParam('campo'),
                $this->getPostParam('condi'),
                $this->getPostParam('value')
            );
        }

        Flash::set_flashdata("filter_independiente", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(
            new IndependienteServices()
        );

        return $this->renderObject($response, false);
    }

    /**
     * devolverAction function
     * @return void
     */
    public function devolverAction()
    {
        $this->setResponse("ajax");
        $independienteServices = new IndependienteServices();
        $notifyEmailServices = new NotifyEmailServices();
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $this->getPostParam('nota');
            $array_corregir = $this->getPostParam('campos_corregir');
            $campos_corregir = implode(";", $array_corregir);

            $mercurio41 = (new Mercurio41)->findFirst("id='{$id}'");
            if ($mercurio41->getEstado() == 'D') {
                throw new Exception("El registro ya se encuentra devuelto, no se requiere de repetir la acción.", 201);
            }

            $independienteServices->devolver($mercurio41, $nota, $codest, $campos_corregir);

            $notifyEmailServices->emailDevolver(
                $mercurio41,
                $independienteServices->msjDevolver($mercurio41, $nota)
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

    /**
     * rechazarAction function
     * @return void
     */
    public function rechazarAction()
    {
        $this->setResponse("ajax");
        $notifyEmailServices = new NotifyEmailServices();
        $indeServices =  new IndependienteServices();
        try {
            $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $this->getPostParam('nota');
            $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");

            $mercurio41 = (new Mercurio41)->findFirst(" id='{$id}'");

            if ($mercurio41->getEstado() == 'X') {
                throw new DebugException("El registro ya se encuentra rechazado, no se requiere de repetir la acción.", 201);
            }

            $indeServices->rechazar($mercurio41, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio41, $indeServices->msjRechazar($mercurio41, $nota));

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
            $mercurio41 = $this->Mercurio41->findFirst("nit='{$nit}' AND estado='A'");
            if (!$mercurio41) {
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

            $asunto = "Afiliacion de la empresa realizada con Exito. Nit: " . $mercurio41->getCedtra();
            $mercurio07 = $this->Mercurio07->findFirst("tipo='{$mercurio41->getTipo()}' and coddoc='{$mercurio41->getCoddoc()}' and documento='{$mercurio41->getDocumento()}'");
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
            $this->setParamToView("mercurio41", $mercurio41);
            $this->setParamToView("actapr", $this->getPostParam('actapr'));
            $this->setParamToView("dia", $feccap->format("d"));
            $this->setParamToView("mes", $meses[intval($feccap->format("m") - 1)]);
            $this->setParamToView("anno", $feccap->format("Y"));
            $this->setParamToView("ruta_firma", "https://comfacaenlinea.com.co/Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg");
            $this->setParamToView("mercurio02", $mercurio02);
            $this->setParamToView("anexo_final", $anexo_final);
            $this->setParamToView("anexo_inicial", $anexo_inicial);
            echo View::renderView("aprobaindepen/mail/aprobar");
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
        Router::redirectToApplication('Cajas/aprobaindepen/pendiente_email');
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
            $mercurio41 = $this->Mercurio41->findFirst("nit='{$nit}' AND estado='A'");
            if (!$mercurio41) {
                throw new Exception("La empresa no está disponible para notificar por email", 501);
            } else {
                $data07 = $this->Mercurio07->find("conditions: documento='{$mercurio41->getDocumento()}'");
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
                    "mercurio41" => array(
                        "id" => $mercurio41->getId(),
                        "nit" => $mercurio41->getCedtra(),
                        "tipdoc" => $mercurio41->getTipdoc(),
                        "razsoc" => $mercurio41->getRazsoc(),
                        "email" => $mercurio41->getEmail(),
                        "estado" => $mercurio41->getEstado(),
                        "fecest" => $mercurio41->getFecest()->getUsingFormatDefault(),
                        "fecini" => $mercurio41->getFecini()->getUsingFormatDefault()
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
     * inforAction function
     * mostrar la ficha de afiliación de la empresa
     * @return void
     */
    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $independienteServices = new IndependienteServices();
            $id = $this->getPostParam('id');
            if (!$id) {
                throw new Exception("Error se requiere del id independiente", 501);
            }

            $mercurio41 = $this->Mercurio41->findFirst("id='{$id}'");
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_independiente"
                )
            );

            $datos_captura =  $procesadorComando->toArray();
            $paramsIndependiente = new ParamsIndependiente();
            $paramsIndependiente->setDatosCaptura($datos_captura);

            $htmlEmpresa = View::render('aprobaindepen/tmp/consulta', array(
                'mercurio41' => $mercurio41,
                'mercurio01' => $this->Mercurio01->findFirst(),
                'det_tipo' => $this->Mercurio06->findFirst("tipo = '{$mercurio41->getTipo()}'")->getDetalle(),
                '_coddoc' => ParamsIndependiente::getTipoDocumentos(),
                '_calemp' => ParamsIndependiente::getCalidadEmpresa(),
                '_codciu' => ParamsIndependiente::getCiudades(),
                '_codzon' => ParamsIndependiente::getZonas(),
                '_codact' => ParamsIndependiente::getActividades(),
                '_tipsoc' => ParamsIndependiente::getTipoSociedades(),
                '_tipdoc' => ParamsIndependiente::getTipoDocumentos(),
                '_cargos' => ParamsIndependiente::getOcupaciones(),
                '_sexos'  => ParamsIndependiente::getSexos(),
                '_estciv' => ParamsIndependiente::getEstadoCivil(),
                '_tipdis' => ParamsIndependiente::getTipoDiscapacidad(),
                '_nivedu' => ParamsIndependiente::getNivelEducativo(),
                '_tipafi' => ParamsIndependiente::getTipoAfiliado(),
            ));

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio41->getCedtra()
                    )
                )
            );
            $out =  $procesadorComando->toArray();

            if ($out['success']) {
                $this->setParamToView("empresa_sisuweb", $out['data']);
            }
            $response = array(
                'success' => true,
                'data' => $mercurio41->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta_empresa" => $htmlEmpresa,
                'adjuntos' => $independienteServices->adjuntos($mercurio41),
                'seguimiento' => $independienteServices->seguimiento($mercurio41),
                'campos_disponibles' => $mercurio41->CamposDisponibles()
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
                "metodo" => "parametros_independiente"
            )
        );

        $paramsIndependiente = new ParamsIndependiente();
        $paramsIndependiente->setDatosCaptura($procesadorComando->toArray());

        $_coddocrepleg = array();
        foreach (ParamsIndependiente::getCodruaDocumentos()  as $ai =>  $valor) {
            if ($valor == 'TI' || $valor == 'RC') continue;
            $_coddocrepleg[$ai] = $valor;
        }

        $this->setParamToView("_tipdur", ParamsIndependiente::getTipoDuracion());
        $this->setParamToView("_codind", ParamsIndependiente::getCodigoIndice());
        $this->setParamToView("_contratista", array('estado' => 'N', 'detalle' => 'NO'));
        $this->setParamToView("_todmes", ParamsIndependiente::getPagaMes());
        $this->setParamToView("_forpre", ParamsIndependiente::getFormaPresentacion());
        $this->setParamToView("_tipsoc", ParamsIndependiente::getTipoSociedades());
        $this->setParamToView("_pymes",  array('estado' => 'N', 'detalle' => 'NO'));
        $this->setParamToView("_tipemp", ParamsIndependiente::getTipoEmpresa());
        $this->setParamToView("_tipapo", ParamsIndependiente::getTipoAportante());
        $this->setParamToView("_ofiafi", array('estado' => '13', 'detalle' => '13'));
        $this->setParamToView("_colegio", array('estado' => 'N', 'detalle' => 'NO'));
        $this->setParamToView("_tipper", ParamsIndependiente::getTipoPersona());
        $this->setParamToView("_codzon", ParamsIndependiente::getZonas());
        $this->setParamToView("_calemp", ParamsIndependiente::getCalidadEmpresa());
        $this->setParamToView("_codciu", ParamsIndependiente::getCiudades());
        $this->setParamToView("_codact", ParamsIndependiente::getActividades());
        $this->setParamToView("_coddoc", ParamsIndependiente::getTipoDocumentos());
        $this->setParamToView("_tippag", ParamsIndependiente::getTipoPago());
        $this->setParamToView("_bancos", ParamsIndependiente::getBancos());
        $this->setParamToView("_tipcue", ParamsIndependiente::getTipoCuenta());
        $this->setParamToView("_giro", ParamsIndependiente::getGiro());
        $this->setParamToView("_codgir", ParamsIndependiente::getCodigoGiro());
        $this->setParamToView("_coddocrepleg", $_coddocrepleg);
    }

    /**
     * editarViewAction function
     * @param integer $id
     * @return void
     */
    public function editarViewAction($id)
    {
        if (!$id) {
            Router::rTa("aprobaindepen/index");
            exit;
        }
        $this->independienteServices = new IndependienteServices();
        $this->setParamToView("hide_header", true);
        $mercurio41 = $this->Mercurio41->findFirst("id='{$id}'");
        $this->setParamToView("mercurio41", $mercurio41);
        $this->setParamToView("tipopc", $this->tipopc);
        $this->setParamToView("seguimiento",  $this->independienteServices->seguimiento($mercurio41));

        $mercurio01 = $this->Mercurio01->findFirst();
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_independiente"
            ),
            false
        );
        $paramsEmpresa = new ParamsIndependiente();
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

        $this->loadParametrosView();
        $mercurio37 = $this->Mercurio37->find(" tipopc=2 AND numero='{$mercurio41->getId()}'");
        $this->independienteServices->loadDisplay($mercurio41);
        $this->setParamToView("mercurio37", $mercurio37);
        $this->setParamToView("idModel", $id);
        $this->setParamToView("det_tipo", $this->Mercurio06->findFirst("tipo = '{$mercurio41->getTipo()}'")->getDetalle());
        $this->setParamToView("mercurio01", $mercurio01);
        $this->setParamToView("title", "Editar Ficha Independiente " . $mercurio41->getCedtra());
    }

    public function edita_empresaAction()
    {
        $this->setResponse("ajax");
        $nit = $this->getPostParam('nit');
        $id = $this->getPostParam('id');
        try {
            $mercurio41 = $this->Mercurio41->findFirst("nit='{$nit}' AND id='{$id}'");
            if (!$mercurio41) {
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
                $this->Mercurio41->updateAll($setters, "conditions: id='{$id}' AND nit='{$nit}'");
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
     * buscarEnSisuAction function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     * @param [type] $nit
     * @return void
     */
    public function buscarEnSisuViewAction($id)
    {

        $mercurio41 = $this->Mercurio41->findFirst("id='{$id}'");
        if (!$mercurio41) {
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
                    "nit" => $mercurio41->getCedtra()
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
        $this->setParamToView("title", "Independiente SisuWeb - {$mercurio41->getCedtra()}");
    }

    /**
     * excel_reporte function
     * pendientes, devueltos y rechazados
     * @return void
     */
    public function excel_reporteAction($estado = 'P')
    {
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
        $solicitudes = $this->Mercurio41->find(" estado='{$estado}' AND usuario='{$usuario}' ORDER BY fecini DESC");
        $j++;
        foreach ($solicitudes as $solicitud) {
            $i = 0;
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $solicitud->getId(), $solicitud->getFecsol());
            $excel->write($j, $i++, $solicitud->getDocumento(), $column_style);
            $excel->write($j, $i++, $solicitud->getCedtra(), $column_style);
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

                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $calemp = 'I';
                $solicitud = $apruebaSolicitud->main(
                    $calemp,
                    $idSolicitud,
                    $_POST
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
        Flash::set_flashdata("filter_independiente", false, true);
        Flash::set_flashdata("filter_params", false, true);
        return $this->renderObject(array(
            'success' => true,
            'query' => Flash::get_flashdata_item("filter_independiente"),
            'filter' => Flash::get_flashdata_item("filter_params"),
        ));
    }


    /**
     * aportesViewAction function
     * @param integer $id
     * @return void
     */
    public function aportesViewAction($id)
    {
        $mercurio41 = $this->Mercurio41->findFirst(" id='{$id}'");
        if (!$mercurio41) {
            Flash::set_flashdata("error", array(
                "msj" => "La empresa no se encuentra registrada.",
                "code" => 201
            ));
            Router::rTa("aprobaindepen/info/" . $id);
            exit();
        }

        $this->setParamToView("hide_header", true);
        $this->setParamToView("idModel", $id);
        $this->setParamToView("cedtra", $mercurio41->getCedtra());
        $this->setParamToView("title", "Aportes de empresa " . $mercurio41->getCedtra());
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
                $mercurio41 = (new Mercurio41)->findFirst(" id='{$id}'");
                if (!$mercurio41) {
                    throw new Exception("La empresa no se encuentra registrada.", 201);
                }

                $procesadorComando = Comman::Api();
                $procesadorComando->runCli(
                    array(
                        "servicio" => "AportesEmpresas",
                        "metodo" => "buscarAportesEmpresa",
                        "params" => $mercurio41->getCedtra()
                    )
                );

                if ($procesadorComando->isJson() == False) {
                    throw new Exception("Error procesando la consulta de aportes", 501);
                }

                $salida = $procesadorComando->toArray();
                $salida['solicitud'] = $mercurio41->getArray();
            } catch (TransactionFailed $e) {
                throw new Exception($e->getMessage(), 501);
            }
        } catch (Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => "No se pudo realizar el movimiento " . "\n" . $err->getMessage() . "\n " . $err->getLine(),
            );
        }
        return $this->renderObject($salida);
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
            $mercurio41 = (new Mercurio41)->findFirst(" id='{$id}' and estado='A' ");
            if (!$mercurio41) {
                throw new DebugException("La empresa no se encuentra aprobada para consultar sus datos.", 501);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_empresa"
                )
            );
            $datos_captura =  $procesadorComando->toArray();
            $paramsEmpresa = new ParamsIndependiente();
            $paramsEmpresa->setDatosCaptura($datos_captura);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio41->getCedtra()
                    )
                )
            );

            if ($procesadorComando->isJson() == False) {
                throw new DebugException("Error al buscar la empresa en Sisuweb", 501);
            }

            $out = $procesadorComando->toArray();
            $empresa = $out['data'];

            $mercurio01 = $this->Mercurio01->findFirst();
            $det_tipo = $this->Mercurio06->findFirst("tipo = '{$mercurio41->getTipo()}'")->getDetalle();

            $mercurio41 = new Mercurio41();
            $mercurio41->createAttributes($empresa);

            $htmlEmpresa = View::render('aprobaindepen/tmp/consulta', array(
                'mercurio41' => $mercurio41,
                'mercurio01' => $mercurio01,
                'det_tipo' => $det_tipo,
                '_coddoc' => ParamsIndependiente::getTipoDocumentos(),
                '_calemp' => ParamsIndependiente::getCalidadEmpresa(),
                '_codciu' => ParamsIndependiente::getCiudades(),
                '_codzon' => ParamsIndependiente::getZonas(),
                '_codact' => ParamsIndependiente::getActividades(),
                '_tipsoc' => ParamsIndependiente::getTipoSociedades()
            ));

            $code_estados = array();
            $query = $this->Mercurio11->find();
            foreach ($query as $row) $code_estados[$row->getCodest()] = $row->getDetalle();

            $this->setParamToView("code_estados", $code_estados);
            $this->setParamToView("mercurio41", $mercurio41);
            $this->setParamToView("consulta_empresa", $htmlEmpresa);
            $this->setParamToView("hide_header", true);
            $this->setParamToView("idModel", $id);
            $this->setParamToView("nit", $mercurio41->getCedtra());
            $this->setParamToView("title", "Empresa Aprobada " . $mercurio41->getCedtra());
        } catch (DebugException $err) {
            Flash::set_flashdata("error", array(
                "msj" => $err->getMessage(),
                "code" => 201
            ));
            Router::rTa("aprobaindepen/index/A");
            exit;
        }
    }

    public function deshacerAction()
    {
        $this->setResponse("ajax");
        $indepeServices = new IndependienteServices();
        $notifyEmailServices = new NotifyEmailServices();
        $action = $this->getPostParam('action');
        $codest = $this->getPostParam('codest');
        $sendEmail = $this->getPostParam('send_email');
        $nota = $this->getPostParam('nota');

        try {
            $id = $this->getPostParam('id');

            $mercurio41 = (new Mercurio41)->findFirst("id='{$id}'");
            if (!$mercurio41) {
                throw new Exception("Los datos de la empresa no son validos para procesar.", 501);
            }

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio41->getCedtra(),
                        "coddoc" => $mercurio41->getTipdoc(),
                    )
                )
            );

            if ($ps->isJson() == False) throw new Exception("Error al buscar la empresa en Sisuweb", 501);

            $out = $ps->toArray();
            $empresaSisu = $out['data'];

            $ps->runCli(
                array(
                    "servicio" => "DeshacerAfiliaciones",
                    "metodo" => "deshacer_aprobacion_independiente",
                    "params" => array(
                        "cedtra" => $mercurio41->getCedtra(),
                        "coddoc" => $mercurio41->getTipdoc(),
                        "fecafi" => $mercurio41->getFecapr(),
                        'fecapr' => $mercurio41->getFecapr(),
                        'nota' => $nota,
                    )
                )
            );

            if ($ps->isJson() == False) {
                throw new Exception("Error al procesar el deshacer la aprobación en SisuWeb.", 501);
            }

            $resdev = $ps->toArray();
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
                    $indepeServices->devolver($mercurio41, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') $notifyEmailServices->emailDevolver($mercurio41, $indepeServices->msjDevolver($mercurio41, $nota));
                }

                if ($action == 'R') {
                    $indepeServices->rechazar($mercurio41, $nota, $codest);
                    if ($sendEmail == 'S') $notifyEmailServices->emailRechazar($mercurio41, $indepeServices->msjRechazar($mercurio41, $nota));
                }

                if ($action == 'I') {
                    $mercurio41->setEstado('I');
                    $mercurio41->setFecest(date('Y-m-d'));
                    $mercurio41->save();
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
